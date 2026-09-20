<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Droplets, 
    Plus, 
    Search, 
    CheckCircle2, 
    HeartPulse, 
    Trash2, 
    Edit, 
    PlusCircle, 
    X, 
    UserPlus, 
    Phone, 
    MapPin, 
    Building, 
    Calendar,
    AlertCircle,
    Eye
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
                title: 'Blood Collection',
                href: '/blood-collection',
            },
        ],
    },
});

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
        donors?: BloodDonorRecord[];
        municipalities?: Record<string, MunicipalityOption[]>;
        stats?: {
            totalDonors: number;
            totalDonations: number;
            eligibleDonors: number;
            activeLocations: number;
        };
    }>(),
    {
        donors: () => [],
        municipalities: () => ({}),
        stats: () => ({
            totalDonors: 0,
            totalDonations: 0,
            eligibleDonors: 0,
            activeLocations: 0,
        }),
    },
);

// Search and Filter State
const searchQuery = ref('');
const selectedBloodTypeFilter = ref('All');

// Modal States
const isCreateOpen = ref(false);
const createModalInitialTab = ref<'new' | 'returning'>('new');
const isViewOpen = ref(false);
const selectedDonorForView = ref<BloodDonorRecord | null>(null);

const isEditModalOpen = ref(false);
const editingDonorId = ref<number | null>(null);

// Inertia Form for Edit
const editForm = useForm({
    full_name: '',
    blood_type: 'O+',
    age: 25,
    gender: 'Male',
    barangay: '',
    municipality: 'Buenavista',
    contact_information: '',
    total_donations: 1,
});

const bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];

// Open Modal for New Collection
const openCreateModal = (tab: 'new' | 'returning' = 'new') => {
    createModalInitialTab.value = tab;
    isCreateOpen.value = true;
};

// Open Modal for View
const openViewModal = (donor: BloodDonorRecord) => {
    selectedDonorForView.value = donor;
    isViewOpen.value = true;
};

// Open Modal for Edit
const openEditModal = (donor: BloodDonorRecord) => {
    editingDonorId.value = donor.id;
    editForm.clearErrors();
    editForm.full_name = donor.full_name;
    editForm.blood_type = donor.blood_type;
    editForm.age = donor.age;
    editForm.gender = donor.gender;
    editForm.barangay = donor.barangay;
    editForm.municipality = donor.municipality;
    editForm.contact_information = donor.contact_information;
    editForm.total_donations = donor.total_donations;
    isEditModalOpen.value = true;
};

// Close Edit Modal
const closeEditModal = () => {
    isEditModalOpen.value = false;
    editForm.reset();
};

// Submit Edit Form
const submitEditForm = () => {
    if (editingDonorId.value) {
        editForm.put(`/blood-collection/${editingDonorId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeEditModal();
            },
        });
    }
};

// Record Quick +1 Donation
const incrementDonation = (donor: BloodDonorRecord) => {
    router.post(`/blood-collection/${donor.id}/record-donation`, {}, {
        preserveScroll: true,
    });
};

// Delete Record
const deleteDonor = (donor: BloodDonorRecord) => {
    if (confirm(`Are you sure you want to remove the donor record for "${donor.full_name}"?`)) {
        router.delete(`/blood-collection/${donor.id}`, {
            preserveScroll: true,
        });
    }
};

// Filtered List
const filteredDonors = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    return props.donors.filter((donor) => {
        const matchesQuery =
            donor.full_name.toLowerCase().includes(query) ||
            donor.barangay.toLowerCase().includes(query) ||
            donor.municipality.toLowerCase().includes(query) ||
            donor.contact_information.toLowerCase().includes(query) ||
            donor.blood_type.toLowerCase().includes(query);

        const matchesBlood =
            selectedBloodTypeFilter.value === 'All' ||
            donor.blood_type === selectedBloodTypeFilter.value;

        return matchesQuery && matchesBlood;
    });
});
</script>

<template>
    <Head title="Blood Collection - Caraga Regional Hospital" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-border/70">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-red-100 text-red-700 dark:bg-red-950/50 dark:text-red-400 shadow-xs">
                        <Droplets class="size-5" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Blood Collection Management
                        </h1>
                        <p class="text-xs sm:text-sm text-muted-foreground">
                            Record, screen, and manage blood donor collections across Caraga Region.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2.5 flex-wrap">
                <button
                    type="button"
                    @click="openCreateModal('returning')"
                    class="h-10 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                >
                    <HeartPulse class="size-4 text-red-600" />
                    <span>Returning Donor</span>
                </button>
                <button 
                    type="button"
                    @click="openCreateModal('new')"
                    class="h-10 px-4 rounded-xl bg-[#a32222] hover:bg-[#8b1d1d] text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-2 cursor-pointer"
                >
                    <Plus class="size-4" />
                    <span>New Donor Collection</span>
                </button>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-blue-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Registered Donors</span>
                    <span class="rounded-xl bg-blue-50 p-2 text-blue-600 dark:bg-blue-950/50 dark:text-blue-400">
                        <UserPlus class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalDonors }}
                </div>
                <span class="text-xs font-medium text-blue-600">Active donor registry</span>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-emerald-500 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Donations</span>
                    <span class="rounded-xl bg-emerald-50 p-2 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <Droplets class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalDonations }}
                </div>
                <span class="text-xs font-medium text-emerald-600">Total units collected</span>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-amber-500 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Active Municipalities</span>
                    <span class="rounded-xl bg-amber-50 p-2 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                        <MapPin class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.activeLocations }}
                </div>
                <span class="text-xs font-medium text-amber-600">Collection locations</span>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-rose-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Eligible Age Range</span>
                    <span class="rounded-xl bg-rose-50 p-2 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400">
                        <CheckCircle2 class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.eligibleDonors }}
                </div>
                <span class="text-xs font-medium text-rose-600">18 - 65 years qualified</span>
            </div>
        </div>

        <!-- Main Collection Directory Container -->
        <div class="rounded-2xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            
            <!-- Controls Bar: Search & Blood Filter Tabs -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-3 border-b border-border/70">
                <div class="relative w-full md:w-80">
                    <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        placeholder="Search full name, barangay, location..." 
                        class="pl-9 h-9.5 text-xs rounded-xl"
                    />
                </div>

                <!-- Blood Type Filter Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-muted-foreground font-semibold mr-1">Filter:</span>
                    <button
                        v-for="bType in ['All', ...bloodTypes]"
                        :key="bType"
                        @click="selectedBloodTypeFilter = bType"
                        :class="[
                            'px-2.5 py-1 rounded-lg font-semibold transition-all cursor-pointer text-xs',
                            selectedBloodTypeFilter === bType
                                ? 'bg-red-700 text-white shadow-xs'
                                : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                        ]"
                    >
                        {{ bType }}
                    </button>
                </div>
            </div>

            <!-- Blood Donors Collection Table Matching Required Format -->
            <div class="overflow-x-auto rounded-xl border border-border/80">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50 text-[11px] uppercase font-bold text-muted-foreground tracking-wider border-b border-border">
                        <tr>
                            <th class="px-4 py-3.5">Full Name</th>
                            <th class="px-4 py-3.5">Blood Type</th>
                            <th class="px-4 py-3.5">Age</th>
                            <th class="px-4 py-3.5">Gender</th>
                            <th class="px-4 py-3.5">Barangay</th>
                            <th class="px-4 py-3.5">Municipality / Location</th>
                            <th class="px-4 py-3.5">Contact Information</th>
                            <th class="px-4 py-3.5 text-center">Total Donations</th>
                            <th class="px-4 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr 
                            v-for="donor in filteredDonors" 
                            :key="donor.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Full Name -->
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-gray-900 dark:text-white">
                                    {{ donor.full_name }}
                                </div>
                                <div class="text-[11px] text-muted-foreground font-mono">
                                    CRH-DONOR-#{{ donor.id }}
                                </div>
                            </td>

                            <!-- Blood Type -->
                            <td class="px-4 py-3.5">
                                <span 
                                    :class="[
                                        'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-black shadow-2xs',
                                        donor.blood_type.includes('O') ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' :
                                        donor.blood_type.includes('A') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                        donor.blood_type.includes('B') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                        'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300'
                                    ]"
                                >
                                    {{ donor.blood_type }}
                                </span>
                            </td>

                            <!-- Age -->
                            <td class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300">
                                {{ donor.age }} yrs
                            </td>

                            <!-- Gender -->
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">
                                {{ donor.gender }}
                            </td>

                            <!-- Barangay -->
                            <td class="px-4 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                                {{ donor.barangay }}
                            </td>

                            <!-- Municipality / Location -->
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1 font-semibold text-red-900 dark:text-red-300">
                                    <MapPin class="size-3 text-red-600 shrink-0" />
                                    {{ donor.municipality }}
                                </span>
                            </td>

                            <!-- Contact Information -->
                            <td class="px-4 py-3.5 font-mono text-xs text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1.5">
                                    <Phone class="size-3 text-gray-400" />
                                    {{ donor.contact_information }}
                                </span>
                            </td>

                            <!-- Total Donations -->
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center rounded-full bg-red-50 text-red-700 dark:bg-red-950/70 dark:text-red-300 px-3 py-1 font-bold text-xs">
                                    {{ donor.total_donations }} {{ donor.total_donations === 1 ? 'Bag' : 'Bags' }}
                                </span>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Details button -->
                                    <button 
                                        type="button" 
                                        @click="openViewModal(donor)"
                                        title="View complete donor profile and donation history"
                                        class="h-8 px-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold gap-1 inline-flex items-center cursor-pointer shadow-2xs dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                                    >
                                        <Eye class="size-3.5 text-slate-500" />
                                        <span>View</span>
                                    </button>

                                    <!-- Record +1 donation button -->
                                    <Button 
                                        size="sm" 
                                        variant="outline" 
                                        @click="incrementDonation(donor)"
                                        title="Record new donation bag for this donor"
                                        class="h-8 px-2.5 text-xs text-emerald-700 hover:bg-emerald-50 hover:text-emerald-800 dark:hover:bg-emerald-950/40 gap-1 cursor-pointer"
                                    >
                                        <PlusCircle class="size-3.5" />
                                        <span>+1 Donation</span>
                                    </Button>

                                    <!-- Edit button -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="openEditModal(donor)"
                                        title="Edit donor details"
                                        class="size-8 p-0 text-muted-foreground hover:text-blue-700 cursor-pointer"
                                    >
                                        <Edit class="size-4" />
                                    </Button>

                                    <!-- Delete button -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="deleteDonor(donor)"
                                        title="Delete record"
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
                v-if="filteredDonors.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-xl border border-dashed border-border bg-muted/10"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-950/60 dark:text-red-400 mb-3">
                    <Droplets class="size-7" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    {{ searchQuery ? 'No matching donor records' : 'No Donor Collections Recorded Yet' }}
                </h3>
                <p class="text-xs sm:text-sm text-muted-foreground max-w-sm mt-1">
                    {{ searchQuery ? 'Try adjusting your search filters or check for spelling errors.' : 'Begin by clicking "New Donor Collection" to log blood donations across Caraga Region.' }}
                </p>
                <Button 
                    v-if="!searchQuery"
                    @click="openCreateModal('new')"
                    class="mt-4 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-xl text-xs gap-1.5 cursor-pointer"
                >
                    <Plus class="size-4" />
                    <span>Record First Donor Collection</span>
                </Button>
            </div>

        </div>

        <!-- CREATE MODAL COMPONENT (First-Time & Returning Donor Tabs) -->
        <CreateModal 
            :is-open="isCreateOpen"
            :donors="props.donors"
            :municipalities="props.municipalities"
            :initial-tab="createModalInitialTab"
            @close="isCreateOpen = false"
        />

        <!-- VIEW MODAL COMPONENT (Donor Profile & Complete Donation History) -->
        <ViewModal 
            :is-open="isViewOpen"
            :donor="selectedDonorForView"
            @close="isViewOpen = false"
        />

        <!-- EDIT DONOR MODAL DIALOG -->
        <div 
            v-if="isEditModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-2xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-border">
                    <div class="flex items-center gap-2.5">
                        <div class="flex size-9 items-center justify-center rounded-xl bg-blue-100 text-blue-700 dark:bg-blue-950 dark:text-blue-400">
                            <Edit class="size-5" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                                Edit Donor Collection
                            </h2>
                            <p class="text-xs text-muted-foreground">
                                Update donor details and collection records
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="closeEditModal"
                        class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground cursor-pointer"
                    >
                        <X class="size-5" />
                    </button>
                </div>

                <!-- Form -->
                <form @submit.prevent="submitEditForm" class="space-y-4 pt-4">
                    
                    <!-- Full Name -->
                    <div class="space-y-1.5">
                        <Label for="edit_full_name" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Full Name <span class="text-red-600">*</span>
                        </Label>
                        <Input 
                            id="edit_full_name"
                            v-model="editForm.full_name"
                            placeholder="e.g. Juan C. Dela Cruz"
                            required
                            class="h-10 text-sm rounded-xl"
                        />
                        <div v-if="editForm.errors.full_name" class="text-xs text-red-600">{{ editForm.errors.full_name }}</div>
                    </div>

                    <!-- Blood Type & Gender -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="edit_blood_type" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Blood Type <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="edit_blood_type"
                                v-model="editForm.blood_type"
                                class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-red-600/20 focus:border-red-600 outline-none"
                            >
                                <option v-for="type in bloodTypes" :key="type" :value="type">{{ type }}</option>
                            </select>
                            <div v-if="editForm.errors.blood_type" class="text-xs text-red-600">{{ editForm.errors.blood_type }}</div>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="edit_gender" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Gender <span class="text-red-600">*</span>
                            </Label>
                            <select
                                id="edit_gender"
                                v-model="editForm.gender"
                                class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:ring-2 focus:ring-red-600/20 focus:border-red-600 outline-none"
                            >
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div v-if="editForm.errors.gender" class="text-xs text-red-600">{{ editForm.errors.gender }}</div>
                        </div>
                    </div>

                    <!-- Age & Total Donations -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <Label for="edit_age" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Age (16-80) <span class="text-red-600">*</span>
                            </Label>
                            <Input 
                                id="edit_age"
                                type="number"
                                v-model="editForm.age"
                                min="16"
                                max="80"
                                required
                                class="h-10 text-sm rounded-xl"
                            />
                            <div v-if="editForm.errors.age" class="text-xs text-red-600">{{ editForm.errors.age }}</div>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="edit_total_donations" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Total Donations <span class="text-red-600">*</span>
                            </Label>
                            <Input 
                                id="edit_total_donations"
                                type="number"
                                v-model="editForm.total_donations"
                                min="1"
                                required
                                class="h-10 text-sm rounded-xl"
                            />
                            <div v-if="editForm.errors.total_donations" class="text-xs text-red-600">{{ editForm.errors.total_donations }}</div>
                        </div>
                    </div>

                    <!-- Barangay -->
                    <div class="space-y-1.5">
                        <Label for="edit_barangay" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Barangay <span class="text-red-600">*</span>
                        </Label>
                        <Input 
                            id="edit_barangay"
                            v-model="editForm.barangay"
                            placeholder="e.g. Poblacion 1"
                            required
                            class="h-10 text-sm rounded-xl"
                        />
                        <div v-if="editForm.errors.barangay" class="text-xs text-red-600">{{ editForm.errors.barangay }}</div>
                    </div>

                    <!-- Municipality / Location Dropdown -->
                    <div class="space-y-1.5">
                        <Label for="edit_municipality" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Municipality / Location <span class="text-red-600">*</span>
                        </Label>
                        <select
                            id="edit_municipality"
                            v-model="editForm.municipality"
                            required
                            class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:ring-2 focus:ring-red-600/20 focus:border-red-600 outline-none"
                        >
                            <optgroup 
                                v-for="(municipalityList, provinceName) in props.municipalities" 
                                :key="provinceName"
                                :label="provinceName"
                            >
                                <option 
                                    v-for="item in municipalityList" 
                                    :key="item.name" 
                                    :value="item.name"
                                >
                                    {{ item.name }} ({{ provinceName }})
                                </option>
                            </optgroup>
                            <option v-if="Object.keys(props.municipalities).length === 0" value="Buenavista">Buenavista</option>
                            <option v-if="Object.keys(props.municipalities).length === 0" value="San Jose">San Jose</option>
                            <option v-if="Object.keys(props.municipalities).length === 0" value="Prosperidad">Prosperidad</option>
                            <option v-if="Object.keys(props.municipalities).length === 0" value="Cantilan">Cantilan</option>
                        </select>
                        <div v-if="editForm.errors.municipality" class="text-xs text-red-600">{{ editForm.errors.municipality }}</div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-1.5">
                        <Label for="edit_contact_information" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Contact Information <span class="text-red-600">*</span>
                        </Label>
                        <Input 
                            id="edit_contact_information"
                            v-model="editForm.contact_information"
                            placeholder="e.g. 0912-345-6789 / juan@crh.gov.ph"
                            required
                            class="h-10 text-sm rounded-xl"
                        />
                        <div v-if="editForm.errors.contact_information" class="text-xs text-red-600">{{ editForm.errors.contact_information }}</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-border">
                        <Button 
                            type="button" 
                            variant="outline" 
                            @click="closeEditModal"
                            class="h-10 rounded-xl"
                        >
                            Cancel
                        </Button>
                        <Button 
                            type="submit" 
                            :disabled="editForm.processing"
                            class="h-10 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-semibold px-5"
                        >
                            <Spinner v-if="editForm.processing" class="mr-2" />
                            <span>Update Collection Record</span>
                        </Button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</template>

