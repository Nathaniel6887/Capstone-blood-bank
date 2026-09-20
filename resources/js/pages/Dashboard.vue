<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Users, 
    CheckCircle2, 
    Hourglass, 
    Ban, 
    UserPlus,
    Calendar,
    Search,
    X,
    Phone,
    MapPin,
    Droplet,
    CalendarCheck
} from '@lucide/vue';
import { dashboard } from '@/routes';

type DonorItem = {
    id: string | number;
    name: string;
    phone: string;
    bloodType: string;
    age: number;
    gender: string;
    location: string;
    status: string;
    lastDonation: string;
    totalDonations?: number;
};

const props = defineProps<{
    totalMunicipalities?: number;
    totalDonors?: number;
    totalDonations?: number;
    registeredDonorsList?: any[];
    municipalitiesByProvince?: Record<string, any[]>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Overview',
                href: dashboard(),
            },
        ],
    },
});

// Default Mock donors matching screenshot
const defaultDonors: DonorItem[] = [
    {
        id: 'CRH-DN-101',
        name: 'Juan Dela Cruz',
        phone: '0918-765-4321',
        bloodType: 'O+',
        age: 34,
        gender: 'Male',
        location: 'Tubajon, Dinagat Islands',
        status: 'Active',
        lastDonation: 'Aug 13, 2026',
        totalDonations: 8,
    },
    {
        id: 'CRH-DN-102',
        name: 'Maria Santos',
        phone: '0917-123-4567',
        bloodType: 'A+',
        age: 23,
        gender: 'Female',
        location: 'San Jose, Dinagat Islands',
        status: 'Active',
        lastDonation: 'May 17, 2026',
        totalDonations: 4,
    },
    {
        id: 'CRH-DN-103',
        name: 'Pedro Garcia',
        phone: '0920-555-4421',
        bloodType: 'B+',
        age: 42,
        gender: 'Male',
        location: 'Basilisa, Dinagat Islands',
        status: 'Active',
        lastDonation: 'Jun 14, 2026',
        totalDonations: 11,
    },
    {
        id: 'CRH-DN-104',
        name: 'Ana Patricia Ramos',
        phone: '0995-123-8890',
        bloodType: 'AB+',
        age: 25,
        gender: 'Female',
        location: 'Cagdianao, Dinagat Islands',
        status: 'Active',
        lastDonation: 'Jul 02, 2026',
        totalDonations: 3,
    },
    {
        id: 'CRH-DN-105',
        name: 'Roberto V. Mendoza',
        phone: '0908-554-1290',
        bloodType: 'O+',
        age: 41,
        gender: 'Male',
        location: 'Loreto, Dinagat Islands',
        status: 'Active',
        lastDonation: 'Aug 01, 2026',
        totalDonations: 14,
    },
    {
        id: 'CRH-DN-106',
        name: 'Kristine Joy Alcantara',
        phone: '0919-456-2211',
        bloodType: 'A+',
        age: 31,
        gender: 'Female',
        location: 'Libjo, Dinagat Islands',
        status: 'Active',
        lastDonation: 'Aug 29, 2026',
        totalDonations: 6,
    },
    {
        id: 'CRH-DN-107',
        name: 'Gabriel R. Fernandez',
        phone: '0947-890-3341',
        bloodType: 'O-',
        age: 39,
        gender: 'Male',
        location: 'Dinagat, Dinagat Islands',
        status: 'Active',
        lastDonation: 'May 18, 2026',
        totalDonations: 9,
    },
];

// Map backend donors if available or fallback to defaultDonors
const allDonors = computed<DonorItem[]>(() => {
    if (props.registeredDonorsList && props.registeredDonorsList.length > 0) {
        return props.registeredDonorsList.map((d: any) => ({
            id: d.id,
            name: d.full_name || d.name,
            phone: d.contact_information || d.phone || '0917-000-0000',
            bloodType: d.blood_type || d.bloodType || 'O+',
            age: d.age || 30,
            gender: d.gender || 'Male',
            location: d.municipality ? `${d.municipality}, Dinagat Islands` : (d.location || 'San Jose, Dinagat Islands'),
            status: d.status || 'Active',
            lastDonation: d.lastDonation || (d.updated_at ? new Date(d.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Aug 13, 2026'),
            totalDonations: d.total_donations || d.totalDonations || 1,
        }));
    }
    return defaultDonors;
});

// Search filter
const searchQuery = ref('');
const filteredDonors = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return allDonors.value;
    return allDonors.value.filter(
        (donor) =>
            donor.name.toLowerCase().includes(q) ||
            donor.bloodType.toLowerCase().includes(q) ||
            donor.location.toLowerCase().includes(q) ||
            donor.phone.includes(q)
    );
});

// Donor Avatar Initials & Color Palette
const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
};

const getAvatarColor = (name: string) => {
    const colors = [
        'bg-[#dbeafe] text-[#1d4ed8]', // blue
        'bg-[#d1fae5] text-[#059669]', // emerald
        'bg-[#fef3c7] text-[#d97706]', // amber
        'bg-[#fce7f3] text-[#be185d]', // pink
        'bg-[#ede9fe] text-[#7c3aed]', // purple
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) {
        hash = name.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

// Details Modal
const selectedDonor = ref<DonorItem | null>(null);
const isDetailsOpen = ref(false);

const openDetails = (donor: DonorItem) => {
    selectedDonor.value = donor;
    isDetailsOpen.value = true;
};

const closeDetails = () => {
    isDetailsOpen.value = false;
    selectedDonor.value = null;
};
</script>

<template>
    <Head title="Overview - Blood Bank Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8 bg-[#f8fafc] dark:bg-neutral-950 min-h-screen">
        
        <!-- 4 Stat Summary Cards Matching the Exact Screenshot -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Card 1: Registered Donors -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-[5px] border-l-[#2563eb] dark:bg-card dark:border-neutral-800 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        REGISTERED DONORS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-[#edf4ff] text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-400">
                        <Users class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ props.totalDonors || 12 }}
                    </div>
                    <div class="mt-1 text-xs font-semibold text-[#2563eb] dark:text-blue-400">
                        Active donor registry
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Donations -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-[5px] border-l-[#10b981] dark:bg-card dark:border-neutral-800 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        TOTAL DONATIONS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-[#ecfdf5] text-[#10b981] dark:bg-emerald-950/60 dark:text-emerald-400">
                        <CheckCircle2 class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ props.totalDonations || 19 }}
                    </div>
                    <div class="mt-1 text-xs font-semibold text-[#10b981] dark:text-emerald-400">
                        Bags collected
                    </div>
                </div>
            </div>

            <!-- Card 3: Active Locations -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-[5px] border-l-[#f59e0b] dark:bg-card dark:border-neutral-800 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        ACTIVE LOCATIONS
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-[#fffbeb] text-[#f59e0b] dark:bg-amber-950/60 dark:text-amber-400">
                        <Hourglass class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ props.totalMunicipalities || 7 }}
                        </span>
                        <span class="text-2xl font-bold text-[#1e3a8a] dark:text-blue-300 tracking-tight">
                            Municipalities
                        </span>
                    </div>
                    <div class="mt-1 text-xs font-semibold text-[#d97706] dark:text-amber-400">
                        Dinagat Islands coverage
                    </div>
                </div>
            </div>

            <!-- Card 4: Eligible Today -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-[5px] border-l-[#ef4444] dark:bg-card dark:border-neutral-800 transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        ELIGIBLE TODAY
                    </span>
                    <div class="flex size-9 items-center justify-center rounded-xl bg-[#fef2f2] text-[#ef4444] dark:bg-red-950/60 dark:text-red-400">
                        <Ban class="size-4.5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        100%
                    </div>
                    <div class="mt-1 text-xs font-semibold text-[#e11d48] dark:text-red-400">
                        Verified donor profiles
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Card: Registered Donors Directory -->
        <div class="rounded-2xl border border-slate-100/90 bg-white p-6 shadow-xs dark:bg-card dark:border-neutral-800">
            
            <!-- Table Header Bar: Title, Search, and Register Donor Button -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-6">
                <!-- Title & Subtitle -->
                <div class="flex items-center gap-3">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-[#edf4ff] text-[#2563eb] dark:bg-blue-950/60 dark:text-blue-400 shrink-0">
                        <Calendar class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                            Registered Donors Directory
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Search, review, and register blood donors in the system.
                        </p>
                    </div>
                </div>

                <!-- Right Controls: Search & Register Donor Button -->
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap">
                    <div class="relative w-full sm:w-72">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search donors by name, bloc"
                            class="w-full h-10 rounded-lg border border-slate-200 bg-white px-3.5 text-sm text-slate-800 placeholder:text-slate-400 focus:border-blue-500 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-100"
                        />
                    </div>
                    <Link
                        href="/blood-collection"
                        class="h-10 px-4 rounded-lg bg-[#a32222] hover:bg-[#8b1d1d] text-white text-sm font-bold flex items-center gap-2 shadow-xs transition-colors shrink-0"
                    >
                        <UserPlus class="size-4" />
                        <span>Register Donor</span>
                    </Link>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-neutral-800 text-[11px] font-bold text-slate-400 dark:text-neutral-500 uppercase tracking-wider">
                            <th class="py-3 px-4">DONOR</th>
                            <th class="py-3 px-4">BLOOD TYPE</th>
                            <th class="py-3 px-4">AGE / GENDER</th>
                            <th class="py-3 px-4">LOCATION</th>
                            <th class="py-3 px-4">STATUS</th>
                            <th class="py-3 px-4">LAST DONATION</th>
                            <th class="py-3 px-4 text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-neutral-800">
                        <tr 
                            v-for="donor in filteredDonors" 
                            :key="donor.id"
                            class="hover:bg-slate-50/70 dark:hover:bg-neutral-800/40 transition-colors"
                        >
                            <!-- Donor Column with Avatar, Name, and Phone -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div 
                                        class="flex size-10 items-center justify-center rounded-full font-bold text-xs shrink-0 shadow-2xs"
                                        :class="getAvatarColor(donor.name)"
                                    >
                                        {{ getInitials(donor.name) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                                            {{ donor.name }}
                                        </div>
                                        <div class="text-xs text-slate-400 font-medium">
                                            {{ donor.phone }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Blood Type Badge -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-[#fef2f2] text-[#dc2626] border border-red-100/80 dark:bg-red-950/60 dark:text-red-400 dark:border-red-900">
                                    {{ donor.bloodType }}
                                </span>
                            </td>

                            <!-- Age / Gender -->
                            <td class="py-4 px-4 text-sm font-medium text-slate-600 dark:text-slate-300">
                                {{ donor.age }} yrs • {{ donor.gender }}
                            </td>

                            <!-- Location -->
                            <td class="py-4 px-4 text-sm font-medium text-slate-700 dark:text-slate-200">
                                {{ donor.location }}
                            </td>

                            <!-- Status Pill Badge -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#10b981] text-white shadow-2xs">
                                    {{ donor.status }}
                                </span>
                            </td>

                            <!-- Last Donation Date -->
                            <td class="py-4 px-4 text-sm font-medium text-slate-600 dark:text-slate-300">
                                {{ donor.lastDonation }}
                            </td>

                            <!-- Action View Details Button -->
                            <td class="py-4 px-4 text-center">
                                <button
                                    type="button"
                                    @click="openDetails(donor)"
                                    class="inline-flex items-center justify-center rounded-lg border border-slate-200/90 bg-white px-3.5 py-1.5 text-xs font-bold text-slate-800 shadow-2xs hover:bg-slate-50 transition-colors cursor-pointer dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700"
                                >
                                    View Details
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-if="filteredDonors.length === 0" class="py-12 text-center text-sm text-slate-400">
                    No donors found matching "{{ searchQuery }}".
                </div>
            </div>

        </div>

        <!-- Donor Profile Details Modal -->
        <div 
            v-if="isDetailsOpen && selectedDonor" 
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4"
            @click.self="closeDetails"
        >
            <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl border border-slate-100 dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-neutral-800">
                    <div class="flex items-center gap-3">
                        <div 
                            class="flex size-11 items-center justify-center rounded-full font-bold text-sm"
                            :class="getAvatarColor(selectedDonor.name)"
                        >
                            {{ getInitials(selectedDonor.name) }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ selectedDonor.name }}
                            </h3>
                            <p class="text-xs text-slate-400">Donor Profile #{{ selectedDonor.id }}</p>
                        </div>
                    </div>
                    <button 
                        type="button" 
                        @click="closeDetails" 
                        class="size-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center cursor-pointer dark:hover:bg-neutral-800"
                    >
                        <X class="size-4.5" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-800">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Blood Type</span>
                            <span class="text-lg font-extrabold text-red-600 dark:text-red-400 mt-0.5 block">{{ selectedDonor.bloodType }}</span>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-neutral-800/60 border border-slate-100 dark:border-neutral-800">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Status</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-1 block">{{ selectedDonor.status }}</span>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex items-center gap-2.5 text-slate-700 dark:text-slate-200">
                            <Phone class="size-4 text-slate-400" />
                            <span>{{ selectedDonor.phone }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700 dark:text-slate-200">
                            <MapPin class="size-4 text-slate-400" />
                            <span>{{ selectedDonor.location }}</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700 dark:text-slate-200">
                            <Droplet class="size-4 text-slate-400" />
                            <span>{{ selectedDonor.age }} yrs old • {{ selectedDonor.gender }} ({{ selectedDonor.totalDonations || 0 }} total donations)</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-slate-700 dark:text-slate-200">
                            <CalendarCheck class="size-4 text-slate-400" />
                            <span>Last Donated: {{ selectedDonor.lastDonation }}</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-slate-100 dark:border-neutral-800 flex items-center justify-end gap-2.5">
                    <button
                        type="button"
                        @click="closeDetails"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 cursor-pointer dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    >
                        Close
                    </button>
                    <Link
                        href="/blood-collection"
                        class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-xs font-bold text-white shadow-xs"
                    >
                        Record New Donation
                    </Link>
                </div>
            </div>
        </div>

    </div>
</template>
