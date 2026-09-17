<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    HeartPulse, 
    Plus, 
    Search, 
    Filter, 
    ArrowUpRight, 
    Bed, 
    AlertTriangle, 
    Droplets, 
    Clock, 
    CheckCircle2, 
    Activity, 
    Printer, 
    Edit, 
    Trash2, 
    X, 
    FileText, 
    User, 
    Stethoscope, 
    AlertCircle, 
    Copy, 
    Check, 
    ChevronDown, 
    Layers,
    ShieldAlert,
    RefreshCw
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

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
    crossmatch_status: 'Compatible' | 'Pending Crossmatch' | 'Emergency Uncrossmatched' | 'Incompatible' | string;
    urgency_level: 'Routine' | 'Urgent' | 'Stat/Emergency' | string;
    status: 'Issued' | 'Transfusion Ongoing' | 'Completed' | 'Returned/Discarded' | string;
    issued_by?: string;
    remarks?: string;
    issued_at: string;
    created_at?: string;
    updated_at?: string;
}

export interface ConsumptionStats {
    unitsIssuedToday: number;
    emergencyUnitsToday: number;
    activeWardsCount: number;
    topWardsSummary: string;
    pendingCrossmatchCount: number;
    lowStockBloodType: string;
    lowStockUnits: number;
    totalIssuesCount: number;
}

const props = withDefaults(
    defineProps<{
        issues?: BloodIssueRecord[];
        stats?: ConsumptionStats;
        bloodInventorySummary?: Record<string, number>;
        wardsList?: string[];
        bloodComponentsList?: string[];
        nextRequisitionNo?: string;
    }>(),
    {
        issues: () => [],
        stats: () => ({
            unitsIssuedToday: 0,
            emergencyUnitsToday: 0,
            activeWardsCount: 0,
            topWardsSummary: 'ICU, ER, Surgery',
            pendingCrossmatchCount: 0,
            lowStockBloodType: 'O-',
            lowStockUnits: 0,
            totalIssuesCount: 0,
        }),
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
            'ICU - Intensive Care Unit',
            'Emergency Trauma & Acute Care',
            'Operating Room / PACU',
            'OB-GYN & Delivery Ward',
            'Surgical Inpatient Ward',
            'Pediatrics & NICU',
            'Internal Medicine Ward',
            'Hemodialysis Center',
            'Oncology & Hematology Unit',
            'Orthopedic Ward',
        ],
        bloodComponentsList: () => [
            'Packed Red Blood Cells (PRBC)',
            'Whole Blood (WB)',
            'Fresh Frozen Plasma (FFP)',
            'Platelet Concentrate (PC)',
            'Cryoprecipitate (CRYO)',
        ],
        nextRequisitionNo: 'REQ-2026-0001',
    },
);

const bloodTypes = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];

// Search and Filter States
const searchQuery = ref('');
const selectedStatusFilter = ref('All');
const selectedBloodTypeFilter = ref('All');
const selectedWardFilter = ref('All');
const selectedUrgencyFilter = ref('All');

// Modal States
const isIssueModalOpen = ref(false);
const isEditing = ref(false);
const editingIssueId = ref<number | null>(null);
const isSlipModalOpen = ref(false);
const selectedSlipIssue = ref<BloodIssueRecord | null>(null);
const copiedReqId = ref<string | null>(null);

// Form
const form = useForm({
    requisition_no: '',
    patient_name: '',
    patient_hrn: '',
    ward_room: 'ICU - Intensive Care Unit',
    blood_type: 'O+',
    blood_component: 'Packed Red Blood Cells (PRBC)',
    units_issued: 1,
    attending_physician: 'Dr. G. Morales',
    crossmatch_status: 'Compatible',
    urgency_level: 'Routine',
    status: 'Issued',
    remarks: '',
});

// Open Issue Blood Unit Modal
const openCreateModal = () => {
    isEditing.value = false;
    editingIssueId.value = null;
    form.reset();
    form.clearErrors();
    form.requisition_no = props.nextRequisitionNo;
    form.ward_room = props.wardsList[0] || 'ICU - Intensive Care Unit';
    form.blood_component = props.bloodComponentsList[0] || 'Packed Red Blood Cells (PRBC)';
    form.blood_type = 'O+';
    form.units_issued = 1;
    form.attending_physician = 'Dr. G. Morales';
    form.crossmatch_status = 'Compatible';
    form.urgency_level = 'Routine';
    form.status = 'Issued';
    form.remarks = '';
    isIssueModalOpen.value = true;
};

// Open Edit Modal
const openEditModal = (issue: BloodIssueRecord) => {
    isEditing.value = true;
    editingIssueId.value = issue.id;
    form.clearErrors();
    form.requisition_no = issue.requisition_no;
    form.patient_name = issue.patient_name;
    form.patient_hrn = issue.patient_hrn;
    form.ward_room = issue.ward_room;
    form.blood_type = issue.blood_type;
    form.blood_component = issue.blood_component;
    form.units_issued = issue.units_issued;
    form.attending_physician = issue.attending_physician;
    form.crossmatch_status = issue.crossmatch_status;
    form.urgency_level = issue.urgency_level;
    form.status = issue.status;
    form.remarks = issue.remarks || '';
    isIssueModalOpen.value = true;
};

// Close Issue / Edit Modal
const closeIssueModal = () => {
    isIssueModalOpen.value = false;
    form.reset();
    editingIssueId.value = null;
    isEditing.value = false;
};

// Open Transfusion Requisition Slip Modal
const openSlipModal = (issue: BloodIssueRecord) => {
    selectedSlipIssue.value = issue;
    isSlipModalOpen.value = true;
};

// Close Slip Modal
const closeSlipModal = () => {
    isSlipModalOpen.value = false;
    selectedSlipIssue.value = null;
};

// Submit Issue Form (Add or Edit)
const submitIssueForm = () => {
    if (isEditing.value && editingIssueId.value) {
        form.put(`/blood-consumtion/${editingIssueId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeIssueModal();
            },
        });
    } else {
        form.post('/blood-consumtion', {
            preserveScroll: true,
            onSuccess: () => {
                closeIssueModal();
            },
        });
    }
};

// Quick Status Update
const updateStatus = (issue: BloodIssueRecord, newStatus: string) => {
    router.patch(
        `/blood-consumtion/${issue.id}/status`,
        { status: newStatus },
        {
            preserveScroll: true,
        },
    );
};

// Delete Issuance Record
const deleteIssue = (issue: BloodIssueRecord) => {
    if (confirm(`Are you sure you want to delete blood issuance record "${issue.requisition_no}" for ${issue.patient_name}?`)) {
        router.delete(`/blood-consumtion/${issue.id}`, {
            preserveScroll: true,
        });
    }
};

// Copy Requisition No
const copyRequisitionNo = (reqNo: string) => {
    navigator.clipboard.writeText(reqNo);
    copiedReqId.value = reqNo;
    setTimeout(() => {
        if (copiedReqId.value === reqNo) {
            copiedReqId.value = null;
        }
    }, 2000);
};

// Print Slip
const printSlip = () => {
    window.print();
};

// Format Date string
const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return isNaN(d.getTime()) 
        ? dateStr 
        : d.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
};

// Filtered Issues computed property
const filteredIssues = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    return props.issues.filter((issue) => {
        const matchesSearch =
            !q ||
            issue.patient_name.toLowerCase().includes(q) ||
            issue.patient_hrn.toLowerCase().includes(q) ||
            issue.requisition_no.toLowerCase().includes(q) ||
            issue.ward_room.toLowerCase().includes(q) ||
            issue.attending_physician.toLowerCase().includes(q) ||
            issue.blood_type.toLowerCase().includes(q) ||
            issue.blood_component.toLowerCase().includes(q);

        const matchesStatus =
            selectedStatusFilter.value === 'All' ||
            issue.status === selectedStatusFilter.value;

        const matchesBloodType =
            selectedBloodTypeFilter.value === 'All' ||
            issue.blood_type === selectedBloodTypeFilter.value;

        const matchesWard =
            selectedWardFilter.value === 'All' ||
            issue.ward_room === selectedWardFilter.value;

        const matchesUrgency =
            selectedUrgencyFilter.value === 'All' ||
            issue.urgency_level === selectedUrgencyFilter.value;

        return matchesSearch && matchesStatus && matchesBloodType && matchesWard && matchesUrgency;
    });
});

// Status counts for tabs
const statusCounts = computed(() => {
    const counts = {
        All: props.issues.length,
        Issued: 0,
        'Transfusion Ongoing': 0,
        Completed: 0,
        'Returned/Discarded': 0,
    };
    props.issues.forEach((item) => {
        if (item.status in counts) {
            counts[item.status as keyof typeof counts]++;
        }
    });
    return counts;
});

// Available stock helper for selected blood type in form
const currentSelectedStock = computed(() => {
    return props.bloodInventorySummary[form.blood_type] ?? 0;
});
</script>

<template>
    <Head title="Blood Consumption - Caraga Regional Hospital" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-border/70">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 shadow-xs">
                        <HeartPulse class="size-5" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Blood Consumption Management</span>
                            <span class="inline-flex items-center rounded-full bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-900/50 px-2.5 py-0.5 text-xs font-semibold text-rose-700 dark:text-rose-400">
                                {{ props.issues.length }} Requisitions Logged
                            </span>
                        </h1>
                        <p class="text-xs sm:text-sm text-muted-foreground">
                            Track blood unit issuance, crossmatching compatibility, transfusion execution, and hospital ward consumption.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <Button 
                    @click="openCreateModal"
                    class="bg-rose-700 hover:bg-rose-800 text-white font-semibold rounded-xl shadow-md shadow-rose-900/20 gap-2 h-10 px-4 cursor-pointer transition-all active:scale-[0.99]"
                >
                    <Plus class="size-4" />
                    <span>Issue Blood Unit</span>
                </Button>
            </div>
        </div>

        <!-- Quick Stats Overview (Dynamic Data from Backend) -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Units Issued Today -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-rose-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Units Issued Today</span>
                    <span class="rounded-xl bg-rose-50 p-2 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400">
                        <ArrowUpRight class="size-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ props.stats.unitsIssuedToday }} {{ props.stats.unitsIssuedToday === 1 ? 'Unit' : 'Units' }}
                    </span>
                    <span 
                        v-if="props.stats.emergencyUnitsToday > 0"
                        class="text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/50 px-2 py-0.5 rounded-md"
                    >
                        {{ props.stats.emergencyUnitsToday }} Emergency
                    </span>
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 block">
                    Total units released from bank today
                </span>
            </div>

            <!-- Active Hospital Wards -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-blue-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Active Wards</span>
                    <span class="rounded-xl bg-blue-50 p-2 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                        <Bed class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.activeWardsCount }} Wards
                </div>
                <span class="text-xs font-medium text-blue-600 truncate block mt-1">
                    {{ props.stats.topWardsSummary }}
                </span>
            </div>

            <!-- Crossmatch Queue / Pending -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-purple-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Crossmatch Queue</span>
                    <span class="rounded-xl bg-purple-50 p-2 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">
                        <Activity class="size-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ props.stats.pendingCrossmatchCount }} Pending
                    </span>
                    <span class="text-xs font-medium text-purple-600">Lab processing</span>
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 block">
                    Awaiting serological compatibility
                </span>
            </div>

            <!-- Low Stock Alert -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-amber-500 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Lowest Stock Reserve</span>
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                        <AlertTriangle class="size-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400">
                        {{ props.stats.lowStockBloodType }}
                    </span>
                    <span class="text-xs font-bold text-amber-700 bg-amber-100 dark:bg-amber-950/60 px-2 py-0.5 rounded-md">
                        {{ props.stats.lowStockUnits }} {{ props.stats.lowStockUnits === 1 ? 'unit left' : 'units left' }}
                    </span>
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1 block">
                    Priority recruitment recommended
                </span>
            </div>

        </div>

        <!-- Main Consumption Directory Container -->
        <div class="rounded-2xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            
            <!-- Controls Bar: Search & Multi-Filters -->
            <div class="flex flex-col gap-3 pb-3 border-b border-border/70">
                
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <!-- Search Input -->
                    <div class="relative w-full md:w-96">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Search patient, HRN, Req #, ward, doctor..." 
                            class="pl-9 h-9.5 text-xs rounded-xl"
                        />
                    </div>

                    <!-- Ward & Urgency Dropdowns -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Ward Filter -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="text-muted-foreground font-semibold text-[11px]">Ward:</span>
                            <select
                                v-model="selectedWardFilter"
                                class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-rose-600 outline-none"
                            >
                                <option value="All">All Wards</option>
                                <option v-for="ward in props.wardsList" :key="ward" :value="ward">
                                    {{ ward }}
                                </option>
                            </select>
                        </div>

                        <!-- Urgency Filter -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="text-muted-foreground font-semibold text-[11px]">Urgency:</span>
                            <select
                                v-model="selectedUrgencyFilter"
                                class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-rose-600 outline-none"
                            >
                                <option value="All">All Urgency</option>
                                <option value="Routine">Routine</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Stat/Emergency">Stat / Emergency</option>
                            </select>
                        </div>

                        <!-- Reset Filter Button -->
                        <Button 
                            v-if="searchQuery || selectedStatusFilter !== 'All' || selectedBloodTypeFilter !== 'All' || selectedWardFilter !== 'All' || selectedUrgencyFilter !== 'All'"
                            size="sm"
                            variant="ghost"
                            @click="searchQuery = ''; selectedStatusFilter = 'All'; selectedBloodTypeFilter = 'All'; selectedWardFilter = 'All'; selectedUrgencyFilter = 'All';"
                            class="h-8.5 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/40 gap-1 px-2 cursor-pointer"
                        >
                            <RefreshCw class="size-3" />
                            <span>Reset</span>
                        </Button>
                    </div>
                </div>

                <!-- Status Tabs & Blood Type Filters -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 pt-2">
                    <!-- Status Filter Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                        <button
                            v-for="statusKey in ['All', 'Issued', 'Transfusion Ongoing', 'Completed', 'Returned/Discarded']"
                            :key="statusKey"
                            @click="selectedStatusFilter = statusKey"
                            :class="[
                                'px-3 py-1.5 rounded-xl font-semibold transition-all cursor-pointer text-xs whitespace-nowrap flex items-center gap-1.5',
                                selectedStatusFilter === statusKey
                                    ? 'bg-rose-700 text-white shadow-xs'
                                    : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                            ]"
                        >
                            <span>{{ statusKey }}</span>
                            <span 
                                :class="[
                                    'px-1.5 py-0.2 rounded-full text-[10px] font-bold',
                                    selectedStatusFilter === statusKey 
                                        ? 'bg-white/20 text-white' 
                                        : 'bg-border text-muted-foreground'
                                ]"
                            >
                                {{ statusCounts[statusKey as keyof typeof statusCounts] ?? 0 }}
                            </span>
                        </button>
                    </div>

                    <!-- Blood Type Filter Pills -->
                    <div class="flex items-center gap-1 overflow-x-auto pb-1 scrollbar-none text-xs">
                        <span class="text-muted-foreground font-semibold mr-1 text-[11px]">Type:</span>
                        <button
                            v-for="bType in ['All', ...bloodTypes]"
                            :key="bType"
                            @click="selectedBloodTypeFilter = bType"
                            :class="[
                                'px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-[11px]',
                                selectedBloodTypeFilter === bType
                                    ? 'bg-rose-700 text-white shadow-xs'
                                    : 'bg-muted/60 text-muted-foreground hover:bg-muted hover:text-foreground'
                            ]"
                        >
                            {{ bType }}
                        </button>
                    </div>
                </div>

            </div>

            <!-- Blood Issues Table -->
            <div class="overflow-x-auto rounded-xl border border-border/80">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50 text-[11px] uppercase font-bold text-muted-foreground tracking-wider border-b border-border">
                        <tr>
                            <th class="px-4 py-3.5">Req #</th>
                            <th class="px-4 py-3.5">Patient Name / HRN</th>
                            <th class="px-4 py-3.5">Ward / Room</th>
                            <th class="px-4 py-3.5">Blood Type & Component</th>
                            <th class="px-4 py-3.5 text-center">Units Issued</th>
                            <th class="px-4 py-3.5">Attending Physician</th>
                            <th class="px-4 py-3.5">Crossmatch</th>
                            <th class="px-4 py-3.5">Transfusion Status</th>
                            <th class="px-4 py-3.5">Issued At</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr 
                            v-for="issue in filteredIssues" 
                            :key="issue.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Requisition # -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <button 
                                        @click="openSlipModal(issue)"
                                        class="font-mono text-xs font-bold text-rose-700 dark:text-rose-400 hover:underline flex items-center gap-1 cursor-pointer"
                                        title="View printable requisition slip"
                                    >
                                        <FileText class="size-3.5 text-rose-600" />
                                        <span>{{ issue.requisition_no }}</span>
                                    </button>
                                    <button
                                        @click="copyRequisitionNo(issue.requisition_no)"
                                        class="text-muted-foreground hover:text-foreground p-0.5 rounded cursor-pointer"
                                        title="Copy Requisition Number"
                                    >
                                        <Check v-if="copiedReqId === issue.requisition_no" class="size-3 text-emerald-600" />
                                        <Copy v-else class="size-3" />
                                    </button>
                                </div>
                                <span 
                                    :class="[
                                        'inline-flex items-center gap-1 text-[10px] font-bold px-1.5 py-0.2 rounded mt-1',
                                        issue.urgency_level === 'Stat/Emergency' ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' :
                                        issue.urgency_level === 'Urgent' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' :
                                        'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
                                    ]"
                                >
                                    {{ issue.urgency_level }}
                                </span>
                            </td>

                            <!-- Patient Name & HRN -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <User class="size-3.5 text-slate-400" />
                                    <span>{{ issue.patient_name }}</span>
                                </div>
                                <div class="text-[11px] text-muted-foreground font-mono pl-5">
                                    {{ issue.patient_hrn }}
                                </div>
                            </td>

                            <!-- Ward / Room -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 font-medium text-xs text-gray-800 dark:text-gray-200">
                                    <Bed class="size-3.5 text-blue-600 shrink-0" />
                                    <span>{{ issue.ward_room }}</span>
                                </div>
                            </td>

                            <!-- Blood Type & Component -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-black shadow-2xs',
                                            issue.blood_type.includes('O') ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' :
                                            issue.blood_type.includes('A') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                            issue.blood_type.includes('B') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                            'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'
                                        ]"
                                    >
                                        {{ issue.blood_type }}
                                    </span>
                                    <span class="text-xs text-muted-foreground truncate max-w-[160px]" :title="issue.blood_component">
                                        {{ issue.blood_component }}
                                    </span>
                                </div>
                            </td>

                            <!-- Units Issued -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 px-2.5 py-1 font-extrabold text-xs">
                                    <Droplets class="size-3" />
                                    {{ issue.units_issued }} {{ issue.units_issued === 1 ? 'Unit' : 'Units' }}
                                </span>
                            </td>

                            <!-- Attending Physician -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 text-xs text-gray-700 dark:text-gray-300 font-medium">
                                    <Stethoscope class="size-3.5 text-slate-400 shrink-0" />
                                    <span>{{ issue.attending_physician }}</span>
                                </div>
                            </td>

                            <!-- Crossmatch Status -->
                            <td class="px-4 py-3.5">
                                <span 
                                    :class="[
                                        'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold',
                                        issue.crossmatch_status === 'Compatible' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                        issue.crossmatch_status === 'Pending Crossmatch' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 animate-pulse' :
                                        issue.crossmatch_status === 'Emergency Uncrossmatched' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' :
                                        'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'
                                    ]"
                                >
                                    <CheckCircle2 v-if="issue.crossmatch_status === 'Compatible'" class="size-3" />
                                    <Clock v-else-if="issue.crossmatch_status === 'Pending Crossmatch'" class="size-3" />
                                    <ShieldAlert v-else class="size-3" />
                                    <span>{{ issue.crossmatch_status }}</span>
                                </span>
                            </td>

                            <!-- Transfusion Status & Quick Switcher -->
                            <td class="px-4 py-3.5">
                                <div class="relative group">
                                    <select
                                        :value="issue.status"
                                        @change="updateStatus(issue, ($event.target as HTMLSelectElement).value)"
                                        :class="[
                                            'text-xs font-bold px-2.5 py-1 rounded-lg border border-transparent hover:border-border cursor-pointer transition-all outline-none',
                                            issue.status === 'Completed' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                            issue.status === 'Transfusion Ongoing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                            issue.status === 'Issued' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' :
                                            'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300'
                                        ]"
                                    >
                                        <option value="Issued">Issued (Ready)</option>
                                        <option value="Transfusion Ongoing">Transfusion Ongoing</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Returned/Discarded">Returned / Discarded</option>
                                    </select>
                                </div>
                            </td>

                            <!-- Issued At -->
                            <td class="px-4 py-3.5 text-xs text-muted-foreground font-mono">
                                {{ formatDate(issue.issued_at) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View / Print Slip -->
                                    <Button 
                                        size="sm" 
                                        variant="outline" 
                                        @click="openSlipModal(issue)"
                                        title="View & Print Requisition Slip"
                                        class="h-8 px-2 text-xs text-rose-700 hover:bg-rose-50 hover:text-rose-800 dark:hover:bg-rose-950/40 gap-1 cursor-pointer"
                                    >
                                        <FileText class="size-3.5" />
                                        <span>Slip</span>
                                    </Button>

                                    <!-- Edit -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="openEditModal(issue)"
                                        title="Edit Requisition Details"
                                        class="size-8 p-0 text-muted-foreground hover:text-blue-700 cursor-pointer"
                                    >
                                        <Edit class="size-4" />
                                    </Button>

                                    <!-- Delete -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="deleteIssue(issue)"
                                        title="Delete Record"
                                        class="size-8 p-0 text-muted-foreground hover:text-red-700 cursor-pointer"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div 
                v-if="filteredIssues.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-xl border border-dashed border-border bg-muted/10"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 mb-3">
                    <HeartPulse class="size-7" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    {{ searchQuery || selectedStatusFilter !== 'All' || selectedBloodTypeFilter !== 'All' || selectedWardFilter !== 'All' ? 'No matching blood issuance records' : 'No Blood Units Issued Yet' }}
                </h3>
                <p class="text-xs sm:text-sm text-muted-foreground max-w-sm mt-1">
                    {{ searchQuery || selectedStatusFilter !== 'All' || selectedBloodTypeFilter !== 'All' ? 'Try adjusting your search query or clear your status filters.' : 'Click "+ Issue Blood Unit" to create a new requisition and issue blood bags to hospital wards.' }}
                </p>
                <Button 
                    v-if="!searchQuery && selectedStatusFilter === 'All' && selectedBloodTypeFilter === 'All'"
                    @click="openCreateModal"
                    class="mt-4 bg-rose-700 hover:bg-rose-800 text-white font-semibold rounded-xl text-xs gap-1.5 cursor-pointer"
                >
                    <Plus class="size-4" />
                    <span>Issue First Blood Unit</span>
                </Button>
            </div>

        </div>

        <!-- WIDE ISSUE / EDIT BLOOD UNIT MODAL DIALOG (max-w-4xl) -->
        <div 
            v-if="isIssueModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-4xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-5 border-b border-border">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-400 shadow-xs">
                            <Droplets class="size-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <span>{{ isEditing ? 'Edit Blood Issuance Record' : 'Issue Blood Unit (Blood Bank Release)' }}</span>
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-900/40">
                                    {{ form.requisition_no || props.nextRequisitionNo }}
                                </span>
                            </h2>
                            <p class="text-xs sm:text-sm text-muted-foreground mt-0.5">
                                {{ isEditing ? 'Update patient requisition parameters, crossmatching result, and transfusion destination' : 'Authorize and record blood unit release, compatibility results, and hospital ward destination' }}
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="closeIssueModal"
                        class="rounded-xl p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors cursor-pointer"
                    >
                        <X class="size-5" />
                    </button>
                </div>

                <!-- Form with Structured Multi-Column Grid Layout -->
                <form @submit.prevent="submitIssueForm" class="space-y-6 pt-5">
                    
                    <!-- Section 1: Requisition & Urgency Priority -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <FileText class="size-3.5" />
                            <span>Requisition & Urgency Level</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Requisition No -->
                            <div class="space-y-1.5">
                                <Label for="requisition_no" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Requisition Slip # <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="requisition_no"
                                    v-model="form.requisition_no"
                                    placeholder="e.g. REQ-2026-0001"
                                    required
                                    class="h-10 text-sm font-mono font-bold rounded-xl"
                                />
                                <div v-if="form.errors.requisition_no" class="text-xs text-red-600">{{ form.errors.requisition_no }}</div>
                            </div>

                            <!-- Urgency Level -->
                            <div class="space-y-1.5">
                                <Label for="urgency_level" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Urgency Priority <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="urgency_level"
                                    v-model="form.urgency_level"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                                >
                                    <option value="Routine">Routine Transfusion</option>
                                    <option value="Urgent">Urgent (Within 2 Hours)</option>
                                    <option value="Stat/Emergency">Stat / Emergency Resuscitation</option>
                                </select>
                                <div v-if="form.errors.urgency_level" class="text-xs text-red-600">{{ form.errors.urgency_level }}</div>
                            </div>

                            <!-- Issuance Status -->
                            <div class="space-y-1.5">
                                <Label for="status" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Initial Status <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="status"
                                    v-model="form.status"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                                >
                                    <option value="Issued">Issued (Ready for Pick-up)</option>
                                    <option value="Transfusion Ongoing">Transfusion Ongoing</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Returned/Discarded">Returned / Discarded</option>
                                </select>
                                <div v-if="form.errors.status" class="text-xs text-red-600">{{ form.errors.status }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Patient & Medical Location -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <User class="size-3.5" />
                            <span>Patient & Hospital Ward Details</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Patient Full Name -->
                            <div class="space-y-1.5 lg:col-span-2">
                                <Label for="patient_name" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Patient Full Name <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="patient_name"
                                    v-model="form.patient_name"
                                    placeholder="e.g. Maria Elena Santos"
                                    required
                                    class="h-10 text-sm rounded-xl font-medium"
                                />
                                <div v-if="form.errors.patient_name" class="text-xs text-red-600">{{ form.errors.patient_name }}</div>
                            </div>

                            <!-- Hospital Record # (HRN) -->
                            <div class="space-y-1.5">
                                <Label for="patient_hrn" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Hospital Record # (HRN) <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="patient_hrn"
                                    v-model="form.patient_hrn"
                                    placeholder="e.g. HRN-99821"
                                    required
                                    class="h-10 text-sm font-mono rounded-xl"
                                />
                                <div v-if="form.errors.patient_hrn" class="text-xs text-red-600">{{ form.errors.patient_hrn }}</div>
                            </div>

                            <!-- Attending Physician -->
                            <div class="space-y-1.5">
                                <Label for="attending_physician" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Attending Physician <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="attending_physician"
                                    v-model="form.attending_physician"
                                    placeholder="e.g. Dr. G. Morales, MD"
                                    required
                                    class="h-10 text-sm rounded-xl"
                                />
                                <div v-if="form.errors.attending_physician" class="text-xs text-red-600">{{ form.errors.attending_physician }}</div>
                            </div>
                        </div>

                        <!-- Destination Ward / Room -->
                        <div class="space-y-1.5">
                            <Label for="ward_room" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Destination Ward / Room / Clinical Unit <span class="text-rose-600">*</span>
                            </Label>
                            <select
                                id="ward_room"
                                v-model="form.ward_room"
                                required
                                class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                            >
                                <option v-for="ward in props.wardsList" :key="ward" :value="ward">
                                    {{ ward }}
                                </option>
                            </select>
                            <div v-if="form.errors.ward_room" class="text-xs text-red-600">{{ form.errors.ward_room }}</div>
                        </div>
                    </div>

                    <!-- Section 3: Blood Product & Compatibility -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-rose-800 dark:text-rose-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <Droplets class="size-3.5" />
                            <span>Blood Product & Serological Crossmatch</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Blood Type with Live Stock Indicator -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <Label for="blood_type" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                        Blood Type <span class="text-rose-600">*</span>
                                    </Label>
                                    <span class="text-[11px] font-extrabold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-1.5 py-0.2 rounded">
                                        {{ currentSelectedStock }} in stock
                                    </span>
                                </div>
                                <select
                                    id="blood_type"
                                    v-model="form.blood_type"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-extrabold focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                                >
                                    <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                                </select>
                                <div v-if="form.errors.blood_type" class="text-xs text-red-600">{{ form.errors.blood_type }}</div>
                            </div>

                            <!-- Component -->
                            <div class="space-y-1.5">
                                <Label for="blood_component" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Blood Component <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="blood_component"
                                    v-model="form.blood_component"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-xs font-semibold focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                                >
                                    <option v-for="comp in props.bloodComponentsList" :key="comp" :value="comp">
                                        {{ comp }}
                                    </option>
                                </select>
                                <div v-if="form.errors.blood_component" class="text-xs text-red-600">{{ form.errors.blood_component }}</div>
                            </div>

                            <!-- Units Issued -->
                            <div class="space-y-1.5">
                                <Label for="units_issued" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Quantity (Units) <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="units_issued"
                                    type="number"
                                    v-model="form.units_issued"
                                    min="1"
                                    max="50"
                                    required
                                    class="h-10 text-sm font-extrabold rounded-xl"
                                />
                                <div v-if="form.errors.units_issued" class="text-xs text-red-600">{{ form.errors.units_issued }}</div>
                            </div>

                            <!-- Crossmatch Compatibility -->
                            <div class="space-y-1.5">
                                <Label for="crossmatch_status" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Crossmatch Result <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="crossmatch_status"
                                    v-model="form.crossmatch_status"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-xs font-semibold focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none"
                                >
                                    <option value="Compatible">Compatible (Safe)</option>
                                    <option value="Pending Crossmatch">Pending Crossmatch (Lab in progress)</option>
                                    <option value="Emergency Uncrossmatched">Emergency Uncrossmatched (O- / O+)</option>
                                    <option value="Incompatible">Incompatible (Do Not Release)</option>
                                </select>
                                <div v-if="form.errors.crossmatch_status" class="text-xs text-red-600">{{ form.errors.crossmatch_status }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Clinical Indication & Remarks -->
                    <div class="space-y-2">
                        <Label for="remarks" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Clinical Indication / Diagnosis & Transfusion Notes
                        </Label>
                        <textarea
                            id="remarks"
                            v-model="form.remarks"
                            rows="2"
                            placeholder="e.g. Acute hemorrhagic shock secondary to trauma, Hb 6.5 g/dL, intra-operative surgical replacement, severe thrombocytopenia..."
                            class="w-full p-3 rounded-xl border border-input bg-background text-xs focus:ring-2 focus:ring-rose-600/20 focus:border-rose-600 outline-none resize-none"
                        ></textarea>
                        <div v-if="form.errors.remarks" class="text-xs text-red-600">{{ form.errors.remarks }}</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-border">
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="closeIssueModal"
                            class="h-10 px-5 rounded-xl cursor-pointer"
                        >
                            Cancel
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing"
                            class="h-10 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-semibold px-6 shadow-sm gap-2 cursor-pointer"
                        >
                            <Spinner v-if="form.processing" class="size-4" />
                            <span>{{ isEditing ? 'Update Issuance Record' : 'Authorize & Issue Blood Unit' }}</span>
                        </Button>
                    </div>

                </form>

            </div>
        </div>

        <!-- WIDE PRINTABLE OFFICIAL TRANSFUSION REQUISITION SLIP MODAL (max-w-3xl) -->
        <div 
            v-if="isSlipModalOpen && selectedSlipIssue"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-3xl rounded-2xl border border-border bg-white text-slate-900 p-6 sm:p-10 shadow-2xl transition-all my-6 print:m-0 print:p-4 print:border-none print:shadow-none">
                
                <!-- Close Button (Hidden on Print) -->
                <button 
                    @click="closeSlipModal"
                    class="absolute top-4 right-4 rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-800 print:hidden cursor-pointer transition-colors"
                >
                    <X class="size-5" />
                </button>

                <!-- Hospital Official Header -->
                <div class="text-center pb-5 border-b-2 border-slate-900 space-y-1">
                    <div class="text-[11px] uppercase font-bold tracking-widest text-slate-500">
                        Republic of the Philippines • Department of Health
                    </div>
                    <div class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">
                        CARAGA REGIONAL HOSPITAL
                    </div>
                    <div class="text-xs font-bold text-rose-800 uppercase tracking-widest">
                        Blood Bank & Transfusion Medicine Section
                    </div>
                    <div class="text-[11px] text-slate-600">
                        Rizal Street, Surigao City, Surigao del Norte 8400 • Tel: (086) 826-2459 • crh.bloodbank@doh.gov.ph
                    </div>
                </div>

                <!-- Document Title & Slip No Bar -->
                <div class="flex items-center justify-between py-3.5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black uppercase tracking-wider bg-rose-100 text-rose-800 px-3 py-1 rounded-md border border-rose-200">
                            Official Blood Release & Transfusion Slip
                        </span>
                        <span 
                            :class="[
                                'text-[11px] font-bold px-2 py-0.5 rounded',
                                selectedSlipIssue.urgency_level === 'Stat/Emergency' ? 'bg-red-100 text-red-800' :
                                selectedSlipIssue.urgency_level === 'Urgent' ? 'bg-amber-100 text-amber-800' :
                                'bg-slate-100 text-slate-700'
                            ]"
                        >
                            {{ selectedSlipIssue.urgency_level }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] uppercase text-slate-500 font-bold">Requisition Number</div>
                        <div class="font-mono text-base font-black text-slate-900">
                            {{ selectedSlipIssue.requisition_no }}
                        </div>
                    </div>
                </div>

                <!-- Patient & Clinical Details Grid (4-Column Layout) -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-b border-slate-200 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Patient Name:</span>
                        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ selectedSlipIssue.patient_name }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Hospital Record # (HRN):</span>
                        <span class="font-mono font-bold text-slate-900 block mt-0.5">{{ selectedSlipIssue.patient_hrn }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Ward / Bed / Department:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ selectedSlipIssue.ward_room }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Attending Physician:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ selectedSlipIssue.attending_physician }}</span>
                    </div>
                </div>

                <!-- Blood Product Specifications Table -->
                <div class="py-4 border-b border-slate-200">
                    <div class="text-[11px] font-bold uppercase text-slate-600 mb-2 tracking-wider">Blood Unit Release Details</div>
                    <table class="w-full text-xs text-left border border-slate-300">
                        <thead class="bg-slate-100 font-bold text-[10px] uppercase text-slate-700">
                            <tr>
                                <th class="p-2.5 border border-slate-300">Blood Type</th>
                                <th class="p-2.5 border border-slate-300">Component</th>
                                <th class="p-2.5 border border-slate-300 text-center">Qty Issued</th>
                                <th class="p-2.5 border border-slate-300">Crossmatch Test</th>
                                <th class="p-2.5 border border-slate-300">Transfusion Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-2.5 border border-slate-300 font-black text-rose-800 text-base">
                                    {{ selectedSlipIssue.blood_type }}
                                </td>
                                <td class="p-2.5 border border-slate-300 font-semibold">
                                    {{ selectedSlipIssue.blood_component }}
                                </td>
                                <td class="p-2.5 border border-slate-300 text-center font-black text-sm">
                                    {{ selectedSlipIssue.units_issued }} {{ selectedSlipIssue.units_issued === 1 ? 'Unit' : 'Units' }}
                                </td>
                                <td class="p-2.5 border border-slate-300 font-bold">
                                    <span :class="selectedSlipIssue.crossmatch_status === 'Compatible' ? 'text-emerald-700' : 'text-rose-700'">
                                        {{ selectedSlipIssue.crossmatch_status }}
                                    </span>
                                </td>
                                <td class="p-2.5 border border-slate-300 font-medium">
                                    {{ selectedSlipIssue.status }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Clinical Remarks -->
                <div class="py-3.5 border-b border-slate-200 text-xs" v-if="selectedSlipIssue.remarks">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Clinical Indication / Diagnosis:</span>
                    <p class="text-slate-800 italic mt-1 bg-slate-50 p-2.5 rounded border border-slate-200">{{ selectedSlipIssue.remarks }}</p>
                </div>

                <!-- Dual Signatures & Authorization Section -->
                <div class="grid grid-cols-2 gap-12 pt-10 pb-4 text-xs">
                    <div class="text-center">
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900 text-sm">
                            {{ selectedSlipIssue.issued_by || 'A. Salazar, RMT' }}
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1.5 tracking-wider">
                            Registered Medical Technologist (Blood Bank Officer)
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900 text-sm">
                            {{ selectedSlipIssue.attending_physician }}
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1.5 tracking-wider">
                            Attending Physician / Receiving Ward Nurse
                        </div>
                    </div>
                </div>

                <!-- Footer Timestamp & Simulated Barcode -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <div>
                        Issued At: {{ formatDate(selectedSlipIssue.issued_at) }}
                    </div>
                    <div class="font-extrabold tracking-widest text-slate-900 text-sm">
                        ||||| || ||||| ||| ||||||| || |||
                    </div>
                </div>

                <!-- Print & Action Buttons (Hidden on Print) -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 print:hidden mt-4">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="closeSlipModal"
                        class="h-10 px-4 rounded-xl cursor-pointer"
                    >
                        Close
                    </Button>
                    <Button 
                        type="button" 
                        @click="printSlip"
                        class="h-10 px-5 rounded-xl bg-rose-700 hover:bg-rose-800 text-white font-bold gap-2 cursor-pointer shadow-sm"
                    >
                        <Printer class="size-4" />
                        <span>Print Transfusion Slip</span>
                    </Button>
                </div>

            </div>
        </div>

    </div>
</template>
