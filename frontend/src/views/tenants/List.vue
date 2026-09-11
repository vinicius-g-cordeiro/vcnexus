<template>
  <section class="space-y-6 mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full max-w-7xl">

    <!-- Search -->
    <Fieldset :legend="t('tenants.list.search.legend')" icon="bi-people-fill"
      :actions="[{ url: '/tenants/new/', name: t('tenants.list.actions.new'), icon: 'bi bi-building-add' }, { url: '/tenants/reports/', name: t('tenants.list.actions.reports'), icon: 'bi bi-file-spreadsheet' }, { url: '/users/list', name: t('tenants.list.actions.users'), icon: 'bi bi-people' }]">
      <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- Filters -->
        <div class="gap-4 grid grid-cols-1 md:grid-cols-4">

          <div class="md:col-span-3">
            <BaseInput v-model="form.search" type="text" :label="t('tenants.list.search.search')" placeholder="" autocomplete="off" />
          </div>

          <div>
            <Select v-model="form.active" :label="t('tenants.list.search.active.label')" placeholder="" :options="options" />
          </div>

        </div>

        <!-- Actions -->
        <div class="flex flex-wrap items-center gap-3">

          <Button native-type="submit" :loading="loading">
            <i v-if="!loading" class="mr-2 bi bi-search"></i>

            {{ loading
              ? 'Searching'
              : t('tenants.list.search.search')
            }}
          </Button>

          <Button native-type="reset" :loading="loading" class="bg-white hover:bg-zinc-50 dark:bg-zinc-900 dark:hover:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300" @click="form.search = ''; form.active = ''; form.page = 1">
            <i class="mr-2 bi bi-x-lg"></i>
            {{ t('tenants.list.search.clear') }}
          </Button>

        </div>
      </form>
    </Fieldset>

    <!-- Results -->
    <Fieldset v-if="error || tenants !== null || loading" :legend="t('tenants.list.results.legend')" icon="bi-list-task">

      <!-- Error -->
      <div v-if="error" class="flex items-center gap-3 bg-red-50 dark:bg-red-950/30 px-4 py-3 border border-red-200 dark:border-red-900/50 rounded-lg text-red-700 dark:text-red-400 text-sm">
        <i class="bi bi-exclamation-triangle-fill"></i>

        <span>
          {{ t('tenants.list.results.errors') }}
        </span>
      </div>


      <!-- Loading -->
      <div v-else-if="loading" class="flex justify-center items-center min-h-48">
        <div class="flex flex-col items-center gap-3 text-neutral-500">
          <i class="text-2xl animate-spin bi bi-arrow-repeat"></i>

          <span class="text-sm">
            {{ t('tenants.list.results.loading') }}
          </span>
        </div>
      </div>


      <!-- Empty -->
      <div v-else-if="!tenants || tenants.length === 0" class="flex flex-col justify-center items-center gap-3 min-h-48 text-neutral-500">
        <i class="text-3xl bi bi-person-x"></i>

        <p class="text-sm">
          {{ t('tenants.list.results.empty') || 'No tenants found.' }}
        </p>
      </div>


      <!-- Results -->
      <template v-else>

        <!-- =========================
         Desktop table
         ========================= -->
        <div class="hidden md:block border border-neutral-200 dark:border-neutral-800 rounded-xs overflow-hidden">

          <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

              <thead class="bg-neutral-50 dark:bg-neutral-900/70 border-neutral-200 dark:border-neutral-800 border-b">
                <tr>
                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('tenants.list.results.headers.tenantInfo') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('tenants.list.results.headers.status') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('tenants.list.results.headers.actions') }}
                  </th>
                </tr>
              </thead>


              <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">

                <tr v-for="(tenant) in tenants" :key="tenant.id" index="id" class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">
                  <!-- tenant -->
                  <td class="px-5 py-4">

                    <div class="flex items-center gap-3">
                      <div class="flex justify-center items-center bg-neutral-100 dark:bg-neutral-800 rounded-full w-9 h-9 text-neutral-600 dark:text-neutral-300 shrink-0">
                        <i class="bi bi-building"></i>
                      </div>
                      <div class="min-w-0">
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">
                          {{ tenant.name }}
                        </p>
                        <div class="mt-0.5 text-neutral-500 text-xs">
                          <div class="drop-shadow-lg blur-[2px]">
                            {{ tenant.email }}
                          </div>
                        </div>
                        <p class="font-medium text-neutral-900 dark:text-neutral-100">
                          <Button variant="link" target="blank" :href="tenant.website">{{ tenant.website }}</Button>
                        </p>
                      </div>

                    </div>


                  </td>


                  <!-- Status -->
                  <td class="px-5 py-4 text-zinc-500 text-xs">
                    <div class="flex items-center gap-3">

                      <div class="flex flex-wrap p-1">

                        <template v-if="tenant.active === '0'">

                          <div class="bg-red-500 p-2 rounded-sm text-zinc-200">
                            <b>{{ t('tenants.list.results.deactivated') }}</b>
                          </div>

                        </template>
                        <template v-else>
                          <div class="bg-emerald-500 p-2 rounded-sm text-zinc-200">
                            <b>{{ t('tenants.list.results.active') }}</b>
                          </div>
                        </template>
                      </div>

                    </div>


                  </td>
                  <!-- Actions -->
                  <td class="px-5 py-4">

                    <div class="flex flex-wrap justify-evenly gap-1">
                      <Button variant="ghost" :to="{ name: 'tenants.view', params: { uuid: tenant.uuid } }" :title="t('tenants.list.results.actions.view')"><i class="bi bi-eye"></i></Button>
                      <Button variant="ghost" :to="{ name: 'tenants.edit', params: { uuid: tenant.uuid } }" :title="t('tenants.list.results.actions.edit')"><i class="bi bi-pencil-square"></i></Button>
                      <template v-if="(authStore.sessionUser.role !== '1' && authStore.sessionUser.role !== '2')">
                        <template v-if="tenant.active === '1'">
                          <Button variant="ghost" @click="handleDeactivate(tenant.uuid)" :title="t('tenants.list.results.actions.delete')"><i class="text-red-500 bi bi-toggle2-off"></i></Button>
                        </template>
                        <template v-else>
                          <Button variant="ghost" @click="handleActivate(tenant.uuid)" :title="t('tenants.list.results.actions.activate')"><i class="text-emerald-500 bi bi-toggle2-on"></i></Button>
                        </template>
                      </template>
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

          <article v-for="tenant in tenants" :key="tenant.id" class="bg-white dark:bg-neutral-950 p-4 border border-neutral-200 dark:border-neutral-800 rounded-xl">

            <!-- tenant header -->
            <div class="flex justify-between items-start gap-3">

              <div class="flex items-center gap-3 min-w-0">

                <div class="flex justify-center items-center bg-neutral-100 dark:bg-neutral-800 rounded-full w-10 h-10 text-neutral-600 dark:text-neutral-300 shrink-0">
                  <i class="text-lg bi bi-person"></i>
                </div>

                <div class="min-w-0">

                  <p class="font-medium text-neutral-900 dark:text-neutral-100 truncate">
                    {{ tenant.name }}
                    {{ tenant.surname }}
                    {{ tenant.lastname }}
                  </p>
                </div>
              </div>


              <!-- Actions -->
              <div class="flex gap-1 shrink-0">

                <button type="button" title="View tenant" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100">
                  <i class="bi bi-eye"></i>
                </button>

                <button type="button" title="Edit tenant" class="flex justify-center items-center hover:bg-neutral-100 dark:hover:bg-neutral-800 rounded-lg w-8 h-8 text-neutral-500 hover:text-neutral-900 dark:hover:text-neutral-100">
                  <i class="bi bi-pencil"></i>
                </button>

              </div>

            </div>


            <!-- tenant information -->
            <div class="space-y-3 mt-4 pt-4 border-neutral-100 dark:border-neutral-800 border-t">

              <!-- Organization -->
              <div class="flex items-start gap-3">

                <i class="mt-0.5 w-4 text-neutral-400 bi bi-building"></i>

                <div class="min-w-0">
                  <p class="font-medium text-neutral-400 text-xs">
                    {{ t('tenants.list.results.organization') }}
                  </p>

                  <p class="text-neutral-700 dark:text-neutral-300 text-sm truncate">
                    {{ tenant.organization }}
                  </p>
                </div>

              </div>


              <!-- Email -->
              <div class="flex items-start gap-3">

                <i class="mt-0.5 w-4 text-neutral-400 bi bi-envelope"></i>

                <div class="min-w-0">
                  <p class="font-medium text-neutral-400 text-xs">
                    {{ t('tenants.list.results.email') }}
                  </p>

                  <p class="text-neutral-700 dark:text-neutral-300 text-sm break-all">
                    {{ tenant.email }}
                  </p>
                </div>

              </div>


              <!-- Created -->
              <div class="flex items-center gap-3">

                <i class="w-4 text-neutral-400 bi bi-calendar-plus"></i>

                <div>
                  <span class="font-medium text-neutral-400 text-xs">
                    {{ t('tenants.list.results.created_at') }}
                  </span>

                  <span class="ml-2 text-neutral-700 dark:text-neutral-300 text-sm">
                    {{ formatDate(tenant.created_at) }}
                  </span>
                </div>

              </div>


              <!-- Updated -->
              <div class="flex items-center gap-3">

                <i class="w-4 text-neutral-400 bi bi-calendar-check"></i>

                <div>
                  <span class="font-medium text-neutral-400 text-xs">
                    {{ t('tenants.list.results.updated_at') }}
                  </span>

                  <span class="ml-2 text-neutral-700 dark:text-neutral-300 text-sm">
                    {{ formatDate(tenant.updated_at) }}
                  </span>
                </div>

              </div>


              <!-- Last login -->
              <div class="flex items-center gap-3">

                <i class="w-4 text-neutral-400 bi bi-calendar-check"></i>

                <div>
                  <span class="font-medium text-neutral-400 text-xs">
                    {{ t('tenants.list.results.last_login') }}
                  </span>

                  <span class="ml-2 text-neutral-700 dark:text-neutral-300 text-sm">
                    {{ formatDate(tenant.last_login, true) }}
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
import { useTenantStore } from '@/stores/tenantStore'
import { useAuthStore } from '@/stores/authStore'

const { t } = useI18n()

const tenantStore = useTenantStore()
const authStore = useAuthStore()

const { tenants, loading, error } = storeToRefs(tenantStore)

const form = reactive({
  search: '',
  active: '',
  page: 1,
})

const options = computed(() => [
  {
    label: t('tenants.list.search.active.active'),
    value: '1',
  },
  {
    label: t('tenants.list.search.active.deactivated'),
    value: '0',
  },
])

async function handleSubmit() {
  await tenantStore.search(form)
}

function formatDate(date, showHour = false) {
  if (!date) {
    return '-'
  }

  const parsed = new Date(date)

  if (Number.isNaN(parsed.getTime())) {
    return '-'
  }

  if (showHour === true) {
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      timeZone: 'America/Sao_Paulo',
    }).format(parsed)
  } else {
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      timeZone: 'America/Sao_Paulo',
    }).format(parsed)
  }

}
</script>