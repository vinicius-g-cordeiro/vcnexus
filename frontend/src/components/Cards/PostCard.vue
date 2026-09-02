<template>
  <article
    :class="[
      'rounded-xl border overflow-hidden transition-shadow hover:shadow-md',
      'dark:bg-neutral-800 bg-neutral-200 dark:border-neutral-700/60 border-neutral-300/70',
      'dark:text-neutral-50 text-neutral-900',
    ]"
  >
    <img
      v-if="image"
      :src="image"
      :alt="title"
      class="w-full h-48 object-cover"
    />

    <div class="p-5">
      <div class="flex items-center gap-2 mb-3">
        <span
          v-if="author.avatar"
          class="h-8 w-8 rounded-full object-cover bg-cover bg-center shrink-0"
          :style="{ backgroundImage: `url(${author.avatar})` }"
        />
        <span
          v-else
          class="h-8 w-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-semibold shrink-0"
        >
          {{ authorInitials }}
        </span>
        <div class="min-w-0">
          <p class="text-sm font-medium truncate">{{ author.name }}</p>
          <p class="text-xs dark:text-neutral-400 text-neutral-500">{{ publishedAt }}</p>
        </div>
      </div>

      <span
        v-if="tag"
        class="inline-block mb-2 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500"
      >
        {{ tag }}
      </span>

      <h3 class="font-semibold text-base leading-snug">{{ title }}</h3>
      <p v-if="excerpt" class="mt-2 text-sm dark:text-neutral-400 text-neutral-500 line-clamp-3">
        {{ excerpt }}
      </p>

      <div class="mt-4 flex items-center gap-4 text-sm dark:text-neutral-400 text-neutral-500">
        <button type="button" class="inline-flex items-center gap-1.5 hover:text-emerald-500 transition-colors" @click="$emit('like')">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" :fill="liked ? 'currentColor' : 'none'" :class="liked ? 'text-emerald-500' : ''" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
          </svg>
          {{ likeCount }}
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 hover:text-emerald-500 transition-colors" @click="$emit('comment')">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
          </svg>
          {{ commentCount }}
        </button>
        <button type="button" class="inline-flex items-center gap-1.5 hover:text-emerald-500 transition-colors ml-auto" @click="$emit('share')">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" />
            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" /><line x1="15.41" y1="6.51" x2="8.59" y2="10.49" />
          </svg>
        </button>
      </div>
    </div>
  </article>
</template>

<script setup>
/**
 * PostCard.vue — social/blog post card with author, engagement actions.
 * Usage:
 * <PostCard
 *   title="Launching our new dashboard"
 *   excerpt="A faster, cleaner way to see everything at a glance."
 *   image="/posts/dashboard.jpg"
 *   tag="Product"
 *   :author="{ name: 'Sam Lee', avatar: '/avatars/sam.jpg' }"
 *   published-at="2h ago"
 *   :like-count="24"
 *   :comment-count="6"
 *   @like="onLike"
 * />
 */
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  excerpt: { type: String, default: '' },
  image: { type: String, default: '' },
  tag: { type: String, default: '' },
  author: {
    type: Object,
    default: () => ({ name: 'Anonymous', avatar: '' }),
  },
  publishedAt: { type: String, default: '' },
  likeCount: { type: Number, default: 0 },
  commentCount: { type: Number, default: 0 },
  liked: { type: Boolean, default: false },
})

defineEmits(['like', 'comment', 'share'])

const authorInitials = computed(() =>
  (props.author.name || '?')
    .split(' ')
    .map((n) => n[0])
    .slice(0, 2)
    .join('')
    .toUpperCase()
)
</script>
