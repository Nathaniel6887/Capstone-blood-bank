<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    FileText, 
    Download, 
    Calendar, 
    BarChart3, 
    TrendingUp, 
    Printer, 
    Plus, 
    Search, 
    MapPin, 
    Globe, 
    Building2, 
    CheckCircle2, 
    Clock, 
    ShieldCheck, 
    AlertCircle, 
    X, 
    Copy, 
    Check, 
    RefreshCw, 
    Filter, 
    Layers, 
    Droplets, 
    ArrowRightLeft,
    Share2,
    Eye,
    Edit,
    Trash2,
    FileSpreadsheet,
    Activity
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

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

export interface BloodReportRecord {
    id: number;
    report_code: string;
    scope: 'Inside Caraga' | 'Outside Caraga' | string;
    facility_name: string;
    province_region: string;
    report_type: string;
    reporting_period: string;
    blood_units_count: number;
    blood_breakdown?: string;
    submitting_officer: string;
    status: 'Verified' | 'Received' | 'Pending Review' | 'Archived' | string;
    summary_notes?: string;
    submitted_at: string;
    created_at?: string;
    updated_at?: string;
}

export interface ReportStats {
    totalReports: number;
    insideCaragaReports: number;
    outsideCaragaReports: number;
    totalUnitsAccounted: number;
    pendingVerifications: number;
    verifiedReports: number;
}

const props = withDefaults(
    defineProps<{
        reports?: BloodReportRecord[];
        stats?: ReportStats;
        insideCaragaSummary?: {
            totalDonors: number;
            totalDonations: number;
            totalIssues: number;
            activeMunicipalities: number;
            donorsByBloodType: any[];
            municipalitiesByProvince: Record<string, any[]>;
        };
        outsideCaragaSummary?: {
            externalReportsCount: number;
            externalUnitsAccounted: number;
            partnerFacilitiesCount: number;
            partnerRegionsCount: number;
        };
        provincesList?: string[];
        outsideRegionsList?: string[];
        reportTypesList?: string[];
        nextReportCode?: string;
    }>(),
    {
        reports: () => [],
        stats: () => ({
            totalReports: 0,
            insideCaragaReports: 0,
            outsideCaragaReports: 0,
            totalUnitsAccounted: 0,
            pendingVerifications: 0,
            verifiedReports: 0,
        }),
        insideCaragaSummary: () => ({
            totalDonors: 0,
            totalDonations: 0,
            totalIssues: 0,
            activeMunicipalities: 0,
            donorsByBloodType: [],
            municipalitiesByProvince: {},
        }),
        outsideCaragaSummary: () => ({
            externalReportsCount: 0,
            externalUnitsAccounted: 0,
            partnerFacilitiesCount: 0,
            partnerRegionsCount: 0,
        }),
        provincesList: () => [
            'Surigao del Norte (Caraga)',
            'Surigao del Sur (Caraga)',
            'Agusan del Norte (Caraga)',
            'Agusan del Sur (Caraga)',
            'Dinagat Islands (Caraga)',
        ],
        outsideRegionsList: () => [
            'Region X - Northern Mindanao (CDO / NMMC)',
            'Region XI - Davao Region (SPMC / Davao Blood Center)',
            'Region VII - Central Visayas (Cebu / VSMMC)',
            'National Capital Region (NCR - Manila DOH / PRC)',
            'Region XII - SOCCSKSARGEN',
            'Region IX - Zamboanga Peninsula',
        ],
        reportTypesList: () => [
            'Monthly Blood Collection Turnout',
            'Inter-Regional Blood Transfer & Requisition',
            'Hospital Ward Consumption Audit',
            'Serological Safety & Screening Summary',
            'Emergency Disaster Blood Allocation',
            'Mobile Blood Drive Turnout Audit',
        ],
        nextReportCode: 'RPT-CRH-2026-0001',
    },
);

// Navigation Scope Tab
const activeScopeTab = ref<'All' | 'Inside Caraga' | 'Outside Caraga' | 'Analytics'>('All');

// Search and Filter States
const searchQuery = ref('');
const selectedReportTypeFilter = ref('All');
const selectedStatusFilter = ref('All');

// Modal States
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingReportId = ref<number | null>(null);
const isSheetModalOpen = ref(false);
const selectedSheetReport = ref<BloodReportRecord | null>(null);
const copiedCode = ref<string | null>(null);

// Form
const form = useForm({
    report_code: '',
    scope: 'Inside Caraga',
    facility_name: '',
    province_region: 'Surigao del Norte (Caraga)',
    report_type: 'Monthly Blood Collection Turnout',
    reporting_period: 'September 2026',
    blood_units_count: 20,
    blood_breakdown: 'O+: 10, A+: 5, B+: 3, AB+: 2',
    submitting_officer: 'Dr. Emmanuel Salcedo, MD',
    status: 'Received',
    summary_notes: '',
});

// Watch Scope change in form to switch province/region default
const handleScopeChange = () => {
    if (form.scope === 'Inside Caraga') {
        form.province_region = props.provincesList[0] || 'Surigao del Norte (Caraga)';
        form.facility_name = 'Surigao City Health Office & Blood Station';
    } else {
        form.province_region = props.outsideRegionsList[0] || 'Region X - Northern Mindanao (CDO / NMMC)';
        form.facility_name = 'Southern Philippines Medical Center (SPMC) Blood Bank - Davao';
    }
};

// Open Create Modal
const openCreateModal = (presetScope: 'Inside Caraga' | 'Outside Caraga' = 'Inside Caraga') => {
    isEditing.value = false;
    editingReportId.value = null;
    form.reset();
    form.clearErrors();
    form.scope = presetScope;
    handleScopeChange();
    form.report_code = props.nextReportCode;
    form.report_type = props.reportTypesList[0] || 'Monthly Blood Collection Turnout';
    form.reporting_period = 'September 2026';
    form.blood_units_count = 25;
    form.blood_breakdown = 'O+: 12, A+: 8, B+: 4, AB+: 1';
    form.submitting_officer = 'Dr. Emmanuel Salcedo, MD';
    form.status = 'Received';
    form.summary_notes = '';
    isModalOpen.value = true;
};

// Open Edit Modal
const openEditModal = (report: BloodReportRecord) => {
    isEditing.value = true;
    editingReportId.value = report.id;
    form.clearErrors();
    form.report_code = report.report_code;
    form.scope = report.scope;
    form.facility_name = report.facility_name;
    form.province_region = report.province_region;
    form.report_type = report.report_type;
    form.reporting_period = report.reporting_period;
    form.blood_units_count = report.blood_units_count;
    form.blood_breakdown = report.blood_breakdown || '';
    form.submitting_officer = report.submitting_officer;
    form.status = report.status;
    form.summary_notes = report.summary_notes || '';
    isModalOpen.value = true;
};

// Close Modal
const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    editingReportId.value = null;
    isEditing.value = false;
};

// Open Sheet View Modal
const openSheetModal = (report: BloodReportRecord) => {
    selectedSheetReport.value = report;
    isSheetModalOpen.value = true;
};

// Close Sheet View Modal
const closeSheetModal = () => {
    isSheetModalOpen.value = false;
    selectedSheetReport.value = null;
};

// Submit Form
const submitForm = () => {
    if (isEditing.value && editingReportId.value) {
        form.put(`/reports/${editingReportId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        form.post('/reports', {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

// Quick Status Update
const updateStatus = (report: BloodReportRecord, newStatus: string) => {
    router.patch(
        `/reports/${report.id}/status`,
        { status: newStatus },
        {
            preserveScroll: true,
        },
    );
};

// Delete Report
const deleteReport = (report: BloodReportRecord) => {
    if (confirm(`Are you sure you want to delete report "${report.report_code}" from ${report.facility_name}?`)) {
        router.delete(`/reports/${report.id}`, {
            preserveScroll: true,
        });
    }
};

// Copy Code
const copyCode = (code: string) => {
    navigator.clipboard.writeText(code);
    copiedCode.value = code;
    setTimeout(() => {
        if (copiedCode.value === code) {
            copiedCode.value = null;
        }
    }, 2000);
};

// Print Sheet
const printSheet = () => {
    window.print();
};

// Export Filtered Reports to CSV
const exportCSV = () => {
    const dataToExport = filteredReports.value;
    if (dataToExport.length === 0) {
        alert('No report data available to export.');
        return;
    }

    const headers = [
        'Report Code',
        'Scope',
        'Facility Name',
        'Province / Region',
        'Report Type',
        'Period',
        'Units Reported',
        'Breakdown',
        'Submitting Officer',
        'Status',
        'Date Received',
    ];

    const rows = dataToExport.map((r) => [
        `"${r.report_code}"`,
        `"${r.scope}"`,
        `"${r.facility_name.replace(/"/g, '""')}"`,
        `"${r.province_region.replace(/"/g, '""')}"`,
        `"${r.report_type.replace(/"/g, '""')}"`,
        `"${r.reporting_period}"`,
        r.blood_units_count,
        `"${(r.blood_breakdown || '').replace(/"/g, '""')}"`,
        `"${r.submitting_officer.replace(/"/g, '""')}"`,
        `"${r.status}"`,
        `"${formatDate(r.submitted_at)}"`,
    ]);

    const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map((e) => e.join(','))].join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `CRH_Blood_Reports_${activeScopeTab.value.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Format Date
const formatDate = (dateStr?: string) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return isNaN(d.getTime()) 
        ? dateStr 
        : d.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
};

// Filtered Reports computed
const filteredReports = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    return props.reports.filter((r) => {
        const matchesScope =
            activeScopeTab.value === 'All' ||
            activeScopeTab.value === 'Analytics' ||
            r.scope === activeScopeTab.value;

        const matchesQuery =
            !q ||
            r.report_code.toLowerCase().includes(q) ||
            r.facility_name.toLowerCase().includes(q) ||
            r.province_region.toLowerCase().includes(q) ||
            r.submitting_officer.toLowerCase().includes(q) ||
            r.report_type.toLowerCase().includes(q) ||
            r.reporting_period.toLowerCase().includes(q);

        const matchesType =
            selectedReportTypeFilter.value === 'All' ||
            r.report_type === selectedReportTypeFilter.value;

        const matchesStatus =
            selectedStatusFilter.value === 'All' ||
            r.status === selectedStatusFilter.value;

        return matchesScope && matchesQuery && matchesType && matchesStatus;
    });
});
</script>

<template>
    <Head title="Reports & Analytics - Caraga Regional Hospital" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-border/70">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 shadow-xs">
                        <FileText class="size-5" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Regional Reports & Analytics</span>
                            <span class="inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-900/50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:text-blue-400">
                                {{ props.reports.length }} Reports Logged
                            </span>
                        </h1>
                        <p class="text-xs sm:text-sm text-muted-foreground">
                            Generate, receive, and audit regional blood reports originating from <strong>Inside Caraga Region</strong> (provincial networks) and <strong>Outside Caraga Region</strong> (inter-regional transfers & DOH exchanges).
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <Button 
                    variant="outline" 
                    @click="exportCSV"
                    class="h-10 text-xs font-semibold gap-1.5 cursor-pointer rounded-xl border-border hover:bg-muted"
                >
                    <Download class="size-4" />
                    <span>Export CSV</span>
                </Button>

                <Button 
                    @click="openCreateModal('Inside Caraga')"
                    class="bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl shadow-md shadow-blue-900/20 gap-2 h-10 px-4 cursor-pointer transition-all active:scale-[0.99]"
                >
                    <Plus class="size-4" />
                    <span>Receive / Log Report</span>
                </Button>
            </div>
        </div>

        <!-- Quick Stats Overview (Inside & Outside Metrics) -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Total Reports Logged -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-blue-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Reports Logged</span>
                    <span class="rounded-xl bg-blue-50 p-2 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                        <FileText class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalReports }}
                </div>
                <span class="text-xs font-medium text-blue-600 mt-1 block">
                    {{ props.stats.verifiedReports }} verified • {{ props.stats.pendingVerifications }} in review
                </span>
            </div>

            <!-- Inside Caraga Network Reports -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-emerald-500 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inside Caraga Network</span>
                    <span class="rounded-xl bg-emerald-50 p-2 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <MapPin class="size-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ props.stats.insideCaragaReports }}
                    </span>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md">
                        5 Provinces
                    </span>
                </div>
                <span class="text-xs font-medium text-emerald-600 mt-1 block">
                    Municipal drives & local health centers
                </span>
            </div>

            <!-- Outside Caraga Transfers Reports -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-purple-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Outside Caraga Transfers</span>
                    <span class="rounded-xl bg-purple-50 p-2 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">
                        <Globe class="size-4" />
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ props.stats.outsideCaragaReports }}
                    </span>
                    <span class="text-xs font-bold text-purple-700 bg-purple-100 dark:bg-purple-950/60 px-2 py-0.5 rounded-md">
                        Inter-Regional
                    </span>
                </div>
                <span class="text-xs font-medium text-purple-600 mt-1 block">
                    Region X, XI, VII, & NCR Exchanges
                </span>
            </div>

            <!-- Total Blood Units Accounted -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-rose-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Units Accounted In Reports</span>
                    <span class="rounded-xl bg-rose-50 p-2 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400">
                        <Droplets class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalUnitsAccounted }} Bags
                </div>
                <span class="text-xs font-medium text-rose-600 mt-1 block">
                    Audited turnover volume
                </span>
            </div>

        </div>

        <!-- Scope Switcher Tabs -->
        <div class="flex items-center gap-2 border-b border-border/70 pb-3">
            <button
                @click="activeScopeTab = 'All'"
                :class="[
                    'px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2',
                    activeScopeTab === 'All'
                        ? 'bg-blue-700 text-white shadow-xs'
                        : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                ]"
            >
                <Layers class="size-3.5" />
                <span>All Regional Reports</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white font-black">
                    {{ props.reports.length }}
                </span>
            </button>

            <button
                @click="activeScopeTab = 'Inside Caraga'"
                :class="[
                    'px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2',
                    activeScopeTab === 'Inside Caraga'
                        ? 'bg-emerald-700 text-white shadow-xs'
                        : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                ]"
            >
                <MapPin class="size-3.5" />
                <span>Inside Caraga Region</span>
                <span :class="activeScopeTab === 'Inside Caraga' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'" class="px-1.5 py-0.2 rounded-full text-[10px] font-black">
                    {{ props.stats.insideCaragaReports }}
                </span>
            </button>

            <button
                @click="activeScopeTab = 'Outside Caraga'"
                :class="[
                    'px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2',
                    activeScopeTab === 'Outside Caraga'
                        ? 'bg-purple-700 text-white shadow-xs'
                        : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                ]"
            >
                <Globe class="size-3.5" />
                <span>Outside Caraga (Inter-Regional)</span>
                <span :class="activeScopeTab === 'Outside Caraga' ? 'bg-white/20 text-white' : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'" class="px-1.5 py-0.2 rounded-full text-[10px] font-black">
                    {{ props.stats.outsideCaragaReports }}
                </span>
            </button>

            <button
                @click="activeScopeTab = 'Analytics'"
                :class="[
                    'px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer flex items-center gap-2',
                    activeScopeTab === 'Analytics'
                        ? 'bg-rose-700 text-white shadow-xs'
                        : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                ]"
            >
                <Activity class="size-3.5" />
                <span>Live Regional Analytics</span>
            </button>
        </div>

        <!-- MAIN TAB 1 & 2 & 3: REPORTS DIRECTORY TABLE -->
        <div v-if="activeScopeTab !== 'Analytics'" class="rounded-2xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            
            <!-- Controls Bar -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-3 border-b border-border/70">
                <!-- Search Input -->
                <div class="relative w-full md:w-96">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        placeholder="Search facility, report code, officer, region..." 
                        class="pl-9 h-9.5 text-xs rounded-xl"
                    />
                </div>

                <!-- Dropdown Filters -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Report Type Filter -->
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-muted-foreground font-semibold text-[11px]">Type:</span>
                        <select
                            v-model="selectedReportTypeFilter"
                            class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-blue-600 outline-none max-w-[200px] truncate"
                        >
                            <option value="All">All Report Types</option>
                            <option v-for="type in props.reportTypesList" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex items-center gap-1.5 text-xs">
                        <span class="text-muted-foreground font-semibold text-[11px]">Status:</span>
                        <select
                            v-model="selectedStatusFilter"
                            class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-blue-600 outline-none"
                        >
                            <option value="All">All Statuses</option>
                            <option value="Verified">Verified</option>
                            <option value="Received">Received</option>
                            <option value="Pending Review">Pending Review</option>
                        </select>
                    </div>

                    <!-- Reset Filter -->
                    <Button 
                        v-if="searchQuery || selectedReportTypeFilter !== 'All' || selectedStatusFilter !== 'All'"
                        size="sm"
                        variant="ghost"
                        @click="searchQuery = ''; selectedReportTypeFilter = 'All'; selectedStatusFilter = 'All';"
                        class="h-8.5 text-xs text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-950/40 gap-1 px-2 cursor-pointer"
                    >
                        <RefreshCw class="size-3" />
                        <span>Reset</span>
                    </Button>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="overflow-x-auto rounded-xl border border-border/80">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50 text-[11px] uppercase font-bold text-muted-foreground tracking-wider border-b border-border">
                        <tr>
                            <th class="px-4 py-3.5">Report Code</th>
                            <th class="px-4 py-3.5">Scope & Facility Origin</th>
                            <th class="px-4 py-3.5">Province / Region</th>
                            <th class="px-4 py-3.5">Report Type & Period</th>
                            <th class="px-4 py-3.5 text-center">Volume (Units)</th>
                            <th class="px-4 py-3.5">Submitting Officer</th>
                            <th class="px-4 py-3.5">Verification Status</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr 
                            v-for="report in filteredReports" 
                            :key="report.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Report Code -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5">
                                    <button 
                                        @click="openSheetModal(report)"
                                        class="font-mono text-xs font-bold text-blue-700 dark:text-blue-400 hover:underline flex items-center gap-1 cursor-pointer"
                                        title="View official printable report sheet"
                                    >
                                        <FileText class="size-3.5 text-blue-600" />
                                        <span>{{ report.report_code }}</span>
                                    </button>
                                    <button
                                        @click="copyCode(report.report_code)"
                                        class="text-muted-foreground hover:text-foreground cursor-pointer"
                                        title="Copy Code"
                                    >
                                        <Check v-if="copiedCode === report.report_code" class="size-3 text-emerald-600" />
                                        <Copy v-else class="size-3" />
                                    </button>
                                </div>
                                <div class="text-[10px] text-muted-foreground font-mono mt-0.5">
                                    {{ formatDate(report.submitted_at) }}
                                </div>
                            </td>

                            <!-- Scope & Facility Name -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-1">
                                    <span 
                                        :class="[
                                            'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-wider',
                                            report.scope === 'Inside Caraga'
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300'
                                                : 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'
                                        ]"
                                    >
                                        <MapPin v-if="report.scope === 'Inside Caraga'" class="size-2.5" />
                                        <Globe v-else class="size-2.5" />
                                        <span>{{ report.scope }}</span>
                                    </span>
                                    <div class="font-bold text-gray-900 dark:text-white truncate max-w-[240px]" :title="report.facility_name">
                                        {{ report.facility_name }}
                                    </div>
                                </div>
                            </td>

                            <!-- Province / Region -->
                            <td class="px-4 py-3.5">
                                <div class="text-xs font-medium text-gray-800 dark:text-gray-200">
                                    {{ report.province_region }}
                                </div>
                            </td>

                            <!-- Report Type & Period -->
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-xs text-gray-900 dark:text-white">
                                    {{ report.report_type }}
                                </div>
                                <div class="text-[11px] text-muted-foreground font-medium">
                                    Period: {{ report.reporting_period }}
                                </div>
                            </td>

                            <!-- Volume Units -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 px-2.5 py-1 font-black text-xs">
                                    <Droplets class="size-3" />
                                    {{ report.blood_units_count }} Bags
                                </span>
                            </td>

                            <!-- Submitting Officer -->
                            <td class="px-4 py-3.5">
                                <div class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate max-w-[180px]">
                                    {{ report.submitting_officer }}
                                </div>
                            </td>

                            <!-- Verification Status & Quick Switcher -->
                            <td class="px-4 py-3.5">
                                <div class="relative">
                                    <select
                                        :value="report.status"
                                        @change="updateStatus(report, ($event.target as HTMLSelectElement).value)"
                                        :class="[
                                            'text-xs font-bold px-2.5 py-1 rounded-lg border border-transparent hover:border-border cursor-pointer transition-all outline-none',
                                            report.status === 'Verified' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                            report.status === 'Received' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                            'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                        ]"
                                    >
                                        <option value="Verified">Verified Official</option>
                                        <option value="Received">Received / Logged</option>
                                        <option value="Pending Review">Pending Review</option>
                                        <option value="Archived">Archived</option>
                                    </select>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Sheet -->
                                    <Button 
                                        size="sm" 
                                        variant="outline" 
                                        @click="openSheetModal(report)"
                                        title="View Official Sheet"
                                        class="h-8 px-2 text-xs text-blue-700 hover:bg-blue-50 hover:text-blue-800 dark:hover:bg-blue-950/40 gap-1 cursor-pointer"
                                    >
                                        <Eye class="size-3.5" />
                                        <span>View</span>
                                    </Button>

                                    <!-- Edit -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="openEditModal(report)"
                                        title="Edit Report"
                                        class="size-8 p-0 text-muted-foreground hover:text-blue-700 cursor-pointer"
                                    >
                                        <Edit class="size-4" />
                                    </Button>

                                    <!-- Delete -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="deleteReport(report)"
                                        title="Delete Report"
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
                v-if="filteredReports.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-xl border border-dashed border-border bg-muted/10"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-950/60 dark:text-blue-400 mb-3">
                    <FileText class="size-7" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    {{ searchQuery || selectedReportTypeFilter !== 'All' || selectedStatusFilter !== 'All' ? 'No matching regional reports found' : 'No Reports Logged for this Category' }}
                </h3>
                <p class="text-xs sm:text-sm text-muted-foreground max-w-sm mt-1">
                    {{ searchQuery || selectedReportTypeFilter !== 'All' || selectedStatusFilter !== 'All' ? 'Try adjusting your search criteria or resetting your filters.' : 'Click "+ Receive / Log Report" to log reports from Inside Caraga or Outside Caraga networks.' }}
                </p>
                <Button 
                    v-if="!searchQuery && selectedReportTypeFilter === 'All' && selectedStatusFilter === 'All'"
                    @click="openCreateModal(activeScopeTab === 'Outside Caraga' ? 'Outside Caraga' : 'Inside Caraga')"
                    class="mt-4 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-xl text-xs gap-1.5 cursor-pointer"
                >
                    <Plus class="size-4" />
                    <span>Log First Regional Report</span>
                </Button>
            </div>

        </div>

        <!-- TAB 4: LIVE REGIONAL ANALYTICS SUMMARY -->
        <div v-if="activeScopeTab === 'Analytics'" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Inside Caraga Breakdown Card -->
                <div class="rounded-2xl border border-border bg-card p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-border">
                        <div class="flex items-center gap-2">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                <MapPin class="size-4" />
                            </span>
                            <div>
                                <h3 class="font-bold text-base text-gray-900 dark:text-white">Inside Caraga Region Coverage</h3>
                                <p class="text-xs text-muted-foreground">Provincial & municipal blood banking turnout</p>
                            </div>
                        </div>
                        <span class="text-xs font-black bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full">
                            5 Provinces Active
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Registered Donors</span>
                            <div class="text-xl font-extrabold mt-1 text-slate-900 dark:text-white">{{ props.insideCaragaSummary.totalDonors }}</div>
                        </div>
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Total Bags Collected</span>
                            <div class="text-xl font-extrabold mt-1 text-emerald-600">{{ props.insideCaragaSummary.totalDonations }}</div>
                        </div>
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Active Locations</span>
                            <div class="text-xl font-extrabold mt-1 text-blue-600">{{ props.insideCaragaSummary.activeMunicipalities }}</div>
                        </div>
                    </div>

                    <!-- Donors by Blood Group in Caraga -->
                    <div class="space-y-2 pt-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Collected ABO & Rh Distribution</span>
                        <div class="grid grid-cols-4 gap-2 text-xs">
                            <div v-for="item in props.insideCaragaSummary.donorsByBloodType" :key="item.blood_type" class="p-2.5 rounded-xl border border-border bg-background text-center">
                                <span class="font-black text-rose-700 dark:text-rose-400 text-sm block">{{ item.blood_type }}</span>
                                <span class="text-xs font-semibold text-muted-foreground">{{ item.total_bags }} Bags</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outside Caraga Partner Network Card -->
                <div class="rounded-2xl border border-border bg-card p-6 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-border">
                        <div class="flex items-center gap-2">
                            <span class="flex size-8 items-center justify-center rounded-lg bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">
                                <Globe class="size-4" />
                            </span>
                            <div>
                                <h3 class="font-bold text-base text-gray-900 dark:text-white">Outside Caraga Inter-Regional Exchange</h3>
                                <p class="text-xs text-muted-foreground">National & regional referral hospital linkages</p>
                            </div>
                        </div>
                        <span class="text-xs font-black bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full">
                            Inter-Regional
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">External Reports</span>
                            <div class="text-xl font-extrabold mt-1 text-slate-900 dark:text-white">{{ props.outsideCaragaSummary.externalReportsCount }}</div>
                        </div>
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Transferred Units</span>
                            <div class="text-xl font-extrabold mt-1 text-purple-600">{{ props.outsideCaragaSummary.externalUnitsAccounted }} Bags</div>
                        </div>
                        <div class="bg-muted/40 p-3 rounded-xl">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Partner Centers</span>
                            <div class="text-xl font-extrabold mt-1 text-rose-600">{{ props.outsideCaragaSummary.partnerFacilitiesCount }}</div>
                        </div>
                    </div>

                    <!-- Partner Networks List -->
                    <div class="space-y-2 pt-2 text-xs">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Key External Linked Centers</span>
                        <div class="space-y-1.5">
                            <div class="p-2.5 rounded-xl border border-border bg-background flex items-center justify-between">
                                <span class="font-semibold">SPMC Blood Center (Davao / Region XI)</span>
                                <span class="font-mono text-xs text-emerald-600 font-bold">Active Link</span>
                            </div>
                            <div class="p-2.5 rounded-xl border border-border bg-background flex items-center justify-between">
                                <span class="font-semibold">NMMC Blood Transfusion (CDO / Region X)</span>
                                <span class="font-mono text-xs text-emerald-600 font-bold">Active Link</span>
                            </div>
                            <div class="p-2.5 rounded-xl border border-border bg-background flex items-center justify-between">
                                <span class="font-semibold">Philippine Red Cross National HQ (Manila / NCR)</span>
                                <span class="font-mono text-xs text-emerald-600 font-bold">Strategic Reserve</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- WIDE RECEIVE / LOG REPORT MODAL DIALOG (max-w-4xl) -->
        <div 
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-4xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-5 border-b border-border">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400 shadow-xs">
                            <FileText class="size-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <span>{{ isEditing ? 'Edit Regional Blood Report' : 'Receive / Log Blood Report' }}</span>
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-900/40">
                                    {{ form.report_code || props.nextReportCode }}
                                </span>
                            </h2>
                            <p class="text-xs sm:text-sm text-muted-foreground mt-0.5">
                                Record and verify blood collection turnouts, inter-regional transfers, or safety audits from Inside or Outside Caraga.
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="closeModal"
                        class="rounded-xl p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors cursor-pointer"
                    >
                        <X class="size-5" />
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitForm" class="space-y-6 pt-5">
                    
                    <!-- Section 1: Geographic Scope Selector -->
                    <div class="space-y-2">
                        <Label class="text-xs font-extrabold uppercase tracking-wider text-blue-800 dark:text-blue-400">
                            Geographic Origin / Scope <span class="text-rose-600">*</span>
                        </Label>
                        <div class="grid grid-cols-2 gap-3">
                            <label 
                                :class="[
                                    'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                                    form.scope === 'Inside Caraga'
                                        ? 'border-emerald-600 bg-emerald-50/50 dark:bg-emerald-950/40 text-emerald-900 dark:text-emerald-300 ring-2 ring-emerald-600/20'
                                        : 'border-border bg-muted/20 hover:bg-muted/40'
                                ]"
                            >
                                <input 
                                    type="radio" 
                                    value="Inside Caraga" 
                                    v-model="form.scope" 
                                    @change="handleScopeChange"
                                    class="accent-emerald-600 size-4" 
                                />
                                <div>
                                    <div class="text-sm font-bold flex items-center gap-1.5">
                                        <MapPin class="size-3.5 text-emerald-600" />
                                        <span>Inside Caraga Region</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground">Provincial hospitals, RHUs, and local mobile drives</div>
                                </div>
                            </label>

                            <label 
                                :class="[
                                    'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition-all',
                                    form.scope === 'Outside Caraga'
                                        ? 'border-purple-600 bg-purple-50/50 dark:bg-purple-950/40 text-purple-900 dark:text-purple-300 ring-2 ring-purple-600/20'
                                        : 'border-border bg-muted/20 hover:bg-muted/40'
                                ]"
                            >
                                <input 
                                    type="radio" 
                                    value="Outside Caraga" 
                                    v-model="form.scope" 
                                    @change="handleScopeChange"
                                    class="accent-purple-600 size-4" 
                                />
                                <div>
                                    <div class="text-sm font-bold flex items-center gap-1.5">
                                        <Globe class="size-3.5 text-purple-600" />
                                        <span>Outside Caraga Region</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground">Inter-regional transfers (Davao, CDO, Cebu, Manila DOH)</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Section 2: Facility & Location Metadata -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-blue-800 dark:text-blue-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <Building2 class="size-3.5" />
                            <span>Originating Facility & Regional Classification</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Facility Name -->
                            <div class="space-y-1.5">
                                <Label for="facility_name" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Originating Facility / Hospital <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="facility_name"
                                    v-model="form.facility_name"
                                    placeholder="e.g. Surigao City Health Office / SPMC Blood Bank"
                                    required
                                    class="h-10 text-sm rounded-xl font-medium"
                                />
                                <div v-if="form.errors.facility_name" class="text-xs text-red-600">{{ form.errors.facility_name }}</div>
                            </div>

                            <!-- Province / Region Dropdown -->
                            <div class="space-y-1.5">
                                <Label for="province_region" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ form.scope === 'Inside Caraga' ? 'Caraga Province' : 'Originating Region / Network' }} <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="province_region"
                                    v-model="form.province_region"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none"
                                >
                                    <template v-if="form.scope === 'Inside Caraga'">
                                        <option v-for="prov in props.provincesList" :key="prov" :value="prov">{{ prov }}</option>
                                    </template>
                                    <template v-else>
                                        <option v-for="reg in props.outsideRegionsList" :key="reg" :value="reg">{{ reg }}</option>
                                    </template>
                                </select>
                                <div v-if="form.errors.province_region" class="text-xs text-red-600">{{ form.errors.province_region }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Report Classification & Period -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-blue-800 dark:text-blue-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <FileSpreadsheet class="size-3.5" />
                            <span>Report Specifications & Turnover Volume</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Report Type -->
                            <div class="space-y-1.5">
                                <Label for="report_type" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Report Classification <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="report_type"
                                    v-model="form.report_type"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-xs font-semibold focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none"
                                >
                                    <option v-for="type in props.reportTypesList" :key="type" :value="type">{{ type }}</option>
                                </select>
                                <div v-if="form.errors.report_type" class="text-xs text-red-600">{{ form.errors.report_type }}</div>
                            </div>

                            <!-- Reporting Period -->
                            <div class="space-y-1.5">
                                <Label for="reporting_period" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Reporting Period <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="reporting_period"
                                    v-model="form.reporting_period"
                                    placeholder="e.g. September 2026 / Q3 2026"
                                    required
                                    class="h-10 text-sm rounded-xl"
                                />
                                <div v-if="form.errors.reporting_period" class="text-xs text-red-600">{{ form.errors.reporting_period }}</div>
                            </div>

                            <!-- Blood Units Count -->
                            <div class="space-y-1.5">
                                <Label for="blood_units_count" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Total Units Accounted <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="blood_units_count"
                                    type="number"
                                    v-model="form.blood_units_count"
                                    min="0"
                                    required
                                    class="h-10 text-sm font-extrabold rounded-xl"
                                />
                                <div v-if="form.errors.blood_units_count" class="text-xs text-red-600">{{ form.errors.blood_units_count }}</div>
                            </div>
                        </div>

                        <!-- Blood Breakdown -->
                        <div class="space-y-1.5">
                            <Label for="blood_breakdown" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Blood Product / ABO Breakdown
                            </Label>
                            <Input 
                                id="blood_breakdown"
                                v-model="form.blood_breakdown"
                                placeholder="e.g. O+: 12, A+: 8, B+: 4, AB+: 1, O-: 1 (Packed RBC / FFP)"
                                class="h-10 text-sm font-mono rounded-xl"
                            />
                            <div v-if="form.errors.blood_breakdown" class="text-xs text-red-600">{{ form.errors.blood_breakdown }}</div>
                        </div>
                    </div>

                    <!-- Section 4: Officer & Verification Status -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-blue-800 dark:text-blue-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <ShieldCheck class="size-3.5" />
                            <span>Submitting Official & Verification</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Submitting Officer -->
                            <div class="space-y-1.5">
                                <Label for="submitting_officer" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Submitting Official / Lead MedTech <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="submitting_officer"
                                    v-model="form.submitting_officer"
                                    placeholder="e.g. Dr. Emmanuel Salcedo, MD / MedTech J. Rivera, RMT"
                                    required
                                    class="h-10 text-sm rounded-xl"
                                />
                                <div v-if="form.errors.submitting_officer" class="text-xs text-red-600">{{ form.errors.submitting_officer }}</div>
                            </div>

                            <!-- Verification Status -->
                            <div class="space-y-1.5">
                                <Label for="report_status" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Verification State <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="report_status"
                                    v-model="form.status"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none"
                                >
                                    <option value="Verified">Verified Official Report</option>
                                    <option value="Received">Received / Logged</option>
                                    <option value="Pending Review">Pending Review</option>
                                    <option value="Archived">Archived</option>
                                </select>
                                <div v-if="form.errors.status" class="text-xs text-red-600">{{ form.errors.status }}</div>
                            </div>
                        </div>

                        <!-- Summary Notes -->
                        <div class="space-y-1.5">
                            <Label for="summary_notes" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Executive Summary / Cold-Chain Findings
                            </Label>
                            <textarea
                                id="summary_notes"
                                v-model="form.summary_notes"
                                rows="2"
                                placeholder="e.g. Cold-chain sea/land transport verified at 4°C. Buffer transfer received in optimal quality..."
                                class="w-full p-3 rounded-xl border border-input bg-background text-xs focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 outline-none resize-none"
                            ></textarea>
                            <div v-if="form.errors.summary_notes" class="text-xs text-red-600">{{ form.errors.summary_notes }}</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-border">
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="closeModal"
                            class="h-10 px-5 rounded-xl cursor-pointer"
                        >
                            Cancel
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="form.processing"
                            class="h-10 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold px-6 shadow-sm gap-2 cursor-pointer"
                        >
                            <Spinner v-if="form.processing" class="size-4" />
                            <span>{{ isEditing ? 'Update Report Entry' : 'Authorize & Log Report' }}</span>
                        </Button>
                    </div>

                </form>

            </div>
        </div>

        <!-- WIDE PRINTABLE OFFICIAL REPORT SHEET MODAL (max-w-4xl) -->
        <div 
            v-if="isSheetModalOpen && selectedSheetReport"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-4xl rounded-2xl border border-border bg-white text-slate-900 p-6 sm:p-10 shadow-2xl transition-all my-6 print:m-0 print:p-4 print:border-none print:shadow-none">
                
                <!-- Close Button (Hidden on Print) -->
                <button 
                    @click="closeSheetModal"
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
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-widest">
                        National Voluntary Blood Services Program (NVBSP) • Regional Blood Center
                    </div>
                    <div class="text-[11px] text-slate-600">
                        Rizal Street, Surigao City, Surigao del Norte 8400 • Tel: (086) 826-2459 • crh.bloodbank@doh.gov.ph
                    </div>
                </div>

                <!-- Document Title & Code -->
                <div class="flex items-center justify-between py-4 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span 
                            :class="[
                                'text-xs font-black uppercase tracking-wider px-3 py-1 rounded-md border',
                                selectedSheetReport.scope === 'Inside Caraga'
                                    ? 'bg-emerald-100 text-emerald-800 border-emerald-300'
                                    : 'bg-purple-100 text-purple-800 border-purple-300'
                            ]"
                        >
                            {{ selectedSheetReport.scope }} Transfusion Report
                        </span>
                        <span class="text-[11px] font-bold px-2.5 py-0.5 rounded bg-blue-100 text-blue-800">
                            {{ selectedSheetReport.status }}
                        </span>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] uppercase text-slate-500 font-bold">Official Report Code</div>
                        <div class="font-mono text-base font-black text-slate-900">
                            {{ selectedSheetReport.report_code }}
                        </div>
                    </div>
                </div>

                <!-- Metadata Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4 border-b border-slate-200 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Originating Facility:</span>
                        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ selectedSheetReport.facility_name }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Province / Region:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ selectedSheetReport.province_region }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Report Classification:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ selectedSheetReport.report_type }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Reporting Period:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ selectedSheetReport.reporting_period }}</span>
                    </div>
                </div>

                <!-- Product & Volume Table -->
                <div class="py-4 border-b border-slate-200">
                    <div class="text-[11px] font-bold uppercase text-slate-600 mb-2 tracking-wider">Turnover Volume & Blood Component Breakdown</div>
                    <table class="w-full text-xs text-left border border-slate-300">
                        <thead class="bg-slate-100 font-bold text-[10px] uppercase text-slate-700">
                            <tr>
                                <th class="p-2.5 border border-slate-300">Scope Classification</th>
                                <th class="p-2.5 border border-slate-300">Component / ABO Breakdown</th>
                                <th class="p-2.5 border border-slate-300 text-center">Total Volume Accounted</th>
                                <th class="p-2.5 border border-slate-300">Audit Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-2.5 border border-slate-300 font-bold text-slate-900">
                                    {{ selectedSheetReport.scope }}
                                </td>
                                <td class="p-2.5 border border-slate-300 font-mono text-slate-800">
                                    {{ selectedSheetReport.blood_breakdown || 'Whole Blood / Packed RBC standard turnover' }}
                                </td>
                                <td class="p-2.5 border border-slate-300 text-center font-black text-base text-rose-800">
                                    {{ selectedSheetReport.blood_units_count }} Units
                                </td>
                                <td class="p-2.5 border border-slate-300 font-semibold text-emerald-700">
                                    {{ selectedSheetReport.status }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Executive Findings / Notes -->
                <div class="py-3.5 border-b border-slate-200 text-xs" v-if="selectedSheetReport.summary_notes">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Executive Summary / Clinical Inspection Notes:</span>
                    <p class="text-slate-800 italic mt-1 bg-slate-50 p-3 rounded border border-slate-200 leading-relaxed">{{ selectedSheetReport.summary_notes }}</p>
                </div>

                <!-- Official Verification Signatures -->
                <div class="grid grid-cols-2 gap-12 pt-10 pb-4 text-xs">
                    <div class="text-center">
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900 text-sm">
                            {{ selectedSheetReport.submitting_officer }}
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1.5 tracking-wider">
                            Submitting Health Officer / Transfusion Lead
                        </div>
                    </div>

                    <div class="text-center">
                        <div class="border-b-2 border-slate-900 pb-1 font-bold text-slate-900 text-sm">
                            Dr. Victoria Fernandez, MD, FPSP
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1.5 tracking-wider">
                            Regional Blood Center Director • CRH Blood Bank
                        </div>
                    </div>
                </div>

                <!-- Footer Timestamp -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <div>
                        Received on: {{ formatDate(selectedSheetReport.submitted_at) }}
                    </div>
                    <div class="font-extrabold tracking-widest text-slate-900 text-sm">
                        ||||| ||| ||||||| || |||||| |||
                    </div>
                </div>

                <!-- Print & Action Buttons (Hidden on Print) -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200 print:hidden mt-4">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="closeSheetModal"
                        class="h-10 px-4 rounded-xl cursor-pointer"
                    >
                        Close
                    </Button>
                    <Button 
                        type="button" 
                        @click="printSheet"
                        class="h-10 px-5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold gap-2 cursor-pointer shadow-sm"
                    >
                        <Printer class="size-4" />
                        <span>Print Official Sheet</span>
                    </Button>
                </div>

            </div>
        </div>

    </div>
</template>
