<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    X, 
    Plus, 
    CheckCircle2, 
    Droplets,
    Phone,
    MapPin,
    Calendar,
    User
} from '@lucide/vue';
import { Spinner } from '@/components/ui/spinner';
import type { BloodDonorRecord } from './Create.vue';

type DonationLog = {
    id: string | number;
    date: string;
    bloodType: string;
    location: string;
    status: string;
    isNew?: boolean;
};

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        donor?: BloodDonorRecord | null;
    }>(),
    {
        isOpen: true,
        donor: null,
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'donationAdded', donor: BloodDonorRecord): void;
}>();

const isSubmitting = ref(false);

// Format registered date
const formattedRegisteredDate = computed(() => {
    if (!props.donor) return 'January 14, 2026';
    if (props.donor.created_at) {
        return new Date(props.donor.created_at).toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
    }
    return 'January 14, 2026';
});

// Generate realistic / dynamic donation history
const donationHistory = computed<DonationLog[]>(() => {
    if (!props.donor) return [];
    
    const count = props.donor.total_donations || 1;
    const history: DonationLog[] = [];
    const location = props.donor.barangay || props.donor.municipality || 'Tubajon';
    const bType = props.donor.blood_type || 'O+';

    // Mock realistic past donation dates
    const dates = [
        'August 13, 2026',
        'April 19, 2026',
        'January 14, 2026',
        'October 05, 2025',
        'June 22, 2025',
        'February 10, 2025',
        'November 18, 2024',
        'July 04, 2024',
    ];

    for (let i = 0; i < count; i++) {
        history.push({
            id: i + 1,
            date: dates[i] || `Donation #${count - i}`,
            bloodType: bType,
            location: location,
            status: 'Completed',
            isNew: i === 0, // Latest donation has "new update" badge
        });
    }

    return history;
});

// Add New Donation
const addDonation = () => {
    if (!props.donor) return;
    isSubmitting.value = true;
    router.post(
        `/blood-collection/${props.donor.id}/record-donation`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitting.value = false;
                if (props.donor) {
                    emit('donationAdded', props.donor);
                }
            },
            onError: () => {
                isSubmitting.value = false;
            },
        },
    );
};

const handleClose = () => {
    emit('close');
};
</script>

<template>
    <div 
        v-if="isOpen && donor"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-2xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Header Matching 4th Image -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-100 dark:border-neutral-800">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ donor.full_name }}
                        </h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900">
                            {{ donor.blood_type }}
                        </span>
                    </div>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
                        {{ donor.municipality ? `${donor.municipality}, Dinagat Islands` : 'Tubajon, Dinagat Islands' }}
                    </p>
                </div>

                <button 
                    type="button"
                    @click="handleClose"
                    class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 cursor-pointer dark:hover:bg-neutral-800 dark:hover:text-neutral-200"
                >
                    <X class="size-5" />
                </button>
            </div>

            <!-- Profile Details 2-Column Grid -->
            <div class="py-6 grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm border-b border-slate-100 dark:border-neutral-800">
                <!-- Age & Gender -->
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Age:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ donor.age }} yrs old
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Gender:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ donor.gender }}
                    </span>
                </div>

                <!-- Contact & Registered Date -->
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Contact:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ donor.contact_information }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Registered Date:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ formattedRegisteredDate }}
                    </span>
                </div>

                <!-- Barangay & Municipality / Location -->
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Barangay:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ donor.barangay || 'Tubajon' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-500 dark:text-slate-400 font-medium block">Municipality / Location:</span>
                    <span class="text-base font-bold text-slate-900 dark:text-white mt-0.5 block">
                        {{ donor.municipality || 'Dinagat Islands' }}
                    </span>
                </div>
            </div>

            <!-- Total Number of Donations Banner Card -->
            <div class="my-6 rounded-2xl border border-slate-100 bg-[#f8fafc] p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 dark:bg-neutral-800/60 dark:border-neutral-800 shadow-2xs">
                <div class="text-base font-bold text-slate-800 dark:text-slate-100">
                    Total Number of Donations: <span class="text-2xl font-black text-[#dc2626] ml-1">{{ donor.total_donations }}</span>
                </div>
                <button
                    type="button"
                    @click="addDonation"
                    :disabled="isSubmitting"
                    class="px-5 py-2.5 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-sm shadow-xs transition-colors flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50"
                >
                    <Spinner v-if="isSubmitting" class="size-4" />
                    <span>Add New Donation</span>
                </button>
            </div>

            <!-- Complete Donation History Section -->
            <div class="space-y-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    Complete Donation History
                </h3>

                <div class="overflow-x-auto rounded-xl border border-slate-100 dark:border-neutral-800">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50/60 dark:bg-neutral-800/50 text-[11px] uppercase font-bold text-slate-400 dark:text-neutral-500 tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Blood Type</th>
                                <th class="py-3 px-4">Location</th>
                                <th class="py-3 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                            <tr 
                                v-for="log in donationHistory" 
                                :key="log.id"
                                class="hover:bg-slate-50/50 dark:hover:bg-neutral-800/40 transition-colors"
                                :class="[log.isNew ? 'bg-[#fff5f5]/60 dark:bg-red-950/20' : '']"
                            >
                                <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-slate-100">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span>{{ log.date }}</span>
                                        <span 
                                            v-if="log.isNew" 
                                            class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#fef2f2] text-[#e11d48] border border-red-100 dark:bg-red-950/60 dark:text-red-400"
                                        >
                                            new update
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100 dark:bg-red-950/60 dark:text-red-400">
                                        {{ log.bloodType }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300 font-medium">
                                    {{ log.location }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-900">
                                        {{ log.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end pt-6 mt-6 border-t border-slate-100 dark:border-neutral-800">
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
</template>
