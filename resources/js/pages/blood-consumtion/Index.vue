<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Plus, 
    Search, 
    RefreshCw, 
    Eye, 
    Edit, 
    Trash2, 
    X,
    MapPin,
    Droplets
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import CreateModal from './Create.vue';
import ViewModal from './View.vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Blood Consumption',
                href: '/blood-consumtion',
            },
        ],
    },
});

export interface BloodIssueRecord {
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
    issued_by?: string;
    remarks?: string;
    issued_at: string;
    created_at?: string;
    updated_at?: string;
}

export interface MunicipalityOption {
    id: number;
    province: string;
    name: string;
}

const props = withDefaults(
    defineProps<{
        issues?: BloodIssueRecord[];
        bloodInventorySummary?: Record<string, number>;
        wardsList?: string[];
        bloodComponentsList?: string[];
        nextRequisitionNo?: string;
        municipalities?: Record<string, MunicipalityOption[]>;
    }>(),
    {
        issues: () => [],
        bloodInventorySummary: () => ({
            'O+': 35,
            'A+': 25,
            'B+': 20,
            'AB+': 12,
            'O-': 6,
            'A-': 8,
            'B-': 7,
            'AB-': 4,
        }),
        wardsList: () => [
            'Tubajon',
            'San Jose',
            'Basilisa',
            'Cagdianao',
            'Dinagat',
            'Libjo',
            'Loreto',
            'ICU - Intensive Care Unit',
            'Emergency Trauma & Acute Care',
            'Operating Room / PACU',
            'OB-GYN & Delivery Ward',
            'Surgical Inpatient Ward',
            'Pediatrics & NICU',
            'Internal Medicine Ward',
            'Hemodialysis Center',
        ],
        bloodComponentsList: () => [
            'Packed Red Blood Cells (PRBC)',
            'Whole Blood (WB)',
            'Fresh Frozen Plasma (FFP)',
            'Platelet Concentrate (PC)',
            'Cryoprecipitate (CRYO)',
        ],
        nextRequisitionNo: 'REQ-2026-0001',
        municipalities: () => ({}),
    },
);

const bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];
const monthsList = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
const yearsList = ['2026', '2025', '2024', '2023'];

// Search & Filter State (Matching Image 1)
const locationSearch = ref('');
const selectedBloodType = ref('All');
const selectedMonth = ref('All');
const selectedYear = ref('All');

// Modal States
const isCreateOpen = ref(false);
const isViewOpen = ref(false);
const selectedIssueForView = ref<BloodIssueRecord | null>(null);

const isEditModalOpen = ref(false);
const editingIssueId = ref<number | null>(null);

// Edit Form State
const editForm = useForm({
    requisition_no: '',
    patient_name: '',
    patient_hrn: '',
    ward_room: 'Tubajon',
    blood_type: 'O+',
    blood_component: 'Packed Red Blood Cells (PRBC)',
    units_issued: 1,
    attending_physician: 'Dr. G. Morales, MD',
    crossmatch_status: 'Compatible',
    urgency_level: 'Routine',
    status: 'Issued',
    remarks: '',
    issued_at: new Date().toISOString().split('T')[0],
});

// Helpers for dates
const getMonthName = (dateStr?: string): string => {
    if (!dateStr) return 'January';
    const d = new Date(dateStr);
    return isNaN(d.getTime()) ? 'January' : d.toLocaleDateString('en-US', { month: 'long' });
};

const getYearStr = (dateStr?: string): string => {
    if (!dateStr) return '2026';
    const d = new Date(dateStr);
    return isNaN(d.getTime()) ? '2026' : d.getFullYear().toString();
};

const formatDateSubmitted = (dateStr?: string): string => {
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

// Reset Filters
const resetFilters = () => {
    locationSearch.value = '';
    selectedBloodType.value = 'All';
    selectedMonth.value = 'All';
    selectedYear.value = 'All';
};

// Filtered Records
const filteredIssues = computed(() => {
    const loc = locationSearch.value.trim().toLowerCase();
    return props.issues.filter((issue) => {
        // Location Match
        const matchesLoc =
            !loc ||
            issue.ward_room.toLowerCase().includes(loc) ||
            issue.patient_name.toLowerCase().includes(loc) ||
            issue.requisition_no.toLowerCase().includes(loc);

        // Blood Type Match
        const matchesBlood =
            selectedBloodType.value === 'All' ||
            issue.blood_type === selectedBloodType.value;

        // Month Match
        const issueMonth = getMonthName(issue.issued_at);
        const matchesMonth =
            selectedMonth.value === 'All' ||
            issueMonth.toLowerCase() === selectedMonth.value.toLowerCase();

        // Year Match
        const issueYear = getYearStr(issue.issued_at);
        const matchesYear =
            selectedYear.value === 'All' ||
            issueYear === selectedYear.value;

        return matchesLoc && matchesBlood && matchesMonth && matchesYear;
    });
});

// Summary Stat Calculations
const totalBagsCount = computed(() => {
    return filteredIssues.value.reduce((sum, item) => sum + (Number(item.units_issued) || 0), 0);
});

const getBagsByType = (bType: string) => {
    return filteredIssues.value
        .filter((item) => item.blood_type === bType)
        .reduce((sum, item) => sum + (Number(item.units_issued) || 0), 0);
};

// Modal Actions
const openCreateModal = () => {
    isCreateOpen.value = true;
};

const openViewModal = (issue: BloodIssueRecord) => {
    selectedIssueForView.value = issue;
    isViewOpen.value = true;
};

const openEditModal = (issue: BloodIssueRecord) => {
    editingIssueId.value = issue.id;
    editForm.clearErrors();
    editForm.requisition_no = issue.requisition_no;
    editForm.patient_name = issue.patient_name;
    editForm.patient_hrn = issue.patient_hrn;
    editForm.ward_room = issue.ward_room;
    editForm.blood_type = issue.blood_type;
    editForm.blood_component = issue.blood_component;
    editForm.units_issued = issue.units_issued;
    editForm.attending_physician = issue.attending_physician;
    editForm.crossmatch_status = issue.crossmatch_status;
    editForm.urgency_level = issue.urgency_level;
    editForm.status = issue.status;
    editForm.remarks = issue.remarks || '';
    if (issue.issued_at) {
        editForm.issued_at = new Date(issue.issued_at).toISOString().split('T')[0];
    }
    isEditModalOpen.value = true;
};

const closeEditModal = () => {
    isEditModalOpen.value = false;
    editForm.reset();
    editingIssueId.value = null;
};

const submitEditForm = () => {
    if (editingIssueId.value) {
        editForm.put(`/blood-consumtion/${editingIssueId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeEditModal();
            },
        });
    }
};

const deleteIssue = (issue: BloodIssueRecord) => {
    if (confirm(`Are you sure you want to delete blood consumption record for ${issue.ward_room} (${issue.blood_type} - ${issue.units_issued} bags)?`)) {
        router.delete(`/blood-consumtion/${issue.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Search Consumption Records - Blood Bank" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Main Container matching Image 1 layout -->
        <div class="rounded-3xl border border-slate-100 bg-white p-6 sm:p-8 shadow-xs space-y-6 dark:bg-card dark:border-border/60">
            
            <!-- Header Row: Title on Left, Issue / Add Button on Right -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Search Consumption Records
                </h1>

                <button 
                    type="button"
                    @click="openCreateModal"
                    class="h-11 px-5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-[0.99] self-start sm:self-auto"
                >
                    <Plus class="size-4" />
                    <span>Add Consumption Record</span>
                </button>
            </div>

            <!-- Controls Filter Bar (Location, Blood Types, Months, Years, Reset) -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                
                <!-- Location Input -->
                <div class="flex-1 min-w-[220px]">
                    <input 
                        v-model="locationSearch"
                        type="text"
                        placeholder="Location (e.g. Tubajon)..."
                        class="w-full h-11 px-4 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    />
                </div>

                <!-- Blood Type Select -->
                <div class="w-full lg:w-44">
                    <select
                        v-model="selectedBloodType"
                        class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    >
                        <option value="All">All Blood Types</option>
                        <option v-for="type in bloodTypes" :key="type" :value="type">
                            {{ type }}
                        </option>
                    </select>
                </div>

                <!-- Month Select -->
                <div class="w-full lg:w-40">
                    <select
                        v-model="selectedMonth"
                        class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    >
                        <option value="All">All Months</option>
                        <option v-for="month in monthsList" :key="month" :value="month">
                            {{ month }}
                        </option>
                    </select>
                </div>

                <!-- Year Select -->
                <div class="w-full lg:w-36">
                    <select
                        v-model="selectedYear"
                        class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    >
                        <option value="All">All Years</option>
                        <option v-for="year in yearsList" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                </div>

                <!-- Reset Button -->
                <button
                    type="button"
                    @click="resetFilters"
                    class="h-11 px-5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm transition-colors cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                >
                    Reset
                </button>

            </div>

            <!-- Quick Summary Stat Boxes (Total Bags, O+, A+, B+, AB+, B-) matching Image 1 -->
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-none">
                
                <!-- Total Bags -->
                <div class="min-w-[100px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block whitespace-nowrap">
                        Total Bags
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ totalBagsCount }}
                    </span>
                </div>

                <!-- O+ -->
                <div class="min-w-[85px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block">
                        O+
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ getBagsByType('O+') }}
                    </span>
                </div>

                <!-- A+ -->
                <div class="min-w-[85px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block">
                        A+
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ getBagsByType('A+') }}
                    </span>
                </div>

                <!-- B+ -->
                <div class="min-w-[85px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block">
                        B+
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ getBagsByType('B+') }}
                    </span>
                </div>

                <!-- AB+ -->
                <div class="min-w-[85px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block">
                        AB+
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ getBagsByType('AB+') }}
                    </span>
                </div>

                <!-- B- -->
                <div class="min-w-[85px] flex-1 rounded-2xl border border-slate-100/80 bg-[#f8fafc] p-4 text-center dark:bg-neutral-800/60 dark:border-neutral-800">
                    <span class="text-xs font-semibold text-slate-400 dark:text-neutral-400 block">
                        B-
                    </span>
                    <span class="text-2xl sm:text-3xl font-black text-[#a32222] dark:text-red-400 block mt-1">
                        {{ getBagsByType('B-') }}
                    </span>
                </div>

            </div>

            <!-- Records Count Label -->
            <div class="text-xs sm:text-sm font-semibold text-slate-500 dark:text-slate-400 pt-1">
                {{ filteredIssues.length }} {{ filteredIssues.length === 1 ? 'record' : 'records' }}
            </div>

            <!-- Consumption Records Table matching Image 1 -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-neutral-800">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50/50 text-xs font-bold text-slate-700 dark:text-slate-300 dark:bg-neutral-800/40 border-b border-slate-100 dark:border-neutral-800">
                        <tr>
                            <th class="px-5 py-4 font-bold">Month</th>
                            <th class="px-5 py-4 font-bold">Blood Type</th>
                            <th class="px-5 py-4 font-bold">Location</th>
                            <th class="px-5 py-4 font-bold">Number of Bags</th>
                            <th class="px-5 py-4 font-bold">Date Submitted</th>
                            <th class="px-5 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        <tr 
                            v-for="issue in filteredIssues" 
                            :key="issue.id"
                            class="hover:bg-slate-50/60 dark:hover:bg-neutral-800/30 transition-colors"
                        >
                            <!-- Month -->
                            <td class="px-5 py-4 font-bold text-slate-800 dark:text-slate-100">
                                {{ getMonthName(issue.issued_at) }}
                            </td>

                            <!-- Blood Type Badge -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900">
                                    {{ issue.blood_type }}
                                </span>
                            </td>

                            <!-- Location -->
                            <td class="px-5 py-4 font-medium text-slate-700 dark:text-slate-200">
                                {{ issue.ward_room }}
                            </td>

                            <!-- Number of Bags -->
                            <td class="px-5 py-4 font-bold text-slate-900 dark:text-white">
                                {{ issue.units_issued }}
                            </td>

                            <!-- Date Submitted -->
                            <td class="px-5 py-4 text-slate-600 dark:text-slate-300 font-medium">
                                {{ formatDateSubmitted(issue.issued_at) }}
                            </td>

                            <!-- Action Buttons -->
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Button -->
                                    <button 
                                        type="button" 
                                        @click="openViewModal(issue)"
                                        title="View complete consumption details"
                                        class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold gap-1 inline-flex items-center cursor-pointer shadow-2xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 transition-colors"
                                    >
                                        <Eye class="size-3.5 text-slate-500" />
                                        <span>View</span>
                                    </button>

                                    <!-- Edit Button -->
                                    <button 
                                        type="button" 
                                        @click="openEditModal(issue)"
                                        title="Edit Record"
                                        class="size-8 rounded-lg p-0 text-slate-400 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center cursor-pointer transition-colors"
                                    >
                                        <Edit class="size-3.5" />
                                    </button>

                                    <!-- Delete Button -->
                                    <button 
                                        type="button" 
                                        @click="deleteIssue(issue)"
                                        title="Delete Record"
                                        class="size-8 rounded-lg p-0 text-slate-400 hover:text-red-600 hover:bg-slate-100 dark:hover:bg-neutral-800 flex items-center justify-center cursor-pointer transition-colors"
                                    >
                                        <Trash2 class="size-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div 
                v-if="filteredIssues.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-2xl border border-dashed border-slate-200 dark:border-neutral-800 bg-slate-50/50 dark:bg-neutral-800/20"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 mb-3">
                    <Droplets class="size-7" />
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ locationSearch || selectedBloodType !== 'All' || selectedMonth !== 'All' || selectedYear !== 'All' ? 'No matching consumption records' : 'No Blood Consumption Records Logged' }}
                </h3>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mt-1">
                    {{ locationSearch || selectedBloodType !== 'All' || selectedMonth !== 'All' || selectedYear !== 'All' ? 'Try adjusting your search criteria or resetting your filters.' : 'Click "Add Consumption Record" to issue blood units and log consumptions.' }}
                </p>
                <button 
                    v-if="!locationSearch && selectedBloodType === 'All' && selectedMonth === 'All' && selectedYear === 'All'"
                    type="button"
                    @click="openCreateModal"
                    class="mt-4 px-5 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer"
                >
                    <Plus class="size-4" />
                    <span>Add First Consumption Record</span>
                </button>
            </div>

        </div>

        <!-- CREATE MODAL COMPONENT -->
        <CreateModal 
            :is-open="isCreateOpen"
            :blood-inventory-summary="props.bloodInventorySummary"
            :wards-list="props.wardsList"
            :blood-components-list="props.bloodComponentsList"
            :municipalities="props.municipalities"
            :next-requisition-no="props.nextRequisitionNo"
            @close="isCreateOpen = false"
        />

        <!-- VIEW MODAL COMPONENT -->
        <ViewModal 
            :is-open="isViewOpen"
            :issue="selectedIssueForView"
            @close="isViewOpen = false"
        />

        <!-- EDIT RECORD MODAL DIALOG -->
        <div 
            v-if="isEditModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
            @click.self="closeEditModal"
        >
            <div class="relative w-full max-w-2xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
                
                <!-- Header -->
                <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-neutral-800">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            Edit Consumption Record
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Update location, blood quantity, and issuance date.
                        </p>
                    </div>

                    <button 
                        type="button" 
                        @click="closeEditModal"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 cursor-pointer dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
                    >
                        <X class="size-5" />
                    </button>
                </div>

                <!-- Edit Form -->
                <form @submit.prevent="submitEditForm" class="space-y-4 pt-4">
                    
                    <!-- Location -->
                    <div class="space-y-1.5">
                        <Label for="edit_ward_room" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Location / Ward <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="edit_ward_room"
                            v-model="editForm.ward_room"
                            type="text"
                            placeholder="e.g. Tubajon"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="editForm.errors.ward_room" class="text-xs text-red-600">{{ editForm.errors.ward_room }}</p>
                    </div>

                    <!-- Blood Type & Number of Bags -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <Label for="edit_blood_type" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Blood Type <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="edit_blood_type"
                                v-model="editForm.blood_type"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="edit_units_issued" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Number of Bags <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="edit_units_issued"
                                type="number"
                                v-model="editForm.units_issued"
                                min="1"
                                max="100"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    <!-- Date Submitted -->
                    <div class="space-y-1.5">
                        <Label for="edit_issued_at" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Date Submitted / Issued <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="edit_issued_at"
                            type="date"
                            v-model="editForm.issued_at"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>

                    <!-- Patient Name & HRN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <Label for="edit_patient_name" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Patient Name
                            </Label>
                            <input 
                                id="edit_patient_name"
                                v-model="editForm.patient_name"
                                placeholder="Patient Name"
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="edit_patient_hrn" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Hospital Record # (HRN)
                            </Label>
                            <input 
                                id="edit_patient_hrn"
                                v-model="editForm.patient_hrn"
                                placeholder="HRN-XXXXX"
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-mono text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>
                    </div>

                    <!-- Clinical Remarks -->
                    <div class="space-y-1.5">
                        <Label for="edit_remarks" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Remarks / Notes
                        </Label>
                        <textarea 
                            id="edit_remarks"
                            v-model="editForm.remarks"
                            rows="2"
                            class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 resize-none"
                        ></textarea>
                    </div>

                    <!-- Action Footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-neutral-800">
                        <button 
                            type="button" 
                            @click="closeEditModal"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 cursor-pointer dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="editForm.processing"
                            class="px-6 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-colors flex items-center gap-2 cursor-pointer disabled:opacity-50"
                        >
                            <Spinner v-if="editForm.processing" class="size-4" />
                            <span>Update Record</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</template>
