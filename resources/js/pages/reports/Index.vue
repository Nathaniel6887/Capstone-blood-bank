<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Printer, 
    Download, 
    Plus, 
    Eye, 
    Calendar,
    FileText,
    ChevronDown,
    MapPin,
    Globe,
    Phone,
    User,
    Droplets,
    Activity,
    Users,
    Layers,
    ShieldCheck
} from '@lucide/vue';
import CreateModal from './Create.vue';
import ViewModal, { type BloodReportRecord, type DonorViewRecord } from './View.vue';
import PrintLayoutModal from './PrintLayout.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Reports & Analytics',
                href: '/reports',
            },
        ],
    },
});

export interface DonorItem {
    id: number;
    full_name: string;
    blood_type: string;
    age: number;
    gender: string;
    barangay: string;
    municipality: string;
    contact_information: string;
    total_donations: number;
    created_at?: string;
}

export interface IssueItem {
    id: number;
    requisition_no: string;
    patient_name: string;
    patient_hrn: string;
    ward_room: string;
    blood_type: string;
    blood_component: string;
    units_issued: number;
    attending_physician: string;
    crossmatch_status: string;
    urgency_level: string;
    status: string;
    issued_at: string;
    remarks?: string;
}

const props = withDefaults(
    defineProps<{
        donors?: DonorItem[];
        reports?: BloodReportRecord[];
        issues?: IssueItem[];
        stats?: {
            totalReports: number;
            insideCaragaReports: number;
            outsideCaragaReports: number;
            totalUnitsAccounted: number;
            pendingVerifications: number;
            verifiedReports: number;
            totalDonors: number;
            totalDonations: number;
            totalIssues: number;
        };
        provincesList?: string[];
        outsideRegionsList?: string[];
        reportTypesList?: string[];
        nextReportCode?: string;
        municipalities?: Record<string, any[]>;
    }>(),
    {
        donors: () => [],
        reports: () => [],
        issues: () => [],
        stats: () => ({
            totalReports: 0,
            insideCaragaReports: 0,
            outsideCaragaReports: 0,
            totalUnitsAccounted: 0,
            pendingVerifications: 0,
            verifiedReports: 0,
            totalDonors: 0,
            totalDonations: 0,
            totalIssues: 0,
        }),
        provincesList: () => [],
        outsideRegionsList: () => [],
        reportTypesList: () => [
            'Quarterly New Donor Report',
            'Monthly Donor Report',
            'Yearly Donor Report',
            'Donor Age Report',
            'Gender Report',
            'Blood Consumption Report',
            'Inside CRH / Caraga Regional Reports',
            'Outside CRH / Inter-Regional Transfers',
        ],
        nextReportCode: 'RPT-CRH-2026-0001',
        municipalities: () => ({}),
    },
);

// Report Selection Dropdown
const selectedReportType = ref('Quarterly New Donor Report');
const selectedYear = ref('2026');
const activeQuarter = ref<'Q1' | 'Q2' | 'Q3' | 'Q4'>('Q1');
const selectedMonth = ref('January');
const selectedAgeGroup = ref<'18-25' | '26-35' | '36-50' | '51+'>('18-25');
const selectedGender = ref<'Male' | 'Female'>('Male');

// Modal States
const isCreateOpen = ref(false);
const isViewOpen = ref(false);
const isPrintOpen = ref(false);
const selectedDonorForView = ref<DonorViewRecord | null>(null);
const selectedReportForView = ref<BloodReportRecord | null>(null);

// Months List
const monthsList = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];

// Helper: Filter donors by year
const yearDonors = computed(() => {
    return props.donors.filter((d) => {
        if (!d.created_at) return true;
        const year = new Date(d.created_at).getFullYear().toString();
        return year === selectedYear.value;
    });
});

// Helper for Quarter Assignment
const getDonorQuarter = (dateStr?: string): 'Q1' | 'Q2' | 'Q3' | 'Q4' => {
    if (!dateStr) return 'Q1';
    const month = new Date(dateStr).getMonth();
    if (month >= 0 && month <= 2) return 'Q1';
    if (month >= 3 && month <= 5) return 'Q2';
    if (month >= 6 && month <= 8) return 'Q3';
    return 'Q4';
};

// Quarterly Donors Datasets
const q1Donors = computed(() => yearDonors.value.filter((d) => getDonorQuarter(d.created_at) === 'Q1'));
const q2Donors = computed(() => yearDonors.value.filter((d) => getDonorQuarter(d.created_at) === 'Q2'));
const q3Donors = computed(() => yearDonors.value.filter((d) => getDonorQuarter(d.created_at) === 'Q3'));
const q4Donors = computed(() => yearDonors.value.filter((d) => getDonorQuarter(d.created_at) === 'Q4'));

// Percentages for Quarters
const totalYearCount = computed(() => yearDonors.value.length || 1);
const q1Percentage = computed(() => Math.round((q1Donors.value.length / totalYearCount.value) * 100));
const q2Percentage = computed(() => Math.round((q2Donors.value.length / totalYearCount.value) * 100));
const q3Percentage = computed(() => Math.round((q3Donors.value.length / totalYearCount.value) * 100));
const q4Percentage = computed(() => Math.round((q4Donors.value.length / totalYearCount.value) * 100));

// Active Quarter Donors for Table
const activeQuarterDonors = computed(() => {
    switch (activeQuarter.value) {
        case 'Q1': return q1Donors.value;
        case 'Q2': return q2Donors.value;
        case 'Q3': return q3Donors.value;
        case 'Q4': return q4Donors.value;
    }
});

const activeQuarterLabel = computed(() => `${activeQuarter.value} ${selectedYear.value}`);
const activeQuarterMonths = computed(() => {
    switch (activeQuarter.value) {
        case 'Q1': return 'January to March';
        case 'Q2': return 'April to June';
        case 'Q3': return 'July to September';
        case 'Q4': return 'October to December';
    }
});

// Monthly Donors Dataset
const monthlyDonors = computed(() => {
    return yearDonors.value.filter((d) => {
        if (!d.created_at) return false;
        const m = new Date(d.created_at).toLocaleDateString('en-US', { month: 'long' });
        return m === selectedMonth.value;
    });
});

// Age Group Dataset
const ageGroupDonors = computed(() => {
    return props.donors.filter((d) => {
        const age = Number(d.age) || 0;
        if (selectedAgeGroup.value === '18-25') return age >= 18 && age <= 25;
        if (selectedAgeGroup.value === '26-35') return age >= 26 && age <= 35;
        if (selectedAgeGroup.value === '36-50') return age >= 36 && age <= 50;
        return age >= 51;
    });
});

const ageGroupCounts = computed(() => {
    return {
        '18-25': props.donors.filter(d => (d.age >= 18 && d.age <= 25)).length,
        '26-35': props.donors.filter(d => (d.age >= 26 && d.age <= 35)).length,
        '36-50': props.donors.filter(d => (d.age >= 36 && d.age <= 50)).length,
        '51+': props.donors.filter(d => (d.age >= 51)).length,
    };
});

// Gender Dataset
const genderDonors = computed(() => {
    return props.donors.filter((d) => d.gender === selectedGender.value);
});

const maleCount = computed(() => props.donors.filter(d => d.gender === 'Male').length);
const femaleCount = computed(() => props.donors.filter(d => d.gender === 'Female').length);

// Inside & Outside Reports Datasets
const insideCaragaReports = computed(() => props.reports.filter(r => r.scope === 'Inside Caraga'));
const outsideCaragaReports = computed(() => props.reports.filter(r => r.scope === 'Outside Caraga'));

// Format Date
const formatRegistrationDate = (dateStr?: string) => {
    if (!dateStr) return 'January 14, 2026';
    const d = new Date(dateStr);
    return isNaN(d.getTime())
        ? dateStr
        : d.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
};

// Modal Openers
const openCreateModal = () => {
    isCreateOpen.value = true;
};

const openPrintModal = () => {
    isPrintOpen.value = true;
};

const openViewDonorModal = (donor: DonorItem) => {
    selectedDonorForView.value = donor;
    selectedReportForView.value = null;
    isViewOpen.value = true;
};

const openViewReportModal = (report: BloodReportRecord) => {
    selectedReportForView.value = report;
    selectedDonorForView.value = null;
    isViewOpen.value = true;
};
</script>

<template>
    <Head :title="`${selectedReportType} - Blood Bank`" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Top Bar: Select Report Dropdown & Action Buttons matching screenshot -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            
            <!-- Select Report Dropdown -->
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">
                    Select Report:
                </span>
                
                <div class="relative">
                    <select
                        v-model="selectedReportType"
                        class="h-11 pl-4 pr-9 rounded-2xl border-2 border-[#dc2626] bg-white text-slate-900 font-bold text-sm shadow-2xs transition-colors cursor-pointer dark:bg-neutral-900 dark:text-white dark:border-red-600 focus:outline-hidden focus:ring-2 focus:ring-red-500/20"
                    >
                        <option value="Monthly Donor Report">Monthly Donor Report</option>
                        <option value="Quarterly New Donor Report">Quarterly New Donor Report</option>
                        <option value="Yearly Donor Report">Yearly Donor Report</option>
                        <option value="Donor Age Report">Donor Age Report</option>
                        <option value="Gender Report">Gender Report</option>
                        <option value="Blood Consumption Report">Blood Consumption Report</option>
                        <option value="Inside CRH / Caraga Regional Reports">Inside CRH / Caraga Regional Reports</option>
                        <option value="Outside CRH / Inter-Regional Transfers">Outside CRH / Inter-Regional Transfers</option>
                    </select>
                </div>
            </div>

            <!-- Right Controls: Auto-Generated Live Indicator + Create Report & Print/Export Buttons -->
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2 text-xs font-bold text-[#2563eb] dark:text-blue-400">
                    <span class="size-2 rounded-full bg-[#2563eb] animate-pulse"></span>
                    <span>Auto-Generated Live</span>
                </div>

                <!-- Create Report (Inside / Outside CRH) Action Button -->
                <button
                    type="button"
                    @click="openCreateModal"
                    class="h-10 px-4 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                >
                    <Plus class="size-3.5" />
                    <span>Create Report (Inside / Outside CRH)</span>
                </button>

                <!-- Print / Export Button (Opens Categorized Print Preview) -->
                <button
                    type="button"
                    @click="openPrintModal"
                    class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                >
                    <Printer class="size-3.5 text-slate-500" />
                    <span>Print / Export</span>
                </button>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 1: QUARTERLY NEW DONOR REPORT (Default Matching Screenshot) -->
        <!-- ========================================================================= -->
        <div v-if="selectedReportType === 'Quarterly New Donor Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Quarterly New Donor Report
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        Automatic count of newly registered first-time donors categorized by 3-month quarterly periods in <strong class="text-slate-800 dark:text-slate-200">{{ selectedYear }}</strong>.
                    </p>
                </div>

                <!-- Reporting Year Dropdown -->
                <div class="flex items-center gap-2 self-start sm:self-auto">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                        Reporting Year:
                    </span>
                    <select
                        v-model="selectedYear"
                        class="h-9 px-3 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-1 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    >
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>
            </div>

            <!-- 4 Quarterly Cards Grid (Q1, Q2, Q3, Q4) matching screenshot -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Q1 CARD -->
                <div
                    @click="activeQuarter = 'Q1'"
                    :class="[
                        'rounded-2xl p-5 transition-all cursor-pointer shadow-xs flex flex-col justify-between',
                        activeQuarter === 'Q1'
                            ? 'border-2 border-[#dc2626] bg-white ring-2 ring-red-500/10 dark:bg-neutral-800/90 dark:border-red-600'
                            : 'border border-slate-100/90 bg-white hover:border-slate-300 dark:bg-neutral-800/40 dark:border-neutral-800'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-base font-extrabold text-slate-900 dark:text-white">Q1 {{ selectedYear }}</span>
                        <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400">January to March</span>
                    </div>

                    <div class="my-4 flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">New Donors:</span>
                        <span class="text-3xl font-black text-[#a32222] dark:text-red-400">
                            {{ q1Donors.length }}
                        </span>
                    </div>

                    <div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-neutral-700 overflow-hidden">
                            <div 
                                class="h-full rounded-full bg-[#dc2626] transition-all duration-300"
                                :style="{ width: `${q1Percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 dark:text-neutral-400 mt-2 block">
                            {{ q1Percentage }}% of annual new donors
                        </span>
                    </div>
                </div>

                <!-- Q2 CARD -->
                <div
                    @click="activeQuarter = 'Q2'"
                    :class="[
                        'rounded-2xl p-5 transition-all cursor-pointer shadow-xs flex flex-col justify-between',
                        activeQuarter === 'Q2'
                            ? 'border-2 border-[#dc2626] bg-white ring-2 ring-red-500/10 dark:bg-neutral-800/90 dark:border-red-600'
                            : 'border border-slate-100/90 bg-white hover:border-slate-300 dark:bg-neutral-800/40 dark:border-neutral-800'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-base font-extrabold text-slate-900 dark:text-white">Q2 {{ selectedYear }}</span>
                        <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400">April to June</span>
                    </div>

                    <div class="my-4 flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">New Donors:</span>
                        <span class="text-3xl font-black text-[#a32222] dark:text-red-400">
                            {{ q2Donors.length }}
                        </span>
                    </div>

                    <div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-neutral-700 overflow-hidden">
                            <div 
                                class="h-full rounded-full bg-[#dc2626] transition-all duration-300"
                                :style="{ width: `${q2Percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 dark:text-neutral-400 mt-2 block">
                            {{ q2Percentage }}% of annual new donors
                        </span>
                    </div>
                </div>

                <!-- Q3 CARD -->
                <div
                    @click="activeQuarter = 'Q3'"
                    :class="[
                        'rounded-2xl p-5 transition-all cursor-pointer shadow-xs flex flex-col justify-between',
                        activeQuarter === 'Q3'
                            ? 'border-2 border-[#dc2626] bg-white ring-2 ring-red-500/10 dark:bg-neutral-800/90 dark:border-red-600'
                            : 'border border-slate-100/90 bg-white hover:border-slate-300 dark:bg-neutral-800/40 dark:border-neutral-800'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-base font-extrabold text-slate-900 dark:text-white">Q3 {{ selectedYear }}</span>
                        <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400">July to September</span>
                    </div>

                    <div class="my-4 flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">New Donors:</span>
                        <span class="text-3xl font-black text-[#a32222] dark:text-red-400">
                            {{ q3Donors.length }}
                        </span>
                    </div>

                    <div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-neutral-700 overflow-hidden">
                            <div 
                                class="h-full rounded-full bg-[#dc2626] transition-all duration-300"
                                :style="{ width: `${q3Percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 dark:text-neutral-400 mt-2 block">
                            {{ q3Percentage }}% of annual new donors
                        </span>
                    </div>
                </div>

                <!-- Q4 CARD -->
                <div
                    @click="activeQuarter = 'Q4'"
                    :class="[
                        'rounded-2xl p-5 transition-all cursor-pointer shadow-xs flex flex-col justify-between',
                        activeQuarter === 'Q4'
                            ? 'border-2 border-[#dc2626] bg-white ring-2 ring-red-500/10 dark:bg-neutral-800/90 dark:border-red-600'
                            : 'border border-slate-100/90 bg-white hover:border-slate-300 dark:bg-neutral-800/40 dark:border-neutral-800'
                    ]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-base font-extrabold text-slate-900 dark:text-white">Q4 {{ selectedYear }}</span>
                        <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400">October to December</span>
                    </div>

                    <div class="my-4 flex items-baseline justify-between">
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">New Donors:</span>
                        <span class="text-3xl font-black text-[#a32222] dark:text-red-400">
                            {{ q4Donors.length }}
                        </span>
                    </div>

                    <div>
                        <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-neutral-700 overflow-hidden">
                            <div 
                                class="h-full rounded-full bg-[#dc2626] transition-all duration-300"
                                :style="{ width: `${q4Percentage}%` }"
                            ></div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 dark:text-neutral-400 mt-2 block">
                            {{ q4Percentage }}% of annual new donors
                        </span>
                    </div>
                </div>

            </div>

            <!-- Table Section Header -->
            <div class="flex items-center justify-between pt-2">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                    New Donors in {{ activeQuarterLabel }} ({{ activeQuarterMonths }})
                </h2>

                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                    {{ activeQuarterDonors.length }} Registered
                </span>
            </div>

            <!-- Donors Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-neutral-800">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 dark:text-slate-400 dark:bg-neutral-800/40 border-b border-slate-100 dark:border-neutral-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Donor Name</th>
                            <th class="px-5 py-4 font-bold">Blood Type</th>
                            <th class="px-5 py-4 font-bold">Age</th>
                            <th class="px-5 py-4 font-bold">Gender</th>
                            <th class="px-5 py-4 font-bold">Location</th>
                            <th class="px-5 py-4 font-bold">Contact</th>
                            <th class="px-5 py-4 font-bold">First Donation / Registered</th>
                            <th class="px-5 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        <tr 
                            v-for="donor in activeQuarterDonors" 
                            :key="donor.id"
                            class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/30 transition-colors"
                        >
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                                {{ donor.full_name }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900">
                                    {{ donor.blood_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ donor.age }}
                            </td>
                            <td class="px-5 py-4">
                                <span 
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold',
                                        donor.gender === 'Male'
                                            ? 'bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300'
                                            : 'bg-pink-50 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300'
                                    ]"
                                >
                                    {{ donor.gender }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700 dark:text-slate-200">
                                {{ donor.municipality }}
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-600 dark:text-slate-400">
                                {{ donor.contact_information }}
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700 dark:text-slate-300">
                                {{ formatRegistrationDate(donor.created_at) }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    @click="openViewDonorModal(donor)"
                                    title="View Donor Record Sheet"
                                    class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold gap-1 inline-flex items-center cursor-pointer shadow-2xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 transition-colors"
                                >
                                    <Eye class="size-3.5 text-slate-500" />
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State for Quarter -->
            <div 
                v-if="activeQuarterDonors.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-2xl border border-dashed border-slate-200 dark:border-neutral-800 bg-slate-50/50 dark:bg-neutral-800/20"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 mb-3">
                    <Calendar class="size-7" />
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    No New Donors Registered in {{ activeQuarterLabel }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mt-1">
                    No first-time blood donors have been logged for {{ activeQuarterMonths }} in {{ selectedYear }}.
                </p>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 2: MONTHLY DONOR REPORT -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Monthly Donor Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        Monthly Donor Report
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        Monthly breakdown of blood donors registered and collection turnout in {{ selectedYear }}.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500">Year:</span>
                    <select v-model="selectedYear" class="h-9 px-3 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 dark:bg-neutral-800 dark:text-neutral-100 dark:border-neutral-700">
                        <option value="2026">2026</option>
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
            </div>

            <!-- Month Select Buttons -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-2 scrollbar-none">
                <button
                    v-for="m in monthsList"
                    :key="m"
                    type="button"
                    @click="selectedMonth = m"
                    :class="[
                        'px-3.5 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap',
                        selectedMonth === m
                            ? 'bg-[#a32222] text-white shadow-xs'
                            : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-neutral-800 dark:text-slate-300'
                    ]"
                >
                    {{ m }}
                </button>
            </div>

            <!-- Table Header -->
            <div class="flex items-center justify-between pt-2">
                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                    Donors Registered in {{ selectedMonth }} {{ selectedYear }}
                </h2>
                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                    {{ monthlyDonors.length }} Registered
                </span>
            </div>

            <!-- Monthly Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-neutral-800">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 dark:text-slate-400 dark:bg-neutral-800/40 border-b border-slate-100 dark:border-neutral-800">
                        <tr>
                            <th class="px-5 py-4">Donor Name</th>
                            <th class="px-5 py-4">Blood Type</th>
                            <th class="px-5 py-4">Age</th>
                            <th class="px-5 py-4">Gender</th>
                            <th class="px-5 py-4">Location</th>
                            <th class="px-5 py-4">Contact</th>
                            <th class="px-5 py-4">Date Registered</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        <tr v-for="donor in monthlyDonors" :key="donor.id" class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/30">
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">{{ donor.full_name }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100">
                                    {{ donor.blood_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium">{{ donor.age }}</td>
                            <td class="px-5 py-4">{{ donor.gender }}</td>
                            <td class="px-5 py-4">{{ donor.municipality }}</td>
                            <td class="px-5 py-4 font-mono text-xs">{{ donor.contact_information }}</td>
                            <td class="px-5 py-4">{{ formatRegistrationDate(donor.created_at) }}</td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewDonorModal(donor)" class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white text-xs font-bold gap-1 inline-flex items-center cursor-pointer shadow-2xs">
                                    <Eye class="size-3.5 text-slate-500" />
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="monthlyDonors.length === 0">
                            <td colspan="8" class="text-center py-8 text-sm text-slate-400">
                                No donors registered in {{ selectedMonth }} {{ selectedYear }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 3: YEARLY DONOR REPORT -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Yearly Donor Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Yearly Donor Summary Report</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Comprehensive multi-year donor registry & turnout metrics.</p>
                </div>
                <div class="text-sm font-bold text-red-700 bg-red-50 px-3 py-1 rounded-xl">
                    {{ props.donors.length }} Total Donors Logged
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-neutral-800">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4">Donor Name</th>
                            <th class="px-5 py-4">Blood Type</th>
                            <th class="px-5 py-4">Total Donations</th>
                            <th class="px-5 py-4">Location</th>
                            <th class="px-5 py-4">Registration Year</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="donor in props.donors" :key="donor.id">
                            <td class="px-5 py-4 font-bold text-slate-900">{{ donor.full_name }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-[#fef2f2] text-[#dc2626]">{{ donor.blood_type }}</span>
                            </td>
                            <td class="px-5 py-4 font-bold">{{ donor.total_donations }} Bags</td>
                            <td class="px-5 py-4">{{ donor.municipality }}</td>
                            <td class="px-5 py-4 font-medium">{{ donor.created_at ? new Date(donor.created_at).getFullYear() : '2026' }}</td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewDonorModal(donor)" class="h-8 px-2.5 rounded-lg border border-slate-200 text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <Eye class="size-3.5" />
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 4: DONOR AGE REPORT -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Donor Age Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Donor Age Demographics Report</h1>
                <p class="text-xs text-slate-500 mt-0.5">Demographic classification of blood donors by age bracket.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div 
                    v-for="bracket in ['18-25', '26-35', '36-50', '51+'] as const"
                    :key="bracket"
                    @click="selectedAgeGroup = bracket"
                    :class="[
                        'rounded-2xl p-4 text-center cursor-pointer transition-all border shadow-xs',
                        selectedAgeGroup === bracket ? 'border-2 border-[#dc2626] bg-[#fef2f2]/40' : 'border-slate-100 bg-slate-50'
                    ]"
                >
                    <span class="text-xs font-bold text-slate-500 uppercase">{{ bracket }} yrs</span>
                    <span class="text-3xl font-black text-[#a32222] block mt-1">{{ ageGroupCounts[bracket] }}</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b">
                        <tr>
                            <th class="px-5 py-4">Donor Name</th>
                            <th class="px-5 py-4">Age</th>
                            <th class="px-5 py-4">Blood Type</th>
                            <th class="px-5 py-4">Gender</th>
                            <th class="px-5 py-4">Location</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="donor in ageGroupDonors" :key="donor.id">
                            <td class="px-5 py-4 font-bold">{{ donor.full_name }}</td>
                            <td class="px-5 py-4 font-bold text-red-700">{{ donor.age }} yrs</td>
                            <td class="px-5 py-4">{{ donor.blood_type }}</td>
                            <td class="px-5 py-4">{{ donor.gender }}</td>
                            <td class="px-5 py-4">{{ donor.municipality }}</td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewDonorModal(donor)" class="h-8 px-2.5 rounded-lg border border-slate-200 text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <Eye class="size-3.5" />
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 5: GENDER REPORT -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Gender Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Donor Gender Distribution Report</h1>
                <p class="text-xs text-slate-500 mt-0.5">Analysis of male and female voluntary blood donor turnout.</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div 
                    @click="selectedGender = 'Male'"
                    :class="[
                        'rounded-2xl p-5 text-center cursor-pointer transition-all border shadow-xs',
                        selectedGender === 'Male' ? 'border-2 border-blue-600 bg-blue-50/50' : 'border-slate-100 bg-slate-50'
                    ]"
                >
                    <span class="text-xs font-bold text-blue-600 uppercase">Male Donors</span>
                    <span class="text-4xl font-black text-blue-700 block mt-1">{{ maleCount }}</span>
                </div>

                <div 
                    @click="selectedGender = 'Female'"
                    :class="[
                        'rounded-2xl p-5 text-center cursor-pointer transition-all border shadow-xs',
                        selectedGender === 'Female' ? 'border-2 border-pink-600 bg-pink-50/50' : 'border-slate-100 bg-slate-50'
                    ]"
                >
                    <span class="text-xs font-bold text-pink-600 uppercase">Female Donors</span>
                    <span class="text-4xl font-black text-pink-700 block mt-1">{{ femaleCount }}</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b">
                        <tr>
                            <th class="px-5 py-4">Donor Name</th>
                            <th class="px-5 py-4">Gender</th>
                            <th class="px-5 py-4">Age</th>
                            <th class="px-5 py-4">Blood Type</th>
                            <th class="px-5 py-4">Location</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="donor in genderDonors" :key="donor.id">
                            <td class="px-5 py-4 font-bold">{{ donor.full_name }}</td>
                            <td class="px-5 py-4">
                                <span :class="donor.gender === 'Male' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700'" class="px-2.5 py-0.5 rounded-md text-xs font-bold">
                                    {{ donor.gender }}
                                </span>
                            </td>
                            <td class="px-5 py-4">{{ donor.age }}</td>
                            <td class="px-5 py-4 font-bold">{{ donor.blood_type }}</td>
                            <td class="px-5 py-4">{{ donor.municipality }}</td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewDonorModal(donor)" class="h-8 px-2.5 rounded-lg border border-slate-200 text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <Eye class="size-3.5" />
                                    <span>View</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 6: BLOOD CONSUMPTION REPORT -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Blood Consumption Report'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Blood Consumption & Issuance Report</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Audited records of blood units released to hospital wards and municipal health stations.</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b">
                        <tr>
                            <th class="px-5 py-4">Req #</th>
                            <th class="px-5 py-4">Location / Ward</th>
                            <th class="px-5 py-4">Blood Type</th>
                            <th class="px-5 py-4">Number of Bags</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Date Issued</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="issue in props.issues" :key="issue.id">
                            <td class="px-5 py-4 font-mono font-bold text-red-700">{{ issue.requisition_no }}</td>
                            <td class="px-5 py-4 font-medium">{{ issue.ward_room }}</td>
                            <td class="px-5 py-4 font-bold">{{ issue.blood_type }}</td>
                            <td class="px-5 py-4 font-bold">{{ issue.units_issued }} Bags</td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700">{{ issue.status }}</span>
                            </td>
                            <td class="px-5 py-4 font-medium">{{ formatRegistrationDate(issue.issued_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 7: INSIDE CRH / CARAGA REGIONAL REPORTS -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Inside CRH / Caraga Regional Reports'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Inside Caraga Regional Network Reports</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Audited blood turnovers from 5 Caraga provinces, municipal health offices, and blood stations.</p>
                </div>
                <button type="button" @click="openCreateModal" class="h-9 px-4 rounded-xl bg-[#a32222] text-white font-bold text-xs flex items-center gap-1.5 cursor-pointer">
                    <Plus class="size-3.5" />
                    <span>+ Log Inside Report</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b">
                        <tr>
                            <th class="px-5 py-4">Report Code</th>
                            <th class="px-5 py-4">Facility Name</th>
                            <th class="px-5 py-4">Province</th>
                            <th class="px-5 py-4">Report Period</th>
                            <th class="px-5 py-4">Volume (Bags)</th>
                            <th class="px-5 py-4">Submitting Officer</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="r in insideCaragaReports" :key="r.id">
                            <td class="px-5 py-4 font-mono font-bold text-blue-700">{{ r.report_code }}</td>
                            <td class="px-5 py-4 font-bold">{{ r.facility_name }}</td>
                            <td class="px-5 py-4">{{ r.province_region }}</td>
                            <td class="px-5 py-4">{{ r.reporting_period }}</td>
                            <td class="px-5 py-4 font-bold text-red-700">{{ r.blood_units_count }} Bags</td>
                            <td class="px-5 py-4">{{ r.submitting_officer }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700">{{ r.status }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewReportModal(r)" class="h-8 px-2.5 rounded-lg border border-slate-200 text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <Eye class="size-3.5" />
                                    <span>View Sheet</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- REPORT 8: OUTSIDE CRH / INTER-REGIONAL TRANSFERS -->
        <!-- ========================================================================= -->
        <div v-else-if="selectedReportType === 'Outside CRH / Inter-Regional Transfers'" class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Outside Caraga Inter-Regional Transfers</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Audited transfers and exchange reports from Region X (NMMC), Region XI (SPMC), Region VII (VSMMC), & NCR.</p>
                </div>
                <button type="button" @click="openCreateModal" class="h-9 px-4 rounded-xl bg-[#a32222] text-white font-bold text-xs flex items-center gap-1.5 cursor-pointer">
                    <Plus class="size-3.5" />
                    <span>+ Log Outside Report</span>
                </button>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-600 border-b">
                        <tr>
                            <th class="px-5 py-4">Report Code</th>
                            <th class="px-5 py-4">Facility / Hospital</th>
                            <th class="px-5 py-4">Partner Region</th>
                            <th class="px-5 py-4">Report Period</th>
                            <th class="px-5 py-4">Volume (Bags)</th>
                            <th class="px-5 py-4">Submitting Officer</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="r in outsideCaragaReports" :key="r.id">
                            <td class="px-5 py-4 font-mono font-bold text-purple-700">{{ r.report_code }}</td>
                            <td class="px-5 py-4 font-bold">{{ r.facility_name }}</td>
                            <td class="px-5 py-4">{{ r.province_region }}</td>
                            <td class="px-5 py-4">{{ r.reporting_period }}</td>
                            <td class="px-5 py-4 font-bold text-red-700">{{ r.blood_units_count }} Bags</td>
                            <td class="px-5 py-4">{{ r.submitting_officer }}</td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-700">{{ r.status }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" @click="openViewReportModal(r)" class="h-8 px-2.5 rounded-lg border border-slate-200 text-xs font-bold inline-flex items-center gap-1 cursor-pointer">
                                    <Eye class="size-3.5" />
                                    <span>View Sheet</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CREATE MODAL COMPONENT (Handles Inside and Outside CRH Reports) -->
        <CreateModal 
            :is-open="isCreateOpen"
            :provinces-list="props.provincesList"
            :outside-regions-list="props.outsideRegionsList"
            :report-types-list="props.reportTypesList"
            :next-report-code="props.nextReportCode"
            :municipalities="props.municipalities"
            @close="isCreateOpen = false"
        />

        <!-- VIEW MODAL COMPONENT (Handles Donor Profile and Official Report Sheets) -->
        <ViewModal 
            :is-open="isViewOpen"
            :donor="selectedDonorForView"
            :report="selectedReportForView"
            @close="isViewOpen = false"
        />

        <!-- CATEGORIZED PRINT PREVIEW MODAL -->
        <PrintLayoutModal
            :is-open="isPrintOpen"
            :report-type="selectedReportType"
            :selected-year="selectedYear"
            :donors="props.donors"
            :reports="props.reports"
            :issues="props.issues"
            @close="isPrintOpen = false"
        />

    </div>
</template>
