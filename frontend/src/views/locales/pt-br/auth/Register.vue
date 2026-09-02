<template>
    <div class="flex bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
        <!-- Left: showcase panel -->
        <div class="hidden relative lg:flex bg-neutral-900 lg:w-1/2 overflow-hidden text-neutral-50">
            <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(circle at 80% 20%, rgba(16,185,129,0.35), transparent 45%), radial-gradient(circle at 20% 80%, rgba(212,175,55,0.25), transparent 45%);" />
            <div class="z-10 relative flex flex-col justify-center p-16">
                <span class="flex justify-center items-center bg-emerald-500 mb-8 rounded-md w-10 h-10 font-bold text-white">
                    {{ brandInitial }}
                </span>
                <h2 class="max-w-sm font-medium text-3xl leading-snug">
                    Everything customized for your workflow!
                </h2>
                <ul class="flex flex-col gap-4 mt-8">
                    <li v-for="point in highlights" :key="point" class="flex items-start gap-3 text-neutral-300 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 w-5 h-5 text-emerald-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        {{ point }}
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right: form -->
        <div class="flex flex-col justify-center px-6 sm:px-12 lg:px-20 py-12 w-full lg:w-1/2">
            <div class="mx-auto w-full max-w-md">
                <!-- Brand (mobile only, since desktop shows it on the left panel) -->
                <a href="/" class="lg:hidden inline-flex items-center gap-2 mb-10">
                    <span class="flex justify-center items-center bg-emerald-500 rounded-md w-8 h-8 font-bold text-white text-sm">
                        {{ brandInitial }}
                    </span>
                    <span class="font-semibold text-lg tracking-tight">{{ brandName }}</span>
                </a>

                <h1 class="font-semibold text-2xl tracking-tight">Create your account</h1>
                <p class="mt-2 text-neutral-500 dark:text-neutral-400 text-sm">
                    Already have an account?
                    <a href="/login" class="font-medium text-emerald-500 hover:text-emerald-400">
                        Sign in
                    </a>
                </p>

                <form class="flex flex-col gap-5 mt-8" @submit.prevent="handleSubmit">
                    <div class="gap-3 grid grid-cols-2">
                        <BaseInput v-model="form.name" type="text" label="First name" placeholder="Jhon" :error="errors.name" required autocomplete="given-name" />
                        <BaseInput v-model="form.lastname" type="text" label="Last name" placeholder="Oliver" :error="errors.lastname" required autocomplete="family-name" />

                    </div>
                    <BaseInput v-model="form.username" type="text" label="Username" placeholder="jo123" :error="errors.username" required autocomplete="username" />
                    
                    <BaseInput v-model="form.email" type="text" label="Email" placeholder="you@example.com" :error="errors.email" required autocomplete="email" />
                    
                    <BaseInput v-model="form.password" type="password" label="Password" placeholder="At least 8 characters" :error="errors.password" :hint="!errors.password ? 'Use 8+ characters with a mix of letters and numbers.' : ''" required autocomplete="new-password" />

                    <BaseInput v-model="form.password_confirmation" type="password" label="Confirm password" placeholder="Re-enter your password" :error="errors.password_confirmation" required autocomplete="new-password" />

                    <BaseCheckbox v-model="form.agree" :error="errors.agree">
                        I agree to the
                        <a href="/terms" class="font-medium text-emerald-500 hover:text-emerald-400">Terms of Service</a>
                        and
                        <a href="/privacy" class="font-medium text-emerald-500 hover:text-emerald-400">Privacy Policy</a>
                    </BaseCheckbox>
                    <p v-if="errors.agree" class="-mt-3 text-red-500 text-xs">{{ errors.agree }}</p>

                    <p v-if="formError" class="text-red-500 text-sm">{{ formError }}</p>

                    <button type="submit" :disabled="isSubmitting"
                        class="inline-flex justify-center items-center bg-emerald-500 hover:bg-emerald-600 disabled:opacity-60 mt-1 px-4 py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 font-medium text-white text-sm transition-colors disabled:cursor-not-allowed">
                        <svg v-if="isSubmitting" class="mr-2 w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                        </svg>
                        {{ isSubmitting ? 'Creating account...' : 'Create account' }}
                    </button>
                </form>

                <div class="flex items-center gap-4 mt-8">
                    <span class="flex-1 bg-neutral-300 dark:bg-neutral-700 h-px" />
                    <span class="text-neutral-500 dark:text-neutral-400 text-xs">or continue with</span>
                    <span class="flex-1 bg-neutral-300 dark:bg-neutral-700 h-px" />
                </div>

                <div class="gap-3 grid grid-cols-2 mt-6">
                    <button type="button" class="inline-flex justify-center items-center gap-2 hover:bg-neutral-300 dark:hover:bg-neutral-700 py-2.5 border border-neutral-300 dark:border-neutral-700 rounded-md font-medium text-sm transition-colors" @click="$emit('oauth', 'google')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M21.35 11.1h-9.17v2.73h6.51c-.33 3.81-3.5 5.44-6.5 5.44C8.36 19.27 5 16.25 5 12c0-4.1 3.2-7.27 7.2-7.27 3.09 0 4.9 1.97 4.9 1.97L19 4.72S16.56 2 12.1 2C6.42 2 2.03 6.8 2.03 12c0 5.05 4.13 10 10.22 10 5.35 0 9.25-3.67 9.25-9.09 0-1.15-.15-1.81-.15-1.81Z" />
                        </svg>
                        Google
                    </button>
                    <button type="button" class="inline-flex justify-center items-center gap-2 hover:bg-neutral-300 dark:hover:bg-neutral-700 py-2.5 border border-neutral-300 dark:border-neutral-700 rounded-md font-medium text-sm transition-colors" @click="$emit('oauth', 'github')">
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.5v-1.75c-2.78.62-3.37-1.36-3.37-1.36-.46-1.2-1.11-1.52-1.11-1.52-.91-.64.07-.63.07-.63 1 .07 1.53 1.05 1.53 1.05.9 1.58 2.34 1.12 2.91.86.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.14-4.56-5.05 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.9-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.63 1.03 2.75 0 3.92-2.34 4.78-4.57 5.04.36.32.68.94.68 1.9v2.82c0 .28.18.61.69.5A10.26 10.26 0 0 0 22 12.25C22 6.58 17.52 2 12 2Z" />
                        </svg>
                        GitHub
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
/**
 * RegisterPage.vue — split-panel registration screen.
 * Reuses BaseInput / BaseCheckbox from the shared input kit.
 *
 * Usage:
 * <RegisterPage brand-name="Acme" @submit="handleRegister" @oauth="handleOAuth" />
 *
 * Emits:
 *  - submit: { firstName, lastName, email, password, password_confirmation }
 *  - oauth: 'google' | 'github'
 */
import { reactive, computed } from 'vue'
import BaseInput from '@/components/Inputs/BaseInput.vue'
import BaseCheckbox from '@/components/Inputs/BaseCheckbox.vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const props = defineProps({
    brandName: {
        type: String,
        default: 'VCNexus',
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
    formError: {
        type: String,
        default: '',
    },
    highlights: {
        type: Array,
        default: () => [
            'Modules for managing your whole business',
            'From workers and tasks to weekly reports and payments',
            'Cancel anytime, no questions asked',
            'Your concerns in mind: Security, Performance, Workflow, Budget!'
        ],
    },
})

const emit = defineEmits(['submit', 'oauth'])

const form = reactive({
    name: '',
    lastName: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    agree: false,
})

const errors = reactive({
    firstName: '',
    lastName: '',
    email: '',
    username: '',
    password: '',
    password_confirmation: '',
    agree: '',
})

const brandInitial = computed(() => props.brandName.charAt(0).toUpperCase())

function validate() {
    errors.name = form.name ? '' : 'Required'
    errors.lastname = form.lastname ? '' : 'Required'
    errors.username = form.username ? '' : 'Required'
    errors.email = form.email ? '' : 'Email is required'
    errors.password = form.password.length >= 8 ? '' : 'Must be at least 8 characters'
    errors.password_confirmation =
        form.password_confirmation === form.password && form.password_confirmation
            ? ''
            : 'Passwords do not match'
    errors.agree = form.agree ? '' : 'You must accept the terms to continue'

    return !Object.values(errors).some(Boolean)
}

async function handleSubmit() {
    if (!validate()) return
    const { password_confirmation, agree, ...payload } = form
    const ok = await authStore.register(form);
    if (ok) {
        router.push({ name: 'dashboard' })
    }
}
</script>
