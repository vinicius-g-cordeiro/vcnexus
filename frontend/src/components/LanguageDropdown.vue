<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const isOpen = ref(false)

const languages = [
    {
        code: 'pt-BR',
        label: 'Português',
        short: 'PT',
    },
    {
        code: 'en',
        label: 'English',
        short: 'EN',
    },
]

const currentLanguage = () => {
    return languages.find(language => language.code === locale.value)
        ?? languages[0]
}

function changeLanguage(code) {
    locale.value = code
    isOpen.value = false

    // Optional: remember the user's choice
    localStorage.setItem('locale', code)
}

function closeDropdown(event) {
    if (!event.target.closest('[data-language-switcher]')) {
        isOpen.value = false
    }
}

onMounted(() => {
    const savedLocale = localStorage.getItem('locale')

    if (savedLocale && languages.some(language => language.code === savedLocale)) {
        locale.value = savedLocale
    }

    document.addEventListener('click', closeDropdown)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', closeDropdown)
})
</script>

<template>
    <div data-language-switcher class="relative">
        <!-- Trigger -->
        <button type="button" class="flex items-center gap-2 bg-neutral-900/70 hover:bg-neutral-800 px-3 py-2 border border-neutral-800 hover:border-neutral-700 rounded-lg text-neutral-300 hover:text-white text-sm transition" @click="isOpen = !isOpen">
            <!-- Globe -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />

                <path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8" />

                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.2 2.5 3.3 5.5 3.3 9s-1.1 6.5-3.3 9c-2.2-2.5-3.3-5.5-3.3-9S9.8 5.5 12 3Z" />
            </svg>

            <span>
                {{ currentLanguage().short }}
            </span>

            <!-- Chevron -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <!-- Dropdown -->
        <Transition enter-active-class="transition duration-150 ease-out" enter-from-class="translate-y-1 opacity-0" enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-100 ease-in" leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
            <div v-if="isOpen" class="right-0 z-50 absolute bg-neutral-950 shadow-xl mt-2 p-1 border border-neutral-800 rounded-xl w-44 overflow-hidden">
                <button v-for="language in languages" :key="language.code" type="button" class="flex justify-between items-center px-3 py-2 rounded-lg w-full text-sm transition" :class="locale === language.code
                                ? 'bg-emerald-500/10 text-emerald-400'
                                : 'text-neutral-400 hover:bg-neutral-900 hover:text-white'
                            " @click="changeLanguage(language.code)">
                    <span>
                        {{ language.label }}
                    </span>

                    <!-- Check -->
                    <svg v-if="locale === language.code" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            </div>
        </Transition>
    </div>
</template>