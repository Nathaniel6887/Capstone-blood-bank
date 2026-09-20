<script setup lang="ts">
import { computed } from 'vue';
import { 
    X, 
    Printer, 
    FileText, 
    MapPin, 
    Globe, 
    Calendar, 
    Droplets, 
    User, 
    ShieldCheck, 
    Building2,
    CheckCircle2
} from '@lucide/vue';
import { Button } from '@/components/ui/button';

export interface BloodReportRecord {
    id: number;
    report_code: string;
    scope: string;
    facility_name: string;
    province_region: string;
    report_type: string;
    reporting_period: string;
    blood_units_count: number;
    blood_breakdown?: string;
    submitting_officer: string;
    status: string;
    summary_notes?: string;
    submitted_at: string;
    created_at?: string;
    updated_at?: string;
}

export interface DonorViewRecord {
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

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        report?: BloodReportRecord | null;
        donor?: DonorViewRecord | null;
    }>(),
    {
        isOpen: true,
        report: null,
        donor: null,
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const handleClose = () => {
    emit('close');
};

const printSheet = () => {
    window.print();
};

const formattedDate = (dateStr?: string) => {
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
</script>

<template>
    <div 
        v-if="isOpen && (report || donor)"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-3xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150 print:m-0 print:p-4 print:border-none print:shadow-none">
            
            <!-- Modal Header (Hidden on Print) -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-neutral-800 print:hidden">
                <div class="flex items-start gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                        <FileText class="size-6 text-[#a32222] dark:text-red-400" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                                {{ report ? 'Official Regional Blood Report Sheet' : 'Quarterly Registered Donor Profile' }}
                            </h2>
                            <span v-if="report" class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                                {{ report.report_code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ report ? 'Verified blood bank transmission, volume audit, and regulatory summary.' : 'First-time registered donor details and donation log.' }}
                        </p>
                    </div>
                </div>

                <button 
                    type="button"
                    @click="handleClose"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 cursor-pointer dark:hover:bg-neutral-800 dark:hover:text-neutral-200 transition-colors"
                >
                    <X class="size-5" />
                </button>
            </div>

            <!-- VIEW 1: DONOR PROFILE VIEW -->
            <div v-if="donor" class="space-y-6 pt-4">
                <!-- Highlight Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-slate-100 bg-[#fef2f2] p-3.5 text-center dark:bg-red-950/20 dark:border-red-900/40">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Blood Type</span>
                        <span class="text-2xl font-black text-[#dc2626] dark:text-red-400 block mt-0.5">
                            {{ donor.blood_type }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Age & Gender</span>
                        <span class="text-base font-bold text-slate-900 dark:text-white block mt-1">
                            {{ donor.age }} yrs • {{ donor.gender }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Location</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block mt-1.5 truncate">
                            {{ donor.municipality }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">First Registered</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block mt-1.5">
                            {{ formattedDate(donor.created_at) }}
                        </span>
                    </div>
                </div>

                <!-- Printable Sheet -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-neutral-950 dark:border-neutral-800 space-y-4">
                    <div class="text-center pb-4 border-b-2 border-slate-900 dark:border-neutral-700 space-y-1">
                        <div class="text-[10px] uppercase font-bold tracking-widest text-slate-500">
                            Republic of the Philippines • Department of Health
                        </div>
                        <div class="text-lg font-black tracking-tight text-slate-900 dark:text-white">
                            CARAGA REGIONAL HOSPITAL • BLOOD DONOR REGISTRY
                        </div>
                        <div class="text-xs font-bold text-red-700 uppercase tracking-widest">
                            Official First-Time Quarterly Donor Record
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-3 text-xs border-b border-slate-200 dark:border-neutral-800">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Donor Full Name:</span>
                            <span class="text-sm font-bold text-slate-900 dark:text-white block mt-0.5">{{ donor.full_name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Contact Information:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white block mt-0.5">{{ donor.contact_information }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Barangay & Municipality:</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ donor.municipality }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Total Donations:</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ donor.total_donations }} {{ donor.total_donations === 1 ? 'Bag' : 'Bags' }}</span>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <div>Verified by: CRH Blood Services Section</div>
                        <div class="font-black text-slate-900 dark:text-white text-sm tracking-widest">||||| |||| ||| |||||||</div>
                    </div>
                </div>
            </div>

            <!-- VIEW 2: REPORT SHEET VIEW -->
            <div v-else-if="report" class="space-y-6 pt-4">
                <!-- Highlight Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-slate-100 bg-[#fef2f2] p-3.5 text-center dark:bg-red-950/20 dark:border-red-900/40">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Volume Accounted</span>
                        <span class="text-2xl font-black text-[#dc2626] dark:text-red-400 block mt-0.5">
                            {{ report.blood_units_count }} Bags
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Scope</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white block mt-1 truncate">
                            {{ report.scope }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Status</span>
                        <span class="text-xs font-bold text-emerald-600 block mt-1.5">
                            {{ report.status }}
                        </span>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Reporting Period</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block mt-1.5 truncate">
                            {{ report.reporting_period }}
                        </span>
                    </div>
                </div>

                <!-- Official Printable Report Sheet -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-neutral-950 dark:border-neutral-800 space-y-4">
                    
                    <div class="text-center pb-4 border-b-2 border-slate-900 dark:border-neutral-700 space-y-1">
                        <div class="text-[10px] uppercase font-bold tracking-widest text-slate-500">
                            Republic of the Philippines • Department of Health
                        </div>
                        <div class="text-lg font-black tracking-tight text-slate-900 dark:text-white">
                            CARAGA REGIONAL HOSPITAL • BLOOD SERVICES NETWORK
                        </div>
                        <div class="text-xs font-bold text-red-700 uppercase tracking-widest">
                            Regional Blood Turnout & Audit Certification
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-3 text-xs border-b border-slate-200 dark:border-neutral-800">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Submitting Facility:</span>
                            <span class="text-sm font-bold text-slate-900 dark:text-white block mt-0.5">{{ report.facility_name }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Province / Region:</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ report.province_region }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Report Classification:</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ report.report_type }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-500 block">Submitting Officer:</span>
                            <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ report.submitting_officer }}</span>
                        </div>
                    </div>

                    <div v-if="report.blood_breakdown" class="py-2 text-xs border-b border-slate-200 dark:border-neutral-800">
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Blood Breakdown:</span>
                        <div class="font-mono text-sm font-bold text-slate-800 dark:text-slate-200 mt-1">
                            {{ report.blood_breakdown }}
                        </div>
                    </div>

                    <div v-if="report.summary_notes" class="py-2 text-xs">
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Summary Notes & Observations:</span>
                        <p class="text-slate-800 dark:text-slate-200 italic mt-1 bg-slate-50 dark:bg-neutral-900 p-2.5 rounded border border-slate-200 dark:border-neutral-800">
                            {{ report.summary_notes }}
                        </p>
                    </div>

                    <!-- Signatures -->
                    <div class="grid grid-cols-2 gap-8 pt-6 pb-2 text-xs text-center">
                        <div>
                            <div class="border-b-2 border-slate-900 dark:border-neutral-700 pb-1 font-bold">
                                {{ report.submitting_officer }}
                            </div>
                            <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                                Submitting Officer / Facility Representative
                            </div>
                        </div>
                        <div>
                            <div class="border-b-2 border-slate-900 dark:border-neutral-700 pb-1 font-bold">
                                Dr. Emmanuel Salcedo, MD / A. Salazar, RMT
                            </div>
                            <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                                CRH Regional Blood Bank Lead Auditor
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200 dark:border-neutral-800 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                        <div>Date Audited: {{ formattedDate(report.submitted_at) }}</div>
                        <div class="font-black text-slate-900 dark:text-white text-sm tracking-widest">||||| ||| ||||| || |||||||</div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Hidden on Print) -->
            <div class="flex items-center justify-end gap-3 pt-5 mt-5 border-t border-slate-100 dark:border-neutral-800 print:hidden">
                <button 
                    type="button" 
                    @click="handleClose"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 cursor-pointer dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800 transition-colors"
                >
                    Close
                </button>
                <button 
                    type="button" 
                    @click="printSheet"
                    class="px-5 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-colors flex items-center gap-2 cursor-pointer"
                >
                    <Printer class="size-4" />
                    <span>Print Report Sheet</span>
                </button>
            </div>

        </div>
    </div>
</template>
