// darkmode.js
// Reactive dark-mode state, synced with `localStorage` and the OS
// color-scheme preference, and applied via the `dark` class on
// <html> (matches TailwindCSS's `darkMode: 'class'` strategy).
//
// Usage:
//   import { useDarkMode } from './darkmode.js'
//   const { isDark, toggle, setDark } = useDarkMode()
//
// Setup (once, e.g. in main.js) to avoid a flash of wrong theme:
//   import { initDarkMode } from './darkmode.js'
//   initDarkMode()

import { ref } from 'vue'

const STORAGE_KEY = 'color-scheme'

const isDark = ref(false)

function applyClass(dark) {
  if (typeof document === 'undefined') return
  document.documentElement.classList.toggle('dark', dark)
}

export function initDarkMode() {
  if (typeof window === 'undefined') return

  const stored = localStorage.getItem(STORAGE_KEY)
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

  isDark.value = stored ? stored === 'dark' : prefersDark
  applyClass(isDark.value)

  // Keep in sync with OS-level changes if the user hasn't set an explicit preference
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem(STORAGE_KEY)) {
      isDark.value = e.matches
      applyClass(isDark.value)
    }
  })
}

export function useDarkMode() {
  function setDark(value) {
    isDark.value = value
    applyClass(value)
    if (typeof window !== 'undefined') {
      localStorage.setItem(STORAGE_KEY, value ? 'dark' : 'light')
    }
  }

  function toggle() {
    setDark(!isDark.value)
  }

  return { isDark, toggle, setDark }
}
