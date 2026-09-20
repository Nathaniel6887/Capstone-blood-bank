<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Users, 
    UserPlus, 
    Search, 
    ShieldCheck, 
    Mail, 
    Phone, 
    Building, 
    Edit, 
    Trash2, 
    Eye, 
    Check, 
    Copy, 
    RefreshCw, 
    HeartPulse, 
    Stethoscope, 
    Droplets 
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import CreateModal, { type UserRecord } from './Create.vue';
import ViewModal from './View.vue';

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
const isCreateModalOpen = ref(false);
const isViewModalOpen = ref(false);
const selectedUserForEdit = ref<UserRecord | null>(null);
const selectedUserForView = ref<UserRecord | null>(null);
const copiedEmployeeId = ref<string | null>(null);

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

// Open Modals
const openCreateModal = () => {
    selectedUserForEdit.value = null;
    isCreateModalOpen.value = true;
};

const openEditModal = (user: UserRecord) => {
    selectedUserForEdit.value = user;
    isCreateModalOpen.value = true;
};

const openViewModal = (user: UserRecord) => {
    selectedUserForView.value = user;
    isViewModalOpen.value = true;
};

const handleViewEdit = (user: UserRecord) => {
    isViewModalOpen.value = false;
    openEditModal(user);
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

    <div class="flex h-full flex-1 flex-col gap-5 p-4 sm:p-6 lg:p-8">
        
        <!-- Main Content Area / Search & Filters Card -->
        <div class="rounded-2xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            
            <!-- Controls Bar: Search, Dept, Status, and Add Staff Action -->
            <div class="flex flex-col gap-3.5 pb-3 border-b border-border/70">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    
                    <!-- Search Input -->
                    <div class="relative w-full md:w-96">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input 
                            v-model="searchQuery" 
                            placeholder="Search staff name, email, ID, role..." 
                            class="pl-9 h-10 text-xs rounded-xl"
                        />
                    </div>

                    <!-- Department, Status Dropdowns and Add Button -->
                    <div class="flex flex-wrap items-center gap-2.5">
                        
                        <!-- Department Filter -->
                        <div class="flex items-center gap-1.5 text-xs">
                            <span class="text-muted-foreground font-semibold text-[11px]">Dept:</span>
                            <select
                                v-model="selectedDepartmentFilter"
                                class="h-9 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-purple-600 outline-none max-w-[190px] truncate"
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
                                class="h-9 px-2.5 rounded-lg border border-input bg-background text-xs font-medium focus:ring-1 focus:ring-purple-600 outline-none"
                            >
                                <option value="All">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="On Leave">On Leave</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Reset Filter Button -->
                        <Button 
                            v-if="searchQuery || selectedRoleFilter !== 'All' || selectedStatusFilter !== 'All' || selectedDepartmentFilter !== 'All'"
                            size="sm"
                            variant="ghost"
                            @click="searchQuery = ''; selectedRoleFilter = 'All'; selectedStatusFilter = 'All'; selectedDepartmentFilter = 'All';"
                            class="h-9 text-xs text-purple-600 hover:text-purple-700 hover:bg-purple-50 dark:hover:bg-purple-950/40 gap-1 px-2.5 cursor-pointer"
                        >
                            <RefreshCw class="size-3" />
                            <span>Reset</span>
                        </Button>

                        <!-- Add Staff Member Button -->
                        <Button 
                            @click="openCreateModal"
                            class="bg-purple-700 hover:bg-purple-800 text-white font-semibold rounded-xl shadow-sm gap-2 h-9 px-4 cursor-pointer transition-all active:scale-[0.99] ml-auto md:ml-2"
                        >
                            <UserPlus class="size-4" />
                            <span>Add Staff Member</span>
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
                                            'flex size-9 items-center justify-center rounded-xl font-black text-xs shadow-2xs cursor-pointer',
                                            user.role?.includes('Administrator') ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' :
                                            user.role?.includes('Medical Technologist') || user.role?.includes('RMT') ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' :
                                            'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300'
                                        ]"
                                        @click="openViewModal(user)"
                                        title="Click to view profile"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                    <div>
                                        <div 
                                            class="font-bold text-gray-900 dark:text-white flex items-center gap-2 hover:text-purple-700 dark:hover:text-purple-400 cursor-pointer transition-colors"
                                            @click="openViewModal(user)"
                                        >
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
                                                @click="copyEmployeeId(user.employee_id || 'CRH-EMP-' + user.id)"
                                                class="text-muted-foreground hover:text-foreground cursor-pointer"
                                                title="Copy Employee ID"
                                            >
                                                <Check v-if="copiedEmployeeId === (user.employee_id || 'CRH-EMP-' + user.id)" class="size-3 text-emerald-600" />
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
                                    <!-- View Button -->
                                    <Button 
                                        size="sm" 
                                        variant="outline" 
                                        @click="openViewModal(user)"
                                        title="View staff profile & permissions"
                                        class="h-8 px-2.5 text-xs text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-neutral-800 gap-1 cursor-pointer"
                                    >
                                        <Eye class="size-3.5" />
                                        <span>View</span>
                                    </Button>

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

        <!-- CREATE / EDIT MODAL COMPONENT -->
        <CreateModal 
            :is-open="isCreateModalOpen"
            :user-to-edit="selectedUserForEdit"
            :roles-list="props.rolesList"
            :departments-list="props.departmentsList"
            :next-employee-id="props.nextEmployeeId"
            @close="isCreateModalOpen = false"
        />

        <!-- VIEW MODAL COMPONENT -->
        <ViewModal 
            :is-open="isViewModalOpen"
            :user="selectedUserForView"
            @close="isViewModalOpen = false"
            @edit="handleViewEdit"
        />

    </div>
</template>
