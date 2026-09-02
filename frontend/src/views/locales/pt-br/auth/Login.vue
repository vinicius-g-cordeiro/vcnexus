<template>
  <div class="flex bg-neutral-200 dark:bg-neutral-800 min-h-screen text-neutral-900 dark:text-neutral-50">
    <!-- Left: form -->
    <div class="flex flex-col justify-center px-6 sm:px-12 lg:px-20 py-12 w-full lg:w-1/2">
      <div class="mx-auto w-full max-w-sm">
        <!-- Brand -->
        <a href="/" class="inline-flex items-center gap-2 mb-10">
          <span class="flex justify-center items-center bg-emerald-500 rounded-md w-8 h-8 font-bold text-white text-sm">
            {{ brandInitial }}
          </span>
          <span class="font-semibold text-lg tracking-tight">{{ brandName }}</span>
        </a>

        <h1 class="font-semibold text-2xl tracking-tight">Welcome back</h1>
        <p class="mt-2 text-neutral-500 dark:text-neutral-400 text-sm">
          Don't have an account?
          <router-link :to="{ name: 'register' }" class="font-medium text-emerald-500 hover:text-emerald-400">
            Create one
          </router-link>
        </p>

        <form class="flex flex-col gap-5 mt-8" @submit.prevent="handleSubmit">
          <BaseInput
            v-model="form.login"
            type="text"
            label="Username/Phone/Email"
            placeholder="you@example.com"
            :error="errors.login"
            required
            autocomplete="login"
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

          <div class="flex justify-between items-center">
            <BaseCheckbox v-model="form.remember" label="Remember me" />
            <a href="/forgot-password" class="font-medium text-emerald-500 hover:text-emerald-400 text-sm">
              Forgot password?
            </a>
          </div>

          <p v-if="formError" class="text-red-500 text-sm">{{ formError }}</p>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="inline-flex justify-center items-center bg-emerald-500 hover:bg-emerald-600 disabled:opacity-60 mt-1 px-4 py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 font-medium text-white text-sm transition-colors disabled:cursor-not-allowed"
          >
            <svg
              v-if="isSubmitting"
              class="mr-2 w-4 h-4 animate-spin"
              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            >
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
            {{ isSubmitting ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <div class="flex items-center gap-4 mt-8">
          <span class="flex-1 bg-neutral-300 dark:bg-neutral-700 h-px" />
          <span class="text-neutral-500 dark:text-neutral-400 text-xs">or continue with</span>
          <span class="flex-1 bg-neutral-300 dark:bg-neutral-700 h-px" />
        </div>

        <div class="gap-3 grid grid-cols-2 mt-6">
          <button
            type="button"
            class="inline-flex justify-center items-center gap-2 hover:bg-neutral-300 dark:hover:bg-neutral-700 py-2.5 border border-neutral-300 dark:border-neutral-700 rounded-md font-medium text-sm transition-colors"
            @click="$emit('oauth', 'google')"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M21.35 11.1h-9.17v2.73h6.51c-.33 3.81-3.5 5.44-6.5 5.44C8.36 19.27 5 16.25 5 12c0-4.1 3.2-7.27 7.2-7.27 3.09 0 4.9 1.97 4.9 1.97L19 4.72S16.56 2 12.1 2C6.42 2 2.03 6.8 2.03 12c0 5.05 4.13 10 10.22 10 5.35 0 9.25-3.67 9.25-9.09 0-1.15-.15-1.81-.15-1.81Z"/></svg>
            Google
          </button>
          <button
            type="button"
            class="inline-flex justify-center items-center gap-2 hover:bg-neutral-300 dark:hover:bg-neutral-700 py-2.5 border border-neutral-300 dark:border-neutral-700 rounded-md font-medium text-sm transition-colors"
            @click="$emit('oauth', 'github')"
          >
            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.5v-1.75c-2.78.62-3.37-1.36-3.37-1.36-.46-1.2-1.11-1.52-1.11-1.52-.91-.64.07-.63.07-.63 1 .07 1.53 1.05 1.53 1.05.9 1.58 2.34 1.12 2.91.86.09-.67.35-1.12.64-1.38-2.22-.26-4.56-1.14-4.56-5.05 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.71 0 0 .84-.28 2.75 1.05a9.3 9.3 0 0 1 5 0c1.9-1.33 2.75-1.05 2.75-1.05.55 1.41.2 2.45.1 2.71.64.72 1.03 1.63 1.03 2.75 0 3.92-2.34 4.78-4.57 5.04.36.32.68.94.68 1.9v2.82c0 .28.18.61.69.5A10.26 10.26 0 0 0 22 12.25C22 6.58 17.52 2 12 2Z"/></svg>
            GitHub
          </button>
        </div>
      </div>
    </div>

    <!-- Right: showcase panel -->
    <div class="hidden relative lg:flex bg-neutral-900 lg:w-1/2 overflow-hidden text-neutral-50">
      <div
        class="absolute inset-0 opacity-40"
        style="background-image: radial-gradient(circle at 20% 20%, rgba(16,185,129,0.35), transparent 45%), radial-gradient(circle at 80% 70%, rgba(212,175,55,0.25), transparent 45%);"
      />
      <div class="z-10 relative flex flex-col justify-end p-16">
        <blockquote class="max-w-md font-medium text-2xl leading-snug">
          "{{ testimonial.quote }}"
        </blockquote>
        <div class="flex items-center gap-3 mt-6">
          <span class="flex justify-center items-center bg-gold rounded-full w-10 h-10 font-semibold text-neutral-900">
            {{ testimonial.initials }}
          </span>
          <div>
            <p class="font-medium text-sm">{{ testimonial.name }}</p>
            <p class="text-neutral-400 text-sm">{{ testimonial.role }}</p>
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
import BaseInput from '@/components/Inputs/BaseInput.vue'
import BaseCheckbox from '@/components/Inputs/BaseCheckbox.vue'
import { useAuthStore } from '@/stores/authStore';
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()

const authStore = useAuthStore();

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
  testimonial: {
    type: Object,
    default: () => ({
      quote: 'English motherf****r, do you speak it?',
      name: 'Julius Winnfield',
      role: 'Hitman',
      initials: 'JW',
    }),
  },
})

const emit = defineEmits(['submit', 'oauth'])

const form = reactive({
  login: '',
  password: '',
  remember: false,
})

const errors = reactive({
  login: '',
  password: '',
})

const brandInitial = computed(() => props.brandName.charAt(0).toUpperCase())

function validate() {
  errors.login = form.login ? '' : 'Username/Email/Phone is required'
  errors.password = form.password ? '' : 'Password is required'
  return !errors.login && !errors.password
}

async function handleSubmit() {
  if (!validate()) return

    const ok = await authStore.login(form)
    if(ok){
        router.push({ name: 'dashboard' })
    }
}
</script>
