<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Droplets, 
    X, 
    Search, 
    UserPlus, 
    HeartHandshake, 
    CheckCircle2,
    Calendar,
    Phone,
    MapPin,
    Building
} from '@lucide/vue';
import { Spinner } from '@/components/ui/spinner';

export interface BloodDonorRecord {
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
    updated_at?: string;
}

export interface MunicipalityOption {
    id: number;
    province: string;
    name: string;
}

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        donors?: BloodDonorRecord[];
        municipalities?: Record<string, MunicipalityOption[]>;
        initialTab?: 'new' | 'returning';
    }>(),
    {
        isOpen: true,
        donors: () => [],
        municipalities: () => ({}),
        initialTab: 'new',
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created', donor: any): void;
    (e: 'donationAdded', donor: BloodDonorRecord): void;
}>();

// Active Tab: 'new' (First-Time Donor) or 'returning' (Returning Donor)
const activeTab = ref<'new' | 'returning'>(props.initialTab || 'new');

// First-Time Donor Form
const form = useForm({
    full_name: '',
    blood_type: 'O+',
    age: 34,
    gender: 'Male',
    barangay: 'Tubajon',
    municipality: 'Dinagat Islands',
    contact_information: '',
    initial_donation_date: new Date().toISOString().split('T')[0],
    donation_facility: 'Tubajon Health Center',
    total_donations: 1,
});

const bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];

// Returning Donor Search
const returningSearchQuery = ref('');
const isSubmittingReturning = ref<number | null>(null);

const searchResults = computed(() => {
    const q = returningSearchQuery.value.trim().toLowerCase();
    if (!q) return [];
    return props.donors.filter(
        (donor) =>
            donor.full_name.toLowerCase().includes(q) ||
            donor.contact_information.includes(q) ||
            donor.municipality.toLowerCase().includes(q)
    );
});

// Submit New First-Time Donor
const submitNewDonor = () => {
    form.post('/blood-collection', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};

// Add Donation for Returning Donor
const addReturningDonation = (donor: BloodDonorRecord) => {
    isSubmittingReturning.value = donor.id;
    router.post(
        `/blood-collection/${donor.id}/record-donation`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isSubmittingReturning.value = null;
                emit('donationAdded', donor);
                emit('close');
            },
            onError: () => {
                isSubmittingReturning.value = null;
            },
        },
    );
};

const handleClose = () => {
    form.reset();
    form.clearErrors();
    returningSearchQuery.value = '';
    emit('close');
};
</script>

<template>
    <div 
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-2xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4">
                <div class="flex items-start gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 shrink-0">
                        <Droplets class="size-5.5" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white leading-tight">
                            {{ activeTab === 'new' ? 'Register First-Time Donor' : 'Record Returning Donor Donation' }}
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ activeTab === 'new' ? 'Record donor profile and initial donation details.' : 'Search an existing donor to add a new donation to their profile.' }}
                        </p>
                    </div>
                </div>

                <button 
                    type="button"
                    @click="handleClose"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 cursor-pointer dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
                >
                    <X class="size-5" />
                </button>
            </div>

            <!-- Segmented Tab Toggle matching image -->
            <div class="my-4 rounded-xl bg-slate-100/90 p-1 flex items-center gap-1 dark:bg-neutral-800">
                <button
                    type="button"
                    @click="activeTab = 'new'"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-center cursor-pointer"
                    :class="[
                        activeTab === 'new'
                            ? 'bg-white text-[#a32222] shadow-xs dark:bg-neutral-700 dark:text-red-400'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200',
                    ]"
                >
                    New / First-Time Donor
                </button>
                <button
                    type="button"
                    @click="activeTab = 'returning'"
                    class="flex-1 py-2.5 px-4 rounded-lg text-sm font-bold transition-all text-center cursor-pointer"
                    :class="[
                        activeTab === 'returning'
                            ? 'bg-white text-[#a32222] shadow-xs dark:bg-neutral-700 dark:text-red-400'
                            : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200',
                    ]"
                >
                    Returning Donor
                </button>
            </div>

            <!-- TAB 1: NEW / FIRST-TIME DONOR FORM -->
            <form v-if="activeTab === 'new'" @submit.prevent="submitNewDonor" class="space-y-4 pt-2">
                
                <!-- Full Name -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        Full Name
                    </label>
                    <input 
                        v-model="form.full_name"
                        type="text"
                        placeholder="e.g. Juan Dela Cruz"
                        required
                        class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                    />
                    <p v-if="form.errors.full_name" class="text-xs text-red-600">{{ form.errors.full_name }}</p>
                </div>

                <!-- Blood Type & Age -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Blood Type
                        </label>
                        <select
                            v-model="form.blood_type"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <option value="" disabled>Select Blood Type</option>
                            <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                        </select>
                        <p v-if="form.errors.blood_type" class="text-xs text-red-600">{{ form.errors.blood_type }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Age
                        </label>
                        <input 
                            v-model="form.age"
                            type="number"
                            min="16"
                            max="80"
                            placeholder="e.g. 34"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="form.errors.age" class="text-xs text-red-600">{{ form.errors.age }}</p>
                    </div>
                </div>

                <!-- Gender & Contact Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Gender
                        </label>
                        <select
                            v-model="form.gender"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        >
                            <option value="" disabled>Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                        <p v-if="form.errors.gender" class="text-xs text-red-600">{{ form.errors.gender }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Contact Information
                        </label>
                        <input 
                            v-model="form.contact_information"
                            type="text"
                            placeholder="e.g. 0918-765-4321"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="form.errors.contact_information" class="text-xs text-red-600">{{ form.errors.contact_information }}</p>
                    </div>
                </div>

                <!-- Barangay & Municipality / Location -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Barangay
                        </label>
                        <input 
                            v-model="form.barangay"
                            type="text"
                            placeholder="Tubajon"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="form.errors.barangay" class="text-xs text-red-600">{{ form.errors.barangay }}</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Municipality / Location
                        </label>
                        <input 
                            v-model="form.municipality"
                            type="text"
                            placeholder="Dinagat Islands"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                        <p v-if="form.errors.municipality" class="text-xs text-red-600">{{ form.errors.municipality }}</p>
                    </div>
                </div>

                <!-- Initial Donation Date & Donation Facility -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Initial Donation Date
                        </label>
                        <input 
                            v-model="form.initial_donation_date"
                            type="date"
                            required
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Donation Facility
                        </label>
                        <input 
                            v-model="form.donation_facility"
                            type="text"
                            placeholder="Tubajon Health Center"
                            class="w-full h-11 px-3.5 rounded-xl border border-slate-200 bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:border-red-500 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>
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
                        <span>Save Donor</span>
                    </button>
                </div>

            </form>

            <!-- TAB 2: RETURNING DONOR SEARCH & LOG -->
            <div v-else class="space-y-4 pt-2">
                
                <!-- Search Input -->
                <div class="relative">
                    <input
                        v-model="returningSearchQuery"
                        type="text"
                        placeholder="Search existing donor by name (e.g. Juan)..."
                        class="w-full h-12 px-4 rounded-xl border border-[#dc2626] bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-red-500/20 dark:border-red-800 dark:bg-neutral-800 dark:text-neutral-100"
                    />
                </div>

                <!-- Initial State (no search query) -->
                <div 
                    v-if="!returningSearchQuery.trim()" 
                    class="rounded-xl border border-slate-200/80 bg-slate-50/50 p-6 text-center dark:border-neutral-800 dark:bg-neutral-800/40"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        Type the donor's name above to search their profile and add a new donation record.
                    </p>
                </div>

                <!-- Results List -->
                <div v-else class="space-y-3">
                    <div 
                        v-for="donor in searchResults" 
                        :key="donor.id"
                        class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs flex items-center justify-between gap-4 dark:border-neutral-800 dark:bg-neutral-800 hover:border-red-200 transition-colors"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-[#a32222] dark:text-red-400">
                                    {{ donor.full_name }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900">
                                    {{ donor.blood_type }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                {{ donor.age }} yrs • {{ donor.gender }} • {{ donor.municipality || 'Dinagat Islands' }} • <strong class="text-red-600">{{ donor.total_donations }}</strong> previous donations
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="addReturningDonation(donor)"
                            :disabled="isSubmittingReturning === donor.id"
                            class="px-4 py-2 rounded-lg bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer disabled:opacity-50 shrink-0"
                        >
                            <Spinner v-if="isSubmittingReturning === donor.id" class="size-3.5" />
                            <span>Add Donation</span>
                        </button>
                    </div>

                    <div v-if="searchResults.length === 0" class="py-8 text-center text-sm text-slate-400">
                        No registered donor found matching "{{ returningSearchQuery }}".
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-end pt-4 border-t border-slate-100 dark:border-neutral-800">
                    <button 
                        type="button" 
                        @click="handleClose"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-700 hover:bg-slate-50 cursor-pointer dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        Close
                    </button>
                </div>

            </div>

        </div>
    </div>
</template>
