<template>
  <div class="min-h-screen flex dark:bg-neutral-800 bg-neutral-200 dark:text-neutral-50 text-neutral-900">
    <!-- Left: form -->
    <div class="flex w-full lg:w-1/2 flex-col justify-center px-6 sm:px-12 lg:px-20 py-12">
      <div class="mx-auto w-full max-w-sm">
        <!-- Brand -->
        <a href="/" class="inline-flex items-center gap-2 mb-10">
          <span class="h-8 w-8 rounded-md bg-emerald-500 flex items-center justify-center text-white font-bold text-sm">
            {{ brandInitial }}
          </span>
          <span class="font-semibold text-lg tracking-tight">{{ brandName }}</span>
        </a>

        <h1 class="text-2xl font-semibold tracking-tight">Welcome back</h1>
        <p class="mt-2 text-sm dark:text-neutral-400 text-neutral-500">
          Don't have an account?
          <a href="/register" class="font-medium text-emerald-500 hover:text-emerald-400">
            Create one
          </a>
        </p>

        <form class="mt-8 flex flex-col gap-5" @submit.prevent="handleSubmit">
          <BaseInput
            v-model="form.email"
            type="text"
            label="Email"
            placeholder="you@example.com"
            :error="errors.email"
            required
            autocomplete="email"
          />

          <BaseInput
            v-model="form.password"
            type="password"
            label="Password"
            placeholder="••••••••"
            :error="errors.password"
            required
            autocomplete="current-password"
          />

          <div class="flex items-center justify-between">
            <BaseCheckbox v-model="form.remember" label="Remember me" />
            <a href="/forgot-password" class="text-sm font-medium text-emerald-500 hover:text-emerald-400">
              Forgot password?
            </a>
          </div>

          <p v-if="formError" class="text-sm text-red-500">{{ formError }}</p>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="mt-1 inline-flex items-center justify-center rounded-md bg-emerald-500 px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <svg
              v-if="isSubmitting"
              class="mr-2 h-4 w-4 animate-spin"
              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            {{ isSubmitting ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <div class="mt-8 flex items-center gap-4">
          <span class="h-px flex-1 dark:bg-neutral-700 bg-neutral-300" />
          <span class="text-xs dark:text-neutral-400 text-neutral-500">or continue with</span>
          <span class="h-px flex-1 dark:bg-neutral-700 bg-neutral-300" />
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-md border dark:border-neutral-700 border-neutral-300 py-2.5 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors"
            @click="$emit('oauth', 'google')"
          >
            <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="currentColor" d="M21.35 11.1h-9.17v2.73h6.51c-.33 3.81-3.5 5.44-6.5 5.44C8.36 19.27 5 16.25 5 12c0-4.1 3.2-7.27 7.2-7.27 3.09 0 4.9 1.97 4.9 1.97L19 4.72S16.56 2 12.1 2C6.42 2 2.03 6.8 2.03 12c0 5.05 4.13 10 10.22 10 5.35 0 9.25-3.67 9.25-9.09 0-1.15-.15-1.81-.15-1.81Z"/></svg>
            Google
          </button>
          <button
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-md border dark:border-neutral-700 border-neutral-300 py-2.5 text-sm font-medium hover:bg-neutral-300 dark:hover:bg-neutral-700 transition-colors"
            @click="$emit('oauth', 'github')"
          >
            <svg class="h-4 w-4" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.5v-1.75c-2.78.62-3.37-1.36-3.37-1.36-.46-1.2-1.11-1.52-1.11-1.52-.91-.64.07-.63.07-.63 1 .07 1.53 1.05 1.53 1.05.9 1.58 2.34 1.12 2.91.86.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.14-4.56-5.05 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.9-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.63 1.03 2.75 0 3.92-2.34 4.78-4.57 5.04.36.32.68.94.68 1.9v2.82c0 .28.18.61.69.5A10.26 10.26 0 0 0 22 12.25C22 6.58 17.52 2 12 2Z"/></svg>
            GitHub
          </button>
        </div>
      </div>
    </div>

    <!-- Right: showcase panel -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-neutral-900 text-neutral-50">
      <div
        class="absolute inset-0 opacity-40"
        style="background-image: radial-gradient(circle at 20% 20%, rgba(16,185,129,0.35), transparent 45%), radial-gradient(circle at 80% 70%, rgba(212,175,55,0.25), transparent 45%);"
      />
      <div class="relative z-10 flex flex-col justify-end p-16">
        <blockquote class="text-2xl font-medium leading-snug max-w-md">
          "{{ testimonial.quote }}"
        </blockquote>
        <div class="mt-6 flex items-center gap-3">
          <span class="h-10 w-10 rounded-full bg-gold flex items-center justify-center font-semibold text-neutral-900">
            {{ testimonial.initials }}
          </span>
          <div>
            <p class="text-sm font-medium">{{ testimonial.name }}</p>
            <p class="text-sm text-neutral-400">{{ testimonial.role }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * LoginPage.vue — split-panel login screen.
 * Reuses BaseInput / BaseCheckbox from the shared input kit.
 *
 * Usage:
 * <LoginPage brand-name="Acme" @submit="handleLogin" @oauth="handleOAuth" />
 *
 * Emits:
 *  - submit: { email, password, remember }  (call setError / setSubmitting via exposed refs, or handle validation yourself upstream)
 *  - oauth: 'google' | 'github'
 */
import { reactive, ref, computed } from 'vue'
import BaseInput from '../Inputs/BaseInput.vue'
import BaseCheckbox from '../Inputs/BaseCheckbox.vue'

const props = defineProps({
  brandName: {
    type: String,
    default: 'Acme',
  },
  isSubmitting: {
    type: Boolean,
    default: false,
  },
  formError: {
    type: String,
    default: '',
  },
  testimonial: {
    type: Object,
    default: () => ({
      quote: 'Switching over took ten minutes and our whole team was up and running the same day.',
      name: 'Alex Rivera',
      role: 'Engineering Lead, Nimbus',
      initials: 'AR',
    }),
  },
})

const emit = defineEmits(['submit', 'oauth'])

const form = reactive({
  email: '',
  password: '',
  remember: false,
})

const errors = reactive({
  email: '',
  password: '',
})

const brandInitial = computed(() => props.brandName.charAt(0).toUpperCase())

function validate() {
  errors.email = form.email ? '' : 'Email is required'
  errors.password = form.password ? '' : 'Password is required'
  return !errors.email && !errors.password
}

function handleSubmit() {
  if (!validate()) return
  emit('submit', { ...form })
}
</script>
