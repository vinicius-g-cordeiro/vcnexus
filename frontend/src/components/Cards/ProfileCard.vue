<template>
  <div
    :class="[
      'rounded-xl border p-6 flex flex-col items-center text-center transition-shadow hover:shadow-md',
      'dark:bg-neutral-800 bg-neutral-200 dark:border-neutral-700/60 border-neutral-300/70',
      'dark:text-neutral-50 text-neutral-900',
    ]"
  >
    <div class="relative">
      <img
        v-if="avatar"
        :src="avatar"
        :alt="name"
        class="h-20 w-20 rounded-full object-cover ring-2 ring-emerald-500"
      />
      <span
        v-else
        class="h-20 w-20 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xl font-semibold ring-2 ring-emerald-500"
      >
        {{ initials }}
      </span>
      <span
        v-if="status"
        :class="[
          'absolute bottom-0.5 right-0.5 h-4 w-4 rounded-full border-2 dark:border-neutral-800 border-neutral-200',
          statusColor,
        ]"
        :title="status"
      />
    </div>

    <h3 class="mt-4 font-semibold text-base">{{ name }}</h3>
    <p v-if="role" class="text-sm text-emerald-500 font-medium">{{ role }}</p>
    <p v-if="bio" class="mt-2 text-sm dark:text-neutral-400 text-neutral-500 line-clamp-3">
      {{ bio }}
    </p>

    <div v-if="stats && stats.length" class="mt-4 flex items-center gap-6 w-full justify-center">
      <div v-for="stat in stats" :key="stat.label" class="text-center">
        <p class="font-semibold text-sm">{{ stat.value }}</p>
        <p class="text-xs dark:text-neutral-400 text-neutral-500">{{ stat.label }}</p>
      </div>
    </div>

    <div v-if="$slots.actions" class="mt-5 flex items-center gap-2 w-full">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
/**
 * ProfileCard.vue — user profile summary card.
 * Usage:
 * <ProfileCard
 *   name="Jane Doe"
 *   role="Product Designer"
 *   bio="Building delightful interfaces for five years."
 *   avatar="/avatars/jane.jpg"
 *   status="online"
 *   :stats="[{ label: 'Posts', value: 128 }, { label: 'Followers', value: '2.4k' }]"
 * >
 *   <template #actions>
 *     <button class="flex-1 rounded-md bg-emerald-500 text-white text-sm py-2">Follow</button>
 *     <button class="flex-1 rounded-md border dark:border-neutral-700 border-neutral-300 text-sm py-2">Message</button>
 *   </template>
 * </ProfileCard>
 */
import { computed } from 'vue'

const props = defineProps({
  name: { type: String, required: true },
  role: { type: String, default: '' },
  bio: { type: String, default: '' },
  avatar: { type: String, default: '' },
  status: {
    type: String,
    default: '', // 'online' | 'away' | 'offline' | ''
  },
  stats: {
    type: Array,
    default: () => [], // [{ label, value }]
  },
})

const initials = computed(() =>
  props.name
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

const statusColor = computed(() => {
  const map = {
    online: 'bg-emerald-500',
    away: 'bg-gold',
    offline: 'dark:bg-neutral-600 bg-neutral-400',
  }
  return map[props.status] ?? 'dark:bg-neutral-600 bg-neutral-400'
})
</script>
