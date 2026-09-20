<script setup lang="ts">
import { ref, computed } from 'vue';
import { 
    X, 
    Printer, 
    Edit, 
    Copy, 
    Check, 
    ShieldCheck, 
    Mail, 
    Phone, 
    Building, 
    Calendar, 
    Users, 
    HeartPulse, 
    Stethoscope, 
    Droplets, 
    CheckCircle2, 
    Lock, 
    BadgeCheck, 
    Clock, 
    ShieldAlert
} from '@lucide/vue';
import { Button } from '@/components/ui/button';

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
        user?: UserRecord | null;
    }>(),
    {
        isOpen: true,
        user: null,
    },
);

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'edit', user: UserRecord): void;
}>();

const copiedField = ref<string | null>(null);

const handleClose = () => {
    emit('close');
};

const handleEdit = () => {
    if (props.user) {
        emit('edit', props.user);
    }
};

const copyToClipboard = (text: string, fieldName: string) => {
    if (!text) return;
    navigator.clipboard.writeText(text);
    copiedField.value = fieldName;
    setTimeout(() => {
        if (copiedField.value === fieldName) {
            copiedField.value = null;
        }
    }, 2000);
};

const printProfile = () => {
    window.print();
};

// Helper: Initials
const initials = computed(() => {
    if (!props.user?.name) return 'ST';
    const parts = props.user.name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return props.user.name.slice(0, 2).toUpperCase();
});

// Format Date
const formattedDate = computed(() => {
    if (!props.user?.created_at) return 'N/A';
    const d = new Date(props.user.created_at);
    return isNaN(d.getTime())
        ? props.user.created_at
        : d.toLocaleDateString('en-US', {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
        });
});

// Permissions list based on role
const rolePermissions = computed(() => {
    const role = props.user?.role || '';
    if (role.includes('Administrator')) {
        return [
            { name: 'System Administration & Security', desc: 'Full administrative privileges, role management, and audit logs.', allowed: true },
            { name: 'User Management & Access Control', desc: 'Create, update, activate, and deactivate staff accounts.', allowed: true },
            { name: 'Blood Bank Inventory Oversight', desc: 'View, verify, and override inventory records across all sections.', allowed: true },
            { name: 'Regional Reports & Analytics', desc: 'Generate and export DOH, NVBSP, and regional network blood reports.', allowed: true },
        ];
    }
    if (role.includes('Medical Technologist') || role.includes('RMT')) {
        return [
            { name: 'Blood Typing & Compatibility Crossmatching', desc: 'Perform and validate ABO/Rh typing and crossmatch tests.', allowed: true },
            { name: 'Serological Screening & Safety Validation', desc: 'Screen blood units for TTI (HIV, HepB, HepC, Syphilis, Malaria).', allowed: true },
            { name: 'Blood Unit Collection & Processing', desc: 'Log whole blood collection and component separation (PRBC, FFP, Platelets).', allowed: true },
            { name: 'Issue Blood to Clinical Wards', desc: 'Authorize and issue crossmatched blood units for patient transfusion.', allowed: true },
        ];
    }
    if (role.includes('Supervisor')) {
        return [
            { name: 'Laboratory Quality Control Audits', desc: 'Supervise daily laboratory testing and quality assurance logs.', allowed: true },
            { name: 'Blood Supply Stock Verification', desc: 'Approve blood emergency reserve thresholds and stock requisitions.', allowed: true },
            { name: 'Donor Deferral & Safety Review', desc: 'Review donor eligibility exemptions and deferred donor records.', allowed: true },
            { name: 'Regional Blood Report Approval', desc: 'Audit and sign off on regional exchange reports and consumption statistics.', allowed: true },
        ];
    }
    if (role.includes('Phlebotomist') || role.includes('Donor Care')) {
        return [
            { name: 'Donor Screening & Intake', desc: 'Record pre-donation vitals, hemoglobin levels, and donor history.', allowed: true },
            { name: 'Mobile Blood Drive Collection', desc: 'Conduct phlebotomy procedures and seal collection bags.', allowed: true },
            { name: 'Donor Registry Updates', desc: 'Update donor contact information and record donation milestones.', allowed: true },
            { name: 'Unit Labeling & Segregation', desc: 'Tag newly collected units for quarantine and transport to CRH laboratory.', allowed: true },
        ];
    }
    return [
        { name: 'Transfusion Requisition Verification', desc: 'Inspect ward transfusion requests and patient blood crossmatch cards.', allowed: true },
        { name: 'Bedside Blood Verification & Administration', desc: 'Perform double-check protocol prior to clinical blood infusion.', allowed: true },
        { name: 'Adverse Reaction Incident Logging', desc: 'Document transfusion reactions and alert blood bank pathologists.', allowed: true },
        { name: 'Hospital Ward Blood Inventory Return', desc: 'Return unconsumed blood bags within regulated cold-chain timeframes.', allowed: true },
    ];
});
</script>

<template>
    <div 
        v-if="isOpen && user"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 sm:p-6 lg:p-8 overflow-y-auto"
        @click.self="handleClose"
    >
        <div class="relative w-full max-w-5xl rounded-2xl border border-slate-100 bg-white p-6 sm:p-8 md:p-9 shadow-2xl transition-all my-6 max-h-[94vh] overflow-y-auto dark:bg-neutral-900 dark:border-neutral-800 animate-in fade-in zoom-in-95 duration-150 print:m-0 print:p-4 print:border-none print:shadow-none">
            
            <!-- Header (Hidden on Print) -->
            <div class="flex items-start justify-between pb-5 border-b border-slate-100 dark:border-neutral-800 print:hidden">
                <div class="flex items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-xl bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-400 shrink-0 shadow-xs">
                        <Users class="size-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Staff Member Profile</h2>
                            <span 
                                :class="[
                                    'text-[11px] font-bold px-2 py-0.5 rounded-full border',
                                    user.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900' :
                                    user.status === 'On Leave' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-900' :
                                    'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700'
                                ]"
                            >
                                {{ user.status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Caraga Regional Hospital • Department of Blood Transfusion & Laboratory Services
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        @click="printProfile" 
                        class="h-8 gap-1 text-xs rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-neutral-800"
                    >
                        <Printer class="size-3.5" />
                        <span>Print</span>
                    </Button>
                    <button 
                        @click="handleClose"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-neutral-800 dark:hover:text-slate-200 transition-colors cursor-pointer"
                    >
                        <X class="size-5" />
                    </button>
                </div>
            </div>

            <!-- ID Card / Badge Hero Banner -->
            <div class="mt-6 rounded-2xl border border-purple-100 bg-gradient-to-r from-purple-50/70 via-slate-50 to-purple-50/40 p-5 dark:from-purple-950/20 dark:via-neutral-900 dark:to-purple-950/10 dark:border-purple-900/30">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 text-center sm:text-left">
                    
                    <!-- Staff Avatar Badge -->
                    <div class="relative">
                        <div class="flex size-20 sm:size-22 items-center justify-center rounded-2xl bg-gradient-to-tr from-purple-700 to-indigo-600 text-white text-2xl font-black shadow-lg shadow-purple-900/20">
                            {{ initials }}
                        </div>
                        <div 
                            :class="[
                                'absolute -bottom-1 -right-1 size-5 rounded-full border-2 border-white dark:border-neutral-900 flex items-center justify-center text-[9px] text-white font-bold',
                                user.status === 'Active' ? 'bg-emerald-500' :
                                user.status === 'On Leave' ? 'bg-amber-500' : 'bg-slate-400'
                            ]"
                            :title="user.status"
                        >
                            ✓
                        </div>
                    </div>

                    <!-- Staff Name & Primary Role -->
                    <div class="flex-1 space-y-1.5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div>
                                <h3 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ user.name }}
                                </h3>
                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-1">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-purple-100 px-2.5 py-0.5 text-xs font-bold text-purple-800 dark:bg-purple-950 dark:text-purple-300 border border-purple-200 dark:border-purple-900/50">
                                        <ShieldCheck v-if="user.role?.includes('Administrator')" class="size-3.5" />
                                        <HeartPulse v-else-if="user.role?.includes('Medical Technologist') || user.role?.includes('RMT')" class="size-3.5" />
                                        <Stethoscope v-else-if="user.role?.includes('Supervisor')" class="size-3.5" />
                                        <Droplets v-else class="size-3.5" />
                                        <span>{{ user.role }}</span>
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                        {{ user.department }}
                                    </span>
                                </div>
                            </div>

                            <!-- Employee ID Pill -->
                            <div class="inline-flex items-center justify-center gap-1.5 bg-white dark:bg-neutral-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-neutral-700 shadow-2xs">
                                <span class="text-[10px] uppercase font-bold text-slate-400">ID:</span>
                                <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">{{ user.employee_id || 'CRH-EMP-' + user.id }}</span>
                                <button 
                                    @click="copyToClipboard(user.employee_id || 'CRH-EMP-' + user.id, 'employee_id')"
                                    class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 cursor-pointer transition-colors"
                                    title="Copy Employee ID"
                                >
                                    <Check v-if="copiedField === 'employee_id'" class="size-3 text-emerald-600" />
                                    <Copy v-else class="size-3" />
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 pt-1 text-xs text-slate-500 dark:text-slate-400 font-mono">
                            <div class="flex items-center gap-1.5">
                                <Mail class="size-3.5 text-purple-600" />
                                <span>{{ user.email }}</span>
                                <button 
                                    @click="copyToClipboard(user.email, 'email')"
                                    class="text-slate-400 hover:text-slate-700 cursor-pointer"
                                    title="Copy Email"
                                >
                                    <Check v-if="copiedField === 'email'" class="size-2.5 text-emerald-600" />
                                    <Copy v-else class="size-2.5" />
                                </button>
                            </div>
                            <div v-if="user.phone_number" class="flex items-center gap-1.5">
                                <Phone class="size-3.5 text-purple-600" />
                                <span>{{ user.phone_number }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Details Grid -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- Card 1: Official Position & Assignment -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-neutral-800 dark:bg-neutral-800/40 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        <Building class="size-4 text-purple-600" />
                        <span>Hospital Assignment</span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Assigned Department</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right max-w-[200px]">{{ user.department }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Official Role / Title</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ user.role }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Enrolled In System</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200 font-semibold">{{ formattedDate }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Account Authorization</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                                <BadgeCheck class="size-3.5" />
                                <span>Authorized Staff</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Contact & Security Credentials -->
                <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 dark:border-neutral-800 dark:bg-neutral-800/40 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        <Lock class="size-4 text-purple-600" />
                        <span>Authentication & Security</span>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Login ID / Username</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ user.email }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Mobile / Emergency Contact</span>
                            <span class="font-mono text-slate-800 dark:text-slate-200 font-semibold">{{ user.phone_number || 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-neutral-700/60">
                            <span class="text-slate-500">Security Access Level</span>
                            <span class="font-bold text-purple-700 dark:text-purple-400">
                                {{ user.role.includes('Administrator') ? 'Level 4 (Full Admin)' : 'Level 2 (Standard Operations)' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Duty Status</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ user.status }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Role Privileges & Responsibilities Section -->
            <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50/30 p-4 dark:border-neutral-800 dark:bg-neutral-800/20 space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                    <ShieldCheck class="size-4 text-purple-600" />
                    <span>Authorized System Privileges & Responsibilities</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div 
                        v-for="(perm, idx) in rolePermissions" 
                        :key="idx"
                        class="flex items-start gap-2.5 p-2.5 rounded-lg bg-white dark:bg-neutral-800/80 border border-slate-100 dark:border-neutral-700/50 shadow-2xs"
                    >
                        <CheckCircle2 class="size-4 text-emerald-600 shrink-0 mt-0.5" />
                        <div>
                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ perm.name }}</div>
                            <div class="text-[11px] text-slate-500 dark:text-slate-400 leading-tight mt-0.5">{{ perm.desc }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer / Action Buttons (Hidden on print) -->
            <div class="mt-6 flex items-center justify-between pt-4 border-t border-slate-100 dark:border-neutral-800 print:hidden">
                <span class="text-xs text-slate-400">
                    Staff ID: <span class="font-mono font-semibold text-slate-600 dark:text-slate-300">{{ user.employee_id || 'CRH-EMP-' + user.id }}</span>
                </span>

                <div class="flex items-center gap-2.5">
                    <Button 
                        variant="outline" 
                        @click="handleClose"
                        class="h-9 px-4 rounded-xl cursor-pointer text-xs"
                    >
                        Close
                    </Button>
                    <Button 
                        @click="handleEdit"
                        class="h-9 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-semibold px-4 gap-1.5 cursor-pointer text-xs shadow-sm"
                    >
                        <Edit class="size-3.5" />
                        <span>Edit Staff Details</span>
                    </Button>
                </div>
            </div>

        </div>
    </div>
</template>
