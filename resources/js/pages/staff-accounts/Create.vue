<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { 
    Users, 
    UserPlus, 
    X, 
    Mail, 
    Key, 
    Phone, 
    Building, 
    ShieldCheck, 
    Check, 
    UserCheck,
    Lock
} from '@lucide/vue';
import { Spinner } from '@/components/ui/spinner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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

const props = withDefaults(
    defineProps<{
        isOpen?: boolean;
        userToEdit?: UserRecord | null;
        rolesList?: string[];
        departmentsList?: string[];
        nextEmployeeId?: string;
    }>(),
    {
        isOpen: true,
        userToEdit: null,
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
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'saved'): void;
}>();

// Form State
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.rolesList[1] || 'Registered Medical Technologist (RMT)',
    employee_id: props.nextEmployeeId || '',
    department: props.departmentsList[0] || 'Blood Bank & Transfusion Medicine',
    phone_number: '',
    status: 'Active',
});

// Watch userToEdit prop changes
watch(
    () => props.userToEdit,
    (user) => {
        if (user) {
            form.name = user.name || '';
            form.email = user.email || '';
            form.employee_id = user.employee_id || '';
            form.role = user.role || props.rolesList[1] || 'Registered Medical Technologist (RMT)';
            form.department = user.department || props.departmentsList[0] || 'Blood Bank & Transfusion Medicine';
            form.phone_number = user.phone_number || '';
            form.status = user.status || 'Active';
            form.password = '';
            form.password_confirmation = '';
            form.clearErrors();
        } else {
            form.reset();
            form.employee_id = props.nextEmployeeId || '';
            form.role = props.rolesList[1] || 'Registered Medical Technologist (RMT)';
            form.department = props.departmentsList[0] || 'Blood Bank & Transfusion Medicine';
            form.status = 'Active';
            form.clearErrors();
        }
    },
    { immediate: true },
);

const handleClose = () => {
    form.reset();
    form.clearErrors();
    emit('close');
};

const submitForm = () => {
    if (props.userToEdit && props.userToEdit.id) {
        form.put(`/staff-accounts/${props.userToEdit.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                emit('saved');
                handleClose();
            },
        });
    } else {
        form.post('/staff-accounts', {
            preserveScroll: true,
            onSuccess: () => {
                emit('saved');
                handleClose();
            },
        });
    }
};
</script>

<template>
    <div 
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6 lg:p-8 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-5xl rounded-2xl border border-border bg-card p-6 sm:p-8 md:p-9 shadow-2xl transition-all my-6 max-h-[94vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-5 border-b border-border dark:border-neutral-800">
                <div class="flex items-center gap-3.5">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 shadow-xs">
                        <UserPlus v-if="!userToEdit" class="size-6" />
                        <ShieldCheck v-else class="size-6" />
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2.5">
                            <span>{{ userToEdit ? 'Edit Staff Account' : 'Register New Staff Member' }}</span>
                            <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded bg-purple-50 text-purple-700 dark:bg-purple-950/80 dark:text-purple-300 border border-purple-200 dark:border-purple-900/50">
                                {{ form.employee_id || nextEmployeeId }}
                            </span>
                        </h2>
                        <p class="text-xs sm:text-sm text-muted-foreground mt-0.5">
                            {{ userToEdit ? 'Update credentials, assigned duties, and security permissions for this personnel.' : 'Enroll authorized medical technologists, phlebotomists, and system officers.' }}
                        </p>
                    </div>
                </div>
                <button 
                    @click="handleClose"
                    class="rounded-xl p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors cursor-pointer"
                >
                    <X class="size-5" />
                </button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-6 pt-5">
                
                <!-- Section 1: Staff Identification -->
                <div class="space-y-3.5">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-purple-800 dark:text-purple-400 flex items-center gap-1.5 pb-1 border-b border-border/60">
                        <Users class="size-3.5" />
                        <span>Personnel Identification</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Full Name -->
                        <div class="space-y-1.5 lg:col-span-2">
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
                        <div class="space-y-1.5 lg:col-span-2">
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

                        <!-- Email -->
                        <div class="space-y-1.5 lg:col-span-2">
                            <Label for="staff_email" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Official Hospital Email <span class="text-rose-600">*</span>
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
                        <div class="space-y-1.5 lg:col-span-2">
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
                <div class="space-y-3.5">
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
                                <option v-for="role in rolesList" :key="role" :value="role">
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
                                <option v-for="dept in departmentsList" :key="dept" :value="dept">
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

                <!-- Section 3: Security & Login Password -->
                <div class="space-y-3.5">
                    <div class="text-xs font-extrabold uppercase tracking-wider text-purple-800 dark:text-purple-400 flex items-center justify-between pb-1 border-b border-border/60">
                        <span class="flex items-center gap-1.5">
                            <Key class="size-3.5" />
                            <span>Security & Authentication</span>
                        </span>
                        <span v-if="userToEdit" class="text-[11px] font-normal text-muted-foreground italic">
                            Leave blank to maintain current password
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div class="space-y-1.5">
                            <Label for="password" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                {{ userToEdit ? 'New Password (Optional)' : 'Login Password' }} <span v-if="!userToEdit" class="text-rose-600">*</span>
                            </Label>
                            <Input 
                                id="password"
                                type="password"
                                v-model="form.password"
                                :placeholder="userToEdit ? '•••••••• (Leave blank to keep unchanged)' : 'Minimum 6 characters'"
                                :required="!userToEdit"
                                class="h-10 text-sm rounded-xl font-mono"
                            />
                            <div v-if="form.errors.password" class="text-xs text-red-600">{{ form.errors.password }}</div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-1.5">
                            <Label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Confirm Password <span v-if="!userToEdit || form.password" class="text-rose-600">*</span>
                            </Label>
                            <Input 
                                id="password_confirmation"
                                type="password"
                                v-model="form.password_confirmation"
                                placeholder="Re-enter password"
                                :required="!userToEdit || !!form.password"
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
                        @click="handleClose"
                        class="h-10 px-5 rounded-xl cursor-pointer"
                    >
                        Cancel
                    </Button>
                    <Button 
                        type="submit" 
                        :disabled="form.processing"
                        class="h-10 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-semibold px-7 shadow-sm gap-2 cursor-pointer"
                    >
                        <Spinner v-if="form.processing" class="size-4" />
                        <span>{{ userToEdit ? 'Save Changes' : 'Register Staff Member' }}</span>
                    </Button>
                </div>

            </form>

        </div>
    </div>
</template>
