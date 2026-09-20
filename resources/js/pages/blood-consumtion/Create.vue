<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Droplets, 
    X, 
    HeartPulse, 
    MapPin, 
    User, 
    Calendar, 
    FileText, 
    Stethoscope, 
    Layers,
    ShieldCheck
} from '@lucide/vue';
import { Spinner } from '@/components/ui/spinner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export interface MunicipalityOption {
    id: number;
    province: string;
    name: string;
}

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        bloodInventorySummary?: Record<string, number>;
        wardsList?: string[];
        bloodComponentsList?: string[];
        municipalities?: Record<string, MunicipalityOption[]>;
        nextRequisitionNo?: string;
    }>(),
    {
        isOpen: true,
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
        municipalities: () => ({}),
        nextRequisitionNo: 'REQ-2026-0001',
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

const bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];

// Form State
const form = useForm({
    requisition_no: props.nextRequisitionNo || '',
    patient_name: 'Maria Elena Santos',
    patient_hrn: 'HRN-99821',
    ward_room: 'Tubajon',
    blood_type: 'O+',
    blood_component: 'Packed Red Blood Cells (PRBC)',
    units_issued: 8,
    attending_physician: 'Dr. G. Morales, MD',
    crossmatch_status: 'Compatible',
    urgency_level: 'Routine',
    status: 'Issued',
    remarks: '',
    issued_at: new Date().toISOString().split('T')[0],
});

// Available Stock Helper
const availableStock = computed(() => {
    return props.bloodInventorySummary[form.blood_type] ?? 0;
});

// Close and Reset
const handleClose = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

// Submit Form
const submitForm = () => {
    form.post('/blood-consumtion', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('created');
            emit('close');
        },
    });
};
</script>

<template>
    <div 
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-3xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-neutral-800">
                <div class="flex items-start gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                        <Droplets class="size-6 text-[#a32222] dark:text-red-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                            <span>Add Consumption Record</span>
                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-red-50 text-red-700 dark:bg-red-950/80 dark:text-red-300 border border-red-200 dark:border-red-900/40">
                                {{ form.requisition_no || props.nextRequisitionNo }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Issue blood bags to clinical departments, rural health units, and recorded consumption destinations.
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

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-5 pt-4">
                
                <!-- Section 1: Location & Consumption Summary -->
                <div class="space-y-3">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-[#a32222] dark:text-red-400 flex items-center gap-1.5 pb-1 border-b border-slate-100 dark:border-neutral-800">
                        <MapPin class="size-3.5" />
                        <span>Location & Blood Product Specifications</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        
                        <!-- Location (Municipality / Hospital Ward) -->
                        <div class="space-y-1.5">
                            <Label for="create_ward_room" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Location / Ward <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_ward_room"
                                v-model="form.ward_room"
                                type="text"
                                placeholder="e.g. Tubajon, San Jose, ICU"
                                required
                                list="location-suggestions"
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                            <datalist id="location-suggestions">
                                <option v-for="w in props.wardsList" :key="w" :value="w" />
                            </datalist>
                            <p v-if="form.errors.ward_room" class="text-xs text-red-600">{{ form.errors.ward_room }}</p>
                        </div>

                        <!-- Blood Type -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <Label for="create_blood_type" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    Blood Type <span class="text-red-600">*</span>
                                </Label>
                                <span class="text-[11px] font-bold text-red-700 bg-red-50 dark:bg-red-950/60 dark:text-red-300 px-1.5 py-0.2 rounded">
                                    {{ availableStock }} bags in stock
                                </span>
                            </div>
                            <select
                                id="create_blood_type"
                                v-model="form.blood_type"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                            </select>
                            <p v-if="form.errors.blood_type" class="text-xs text-red-600">{{ form.errors.blood_type }}</p>
                        </div>

                        <!-- Number of Bags / Units Issued -->
                        <div class="space-y-1.5">
                            <Label for="create_units_issued" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Number of Bags <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_units_issued"
                                type="number"
                                v-model="form.units_issued"
                                min="1"
                                max="100"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                            <p v-if="form.errors.units_issued" class="text-xs text-red-600">{{ form.errors.units_issued }}</p>
                        </div>

                    </div>
                </div>

                <!-- Section 2: Date & Requisition Parameters -->
                <div class="space-y-3">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-[#a32222] dark:text-red-400 flex items-center gap-1.5 pb-1 border-b border-slate-100 dark:border-neutral-800">
                        <Calendar class="size-3.5" />
                        <span>Date & Component Details</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        
                        <!-- Date Submitted / Issued -->
                        <div class="space-y-1.5">
                            <Label for="create_issued_at" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Date Submitted / Issued <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_issued_at"
                                type="date"
                                v-model="form.issued_at"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                        <!-- Blood Component -->
                        <div class="space-y-1.5">
                            <Label for="create_blood_component" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Component Type <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="create_blood_component"
                                v-model="form.blood_component"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option v-for="comp in props.bloodComponentsList" :key="comp" :value="comp">
                                    {{ comp }}
                                </option>
                            </select>
                        </div>

                        <!-- Urgency Priority -->
                        <div class="space-y-1.5">
                            <Label for="create_urgency_level" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Urgency Priority <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="create_urgency_level"
                                v-model="form.urgency_level"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option value="Routine">Routine Transfusion</option>
                                <option value="Urgent">Urgent (Within 2 Hours)</option>
                                <option value="Stat/Emergency">Stat / Emergency Resuscitation</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- Section 3: Clinical & Patient Information -->
                <div class="space-y-3">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-[#a32222] dark:text-red-400 flex items-center gap-1.5 pb-1 border-b border-slate-100 dark:border-neutral-800">
                        <User class="size-3.5" />
                        <span>Clinical & Recipient Details</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        
                        <!-- Patient Name -->
                        <div class="space-y-1.5">
                            <Label for="create_patient_name" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Patient Name <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_patient_name"
                                v-model="form.patient_name"
                                placeholder="e.g. Maria Elena Santos"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                            <p v-if="form.errors.patient_name" class="text-xs text-red-600">{{ form.errors.patient_name }}</p>
                        </div>

                        <!-- Hospital Record # (HRN) -->
                        <div class="space-y-1.5">
                            <Label for="create_patient_hrn" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Hospital Record # (HRN) <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_patient_hrn"
                                v-model="form.patient_hrn"
                                placeholder="e.g. HRN-99821"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-mono text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                            <p v-if="form.errors.patient_hrn" class="text-xs text-red-600">{{ form.errors.patient_hrn }}</p>
                        </div>

                        <!-- Attending Physician -->
                        <div class="space-y-1.5">
                            <Label for="create_attending_physician" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Attending Physician <span class="text-red-600">*</span>
                            </Label>
                            <input 
                                id="create_attending_physician"
                                v-model="form.attending_physician"
                                placeholder="e.g. Dr. G. Morales, MD"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                        <!-- Crossmatch Status -->
                        <div class="space-y-1.5">
                            <Label for="create_crossmatch_status" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Crossmatch Result <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="create_crossmatch_status"
                                v-model="form.crossmatch_status"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option value="Compatible">Compatible (Safe)</option>
                                <option value="Pending Crossmatch">Pending Crossmatch (Lab in progress)</option>
                                <option value="Emergency Uncrossmatched">Emergency Uncrossmatched (O- / O+)</option>
                                <option value="Incompatible">Incompatible</option>
                            </select>
                        </div>

                        <!-- Transfusion Status -->
                        <div class="space-y-1.5">
                            <Label for="create_status" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Status <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="create_status"
                                v-model="form.status"
                                required
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            >
                                <option value="Issued">Issued (Ready)</option>
                                <option value="Transfusion Ongoing">Transfusion Ongoing</option>
                                <option value="Completed">Completed</option>
                                <option value="Returned/Discarded">Returned / Discarded</option>
                            </select>
                        </div>

                        <!-- Requisition Slip # -->
                        <div class="space-y-1.5">
                            <Label for="create_requisition_no" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                Requisition Slip #
                            </Label>
                            <input 
                                id="create_requisition_no"
                                v-model="form.requisition_no"
                                placeholder="e.g. REQ-2026-0001"
                                class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-mono text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                            />
                        </div>

                    </div>
                </div>

                <!-- Section 4: Clinical Indication / Remarks -->
                <div class="space-y-1.5">
                    <Label for="create_remarks" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Clinical Remarks / Indication
                    </Label>
                    <textarea 
                        id="create_remarks"
                        v-model="form.remarks"
                        rows="2"
                        placeholder="e.g. Routine municipal health replenishment, urgent transfusion for acute blood loss, post-op care..."
                        class="w-full p-3 rounded-xl border border-slate-200 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100 resize-none"
                    ></textarea>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-neutral-800">
                    <button 
                        type="button" 
                        @click="handleClose"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 cursor-pointer dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="px-6 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-colors flex items-center gap-2 cursor-pointer disabled:opacity-50"
                    >
                        <Spinner v-if="form.processing" class="size-4" />
                        <span>Save Consumption Record</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</template>
