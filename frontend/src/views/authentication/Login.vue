<template>
    <section class="flex flex-row justify-between items-center gap-2 min-h-screen" >
        <aside class="flex flex-col gap-2 bg-zinc-100 dark:bg-zinc-700 shadow m-6 p-10 rounded-md h-full">
            <img class="w-32 h-32" src="@/assets/logo.png" alt="logo">
            <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
                <span class="text-olive-wood-400">
                    {{ app.title || 'VCNexus' }}
                </span>
            </h1>

            <p class="text-slate-400 text-sm sm:text-base leading-6">
                {{ app.description }}
            </p>

            <ul>
                <li v-for="point in app.sellingPoints" :key="point" class="text-sm sm:text-base leading-4 list-disc" :class="point.color">
                    {{ point.text }}
                </li>
            </ul>

        </aside>
        <section class="flex flex-col gap-2 bg-zinc-100 dark:bg-zinc-700 shadow m-6 p-10 rounded-md w-2/3">
            <h1 class="font-bold text-4xl sm:text-5xl tracking-tight">
                Login
            </h1>
            <form @submit.prevent="handleSubmit" autocomplete="off" class="flex flex-col gap-2 mt-4">
                <Input class="w-full" type="text" placeholder="login" v-model="form.login" name="login" id="login" label="Login" required />
                <Input class="w-full" type="password" placeholder="Password" v-model="form.password" name="password" id="password" showPassword label="Password" required />

                <div class="flex justify-end gap-2 mt-4">
                    <button type="submit"
                        class="inline-flex justify-center items-center bg-olive-wood-500 hover:bg-olive-wood-600 disabled:opacity-60 mt-1 px-4 py-2.5 rounded-md focus:outline-none focus:ring-2 focus:ring-olive-wood-500 focus:ring-offset-2 dark:focus:ring-offset-neutral-800 font-medium text-white text-sm transition-colors disabled:cursor-not-allowed">
                        Login
                    </button>
                </div>
            </form>
        </section>
    </section>
</template>

<script setup>
import { useAuthStore } from '@/stores/authentication/authenticationStore'
import { reactive } from 'vue'
import { useRouter } from 'vue-router'
import Input from '@/components/inputs/Input.vue'
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


const app =
{
    title: 'VCNexus',
    description: 'ERP system',
    flavor_text: 'VCNexus is a multi-tenant ERP system that allows you to manage your business operations and data from multiple locations and clients in a single platform.',
    sellingPoints: [
        {
            text: 'Organize your business operations and data from multiple locations and clients in a single platform.',
            color: 'text-carbon-black-400 dark:text-carbon-black-600',
        },
        {
            text: 'Every tenant has their own database and schema, so you can keep your data isolated from other tenants.',
            color: 'text-carbon-black-400 dark:text-carbon-black-600',
        },
        {
            text: 'Simple and easy to use, with a clean and modern design.',
            color: 'text-carbon-black-400 dark:text-carbon-black-600',
        },
        {
            text: 'Fully customizable, with a wide range of features and integrations.',
            color: 'text-carbon-black-400 dark:text-carbon-black-600',
        },
    ],
    
    designed_by: 'Vinícius Cordeiro',
}


</script>