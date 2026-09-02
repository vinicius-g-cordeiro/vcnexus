<template>
  <section class="space-y-6 mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full max-w-7xl">

    <!-- Search -->
    <Fieldset :legend="t('users.list.search.legend')" icon="bi-people-fill">
      <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- Filters -->
        <div class="gap-4 grid grid-cols-1 md:grid-cols-4">

          <div class="md:col-span-3">
            <BaseInput v-model="form.search" type="text" :label="t('users.list.search.search')" placeholder="" autocomplete="off" />
          </div>

          <div>
            <Select v-model="form.active" :label="t('users.list.search.active.label')" placeholder="" :options="options" />
          </div>

        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3">

          <Button native-type="submit" :loading="loading">
            <i v-if="!loading" class="mr-2 bi bi-search"></i>

            {{ loading
              ? 'Searching'
              : t('users.list.search.search')
            }}
          </Button>

          <Button native-type="reset" :loading="loading" class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300" @click="form.search = ''; form.active = ''; form.page = 1">
            <i class="mr-2 bi bi-x-lg"></i>

            {{ t('users.list.search.clear') }}
          </Button>

        </div>
      </form>
    </Fieldset>

    <!-- Results -->
    <Fieldset v-if="error || users !== null || loading" :legend="t('users.list.results.legend')" icon="bi-list-task">

      <!-- Error -->
      <div v-if="error" class="flex items-center gap-3 bg-red-50 dark:bg-red-950/30 px-4 py-3 border border-red-200 dark:border-red-900/50 rounded-lg text-red-700 dark:text-red-400 text-sm">
        <i class="bi bi-exclamation-triangle-fill"></i>

        <span>
          {{ t('users.list.results.errors') }}
        </span>
      </div>


      <!-- Loading -->
      <div v-else-if="loading" class="flex justify-center items-center min-h-48">
        <div class="flex flex-col items-center gap-3 text-neutral-500">
          <i class="text-2xl animate-spin bi bi-arrow-repeat"></i>

          <span class="text-sm">
            {{ t('users.list.results.loading') }}
          </span>
        </div>
      </div>


      <!-- Empty -->
      <div v-else-if="!users || users.length === 0" class="flex flex-col justify-center items-center gap-3 min-h-48 text-neutral-500">
        <i class="text-3xl bi bi-person-x"></i>

        <p class="text-sm">
          {{ t('users.list.results.empty') || 'No users found.' }}
        </p>
      </div>


      <!-- Results -->
      <template v-else>

        <!-- =========================
         Desktop table
         ========================= -->
        <div class="hidden md:block border border-neutral-200 dark:border-neutral-800 rounded-xl overflow-hidden">

          <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

              <thead class="bg-neutral-50 dark:bg-neutral-900/70 border-neutral-200 dark:border-neutral-800 border-b">
                <tr>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.name') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.organization') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.email') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.created_at') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.updated_at') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500 text-right">
                    {{ t('users.list.results.actions') }}
                  </th>

                </tr>
              </thead>


              <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">

                <tr v-for="user in users" :key="user.id" class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">

                  <!-- User -->
                  <td class="px-5 py-4">

                    <div class="flex items-center gap-3">

                      <div class="flex justify-center items-center bg-neutral-100 dark:bg-neutral-800 rounded-full w-9 h-9 text-neutral-600 dark:text-neutral-300 shrink-0">
                        <i class="bi bi-person"></i>
                      </div>

                      <div class="min-w-0">

                        <p class="font-medium text-neutral-900 dark:text-neutral-100">
                          {{ user.name }}
                          {{ user.surname }}
                          {{ user.lastname }}
                        </p>

                        <p class="mt-0.5 text-neutral-500 text-xs">
                          @{{ user.username }}
                        </p>

                      </div>

                    </div>

                  </td>


                  <!-- Organization -->
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                      <i class="text-neutral-400 bi bi-building"></i>
                      <span>{{ user.organization }}</span>
                    </div>
                  </td>


                  <!-- Email -->
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-2 text-neutral-600 dark:text-neutral-400">
                      <i class="text-neutral-400 bi bi-envelope"></i>

                      <span class="max-w-56 truncate">
                        {{ user.email }}
                      </span>
                    </div>
                  </td>


                  <!-- Created -->
                  <td class="px-5 py-4 text-neutral-500 whitespace-nowrap">
                    {{ formatDate(user.created_at) }}
                  </td>


                  <!-- Updated -->
                  <td class="px-5 py-4 text-neutral-500 whitespace-nowrap">
                    {{ formatDate(user.updated_at) }}
                  </td>


                  <!-- Actions -->
                  <td class="px-5 py-4">

                    <div class="flex justify-end gap-1">

                      <button type="button" title="View user" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100 transition">
                        <i class="bi bi-eye"></i>
                      </button>

                      <button type="button" title="Edit user" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100 transition">
                        <i class="bi bi-pencil"></i>
                      </button>

                      <button type="button" title="More actions" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100 transition">
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>

                    </div>

                  </td>

                </tr>

              </tbody>

            </table>

          </div>

        </div>


        <!-- =========================
         Mobile cards
         ========================= -->
        <div class="md:hidden space-y-3">

          <article v-for="user in users" :key="user.id" class="bg-white dark:bg-neutral-950 p-4 border border-neutral-200 dark:border-neutral-800 rounded-xl">

            <!-- User header -->
            <div class="flex justify-between items-start gap-3">

              <div class="flex items-center gap-3 min-w-0">

                <div class="flex justify-center items-center bg-neutral-100 dark:bg-neutral-800 rounded-full w-10 h-10 text-neutral-600 dark:text-neutral-300 shrink-0">
                  <i class="text-lg bi bi-person"></i>
                </div>

                <div class="min-w-0">

                  <p class="font-medium text-neutral-900 dark:text-neutral-100 truncate">
                    {{ user.name }}
                    {{ user.surname }}
                    {{ user.lastname }}
                  </p>

                  <p class="text-neutral-500 text-xs">
                    @{{ user.username }}
                  </p>

                </div>

              </div>


              <!-- Actions -->
              <div class="flex gap-1 shrink-0">

                <button type="button" title="View user" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100">
                  <i class="bi bi-eye"></i>
                </button>

                <button type="button" title="Edit user" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100">
                  <i class="bi bi-pencil"></i>
                </button>

              </div>

            </div>


            <!-- User information -->
            <div class="space-y-3 mt-4 pt-4 border-neutral-100 dark:border-neutral-800 border-t">

              <!-- Organization -->
              <div class="flex items-start gap-3">

                <i class="mt-0.5 w-4 text-neutral-400 bi bi-building"></i>

                <div class="min-w-0">
                  <p class="font-medium text-neutral-400 text-xs">
                    {{ t('users.list.results.organization') }}
                  </p>

                  <p class="text-neutral-700 dark:text-neutral-300 text-sm truncate">
                    {{ user.organization }}
                  </p>
                </div>

              </div>


              <!-- Email -->
              <div class="flex items-start gap-3">

                <i class="mt-0.5 w-4 text-neutral-400 bi bi-envelope"></i>

                <div class="min-w-0">
                  <p class="font-medium text-neutral-400 text-xs">
                    {{ t('users.list.results.email') }}
                  </p>

                  <p class="text-neutral-700 dark:text-neutral-300 text-sm break-all">
                    {{ user.email }}
                  </p>
                </div>

              </div>


              <!-- Created -->
              <div class="flex items-center gap-3">

                <i class="w-4 text-neutral-400 bi bi-calendar-plus"></i>

                <div>
                  <span class="font-medium text-neutral-400 text-xs">
                    {{ t('users.list.results.created_at') }}
                  </span>

                  <span class="ml-2 text-neutral-700 dark:text-neutral-300 text-sm">
                    {{ formatDate(user.created_at) }}
                  </span>
                </div>

              </div>


              <!-- Updated -->
              <div class="flex items-center gap-3">

                <i class="w-4 text-neutral-400 bi bi-calendar-check"></i>

                <div>
                  <span class="font-medium text-neutral-400 text-xs">
                    {{ t('users.list.results.updated_at') }}
                  </span>

                  <span class="ml-2 text-neutral-700 dark:text-neutral-300 text-sm">
                    {{ formatDate(user.updated_at) }}
                  </span>
                </div>

              </div>

            </div>

          </article>

        </div>

      </template>

    </Fieldset>

  </section>
</template>


<script setup>
import { computed, reactive } from 'vue'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'

import Fieldset from '@/components/Fieldset.vue'
import BaseInput from '@/components/BaseInput.vue'
import Select from '@/components/Select.vue'
import Button from '@/components/Button.vue'
import { useUserStore } from '@/stores/userStore'

const { t } = useI18n()

const userStore = useUserStore()

const { users, loading, error } = storeToRefs(userStore)

const form = reactive({
  search: '',
  active: '',
  page: 1,
})

const options = computed(() => [
  {
    label: t('users.list.search.active.active'),
    value: '1',
  },
  {
    label: t('users.list.search.active.deactivated'),
    value: '0',
  },
])

async function handleSubmit() {
  await userStore.search(form)
}

function formatDate(date) {
  if (!date) {
    return '-'
  }

  const parsed = new Date(date)

  if (Number.isNaN(parsed.getTime())) {
    return '-'
  }

  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(parsed)
}
</script>