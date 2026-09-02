<template>
  <div
    :class="[
      'relative rounded-xl border p-5 transition-shadow hover:shadow-md',
      'dark:bg-neutral-800 bg-neutral-100 dark:border-neutral-700/60 border-neutral-300/70',
      'dark:text-neutral-50 text-neutral-900',
      pinned ? 'border-l-4 border-l-gold' : '',
    ]"
  >
    <div class="flex justify-between items-start gap-3">
      <div class="flex items-center gap-2">
        <span
          v-if="category"
          :class="[
            'inline-block px-2 py-0.5 rounded-full text-xs font-medium',
            categoryStyle,
          ]"
        >
          {{ category }}
        </span>
        <svg v-if="pinned" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gold" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2a1 1 0 0 1 1 1v6.28l4.4 3.3a1 1 0 0 1 .4.8V15a1 1 0 0 1-1 1h-4v5l-.5 1-.5-1v-5H7a1 1 0 0 1-1-1v-1.62a1 1 0 0 1 .4-.8L10.8 9.3V3a1 1 0 0 1 1-1h.2Z"/>
        </svg>
      </div>
      <span class="text-neutral-500 dark:text-neutral-400 text-xs shrink-0">{{ postedAt }}</span>
    </div>

    <h3 class="mt-3 font-semibold text-base leading-snug">{{ title }}</h3>
    <p v-if="body" class="mt-1.5 text-neutral-500 dark:text-neutral-400 text-sm line-clamp-4">
      {{ body }}
    </p>

    <div class="flex justify-between items-center mt-4">
      <div class="flex items-center gap-2 min-w-0">
        <span
          v-if="author.avatar"
          class="bg-cover bg-center rounded-full w-6 h-6 object-cover shrink-0"
          :style="{ backgroundImage: `url(${author.avatar})` }"
        />
        <span
          v-else-if="author.name"
          class="flex justify-center items-center bg-emerald-500 rounded-full w-6 h-6 font-semibold text-[10px] text-white shrink-0"
        >
          {{ authorInitials }}
        </span>
        <span class="text-neutral-500 dark:text-neutral-400 text-xs truncate">{{ author.name }}</span>
      </div>

      <span
        v-if="urgent"
        class="flex items-center gap-1 font-medium text-red-500 text-xs shrink-0"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
          <line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" />
        </svg>
        Urgent
      </span>
    </div>
  </div>
</template>

<script setup>
/**
 * BulletinCard.vue — notice-board style card for announcements,
 * classifieds, or community posts. Supports pinning and a small
 * category badge.
 *
 * Usage:
 * <BulletinCard
 *   title="Office closed Monday for maintenance"
 *   body="Building maintenance will run all day; please work remotely."
 *   category="Announcement"
 *   :author="{ name: 'Facilities Team' }"
 *   posted-at="Yesterday"
 *   pinned
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  body: { type: String, default: '' },
  category: { type: String, default: '' },
  categoryColor: {
    type: String,
    default: 'emerald', // 'emerald' | 'gold' | 'neutral'
  },
  author: {
    type: Object,
    default: () => ({ name: '', avatar: '' }),
  },
  postedAt: { type: String, default: '' },
  pinned: { type: Boolean, default: false },
  urgent: { type: Boolean, default: false },
})

const authorInitials = computed(() =>
  (props.author.name || '')
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)

const categoryStyle = computed(() => {
  const map = {
    emerald: 'bg-emerald-500/10 text-emerald-500',
    gold: 'bg-gold/10 text-gold',
    neutral: 'dark:bg-neutral-700 bg-neutral-200 dark:text-neutral-300 text-neutral-700',
  }
  return map[props.categoryColor] ?? map.emerald
})
</script>
