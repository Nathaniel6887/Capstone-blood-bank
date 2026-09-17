<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Mail, Lock, ShieldCheck, HeartPulse, Droplets } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import PasskeyVerify from '@/components/PasskeyVerify.vue';

defineOptions({
    layout: null,
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Caraga Regional Hospital - Blood Bank Portal">
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    </Head>

    <div class="relative min-h-screen w-full bg-slate-100 font-sans antialiased overflow-x-hidden flex flex-col justify-between">
        <!-- Desktop Full-Screen Background Matching the Design -->
        <div 
            class="fixed inset-0 hidden lg:block bg-cover bg-center bg-no-repeat z-0"
            style="background-image: url('/images/bloodbank-bg.png');"
        ></div>

        <!-- Mobile & Tablet Background with Soft Backdrop -->
        <div 
            class="fixed inset-0 lg:hidden bg-cover bg-center bg-no-repeat z-0"
            style="background-image: url('/images/bloodbank-bg.png');"
        >
            <div class="absolute inset-0 bg-gradient-to-b from-white/90 via-white/85 to-red-950/40 backdrop-blur-xs"></div>
        </div>

        <!-- Main Content Area -->
        <main class="relative z-10 w-full min-h-screen flex flex-col lg:flex-row items-center justify-between p-4 sm:p-6 lg:p-8 xl:p-12">
            
            <!-- Left Side Branding (Rendered on Mobile/Tablet for high clarity, hidden on LG since it is in the background image) -->
            <div class="w-full lg:hidden flex flex-col items-center text-center pt-6 pb-4 max-w-lg mx-auto">
                <div class="inline-flex items-center justify-center size-16 rounded-2xl bg-gradient-to-tr from-red-700 to-rose-600 text-white shadow-xl shadow-red-700/30 mb-4 animate-pulse">
                    <Droplets class="size-9 fill-current" />
                </div>

                <p class="text-xs uppercase tracking-widest font-semibold text-gray-500 mb-1">
                    Welcome to
                </p>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-red-900 leading-tight">
                    CARAGA REGIONAL HOSPITAL
                </h1>
                <div class="flex items-center justify-center gap-1.5 text-2xl sm:text-3xl font-extrabold text-red-800 tracking-wide mt-0.5">
                    <span>BL</span>
                    <span class="inline-flex items-center justify-center text-red-600">
                        <Droplets class="size-6 sm:size-7 fill-red-600" />
                    </span>
                    <span>OD BANK</span>
                </div>

                <div class="flex items-center justify-center gap-2 my-3 w-36 mx-auto">
                    <div class="h-0.5 w-full bg-red-300"></div>
                    <span class="text-red-600 text-xs">♥</span>
                    <div class="h-0.5 w-full bg-red-300"></div>
                </div>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed max-w-md px-2">
                    We are committed to provide safe, sufficient, and quality blood to save lives and improve health in our community.
                </p>
            </div>

            <!-- Left Placeholder for Desktop to allow click-through / spacing -->
            <div class="hidden lg:block lg:w-1/2 xl:w-7/12 pointer-events-none"></div>

            <!-- Right Side Login Card (Positioned precisely over the white card area on desktop) -->
            <div class="w-full lg:w-1/2 xl:w-5/12 flex items-center justify-center lg:justify-end my-auto py-4">
                <div class="w-full max-w-[420px] sm:max-w-[440px] xl:max-w-[470px] lg:mr-[6%] xl:mr-[8%] 2xl:mr-[12%] bg-white/95 lg:bg-white rounded-3xl shadow-2xl shadow-red-950/20 lg:shadow-xl border border-gray-100/80 p-6 sm:p-8 md:p-10 transition-all duration-300">
                    
                    <!-- Card Header -->
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-red-50 text-red-700 border border-red-100">
                                <HeartPulse class="size-5 text-red-700" />
                            </div>
                            <div>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">
                                    Portal Sign In
                                </h2>
                                <p class="text-xs sm:text-sm text-gray-500">
                                    Blood Bank Management System
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Alert Banner -->
                    <div
                        v-if="status"
                        class="mb-5 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50/90 p-3 text-sm text-emerald-800 shadow-xs"
                    >
                        <ShieldCheck class="size-4 shrink-0 text-emerald-600" />
                        <span>{{ status }}</span>
                    </div>

                    <!-- Passkey Verification -->
                    <PasskeyVerify class="mb-4" />

                    <!-- Login Form -->
                    <Form
                        v-bind="store.form()"
                        :reset-on-success="['password']"
                        v-slot="{ errors, processing }"
                        class="flex flex-col gap-5"
                    >
                        <div class="grid gap-5">
                            <!-- Email Input -->
                            <div class="grid gap-1.5">
                                <Label for="email" class="text-xs font-semibold uppercase tracking-wider text-gray-700">
                                    Email Address
                                </Label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <Mail class="size-4.5" />
                                    </div>
                                    <Input
                                        id="email"
                                        type="email"
                                        name="email"
                                        required
                                        autofocus
                                        :tabindex="1"
                                        autocomplete="email"
                                        placeholder="user@crh.gov.ph"
                                        class="pl-10 h-11 rounded-xl border-gray-200 bg-gray-50/50 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-red-600 focus:ring-2 focus:ring-red-600/20 transition-all text-sm"
                                    />
                                </div>
                                <InputError :message="errors.email" class="text-xs mt-0.5 text-red-600" />
                            </div>

                            <!-- Password Input -->
                            <div class="grid gap-1.5">
                                <div class="flex items-center justify-between">
                                    <Label for="password" class="text-xs font-semibold uppercase tracking-wider text-gray-700">
                                        Password
                                    </Label>
                                    <TextLink
                                        v-if="canResetPassword"
                                        :href="request()"
                                        class="text-xs font-medium text-red-700 hover:text-red-800 transition-colors"
                                        :tabindex="5"
                                    >
                                        Forgot password?
                                    </TextLink>
                                </div>
                                <div class="relative">
                                    <PasswordInput
                                        id="password"
                                        name="password"
                                        required
                                        :tabindex="2"
                                        autocomplete="current-password"
                                        placeholder="••••••••••••"
                                        class="h-11 rounded-xl border-gray-200 bg-gray-50/50 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-red-600 focus:ring-2 focus:ring-red-600/20 transition-all text-sm"
                                    />
                                </div>
                                <InputError :message="errors.password" class="text-xs mt-0.5 text-red-600" />
                            </div>

                            <!-- Remember Me Checkbox -->
                            <div class="flex items-center justify-between pt-1">
                                <Label for="remember" class="flex items-center space-x-2.5 cursor-pointer text-xs sm:text-sm font-normal text-gray-600">
                                    <Checkbox id="remember" name="remember" :tabindex="3" class="data-[state=checked]:bg-red-700 data-[state=checked]:border-red-700" />
                                    <span>Remember this device</span>
                                </Label>
                            </div>

                            <!-- Submit Button -->
                            <Button
                                type="submit"
                                class="mt-2 w-full h-11 rounded-xl bg-gradient-to-r from-red-800 via-red-700 to-red-800 hover:from-red-900 hover:via-red-800 hover:to-red-900 text-white font-semibold text-sm shadow-lg shadow-red-900/25 hover:shadow-xl hover:shadow-red-900/35 transition-all duration-200 cursor-pointer active:scale-[0.99]"
                                :tabindex="4"
                                :disabled="processing"
                                data-test="login-button"
                            >
                                <Spinner v-if="processing" class="mr-2 text-white" />
                                <span>{{ processing ? 'Authenticating...' : 'Sign In to Blood Bank' }}</span>
                            </Button>
                        </div>
                    </Form>

                    <!-- Security & System Notice Footer -->
                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-center justify-center gap-1.5 text-[11px] text-gray-400 text-center">
                        <ShieldCheck class="size-3.5 text-red-700 shrink-0" />
                        <span>Authorized Medical Personnel Only • All Access Logged</span>
                    </div>

                </div>
            </div>

        </main>
    </div>
</template>
