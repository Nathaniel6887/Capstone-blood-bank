<script setup lang="ts">
import { computed } from 'vue';
import { 
    X, 
    Printer, 
    Droplets, 
    MapPin, 
    User, 
    Calendar, 
    FileText, 
    Stethoscope, 
    CheckCircle2, 
    Clock, 
    ShieldAlert, 
    Building
} from '@lucide/vue';
import { Button } from '@/components/ui/button';

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

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        issue?: BloodIssueRecord | null;
    }>(),
    {
        isOpen: true,
        issue: null,
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const handleClose = () => {
    emit('close');
};

const printSlip = () => {
    window.print();
};

// Formatted Date
const formattedDate = computed(() => {
    if (!props.issue?.issued_at) return 'N/A';
    const d = new Date(props.issue.issued_at);
    return isNaN(d.getTime())
        ? props.issue.issued_at
        : d.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
});

// Month Name
const formattedMonth = computed(() => {
    if (!props.issue?.issued_at) return 'January';
    const d = new Date(props.issue.issued_at);
    return isNaN(d.getTime())
        ? 'January'
        : d.toLocaleDateString('en-US', { month: 'long' });
});
</script>

<template>
    <div 
        v-if="isOpen && issue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-3xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150 print:m-0 print:p-4 print:border-none print:shadow-none">
            
            <!-- Header (Hidden on Print) -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-neutral-800 print:hidden">
                <div class="flex items-start gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                        <Droplets class="size-6 text-[#a32222] dark:text-red-400" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                                Consumption Record Details
                            </h2>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-neutral-300">
                                {{ issue.requisition_no }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Requisition release, ward destination, and serological transfusion log.
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

            <!-- Key Consumption Highlight Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 my-5">
                <!-- Blood Type -->
                <div class="rounded-xl border border-slate-100 bg-[#fef2f2] p-3.5 text-center dark:bg-red-950/20 dark:border-red-900/40">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Blood Type</span>
                    <span class="text-2xl font-black text-[#dc2626] dark:text-red-400 block mt-0.5">
                        {{ issue.blood_type }}
                    </span>
                </div>

                <!-- Number of Bags -->
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Number of Bags</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white block mt-0.5">
                        {{ issue.units_issued }} {{ issue.units_issued === 1 ? 'Bag' : 'Bags' }}
                    </span>
                </div>

                <!-- Location -->
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Location / Ward</span>
                    <span class="text-base font-bold text-slate-800 dark:text-slate-200 block mt-1 truncate" :title="issue.ward_room">
                        {{ issue.ward_room }}
                    </span>
                </div>

                <!-- Date Submitted -->
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-3.5 text-center dark:bg-neutral-800 dark:border-neutral-700">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 block uppercase">Month & Date</span>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block mt-1.5">
                        {{ formattedDate }}
                    </span>
                </div>
            </div>

            <!-- Printable Official Requisition Slip Container -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 dark:bg-neutral-950 dark:border-neutral-800 text-slate-900 dark:text-neutral-100 space-y-4">
                
                <!-- Hospital Header -->
                <div class="text-center pb-4 border-b-2 border-slate-900 dark:border-neutral-700 space-y-1">
                    <div class="text-[10px] uppercase font-bold tracking-widest text-slate-500">
                        Republic of the Philippines • Department of Health
                    </div>
                    <div class="text-lg font-black tracking-tight text-slate-900 dark:text-white">
                        CARAGA REGIONAL HOSPITAL
                    </div>
                    <div class="text-xs font-bold text-red-700 uppercase tracking-widest">
                        Blood Bank & Transfusion Medicine Section
                    </div>
                    <div class="text-[10px] text-slate-500">
                        Surigao City, Caraga Region • Tel: (086) 826-2459 • crh.bloodbank@doh.gov.ph
                    </div>
                </div>

                <!-- Requisition Header Bar -->
                <div class="flex items-center justify-between py-2 border-b border-slate-200 dark:border-neutral-800 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold uppercase px-2.5 py-0.5 rounded bg-red-100 text-red-800 text-[11px]">
                            Official Blood Release Slip
                        </span>
                        <span 
                            :class="[
                                'font-bold px-2 py-0.5 rounded text-[11px]',
                                issue.urgency_level === 'Stat/Emergency' ? 'bg-red-100 text-red-800' :
                                issue.urgency_level === 'Urgent' ? 'bg-amber-100 text-amber-800' :
                                'bg-slate-100 text-slate-700'
                            ]"
                        >
                            {{ issue.urgency_level }}
                        </span>
                    </div>
                    <div class="font-mono font-black text-sm text-slate-900 dark:text-white">
                        {{ issue.requisition_no }}
                    </div>
                </div>

                <!-- Clinical & Patient Details Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-3 text-xs border-b border-slate-200 dark:border-neutral-800">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Patient Name:</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white block mt-0.5">{{ issue.patient_name }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Hospital Record # (HRN):</span>
                        <span class="font-mono font-bold text-slate-900 dark:text-white block mt-0.5">{{ issue.patient_hrn }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Location / Ward:</span>
                        <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ issue.ward_room }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Attending Physician:</span>
                        <span class="font-bold text-slate-900 dark:text-white block mt-0.5">{{ issue.attending_physician }}</span>
                    </div>
                </div>

                <!-- Component & Compatibility Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-3 text-xs border-b border-slate-200 dark:border-neutral-800">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Blood Component:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 block mt-0.5">{{ issue.blood_component }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Crossmatch Result:</span>
                        <span 
                            :class="[
                                'inline-flex items-center gap-1 font-bold mt-0.5',
                                issue.crossmatch_status === 'Compatible' ? 'text-emerald-600' :
                                issue.crossmatch_status === 'Pending Crossmatch' ? 'text-purple-600' : 'text-red-600'
                            ]"
                        >
                            {{ issue.crossmatch_status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Transfusion Status:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 block mt-0.5">{{ issue.status }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-slate-500 block">Issued By:</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 block mt-0.5">{{ issue.issued_by || 'MedTech A. Salazar, RMT' }}</span>
                    </div>
                </div>

                <!-- Clinical Indication / Remarks -->
                <div v-if="issue.remarks" class="py-2 text-xs">
                    <span class="text-[10px] font-bold uppercase text-slate-500 block">Clinical Indication / Diagnosis:</span>
                    <p class="text-slate-800 dark:text-slate-200 italic mt-1 bg-slate-50 dark:bg-neutral-900 p-2 rounded border border-slate-200 dark:border-neutral-800">
                        {{ issue.remarks }}
                    </p>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-2 gap-8 pt-6 pb-2 text-xs text-center">
                    <div>
                        <div class="border-b-2 border-slate-900 dark:border-neutral-700 pb-1 font-bold">
                            {{ issue.issued_by || 'A. Salazar, RMT' }}
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                            Blood Bank Officer (RMT)
                        </div>
                    </div>
                    <div>
                        <div class="border-b-2 border-slate-900 dark:border-neutral-700 pb-1 font-bold">
                            {{ issue.attending_physician }}
                        </div>
                        <div class="text-[10px] uppercase font-bold text-slate-500 mt-1 tracking-wider">
                            Attending Physician / Receiving Officer
                        </div>
                    </div>
                </div>

                <!-- Barcode & Timestamp -->
                <div class="pt-3 border-t border-slate-200 dark:border-neutral-800 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <div>
                        Date Issued: {{ formattedDate }}
                    </div>
                    <div class="font-black text-slate-900 dark:text-white text-sm tracking-widest">
                        ||||| ||| ||||| || |||||||
                    </div>
                </div>

            </div>

            <!-- Modal Actions (Hidden on Print) -->
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
                    @click="printSlip"
                    class="px-5 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-colors flex items-center gap-2 cursor-pointer"
                >
                    <Printer class="size-4" />
                    <span>Print Record Slip</span>
                </button>
            </div>

        </div>
    </div>
</template>
