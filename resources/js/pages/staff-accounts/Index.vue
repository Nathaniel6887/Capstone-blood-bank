<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Users, 
    UserPlus, 
    Search, 
    ShieldCheck, 
    Mail, 
    Key, 
    Phone, 
    Building, 
    Edit, 
    Trash2, 
    X, 
    Check, 
    Copy, 
    RefreshCw, 
    UserCheck, 
    ShieldAlert, 
    Clock, 
    Sparkles,
    HeartPulse,
    Droplets,
    Stethoscope
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Staff Accounts',
                href: '/staff-accounts',
            },
        ],
    },
});

export interface UserRecord {
    id: number;
    name: string;
    email: string;
    role: string;
    employee_id: string;
    department: string;
    phone_number?: string;
    status: 'Active' | 'Inactive' | 'On Leave' | string;
    email_verified_at?: string;
    created_at?: string;
    updated_at?: string;
}

export interface StaffStats {
    totalStaff: number;
    totalAdmins: number;
    totalMedTechs: number;
    activeStaff: number;
    onLeaveStaff: number;
}

const props = withDefaults(
    defineProps<{
        users?: UserRecord[];
        stats?: StaffStats;
        rolesList?: string[];
        departmentsList?: string[];
        nextEmployeeId?: string;
        currentUserId?: number;
    }>(),
    {
        users: () => [],
        stats: () => ({
            totalStaff: 0,
            totalAdmins: 0,
            totalMedTechs: 0,
            activeStaff: 0,
            onLeaveStaff: 0,
        }),
        rolesList: () => [
            'System Administrator',
            'Registered Medical Technologist (RMT)',
            'Laboratory Supervisor',
            'Blood Bank Officer',
            'Phlebotomist / Donor Care',
            'Quality Assurance Officer',
            'Transfusion Safety Nurse',
        ],
        departmentsList: () => [
            'Blood Bank & Transfusion Medicine',
            'Laboratory & Clinical Pathology',
            'Donor Recruitment & Mobile Collection',
            'Hematology & Immunohematology Section',
            'Quality Assurance & Component Processing',
        ],
        nextEmployeeId: 'CRH-EMP-001',
        currentUserId: 0,
    },
);

// Search & Filter States
const searchQuery = ref('');
const selectedRoleFilter = ref('All');
const selectedStatusFilter = ref('All');
const selectedDepartmentFilter = ref('All');

// Modal States
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingUserId = ref<number | null>(null);
const copiedEmployeeId = ref<string | null>(null);

// Form
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'Registered Medical Technologist (RMT)',
    employee_id: '',
    department: 'Blood Bank & Transfusion Medicine',
    phone_number: '',
    status: 'Active',
});

// Helper: Get Initials
const getInitials = (name: string) => {
    if (!name) return 'ST';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
};

// Helper: Role Badge Color
const getRoleBadgeClasses = (role: string) => {
    if (role.includes('Administrator')) {
        return 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 border-purple-200 dark:border-purple-900/50';
    }
    if (role.includes('Medical Technologist') || role.includes('RMT')) {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border-blue-200 dark:border-blue-900/50';
    }
    if (role.includes('Supervisor')) {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border-amber-200 dark:border-amber-900/50';
    }
    if (role.includes('Blood Bank')) {
        return 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border-rose-200 dark:border-rose-900/50';
    }
    return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50';
};

// Open Create Modal
const openCreateModal = () => {
    isEditing.value = false;
    editingUserId.value = null;
    form.reset();
    form.clearErrors();
    form.employee_id = props.nextEmployeeId;
    form.role = props.rolesList[1] || 'Registered Medical Technologist (RMT)';
    form.department = props.departmentsList[0] || 'Blood Bank & Transfusion Medicine';
    form.status = 'Active';
    isModalOpen.value = true;
};

// Open Edit Modal
const openEditModal = (user: UserRecord) => {
    isEditing.value = true;
    editingUserId.value = user.id;
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.employee_id = user.employee_id;
    form.role = user.role;
    form.department = user.department;
    form.phone_number = user.phone_number || '';
    form.status = user.status;
    form.password = '';
    form.password_confirmation = '';
    isModalOpen.value = true;
};

// Close Modal
const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    editingUserId.value = null;
    isEditing.value = false;
};

// Submit Form
const submitForm = () => {
    if (isEditing.value && editingUserId.value) {
        form.put(`/staff-accounts/${editingUserId.value}`, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    } else {
        form.post('/staff-accounts', {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    }
};

// Quick Status Update
const updateStatus = (user: UserRecord, newStatus: string) => {
    router.patch(
        `/staff-accounts/${user.id}/status`,
        { status: newStatus },
        {
            preserveScroll: true,
        },
    );
};

// Delete Staff
const deleteStaff = (user: UserRecord) => {
    if (user.id === props.currentUserId) {
        alert('You cannot delete your own logged-in administrator account.');
        return;
    }

    if (confirm(`Are you sure you want to remove the staff account for "${user.name}" (${user.email})?`)) {
        router.delete(`/staff-accounts/${user.id}`, {
            preserveScroll: true,
        });
    }
};

// Copy Employee ID
const copyEmployeeId = (empId: string) => {
    if (!empId) return;
    navigator.clipboard.writeText(empId);
    copiedEmployeeId.value = empId;
    setTimeout(() => {
        if (copiedEmployeeId.value === empId) {
            copiedEmployeeId.value = null;
        }
    }, 2000);
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

// Filtered Users computed
const filteredUsers = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    return props.users.filter((user) => {
        const matchesQuery =
            !q ||
            user.name.toLowerCase().includes(q) ||
            user.email.toLowerCase().includes(q) ||
            (user.employee_id && user.employee_id.toLowerCase().includes(q)) ||
            (user.department && user.department.toLowerCase().includes(q)) ||
            (user.role && user.role.toLowerCase().includes(q)) ||
            (user.phone_number && user.phone_number.toLowerCase().includes(q));

        const matchesRole =
            selectedRoleFilter.value === 'All' ||
            user.role === selectedRoleFilter.value;

        const matchesStatus =
            selectedStatusFilter.value === 'All' ||
            user.status === selectedStatusFilter.value;

        const matchesDepartment =
            selectedDepartmentFilter.value === 'All' ||
            user.department === selectedDepartmentFilter.value;

        return matchesQuery && matchesRole && matchesStatus && matchesDepartment;
    });
});
</script>

<template>
    <Head title="Staff Accounts - Caraga Regional Hospital" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
        
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pb-2 border-b border-border/70">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex size-10 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 shadow-xs">
                        <Users class="size-5" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                            <span>Staff Accounts Management</span>
                            <span class="inline-flex items-center rounded-full bg-purple-50 dark:bg-purple-950/70 border border-purple-200 dark:border-purple-900/50 px-2.5 py-0.5 text-xs font-semibold text-purple-700 dark:text-purple-400">
                                {{ props.users.length }} Personnel Registered
                            </span>
                        </h1>
                        <p class="text-xs sm:text-sm text-muted-foreground">
                            Manage authorized medical technologists, phlebotomists, laboratory supervisors, and blood bank system administrators.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <Button 
                    @click="openCreateModal"
                    class="bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-xl shadow-md shadow-purple-900/20 gap-2 h-10 px-4 cursor-pointer transition-all active:scale-[0.99]"
                >
                    <UserPlus class="size-4" />
                    <span>Add Staff Member</span>
                </Button>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            
            <!-- Total Staff Members -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-purple-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Staff Personnel</span>
                    <span class="rounded-xl bg-purple-50 p-2 text-purple-700 dark:bg-purple-950/50 dark:text-purple-400">
                        <Users class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalStaff }}
                </div>
                <span class="text-xs font-medium text-purple-600 mt-1 block">
                    Active hospital workforce
                </span>
            </div>

            <!-- Medical Technologists (RMTs) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-blue-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Medical Technologists (RMTs)</span>
                    <span class="rounded-xl bg-blue-50 p-2 text-blue-700 dark:bg-blue-950/50 dark:text-blue-400">
                        <HeartPulse class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalMedTechs }}
                </div>
                <span class="text-xs font-medium text-blue-600 mt-1 block">
                    Laboratory & blood testing officers
                </span>
            </div>

            <!-- Active Staff On Duty -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-emerald-500 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Active Staff On Duty</span>
                    <span class="rounded-xl bg-emerald-50 p-2 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                        <UserCheck class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.activeStaff }}
                </div>
                <span class="text-xs font-medium text-emerald-600 mt-1 block">
                    {{ props.stats.onLeaveStaff }} staff on official leave
                </span>
            </div>

            <!-- System Administrators -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-xs border-l-4 border-l-rose-600 dark:bg-card dark:border-border/60">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">System Administrators</span>
                    <span class="rounded-xl bg-rose-50 p-2 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400">
                        <ShieldCheck class="size-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ props.stats.totalAdmins }}
                </div>
                <span class="text-xs font-medium text-rose-600 mt-1 block">
                    Full system access permissions
                </span>
            </div>

        </div>

        <!-- Main Content Area / Search & Filters -->
        <div class="rounded-2xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            
            <!-- Controls Bar -->
            <div class="flex flex-col gap-3 pb-3 border-b border-border/70">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative w-full md:w-96">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Search staff name, email, employee ID, role..." 
                            class="pl-9 h-9.5 text-xs rounded-xl"
                        />
                    </div>

                    <!-- Department & Status Dropdowns -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Department Filter -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="text-muted-foreground font-semibold text-[11px]">Dept:</span>
                            <select
                                v-model="selectedDepartmentFilter"
                                class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-purple-600 outline-none max-w-[200px] truncate"
                            >
                                <option value="All">All Departments</option>
                                <option v-for="dept in props.departmentsList" :key="dept" :value="dept">
                                    {{ dept }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="text-muted-foreground font-semibold text-[11px]">Status:</span>
                            <select
                                v-model="selectedStatusFilter"
                                class="h-8.5 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-purple-600 outline-none"
                            >
                                <option value="All">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Reset Filter -->
                        <Button 
                            v-if="searchQuery || selectedRoleFilter !== 'All' || selectedStatusFilter !== 'All' || selectedDepartmentFilter !== 'All'"
                            size="sm"
                            variant="ghost"
                            @click="searchQuery = ''; selectedRoleFilter = 'All'; selectedStatusFilter = 'All'; selectedDepartmentFilter = 'All';"
                            class="h-8.5 text-xs text-purple-600 hover:text-purple-700 hover:bg-purple-50 dark:hover:bg-purple-950/40 gap-1 px-2 cursor-pointer"
                        >
                            <RefreshCw class="size-3" />
                            <span>Reset</span>
                        </Button>
                    </div>

                </div>

                <!-- Role Filter Pills -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs pt-1">
                    <span class="text-muted-foreground font-semibold mr-1 text-[11px]">Role:</span>
                    <button
                        @click="selectedRoleFilter = 'All'"
                        :class="[
                            'px-3 py-1 rounded-xl font-bold transition-all cursor-pointer text-xs whitespace-nowrap',
                            selectedRoleFilter === 'All'
                                ? 'bg-purple-700 text-white shadow-xs'
                                : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                        ]"
                    >
                        All Roles
                    </button>
                    <button
                        v-for="role in props.rolesList"
                        :key="role"
                        @click="selectedRoleFilter = role"
                        :class="[
                            'px-3 py-1 rounded-xl font-semibold transition-all cursor-pointer text-xs whitespace-nowrap',
                            selectedRoleFilter === role
                                ? 'bg-purple-700 text-white shadow-xs'
                                : 'bg-muted/70 text-muted-foreground hover:bg-muted hover:text-foreground'
                        ]"
                    >
                        {{ role }}
                    </button>
                </div>
            </div>

            <!-- Staff Accounts List Table -->
            <div class="overflow-x-auto rounded-xl border border-border/80">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-muted/50 text-[11px] uppercase font-bold text-muted-foreground tracking-wider border-b border-border">
                        <tr>
                            <th class="px-4 py-3.5">Staff Member</th>
                            <th class="px-4 py-3.5">Role & Department</th>
                            <th class="px-4 py-3.5">Contact Information</th>
                            <th class="px-4 py-3.5">Account Status</th>
                            <th class="px-4 py-3.5">Joined Date</th>
                            <th class="px-4 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr 
                            v-for="user in filteredUsers" 
                            :key="user.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Staff Member Name & Employee ID -->
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div 
                                        :class="[
                                            'flex size-9 items-center justify-center rounded-xl font-black text-xs shadow-2xs',
                                            user.role?.includes('Administrator') ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' :
                                            user.role?.includes('Medical Technologist') || user.role?.includes('RMT') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                            'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'
                                        ]"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>{{ user.name }}</span>
                                            <span 
                                                v-if="user.id === props.currentUserId"
                                                class="text-[10px] font-extrabold bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300 px-1.5 py-0.2 rounded"
                                            >
                                                You
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground font-mono">
                                            <span>{{ user.employee_id || 'CRH-EMP-' + user.id }}</span>
                                            <button 
                                                @click="copyEmployeeId(user.employee_id)"
                                                class="text-muted-foreground hover:text-foreground cursor-pointer"
                                                title="Copy Employee ID"
                                            >
                                                <Check v-if="copiedEmployeeId === user.employee_id" class="size-3 text-emerald-600" />
                                                <Copy v-else class="size-3" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role & Department -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-1">
                                    <span 
                                        :class="[
                                            'inline-flex items-center gap-1 rounded-lg px-2.5 py-0.5 text-xs font-semibold border shadow-2xs',
                                            getRoleBadgeClasses(user.role)
                                        ]"
                                    >
                                        <ShieldCheck v-if="user.role?.includes('Administrator')" class="size-3" />
                                        <HeartPulse v-else-if="user.role?.includes('Medical Technologist') || user.role?.includes('RMT')" class="size-3" />
                                        <Stethoscope v-else-if="user.role?.includes('Supervisor')" class="size-3" />
                                        <Droplets v-else class="size-3" />
                                        <span>{{ user.role }}</span>
                                    </span>
                                    <div class="text-xs text-muted-foreground truncate max-w-[220px]">
                                        {{ user.department }}
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Info -->
                            <td class="px-4 py-3.5">
                                <div class="space-y-0.5 text-xs">
                                    <div class="flex items-center gap-1.5 text-gray-700 dark:text-gray-300 font-mono">
                                        <Mail class="size-3 text-muted-foreground" />
                                        <span>{{ user.email }}</span>
                                    </div>
                                    <div v-if="user.phone_number" class="flex items-center gap-1.5 text-muted-foreground font-mono text-[11px]">
                                        <Phone class="size-3 text-muted-foreground" />
                                        <span>{{ user.phone_number }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Account Status & Quick Switcher -->
                            <td class="px-4 py-3.5">
                                <div class="relative">
                                    <select
                                        :value="user.status"
                                        @change="updateStatus(user, ($event.target as HTMLSelectElement).value)"
                                        :class="[
                                            'text-xs font-bold px-2.5 py-1 rounded-lg border border-transparent hover:border-border cursor-pointer transition-all outline-none',
                                            user.status === 'Active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' :
                                            user.status === 'On Leave' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' :
                                            'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300'
                                        ]"
                                    >
                                        <option value="Active">Active</option>
                                        <option value="On Leave">On Leave</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                            </td>

                            <!-- Joined Date -->
                            <td class="px-4 py-3.5 text-xs text-muted-foreground font-mono">
                                {{ formatDate(user.created_at) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Button -->
                                    <Button 
                                        size="sm" 
                                        variant="outline" 
                                        @click="openEditModal(user)"
                                        title="Edit staff details"
                                        class="h-8 px-2.5 text-xs text-purple-700 hover:bg-purple-50 hover:text-purple-800 dark:hover:bg-purple-950/40 gap-1 cursor-pointer"
                                    >
                                        <Edit class="size-3.5" />
                                        <span>Edit</span>
                                    </Button>

                                    <!-- Delete Button -->
                                    <Button 
                                        size="sm" 
                                        variant="ghost" 
                                        @click="deleteStaff(user)"
                                        :disabled="user.id === props.currentUserId"
                                        :title="user.id === props.currentUserId ? 'Cannot delete logged-in account' : 'Delete staff account'"
                                        class="size-8 p-0 text-muted-foreground hover:text-red-700 disabled:opacity-30 cursor-pointer"
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
                v-if="filteredUsers.length === 0" 
                class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-xl border border-dashed border-border bg-muted/10"
            >
                <div class="flex size-14 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 dark:bg-purple-950/60 dark:text-purple-400 mb-3">
                    <Users class="size-7" />
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                    {{ searchQuery || selectedRoleFilter !== 'All' || selectedStatusFilter !== 'All' ? 'No matching staff members found' : 'No Staff Accounts Registered' }}
                </h3>
                <p class="text-xs sm:text-sm text-muted-foreground max-w-sm mt-1">
                    {{ searchQuery || selectedRoleFilter !== 'All' || selectedStatusFilter !== 'All' ? 'Try adjusting your search criteria or clearing your filters.' : 'Click "+ Add Staff Member" to register authorized blood bank personnel.' }}
                </p>
                <Button 
                    v-if="!searchQuery && selectedRoleFilter === 'All' && selectedStatusFilter === 'All'"
                    @click="openCreateModal"
                    class="mt-4 bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-xl text-xs gap-1.5 cursor-pointer"
                >
                    <UserPlus class="size-4" />
                    <span>Add First Staff Member</span>
                </Button>
            </div>

        </div>

        <!-- WIDE ADD / EDIT STAFF MEMBER MODAL DIALOG (max-w-3xl) -->
        <div 
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-xs p-4 sm:p-6 overflow-y-auto"
        >
            <div class="relative w-full max-w-3xl rounded-2xl border border-border bg-card p-6 sm:p-8 shadow-2xl transition-all my-8 max-h-[92vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-5 border-b border-border">
                    <div class="flex items-center gap-3">
                        <div class="flex size-11 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-400 shadow-xs">
                            <UserPlus class="size-6" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                                <span>{{ isEditing ? 'Edit Staff Account' : 'Register New Staff Member' }}</span>
                                <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-900/40">
                                    {{ form.employee_id || props.nextEmployeeId }}
                                </span>
                            </h2>
                            <p class="text-xs sm:text-sm text-muted-foreground mt-0.5">
                                {{ isEditing ? 'Update personnel credentials, assigned role, and department status' : 'Create authorized credentials for medical technologists and laboratory officers' }}
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
                    
                    <!-- Section 1: Staff Identification -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-purple-800 dark:text-purple-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <Users class="size-3.5" />
                            <span>Staff Identification & Credentials</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Full Name -->
                            <div class="space-y-1.5 md:col-span-2">
                                <Label for="staff_name" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Full Name <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="staff_name"
                                    v-model="form.name"
                                    placeholder="e.g. Alexander Salazar, RMT"
                                    required
                                    class="h-10 text-sm rounded-xl font-medium"
                                />
                                <div v-if="form.errors.name" class="text-xs text-red-600">{{ form.errors.name }}</div>
                            </div>

                            <!-- Employee ID -->
                            <div class="space-y-1.5">
                                <Label for="employee_id" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Employee ID <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="employee_id"
                                    v-model="form.employee_id"
                                    placeholder="e.g. CRH-EMP-001"
                                    required
                                    class="h-10 text-sm font-mono font-bold rounded-xl"
                                />
                                <div v-if="form.errors.employee_id" class="text-xs text-red-600">{{ form.errors.employee_id }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div class="space-y-1.5">
                                <Label for="staff_email" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Hospital Email Address <span class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="staff_email"
                                    type="email"
                                    v-model="form.email"
                                    placeholder="e.g. asalazar@crh.gov.ph"
                                    required
                                    class="h-10 text-sm font-mono rounded-xl"
                                />
                                <div v-if="form.errors.email" class="text-xs text-red-600">{{ form.errors.email }}</div>
                            </div>

                            <!-- Phone Number -->
                            <div class="space-y-1.5">
                                <Label for="phone_number" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Contact / Mobile Number
                                </Label>
                                <Input 
                                    id="phone_number"
                                    v-model="form.phone_number"
                                    placeholder="e.g. 0917-445-9012"
                                    class="h-10 text-sm font-mono rounded-xl"
                                />
                                <div v-if="form.errors.phone_number" class="text-xs text-red-600">{{ form.errors.phone_number }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Role & Department Assignment -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-purple-800 dark:text-purple-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                            <Building class="size-3.5" />
                            <span>Role & Department Assignment</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Role -->
                            <div class="space-y-1.5">
                                <Label for="role" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Designated Role <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600 outline-none"
                                >
                                    <option v-for="role in props.rolesList" :key="role" :value="role">
                                        {{ role }}
                                    </option>
                                </select>
                                <div v-if="form.errors.role" class="text-xs text-red-600">{{ form.errors.role }}</div>
                            </div>

                            <!-- Department -->
                            <div class="space-y-1.5">
                                <Label for="department" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Department / Section <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="department"
                                    v-model="form.department"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600 outline-none"
                                >
                                    <option v-for="dept in props.departmentsList" :key="dept" :value="dept">
                                        {{ dept }}
                                    </option>
                                </select>
                                <div v-if="form.errors.department" class="text-xs text-red-600">{{ form.errors.department }}</div>
                            </div>

                            <!-- Status -->
                            <div class="space-y-1.5">
                                <Label for="staff_status" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Account Status <span class="text-rose-600">*</span>
                                </Label>
                                <select
                                    id="staff_status"
                                    v-model="form.status"
                                    required
                                    class="w-full h-10 px-3 rounded-xl border border-input bg-background text-sm font-semibold focus:ring-2 focus:ring-purple-600/20 focus:border-purple-600 outline-none"
                                >
                                    <option value="Active">Active</option>
                                    <option value="On Leave">On Leave</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                <div v-if="form.errors.status" class="text-xs text-red-600">{{ form.errors.status }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Security & Password -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold uppercase tracking-wider text-purple-800 dark:text-purple-400 flex items-center justify-between pb-1 border-b border-border/60">
                            <span class="flex items-center gap-1.5">
                                <Key class="size-3.5" />
                                <span>Security & Login Password</span>
                            </span>
                            <span v-if="isEditing" class="text-[11px] font-normal text-muted-foreground italic">
                                Leave blank to maintain current password
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div class="space-y-1.5">
                                <Label for="password" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    {{ isEditing ? 'New Password (Optional)' : 'Login Password' }} <span v-if="!isEditing" class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    :placeholder="isEditing ? '•••••••• (Leave blank to keep unchanged)' : 'Minimum 6 characters'"
                                    :required="!isEditing"
                                    class="h-10 text-sm rounded-xl font-mono"
                                />
                                <div v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="space-y-1.5">
                                <Label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                    Confirm Password <span v-if="!isEditing || form.password" class="text-rose-600">*</span>
                                </Label>
                                <Input 
                                    id="password_confirmation"
                                    type="password"
                                    v-model="form.password_confirmation"
                                    placeholder="Re-enter password"
                                    :required="!isEditing || !!form.password"
                                    class="h-10 text-sm rounded-xl font-mono"
                                />
                                <div v-if="form.errors.password_confirmation" class="text-xs text-red-600">{{ form.errors.password_confirmation }}</div>
                            </div>
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
                            class="h-10 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-semibold px-6 shadow-sm gap-2 cursor-pointer"
                        >
                            <Spinner v-if="form.processing" class="size-4" />
                            <span>{{ isEditing ? 'Update Staff Account' : 'Register Staff Member' }}</span>
                        </Button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</template>
