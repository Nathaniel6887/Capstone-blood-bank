<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Users, 
    CheckCircle2, 
    Hourglass, 
    Ban, 
    Droplets, 
    HeartPulse, 
    Search, 
    Plus, 
    ArrowUpRight, 
    AlertTriangle, 
    ShieldCheck, 
    Activity, 
    Clock, 
    Filter,
    UserPlus,
    FileSpreadsheet,
    Calendar
} from '@lucide/vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// Blood Stock Inventory Data
const bloodInventory = [
    { type: 'O+', units: 48, status: 'Optimal', color: 'bg-red-500', percentage: 85 },
    { type: 'A+', units: 34, status: 'Adequate', color: 'bg-blue-500', percentage: 65 },
    { type: 'B+', units: 28, status: 'Adequate', color: 'bg-emerald-500', percentage: 55 },
    { type: 'AB+', units: 14, status: 'Low', color: 'bg-purple-500', percentage: 30 },
    { type: 'O-', units: 4, status: 'Critical', color: 'bg-rose-600', percentage: 15 },
    { type: 'A-', units: 6, status: 'Low', color: 'bg-amber-500', percentage: 20 },
    { type: 'B-', units: 5, status: 'Low', color: 'bg-orange-500', percentage: 18 },
    { type: 'AB-', units: 2, status: 'Critical', color: 'bg-red-700', percentage: 10 },
];

// Registered Donors Directory Mock/Summary
const donorsDirectory = ref([
    {
        id: 'CRH-DN-101',
        name: 'Maria Elena Santos',
        bloodType: 'O+',
        municipality: 'Buenavista',
        province: 'Agusan del Norte',
        gender: 'Female',
        age: 28,
        totalDonations: 4,
        lastDonation: '2026-08-10',
        status: 'Eligible',
    },
    {
        id: 'CRH-DN-102',
        name: 'Juan Carlo Dela Cruz',
        bloodType: 'A+',
        municipality: 'San Jose',
        province: 'Dinagat Islands',
        gender: 'Male',
        age: 34,
        totalDonations: 7,
        lastDonation: '2026-06-15',
        status: 'Eligible',
    },
    {
        id: 'CRH-DN-103',
        name: 'Roberto V. Mendoza',
        bloodType: 'B+',
        municipality: 'Prosperidad',
        province: 'Agusan del Sur',
        gender: 'Male',
        age: 41,
        totalDonations: 12,
        lastDonation: '2026-07-22',
        status: 'Eligible',
    },
    {
        id: 'CRH-DN-104',
        name: 'Ana Patricia Ramos',
        bloodType: 'AB+',
        municipality: 'General Luna',
        province: 'Surigao del Norte',
        gender: 'Female',
        age: 25,
        totalDonations: 2,
        lastDonation: '2026-09-01',
        status: 'Temporary Deferral',
    },
    {
        id: 'CRH-DN-105',
        name: 'Gabriel R. Fernandez',
        bloodType: 'O-',
        municipality: 'Cantilan',
        province: 'Surigao del Sur',
        gender: 'Male',
        age: 39,
        totalDonations: 9,
        lastDonation: '2026-05-18',
        status: 'Eligible',
    },
    {
        id: 'CRH-DN-106',
        name: 'Kristine Joy Alcantara',
        bloodType: 'A+',
        municipality: 'Nasipit',
        province: 'Agusan del Norte',
        gender: 'Female',
        age: 31,
        totalDonations: 5,
        lastDonation: '2026-08-29',
        status: 'Eligible',
    },
    {
        id: 'CRH-DN-107',
        name: 'Emmanuel S. Dizon',
        bloodType: 'O+',
        municipality: 'Tubajon',
        province: 'Dinagat Islands',
        gender: 'Male',
        age: 45,
        totalDonations: 15,
        lastDonation: '2026-06-04',
        status: 'Eligible',
    },
]);

// Search & Filter for Donors Directory
const donorSearch = ref('');
const selectedBloodFilter = ref('All');

const filteredDonors = computed(() => {
    const q = donorSearch.value.trim().toLowerCase();
    return donorsDirectory.value.filter((donor) => {
        const matchesQuery = 
            donor.name.toLowerCase().includes(q) ||
            donor.id.toLowerCase().includes(q) ||
            donor.municipality.toLowerCase().includes(q) ||
            donor.province.toLowerCase().includes(q);

        const matchesBlood = 
            selectedBloodFilter.value === 'All' || 
            donor.bloodType === selectedBloodFilter.value;

        return matchesQuery && matchesBlood;
    });
});

// Recent Blood Collections Log
const recentCollections = [
    { bag: 'CRH-B26-0914', donor: 'Maria Elena Santos', type: 'O+', volume: '450 mL', time: '10:45 AM', medtech: 'R. Tan, RMT', status: 'Tested & Stored' },
    { bag: 'CRH-B26-0913', donor: 'Juan Carlo Dela Cruz', type: 'A+', volume: '450 mL', time: '09:30 AM', medtech: 'L. Garcia, RMT', status: 'Tested & Stored' },
    { bag: 'CRH-B26-0912', donor: 'Gabriel R. Fernandez', type: 'O-', volume: '450 mL', time: '08:15 AM', medtech: 'R. Tan, RMT', status: 'In Crossmatch' },
];

// Active Hospital Ward Requisitions
const hospitalRequisitions = [
    { req: 'REQ-0891', patient: 'Elena Roxas', ward: 'ICU - Bed 4', type: 'O+ (PRBC)', units: '2 Units', urgency: 'Emergency', time: '15m ago' },
    { req: 'REQ-0892', patient: 'Roberto Tan', ward: 'Emergency Trauma', type: 'O- (PRBC)', units: '1 Unit', urgency: 'Stat / Critical', time: '35m ago' },
    { req: 'REQ-0893', patient: 'Luzviminda Cruz', ward: 'OB-GYN Surgery', type: 'A+ (FFP)', units: '2 Units', urgency: 'Routine', time: '1h ago' },
];
</script>

<template>
    <Head title="Dashboard - Caraga Regional Hospital Blood Bank" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Dashboard Top Header Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-border/60">
            <div>
                <div class="flex items-center gap-2">
                    <span class="size-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                        Blood Bank Summary & Operations
                    </h1>
                </div>
                <p class="text-xs sm:text-sm text-muted-foreground mt-0.5">
                    Caraga Regional Hospital • Central Operations Overview & Donor Registry
                </p>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                <Button as-child variant="outline" class="h-9 gap-1.5 text-xs font-semibold">
                    <Link href="/blood-collection">
                        <Droplets class="size-3.5 text-red-600" />
                        <span>Record Collection</span>
                    </Link>
                </Button>
                <Button as-child class="h-9 gap-1.5 text-xs font-semibold bg-red-700 hover:bg-red-800 text-white shadow-xs">
                    <Link href="/blood-consumtion">
                        <HeartPulse class="size-3.5" />
                        <span>Issue Blood Unit</span>
                    </Link>
                </Button>
            </div>
        </div>

        <!-- 4 Stat Cards Matching the User's Screenshot Design -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Card 1: Registered Donors (Blue Accent) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-[#2563eb] dark:bg-card dark:border-border/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        REGISTERED DONORS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400">
                        <Users class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        12
                    </div>
                    <div class="mt-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                        Active donor registry
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Donations (Green Accent) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-[#16a34a] dark:bg-card dark:border-border/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        TOTAL DONATIONS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400">
                        <CheckCircle2 class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        19
                    </div>
                    <div class="mt-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                        Bags collected
                    </div>
                </div>
            </div>

            <!-- Card 3: Active Locations (Orange/Amber Accent) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-[#f59e0b] dark:bg-card dark:border-border/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        ACTIVE LOCATIONS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400">
                        <Hourglass class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">7</span>
                        <span class="text-xl font-bold text-slate-800 dark:text-slate-200">Municipalities</span>
                    </div>
                    <div class="mt-1 text-sm font-medium text-amber-600 dark:text-amber-400">
                        Dinagat Islands coverage
                    </div>
                </div>
            </div>

            <!-- Card 4: Eligible Today (Red Accent) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-[#dc2626] dark:bg-card dark:border-border/60 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        ELIGIBLE TODAY
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400">
                        <Ban class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        100%
                    </div>
                    <div class="mt-1 text-sm font-medium text-red-600 dark:text-red-400">
                        Verified donor profiles
                    </div>
                </div>
            </div>

        </div>

        <!-- SECTION: Blood Inventory & Real-Time Collections Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Blood Stock Level Grid (2 Cols on LG) -->
            <div class="lg:col-span-2 rounded-2xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border/70">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-red-50 text-red-700 dark:bg-red-950 dark:text-red-400">
                                <Droplets class="size-4.5" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                                    Blood Stock Inventory Summary
                                </h2>
                                <p class="text-xs text-muted-foreground">Real-time units stored across blood groups in CRH Blood Bank.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400">
                            <ShieldCheck class="size-3.5" />
                            141 Total Units Available
                        </span>
                    </div>

                    <!-- Blood Type Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
                        <div 
                            v-for="item in bloodInventory" 
                            :key="item.type"
                            class="rounded-xl border border-border/80 bg-muted/20 p-3.5 hover:border-red-300 dark:hover:border-red-900 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-black text-red-900 dark:text-red-300">{{ item.type }}</span>
                                <span 
                                    :class="[
                                        'px-2 py-0.5 rounded-md text-[10px] font-bold uppercase',
                                        item.status === 'Optimal' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                        item.status === 'Adequate' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                        item.status === 'Low' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' :
                                        'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'
                                    ]"
                                >
                                    {{ item.status }}
                                </span>
                            </div>
                            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                                {{ item.units }} <span class="text-xs font-normal text-muted-foreground">units</span>
                            </div>
                            <!-- Mini Progress Bar -->
                            <div class="mt-2 h-1.5 w-full rounded-full bg-border overflow-hidden">
                                <div :class="['h-full rounded-full', item.color]" :style="{ width: item.percentage + '%' }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Critical Alert Warning Bar if any -->
                <div class="mt-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 flex items-center justify-between text-xs text-amber-800 dark:text-amber-300">
                    <div class="flex items-center gap-2">
                        <AlertTriangle class="size-4 text-amber-600 shrink-0" />
                        <span><strong>Critical Notice:</strong> O-Negative and AB-Negative inventory levels are below 10-unit reserve threshold.</span>
                    </div>
                    <Button size="sm" variant="ghost" class="h-7 text-xs text-amber-900 dark:text-amber-200 hover:bg-amber-100">Request Donors →</Button>
                </div>
            </div>

            <!-- Hospital Requisitions & Issuance Summary (1 Col on LG) -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border/70">
                        <div class="flex items-center gap-2.5">
                            <div class="flex size-8 items-center justify-center rounded-lg bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-400">
                                <HeartPulse class="size-4.5" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold tracking-tight text-gray-900 dark:text-white">
                                    Ward Requisitions
                                </h2>
                                <p class="text-xs text-muted-foreground">Recent blood issuance requests</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div 
                            v-for="req in hospitalRequisitions" 
                            :key="req.req"
                            class="p-3 rounded-xl border border-border/80 bg-muted/15 flex items-center justify-between text-xs"
                        >
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ req.patient }}</span>
                                    <span class="rounded bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 px-1.5 py-0.2 font-mono text-[10px] font-bold">{{ req.type }}</span>
                                </div>
                                <div class="text-[11px] text-muted-foreground">
                                    {{ req.ward }} • {{ req.units }}
                                </div>
                            </div>
                            <div class="text-right">
                                <span 
                                    :class="[
                                        'px-2 py-0.5 rounded text-[10px] font-bold block',
                                        req.urgency.includes('Critical') || req.urgency.includes('Emergency') ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-gray-100 text-gray-700 dark:bg-muted dark:text-gray-300'
                                    ]"
                                >
                                    {{ req.urgency }}
                                </span>
                                <span class="text-[10px] text-muted-foreground mt-0.5 block">{{ req.time }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-border flex items-center justify-between">
                    <span class="text-xs text-muted-foreground">Total Today: 14 Issued</span>
                    <Button as-child size="sm" variant="ghost" class="text-xs text-red-700 hover:text-red-800">
                        <Link href="/blood-consumtion">View Full Log →</Link>
                    </Button>
                </div>
            </div>

        </div>

        <!-- SECTION: Registered Donors Directory -->
        <div class="rounded-2xl border border-border bg-card p-6 shadow-xs space-y-5">
            
            <!-- Section Header & Filter Controls -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-border/70">
                <div class="flex items-center gap-3">
                    <div class="flex size-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400">
                        <Users class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">
                            Registered Donors Directory
                        </h2>
                        <p class="text-xs text-muted-foreground">
                            Verified Caraga Region voluntary donor profiles and blood collection history.
                        </p>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="flex flex-col sm:flex-row items-center gap-2.5">
                    <div class="relative w-full sm:w-64">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input 
                            v-model="donorSearch" 
                            placeholder="Search donor name, ID, municipality..." 
                            class="pl-9 h-9 text-xs rounded-xl"
                        />
                    </div>
                    <Button as-child class="h-9 gap-1.5 text-xs font-semibold bg-blue-700 hover:bg-blue-800 text-white">
                        <Link href="/blood-collection">
                            <Plus class="size-3.5" />
                            <span>Add New Donor</span>
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Blood Type Quick Selector Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                <span class="text-muted-foreground font-semibold mr-1">Blood Type:</span>
                <button
                    v-for="bType in ['All', 'O+', 'A+', 'B+', 'AB+', 'O-']"
                    :key="bType"
                    @click="selectedBloodFilter = bType"
                    :class="[
                        'px-3 py-1 rounded-lg font-semibold transition-all cursor-pointer',
                        selectedBloodFilter === bType
                            ? 'bg-blue-700 text-white shadow-xs'
                            : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                    ]"
                >
                    {{ bType }}
                </button>
            </div>

            <!-- Donors Directory Table -->
            <div class="overflow-x-auto rounded-xl border border-border/70">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted/40 text-[11px] uppercase font-bold text-muted-foreground tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Donor ID</th>
                            <th class="px-4 py-3">Donor Name</th>
                            <th class="px-4 py-3">Blood Group</th>
                            <th class="px-4 py-3">Municipality & Province</th>
                            <th class="px-4 py-3">Total Donations</th>
                            <th class="px-4 py-3">Last Donated</th>
                            <th class="px-4 py-3">Eligibility Status</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr 
                            v-for="donor in filteredDonors" 
                            :key="donor.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-3 font-mono text-xs font-semibold text-blue-700 dark:text-blue-400">
                                {{ donor.id }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                {{ donor.name }}
                                <span class="block text-[11px] text-muted-foreground font-normal">{{ donor.gender }}, {{ donor.age }} yrs old</span>
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    :class="[
                                        'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold',
                                        donor.bloodType.includes('O') ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' :
                                        donor.bloodType.includes('A') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                        donor.bloodType.includes('B') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                        'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'
                                    ]"
                                >
                                    {{ donor.bloodType }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ donor.municipality }}</span>
                                <span class="block text-xs text-muted-foreground">{{ donor.province }}</span>
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ donor.totalDonations }} times
                            </td>
                            <td class="px-4 py-3 text-xs text-muted-foreground">
                                {{ donor.lastDonation }}
                            </td>
                            <td class="px-4 py-3">
                                <span 
                                    :class="[
                                        'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                        donor.status === 'Eligible' 
                                            ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' 
                                            : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                    ]"
                                >
                                    <CheckCircle2 v-if="donor.status === 'Eligible'" class="size-3" />
                                    <Clock v-else class="size-3" />
                                    {{ donor.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Button as-child size="sm" variant="outline" class="h-7 text-xs">
                                    <Link href="/blood-collection">Record</Link>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="filteredDonors.length === 0" class="text-center py-8 text-muted-foreground text-xs">
                No donors found matching criteria.
            </div>

        </div>

    </div>
</template>
