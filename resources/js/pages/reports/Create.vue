<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    FileText, 
    X, 
    MapPin, 
    Globe, 
    Building2, 
    Calendar, 
    Droplets, 
    User, 
    ShieldCheck
} from '@lucide/vue';
import { Spinner } from '@/components/ui/spinner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        provincesList?: string[];
        outsideRegionsList?: string[];
        reportTypesList?: string[];
        nextReportCode?: string;
        municipalities?: Record<string, any[]>;
    }>(),
    {
        isOpen: true,
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
            'Quarterly New Donor Report',
            'Monthly Blood Collection Turnout',
            'Inter-Regional Blood Transfer & Requisition',
            'Hospital Ward Consumption Audit',
            'Serological Safety & Screening Summary',
            'Emergency Disaster Blood Allocation',
            'Mobile Blood Drive Turnout Audit',
        ],
        nextReportCode: 'RPT-CRH-2026-0001',
        municipalities: () => ({}),
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created'): void;
}>();

// Form State
const form = useForm({
    report_code: props.nextReportCode || '',
    scope: 'Inside Caraga',
    facility_name: 'Dinagat Islands Provincial Health Office',
    province_region: 'Dinagat Islands (Caraga)',
    report_type: 'Quarterly New Donor Report',
    reporting_period: 'Q1 2026 (January to March)',
    blood_units_count: 24,
    blood_breakdown: 'O+: 10, A+: 6, B+: 5, AB+: 3',
    submitting_officer: 'Dr. Emmanuel Salcedo, MD',
    status: 'Verified',
    summary_notes: '',
});

// Scope Switch Handler
const handleScopeChange = () => {
    if (form.scope === 'Inside Caraga') {
        form.province_region = props.provincesList[0] || 'Surigao del Norte (Caraga)';
        form.facility_name = 'Surigao City Health Office & Blood Station';
    } else {
        form.province_region = props.outsideRegionsList[0] || 'Region X - Northern Mindanao (CDO / NMMC)';
        form.facility_name = 'Southern Philippines Medical Center (SPMC) Blood Bank - Davao';
    }
};

const handleClose = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const submitForm = () => {
    form.post('/reports', {
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
                        <FileText class="size-6 text-[#a32222] dark:text-red-400" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                            <span>Log & Generate Blood Report</span>
                            <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-red-50 text-red-700 dark:bg-red-950/80 dark:text-red-300 border border-red-200 dark:border-red-900/40">
                                {{ form.report_code || props.nextReportCode }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Record quarterly donor registries, collection summaries, or inter-facility transfers.
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
                
                <!-- Scope Segmented Toggle -->
                <div class="space-y-1.5">
                    <Label class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Regional Jurisdiction / Scope <span class="text-red-600">*</span>
                    </Label>
                    <div class="grid grid-cols-2 gap-2 p-1 rounded-xl bg-slate-100 dark:bg-neutral-800">
                        <button
                            type="button"
                            @click="form.scope = 'Inside Caraga'; handleScopeChange()"
                            :class="[
                                'py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer',
                                form.scope === 'Inside Caraga'
                                    ? 'bg-white text-[#a32222] shadow-xs dark:bg-neutral-700 dark:text-red-400'
                                    : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200'
                            ]"
                        >
                            <MapPin class="size-3.5" />
                            <span>Inside Caraga Region</span>
                        </button>
                        <button
                            type="button"
                            @click="form.scope = 'Outside Caraga'; handleScopeChange()"
                            :class="[
                                'py-2.5 px-3 rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer',
                                form.scope === 'Outside Caraga'
                                    ? 'bg-white text-[#a32222] shadow-xs dark:bg-neutral-700 dark:text-red-400'
                                    : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200'
                            ]"
                        >
                            <Globe class="size-3.5" />
                            <span>Outside Caraga (Inter-Regional)</span>
                        </button>
                    </div>
                </div>

                <!-- Section 1: Facility & Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Facility Name -->
                    <div class="space-y-1.5">
                        <Label for="rep_facility" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Submitting Facility / RHU / Hospital <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="rep_facility"
                            v-model="form.facility_name"
                            placeholder="e.g. Tubajon Municipal Health Station"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="form.errors.facility_name" class="text-xs text-red-600">{{ form.errors.facility_name }}</p>
                    </div>

                    <!-- Province / Region -->
                    <div class="space-y-1.5">
                        <Label for="rep_province" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Province / Region Origin <span class="text-red-600">*</span>
                        </Label>
                        <select
                            id="rep_province"
                            v-model="form.province_region"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <template v-if="form.scope === 'Inside Caraga'">
                                <option v-for="prov in props.provincesList" :key="prov" :value="prov">{{ prov }}</option>
                            </template>
                            <template v-else>
                                <option v-for="reg in props.outsideRegionsList" :key="reg" :value="reg">{{ reg }}</option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Section 2: Report Classification & Period -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <!-- Report Type -->
                    <div class="space-y-1.5">
                        <Label for="rep_type" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Report Classification <span class="text-red-600">*</span>
                        </Label>
                        <select
                            id="rep_type"
                            v-model="form.report_type"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <option v-for="t in props.reportTypesList" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>

                    <!-- Reporting Period -->
                    <div class="space-y-1.5">
                        <Label for="rep_period" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Reporting Period <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="rep_period"
                            v-model="form.reporting_period"
                            placeholder="e.g. Q1 2026 (Jan - Mar)"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>

                    <!-- Volume Units Accounted -->
                    <div class="space-y-1.5">
                        <Label for="rep_units" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Volume (Bags / Units) <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="rep_units"
                            type="number"
                            v-model="form.blood_units_count"
                            min="0"
                            max="10000"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>
                </div>

                <!-- Section 3: Submitting Officer, Breakdown & Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Submitting Officer -->
                    <div class="space-y-1.5">
                        <Label for="rep_officer" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Submitting Officer / Lead MedTech <span class="text-red-600">*</span>
                        </Label>
                        <input 
                            id="rep_officer"
                            v-model="form.submitting_officer"
                            placeholder="e.g. Dr. Emmanuel Salcedo, MD"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>

                    <!-- Verification Status -->
                    <div class="space-y-1.5">
                        <Label for="rep_status" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                            Initial Verification Status <span class="text-red-600">*</span>
                        </Label>
                        <select
                            id="rep_status"
                            v-model="form.status"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <option value="Verified">Verified Official</option>
                            <option value="Received">Received / Logged</option>
                            <option value="Pending Review">Pending Review</option>
                        </select>
                    </div>
                </div>

                <!-- Blood Type Breakdown -->
                <div class="space-y-1.5">
                    <Label for="rep_breakdown" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Blood Type Breakdown (Optional)
                    </Label>
                    <input 
                        id="rep_breakdown"
                        v-model="form.blood_breakdown"
                        placeholder="e.g. O+: 10, A+: 6, B+: 5, AB+: 3"
                        class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-mono text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    />
                </div>

                <!-- Notes / Remarks -->
                <div class="space-y-1.5">
                    <Label for="rep_notes" class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Summary Notes / Audit Observations
                    </Label>
                    <textarea 
                        id="rep_notes"
                        v-model="form.summary_notes"
                        rows="2"
                        placeholder="e.g. Verified compliance with DOH blood storage guidelines. Safe transfusion parameters achieved..."
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
                        <span>Save & Generate Report</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</template>
