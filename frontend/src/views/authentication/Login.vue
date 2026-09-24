<template>
    <section class="z-10 relative flex flex-col items-center max-w-xl text-center">
        <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
            Login
        </h1>

        <form @submit.prevent="handleSubmit">
            <div class="flex flex-col gap-2 mt-4">
                <label for="login">Login(Email/Username/Phone)</label>
                <input type="text" placeholder="Login" @input="form.login = $event.target.value" :value="form.login" name="login" id="login" class="w-full" />
            </div>

            <div class="flex flex-col gap-2 mt-4">
                <label for="password">Password</label>
                <input type="password" name="password" placeholder="Password" @input="form.password = $event.target.value" :value="form.password" id="password" class="w-full" />
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="submit"
                    class="inline-flex justify-center items-center bg-olive-wood-500 hover:bg-olive-wood-600 disabled:opacity-60 mt-1 px-4 py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-olive-wood-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 font-medium text-white text-sm transition-colors disabled:cursor-not-allowed">
                    Login
                </button>
            </div>
        </form>
    </section>
</template>

<script setup>
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
const router = useRouter()

const authStore = useAuthStore()

const form = reactive({
    login: '',
    password: '',
})

const errors = reactive({
    login: '',
    password: '',
})

function validate() {
    errors.login = form.login ? '' : 'Login is required'
    errors.password = form.password ? '' : 'Password is required'
    return !Object.values(errors).some(error => error)
}

const emit = defineEmits(['submit', 'oauth'])


async function handleSubmit() {
    if (!validate()) return;
    const ok = await authStore.login(form)
    if (ok) {
        router.push({ name: "home" });
    }
}


</script>