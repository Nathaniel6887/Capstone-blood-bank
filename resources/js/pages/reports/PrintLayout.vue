<script setup lang="ts">
import { ref, computed } from 'vue';
import { 
    Printer, 
    X, 
    FileText, 
    Download, 
    CheckCircle2, 
    Calendar, 
    Building2,
    Filter
} from '@lucide/vue';
import type { DonorItem, IssueItem } from './Index.vue';
import type { BloodReportRecord } from './View.vue';

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        reportType: string;
        selectedYear: string;
        donors?: DonorItem[];
        reports?: BloodReportRecord[];
        issues?: IssueItem[];
    }>(),
    {
        isOpen: false,
        donors: () => [],
        reports: () => [],
        issues: () => [],
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
}>();

// Filter Category within print dialog
const selectedCategory = ref<string>('All');
const includeSignatures = ref(true);
const includeSummaryStats = ref(true);

// Helpers
const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return isNaN(d.getTime())
        ? dateStr
        : d.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
};

const currentDate = new Date().toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

// Quarter helper
const getDonorQuarter = (dateStr?: string): 'Q1' | 'Q2' | 'Q3' | 'Q4' => {
    if (!dateStr) return 'Q1';
    const month = new Date(dateStr).getMonth();
    if (month >= 0 && month <= 2) return 'Q1';
    if (month >= 3 && month <= 5) return 'Q2';
    if (month >= 6 && month <= 8) return 'Q3';
    return 'Q4';
};

// Filtered donors by year
const yearDonors = computed(() => {
    return props.donors.filter((d) => {
        if (!d.created_at) return true;
        const year = new Date(d.created_at).getFullYear().toString();
        return year === props.selectedYear;
    });
});

// ==========================================
// CATEGORIZED DATA STRUCTURES
// ==========================================

// 1. QUARTERLY CATEGORIES
const quarterlyCategories = computed(() => {
    const quarters = [
        { id: 'Q1', title: `Quarter 1 (January - March ${props.selectedYear})`, donors: yearDonors.value.filter(d => getDonorQuarter(d.created_at) === 'Q1') },
        { id: 'Q2', title: `Quarter 2 (April - June ${props.selectedYear})`, donors: yearDonors.value.filter(d => getDonorQuarter(d.created_at) === 'Q2') },
        { id: 'Q3', title: `Quarter 3 (July - September ${props.selectedYear})`, donors: yearDonors.value.filter(d => getDonorQuarter(d.created_at) === 'Q3') },
        { id: 'Q4', title: `Quarter 4 (October - December ${props.selectedYear})`, donors: yearDonors.value.filter(d => getDonorQuarter(d.created_at) === 'Q4') },
    ];
    if (selectedCategory.value === 'All') return quarters;
    return quarters.filter(q => q.id === selectedCategory.value);
});

// 2. MONTHLY CATEGORIES
const monthlyCategories = computed(() => {
    const months = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    const categories = months.map(m => {
        const matching = yearDonors.value.filter(d => {
            if (!d.created_at) return false;
            return new Date(d.created_at).toLocaleDateString('en-US', { month: 'long' }) === m;
        });
        return { id: m, title: `${m} ${props.selectedYear}`, donors: matching };
    }).filter(c => c.donors.length > 0 || selectedCategory.value === c.id);

    if (selectedCategory.value === 'All') return categories;
    return categories.filter(c => c.id === selectedCategory.value);
});

// 3. AGE BRACKET CATEGORIES
const ageCategories = computed(() => {
    const brackets = [
        { id: '18-25', title: 'Age Group: 18 - 25 Years Old (Young Adult Donors)', donors: props.donors.filter(d => d.age >= 18 && d.age <= 25) },
        { id: '26-35', title: 'Age Group: 26 - 35 Years Old (Prime Adult Donors)', donors: props.donors.filter(d => d.age >= 26 && d.age <= 35) },
        { id: '36-50', title: 'Age Group: 36 - 50 Years Old (Experienced Donors)', donors: props.donors.filter(d => d.age >= 36 && d.age <= 50) },
        { id: '51+', title: 'Age Group: 51 - 65 Years Old (Senior Qualified Donors)', donors: props.donors.filter(d => d.age >= 51) },
    ];
    if (selectedCategory.value === 'All') return brackets;
    return brackets.filter(b => b.id === selectedCategory.value);
});

// 4. GENDER CATEGORIES
const genderCategories = computed(() => {
    const genders = [
        { id: 'Male', title: 'Gender Category: Male Registered Donors', donors: props.donors.filter(d => d.gender === 'Male') },
        { id: 'Female', title: 'Gender Category: Female Registered Donors', donors: props.donors.filter(d => d.gender === 'Female') },
    ];
    if (selectedCategory.value === 'All') return genders;
    return genders.filter(g => g.id === selectedCategory.value);
});

// 5. CONSUMPTION BY LOCATION / WARD CATEGORIES
const consumptionCategories = computed(() => {
    const map = new Map<string, IssueItem[]>();
    props.issues.forEach(issue => {
        const loc = issue.ward_room || 'General Ward';
        if (!map.has(loc)) map.set(loc, []);
        map.get(loc)!.push(issue);
    });

    const categories = Array.from(map.entries()).map(([loc, list]) => ({
        id: loc,
        title: `Location / Facility: ${loc}`,
        totalBags: list.reduce((sum, item) => sum + (Number(item.units_issued) || 0), 0),
        issues: list,
    }));

    if (selectedCategory.value === 'All') return categories;
    return categories.filter(c => c.id === selectedCategory.value);
});

// 6. REGIONAL REPORTS CATEGORIES (PROVINCE / REGION)
const regionalReportCategories = computed(() => {
    const isOutside = props.reportType.includes('Outside');
    const filteredReports = props.reports.filter(r => isOutside ? r.scope === 'Outside Caraga' : r.scope === 'Inside Caraga');
    
    const map = new Map<string, BloodReportRecord[]>();
    filteredReports.forEach(r => {
        const key = r.province_region || 'Unassigned';
        if (!map.has(key)) map.set(key, []);
        map.get(key)!.push(r);
    });

    const categories = Array.from(map.entries()).map(([prov, list]) => ({
        id: prov,
        title: `${isOutside ? 'Partner Region' : 'Province'}: ${prov}`,
        totalVolume: list.reduce((sum, item) => sum + (Number(item.blood_units_count) || 0), 0),
        reports: list,
    }));

    if (selectedCategory.value === 'All') return categories;
    return categories.filter(c => c.id === selectedCategory.value);
});

// Total Summary Statistics for Printout
const summaryTotalCount = computed(() => {
    if (props.reportType === 'Quarterly New Donor Report') return yearDonors.value.length;
    if (props.reportType === 'Monthly Donor Report') return monthlyCategories.value.reduce((sum, c) => sum + c.donors.length, 0);
    if (props.reportType === 'Blood Consumption Report') return props.issues.reduce((sum, item) => sum + (Number(item.units_issued) || 0), 0);
    if (props.reportType.includes('Reports') || props.reportType.includes('Transfers')) {
        return regionalReportCategories.value.reduce((sum, c) => sum + c.totalVolume, 0);
    }
    return props.donors.length;
});

// Blood Type Breakdown Map for Summary
const summaryBloodBreakdown = computed(() => {
    const map: Record<string, number> = { 'O+': 0, 'A+': 0, 'B+': 0, 'AB+': 0, 'O-': 0, 'A-': 0, 'B-': 0, 'AB-': 0 };
    if (props.reportType === 'Blood Consumption Report') {
        props.issues.forEach(i => {
            if (i.blood_type in map) map[i.blood_type] += Number(i.units_issued) || 0;
        });
    } else {
        props.donors.forEach(d => {
            if (d.blood_type in map) map[d.blood_type]++;
        });
    }
    return map;
});

// Trigger Print
const executePrint = () => {
    window.print();
};
</script>

<template>
    <div 
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-6 overflow-y-auto print:p-0 print:bg-white print:static print:overflow-visible"
    >
        <div class="relative w-full max-w-5xl rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 shadow-2xl transition-all my-6 max-h-[94vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 print:max-w-none print:m-0 print:p-6 print:border-none print:shadow-none print:max-h-none print:overflow-visible text-slate-900">
            
            <!-- CONTROLS BAR (Hidden on Print) -->
            <div class="pb-5 mb-5 border-b border-slate-200 dark:border-neutral-800 flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400">
                        <Printer class="size-5 text-[#a32222]" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                            Categorized Print Layout Preview
                        </h2>
                        <p class="text-xs text-slate-500">
                            Configure report category grouping and generate official hospital document.
                        </p>
                    </div>
                </div>

                <!-- Print Options: Category Filter & Actions -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Category Selector -->
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="font-bold text-slate-600 dark:text-slate-400">Category:</span>
                        <select
                            v-model="selectedCategory"
                            class="h-9 px-3 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 dark:bg-neutral-800 dark:text-neutral-100 dark:border-neutral-700 focus:outline-hidden"
                        >
                            <option value="All">All Categories (Comprehensive)</option>
                            
                            <!-- Quarterly Options -->
                            <template v-if="reportType === 'Quarterly New Donor Report'">
                                <option value="Q1">Quarter 1 (Jan - Mar)</option>
                                <option value="Q2">Quarter 2 (Apr - Jun)</option>
                                <option value="Q3">Quarter 3 (Jul - Sep)</option>
                                <option value="Q4">Quarter 4 (Oct - Dec)</option>
                            </template>

                            <!-- Monthly Options -->
                            <template v-else-if="reportType === 'Monthly Donor Report'">
                                <option v-for="m in ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']" :key="m" :value="m">
                                    {{ m }}
                                </option>
                            </template>

                            <!-- Age Options -->
                            <template v-else-if="reportType === 'Donor Age Report'">
                                <option value="18-25">18 - 25 yrs (Young Adults)</option>
                                <option value="26-35">26 - 35 yrs (Prime Adults)</option>
                                <option value="36-50">36 - 50 yrs (Experienced)</option>
                                <option value="51+">51+ yrs (Senior Qualified)</option>
                            </template>

                            <!-- Gender Options -->
                            <template v-else-if="reportType === 'Gender Report'">
                                <option value="Male">Male Donors</option>
                                <option value="Female">Female Donors</option>
                            </template>
                        </select>
                    </div>

                    <!-- Signatures Toggle -->
                    <label class="flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 cursor-pointer">
                        <input type="checkbox" v-model="includeSignatures" class="rounded text-[#a32222] focus:ring-0" />
                        <span>Signatures</span>
                    </label>

                    <!-- Print Button -->
                    <button
                        type="button"
                        @click="executePrint"
                        class="h-9 px-4 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                    >
                        <Printer class="size-3.5" />
                        <span>Print Document</span>
                    </button>

                    <!-- Close Button -->
                    <button
                        type="button"
                        @click="emit('close')"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 cursor-pointer transition-colors"
                    >
                        <X class="size-5" />
                    </button>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- OFFICIAL PRINT DOCUMENT BODY (Categorized & Styled for Paper/PDF) -->
            <!-- ========================================================================= -->
            <div class="space-y-6 text-slate-900 print:text-black">
                
                <!-- OFFICIAL HOSPITAL HEADER -->
                <div class="text-center pb-5 border-b-2 border-slate-900 space-y-1">
                    <div class="text-[10px] uppercase font-bold tracking-widest text-slate-600">
                        Republic of the Philippines • Department of Health • Center for Health Development Caraga
                    </div>
                    <div class="text-xl sm:text-2xl font-black tracking-tight text-slate-900">
                        CARAGA REGIONAL HOSPITAL
                    </div>
                    <div class="text-xs font-black text-[#a32222] uppercase tracking-widest">
                        Blood Bank & Transfusion Medicine Section • Regional Network Registry
                    </div>
                    <div class="text-[10px] text-slate-500 font-medium">
                        Rizal Street, Surigao City, Surigao del Norte 8400 • Tel: (086) 826-2459 • crh.bloodbank@doh.gov.ph
                    </div>
                </div>

                <!-- DOCUMENT TITLE & CLASSIFICATION BAR -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-slate-300 gap-2 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Official Document Classification:</span>
                        <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">
                            {{ reportType }} ({{ selectedYear }})
                        </h3>
                    </div>
                    <div class="text-left sm:text-right space-y-0.5">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">
                            Generated On: <span class="text-slate-900 font-bold font-mono">{{ currentDate }}</span>
                        </div>
                        <div class="text-[10px] font-bold text-slate-500 uppercase">
                            Report Status: <span class="text-emerald-700 font-extrabold uppercase">Official Certified</span>
                        </div>
                    </div>
                </div>

                <!-- SUMMARY AUDIT METRIC BANNER -->
                <div v-if="includeSummaryStats" class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-xl border border-slate-300 bg-slate-50 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Total Volume / Log:</span>
                        <span class="text-xl font-black text-[#a32222] block mt-0.5">
                            {{ summaryTotalCount }} {{ reportType.includes('Consumption') || reportType.includes('Transfer') || reportType.includes('Regional') ? 'Bags Accounted' : 'Registered Donors' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Selected Scope / Year:</span>
                        <span class="text-sm font-bold text-slate-900 block mt-1">
                            Year {{ selectedYear }} ({{ selectedCategory === 'All' ? 'All Categories' : selectedCategory }})
                        </span>
                    </div>

                    <div class="col-span-2">
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Blood Type Summary Distribution:</span>
                        <div class="flex items-center gap-2 flex-wrap mt-1 font-mono text-[11px] font-bold text-slate-800">
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">O+: {{ summaryBloodBreakdown['O+'] }}</span>
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">A+: {{ summaryBloodBreakdown['A+'] }}</span>
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">B+: {{ summaryBloodBreakdown['B+'] }}</span>
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">AB+: {{ summaryBloodBreakdown['AB+'] }}</span>
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">O-: {{ summaryBloodBreakdown['O-'] }}</span>
                            <span class="bg-white px-2 py-0.5 rounded border border-slate-200">A-: {{ summaryBloodBreakdown['A-'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- CATEGORIZED SECTIONS TABLE -->
                <!-- ========================================================================= -->

                <!-- 1. QUARTERLY CATEGORIES TABLE -->
                <div v-if="reportType === 'Quarterly New Donor Report'" class="space-y-6">
                    <div 
                        v-for="quarter in quarterlyCategories" 
                        :key="quarter.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <!-- Category Header Banner -->
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ quarter.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                {{ quarter.donors.length }} {{ quarter.donors.length === 1 ? 'Donor' : 'Donors' }} Logged
                            </span>
                        </div>

                        <!-- Category Donors Table -->
                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">#</th>
                                    <th class="p-2 border-r border-slate-300">Donor Name</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Blood Type</th>
                                    <th class="p-2 border-r border-slate-300">Age / Gender</th>
                                    <th class="p-2 border-r border-slate-300">Municipality / Location</th>
                                    <th class="p-2 border-r border-slate-300">Contact Number</th>
                                    <th class="p-2">Date Registered</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="(donor, idx) in quarter.donors" :key="donor.id" class="hover:bg-slate-50">
                                    <td class="p-2 border-r border-slate-300 font-mono text-center">{{ idx + 1 }}</td>
                                    <td class="p-2 border-r border-slate-300 font-bold">{{ donor.full_name }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black text-rose-800">{{ donor.blood_type }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.age }} yrs • {{ donor.gender }}</td>
                                    <td class="p-2 border-r border-slate-300 font-medium">{{ donor.municipality }}</td>
                                    <td class="p-2 border-r border-slate-300 font-mono">{{ donor.contact_information }}</td>
                                    <td class="p-2 font-medium">{{ formatDate(donor.created_at) }}</td>
                                </tr>
                                <tr v-if="quarter.donors.length === 0">
                                    <td colspan="7" class="p-3 text-center text-slate-400 italic">
                                        No registered donors logged for this quarterly period.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. MONTHLY CATEGORIES TABLE -->
                <div v-else-if="reportType === 'Monthly Donor Report'" class="space-y-6">
                    <div 
                        v-for="month in monthlyCategories" 
                        :key="month.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ month.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                {{ month.donors.length }} Donors
                            </span>
                        </div>

                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">#</th>
                                    <th class="p-2 border-r border-slate-300">Donor Name</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Blood Type</th>
                                    <th class="p-2 border-r border-slate-300">Age / Gender</th>
                                    <th class="p-2 border-r border-slate-300">Location</th>
                                    <th class="p-2 border-r border-slate-300">Contact</th>
                                    <th class="p-2">Date Logged</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="(donor, idx) in month.donors" :key="donor.id">
                                    <td class="p-2 border-r border-slate-300 text-center font-mono">{{ idx + 1 }}</td>
                                    <td class="p-2 border-r border-slate-300 font-bold">{{ donor.full_name }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black text-rose-800">{{ donor.blood_type }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.age }} yrs • {{ donor.gender }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.municipality }}</td>
                                    <td class="p-2 border-r border-slate-300 font-mono">{{ donor.contact_information }}</td>
                                    <td class="p-2">{{ formatDate(donor.created_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. AGE BRACKET CATEGORIES TABLE -->
                <div v-else-if="reportType === 'Donor Age Report'" class="space-y-6">
                    <div 
                        v-for="bracket in ageCategories" 
                        :key="bracket.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ bracket.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                {{ bracket.donors.length }} Donors
                            </span>
                        </div>

                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">#</th>
                                    <th class="p-2 border-r border-slate-300">Donor Name</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Age</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Blood Type</th>
                                    <th class="p-2 border-r border-slate-300">Gender</th>
                                    <th class="p-2 border-r border-slate-300">Location</th>
                                    <th class="p-2">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="(donor, idx) in bracket.donors" :key="donor.id">
                                    <td class="p-2 border-r border-slate-300 text-center font-mono">{{ idx + 1 }}</td>
                                    <td class="p-2 border-r border-slate-300 font-bold">{{ donor.full_name }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-bold text-[#a32222]">{{ donor.age }} yrs</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black">{{ donor.blood_type }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.gender }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.municipality }}</td>
                                    <td class="p-2 font-mono">{{ donor.contact_information }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 4. GENDER CATEGORIES TABLE -->
                <div v-else-if="reportType === 'Gender Report'" class="space-y-6">
                    <div 
                        v-for="gender in genderCategories" 
                        :key="gender.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ gender.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                {{ gender.donors.length }} Donors
                            </span>
                        </div>

                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">#</th>
                                    <th class="p-2 border-r border-slate-300">Donor Name</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Gender</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Age</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Blood Type</th>
                                    <th class="p-2 border-r border-slate-300">Location</th>
                                    <th class="p-2">Contact</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="(donor, idx) in gender.donors" :key="donor.id">
                                    <td class="p-2 border-r border-slate-300 text-center font-mono">{{ idx + 1 }}</td>
                                    <td class="p-2 border-r border-slate-300 font-bold">{{ donor.full_name }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-bold">{{ donor.gender }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center">{{ donor.age }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black">{{ donor.blood_type }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ donor.municipality }}</td>
                                    <td class="p-2 font-mono">{{ donor.contact_information }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. BLOOD CONSUMPTION BY LOCATION TABLE -->
                <div v-else-if="reportType === 'Blood Consumption Report'" class="space-y-6">
                    <div 
                        v-for="category in consumptionCategories" 
                        :key="category.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ category.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                Total: {{ category.totalBags }} Bags Issued
                            </span>
                        </div>

                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">Requisition #</th>
                                    <th class="p-2 border-r border-slate-300">Patient / HRN</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Blood Type</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Quantity</th>
                                    <th class="p-2 border-r border-slate-300">Component</th>
                                    <th class="p-2 border-r border-slate-300">Status</th>
                                    <th class="p-2">Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="issue in category.issues" :key="issue.id">
                                    <td class="p-2 border-r border-slate-300 font-mono font-bold text-[#a32222]">{{ issue.requisition_no }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ issue.patient_name }} ({{ issue.patient_hrn }})</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black">{{ issue.blood_type }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black">{{ issue.units_issued }} Bags</td>
                                    <td class="p-2 border-r border-slate-300">{{ issue.blood_component }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ issue.status }}</td>
                                    <td class="p-2 font-medium">{{ formatDate(issue.issued_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 6. REGIONAL REPORTS / TRANSFERS BY PROVINCE OR REGION -->
                <div v-else class="space-y-6">
                    <div 
                        v-for="prov in regionalReportCategories" 
                        :key="prov.id"
                        class="space-y-2 break-inside-avoid"
                    >
                        <div class="flex items-center justify-between bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-xs">
                            <span class="uppercase tracking-wider font-extrabold">{{ prov.title }}</span>
                            <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] font-black">
                                Total: {{ prov.totalVolume }} Bags Accounted
                            </span>
                        </div>

                        <table class="w-full text-left text-xs border border-slate-300">
                            <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px] border-b border-slate-300">
                                <tr>
                                    <th class="p-2 border-r border-slate-300">Report Code</th>
                                    <th class="p-2 border-r border-slate-300">Facility Origin</th>
                                    <th class="p-2 border-r border-slate-300">Report Classification</th>
                                    <th class="p-2 border-r border-slate-300">Reporting Period</th>
                                    <th class="p-2 border-r border-slate-300 text-center">Volume</th>
                                    <th class="p-2 border-r border-slate-300">Submitting Officer</th>
                                    <th class="p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr v-for="r in prov.reports" :key="r.id">
                                    <td class="p-2 border-r border-slate-300 font-mono font-bold text-[#a32222]">{{ r.report_code }}</td>
                                    <td class="p-2 border-r border-slate-300 font-bold">{{ r.facility_name }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ r.report_type }}</td>
                                    <td class="p-2 border-r border-slate-300">{{ r.reporting_period }}</td>
                                    <td class="p-2 border-r border-slate-300 text-center font-black">{{ r.blood_units_count }} Bags</td>
                                    <td class="p-2 border-r border-slate-300">{{ r.submitting_officer }}</td>
                                    <td class="p-2 font-bold">{{ r.status }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- OFFICIAL SIGNATURES & REGULATORY VERIFICATION BLOCK -->
                <!-- ========================================================================= -->
                <div v-if="includeSignatures" class="pt-10 pb-4 border-t-2 border-slate-900 grid grid-cols-3 gap-6 text-xs text-center break-inside-avoid">
                    <div>
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900">
                            A. Salazar, RMT / K. Lopez, RMT
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                            Prepared by: Blood Bank Officer (RMT)
                        </div>
                    </div>

                    <div>
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900">
                            Dr. Emmanuel Salcedo, MD, FPSP
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                            Verified by: Head, Blood Bank Section
                        </div>
                    </div>

                    <div>
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900">
                            Dr. Cheryl A. Gotinga, MD, FPCHA
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                            Approved by: Medical Center Chief II
                        </div>
                    </div>
                </div>

                <!-- FOOTER BARCODE & CERTIFICATION STAMP -->
                <div class="pt-4 border-t border-slate-300 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <div>Caraga Regional Hospital • ISO 9001:2015 Certified Blood Services</div>
                    <div class="font-black text-slate-900 text-sm tracking-widest">||||| |||| ||| |||||||</div>
                </div>

            </div>

        </div>
    </div>
</template>
