<template>
  <section class="space-y-6 mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full max-w-7xl">

    <!-- Search -->
    <Fieldset :legend="t('users.list.search.legend')" icon="bi-people-fill"
      :actions="[{ url: '/users/new/', name: t('users.list.actions.new'), icon: 'bi bi-person-add' }, { url: '/users/reports/', name: t('users.list.actions.reports'), icon: 'bi bi-file-spreadsheet' }, { url: '/users/documents/', name: t('users.list.actions.documents'), icon: 'bi bi-file-earmark' }]">
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
        <div class="hidden md:block border border-neutral-200 dark:border-neutral-800 rounded-xs overflow-hidden">

          <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

              <thead class="bg-neutral-50 dark:bg-neutral-900/70 border-neutral-200 dark:border-neutral-800 border-b">
                <tr>
                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    #
                  </th>
                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.headers.userInfo') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.headers.status') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-neutral-500">
                    {{ t('users.list.results.headers.actions') }}
                  </th>
                </tr>
              </thead>


              <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">

                <tr v-for="(user, index) in users" :key="user.id" index="id" class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50 transition-colors">
                  <td class="px-5 py-4">
                    {{ index }}
                  </td>
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
                          <span>{{ getRole(user.role)?.label }}</span>
                        </p>

                        <p class="mt-0.5 text-neutral-500 text-xs">
                          @{{ user.username }}
                        </p>

                        <p class="mt-0.5 text-neutral-500 text-xs">
                          {{ user.email }}
                        </p>

                        <p class="mt-0.5 text-neutral-500 text-xs">
                          <i class="bi bi-building"></i><b>{{ user.organization }}</b>
                        </p>
                      </div>

                    </div>


                  </td>



                  <!-- Status -->
                  <td class="px-5 py-4 text-neutral-500">
                    <div class="flex items-center gap-3">

                      <div class="flex justify-center items-center bg-neutral-100 dark:bg-neutral-800 rounded-full w-9 h-9 text-neutral-600 dark:text-neutral-300 shrink-0">
                        <i class="bi bi-clock"></i>
                      </div>

                      <div class="min-w-0">

                        <p class="mt-0.5 text-neutral-500 text-xs">
                          <b>{{ t('users.list.results.headers.created_at') }}: </b>{{ formatDate(user.created_at, true) }}
                        </p>

                        <template v-if="user.updated_at">
                          <p class="mt-0.5 text-neutral-500 text-xs">
                            <b>{{ t('users.list.results.headers.updated_at') }}: </b>{{ formatDate(user.updated_at, true) }}
                          </p>
                        </template>
                        <template v-if="user.last_login">
                          <p class="mt-0.5 text-neutral-500 text-xs">
                            <b>{{ t('users.list.results.headers.last_login') }}: </b>{{ formatDate(user.last_login, true) }}
                          </p>
                        </template>

                        <template v-if="user.deleted_at">
                          <p class="mt-0.5 text-red-500 text-xs">
                            <b>{{ t('users.list.results.headers.deleted_at') }}: </b>{{ formatDate(user.deleted_at, true) }} por <b>{{ user.deleted_by }}</b>
                            
                          </p>
                        </template>
                      </div>

                    </div>


                  </td>

                  <!-- Actions -->
                  <td class="flex justify-center px-5 py-4">

                    <div class="flex flex-wrap gap-1">
                      <Button variant="ghost" :to="{ name: 'users-view', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.view')"><i class="bi bi-eye"></i></Button>

                      <Button variant="ghost" :to="{ name: 'users-edit', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.edit')"><i class="bi bi-pencil-square"></i></Button>
                      <template v-if=" (user.role !== '1' && user.role !== '2') && user.uuid !== authStore.sessionUser.uuid">
                        <template v-if="user.active === '1'">
                          <Button variant="ghost" @click="handleDelete(user.uuid)" :title="t('users.list.results.actions.delete')"><i class="bi bi-toggle2-off"></i></Button>
                        </template>
                        <template v-else>
                          <Button variant="ghost" @click="handleActivate(user.uuid)" :title="t('users.list.results.actions.activate')"><i class="bi bi-toggle2-on"></i></Button>
                        </template>
                        <Button variant="ghost" @click="handleBlock(user.uuid)" :title="t('users.list.results.actions.block')"><i class="text-red-400 bi-ban bi"></i></Button>
                      </template>

                    </div>

                  </td>

                </tr>

              </tbody>

            </table>

          </div>

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
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

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

async function handleDelete(id) {
  const deleted = await userStore.deleteUser(id)
  if(deleted) handleSubmit()
}


async function handleActivate(id) {
  const deleted = await userStore.activateUser(id)
  if(deleted) handleSubmit()
}

async function handleBlock(id) {
  const deleted = await userStore.deleteUser(id)
  
}

const roles = [
    { label: 'Super Administrator', value: 1, description: 'Full access to every area of the system.' },
    { label: 'Administrator', value: 2, description: 'Full access to every area of the system based on the tenant access.' },
    { label: 'Manager', value: 3, description: 'Can manage workers and view reports.' },
    { label: 'Worker', value: 4, description: 'Can view and update assigned jobs.' },
    { label: 'Viewer', value: 5, description: 'Read-only access.' }
];

const getRole = (role) => {
    return roles.find(item => item.value === Number(role));
};



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
      second: '2-digit'
    }).format(parsed)
  } else {
    return new Intl.DateTimeFormat('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
    }).format(parsed)
  }

}
</script>