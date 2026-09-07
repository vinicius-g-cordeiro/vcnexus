<template>
  <section class="space-y-6 mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full max-w-7xl">

    <!-- Search -->
    <Fieldset :legend="t('users.list.search.legend')" icon="bi-people-fill"
      :actions="[{ url: '/users/new/', name: t('users.list.actions.new'), icon: 'bi bi-person-add' }, { url: '/users/reports/', name: t('users.list.actions.reports'), icon: 'bi bi-file-spreadsheet' }, { url: '/users/documents/', name: t('users.list.actions.documents'), icon: 'bi bi-file-earmark' }]">
      <form class="space-y-6" @submit.prevent="handleSubmit">
        <!-- Filters -->
        <div class="gap-4 grid grid-cols-1 md:grid-cols-4">

          <div class="md:col-span-2">
            <BaseInput v-model="form.search" type="text" :label="t('users.list.search.search')" placeholder="" autocomplete="off" />
          </div>

          <div>
            <Select v-model="form.active" :label="t('users.list.search.active.label')" placeholder="" :options="activeOptions" />
          </div>

          <div>
            <Select v-model="form.blocked" :label="t('users.list.search.blocked.label')" placeholder="" :options="blockedOptions" />
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
        <div class="flex flex-col items-center gap-3 text-zinc-500">
          <i class="text-2xl animate-spin bi bi-arrow-repeat"></i>

          <span class="text-sm">
            {{ t('users.list.results.loading') }}
          </span>
        </div>
      </div>


      <!-- Empty -->
      <div v-else-if="!users || users.length === 0" class="flex flex-col justify-center items-center gap-3 min-h-48 text-zinc-500">
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
        <div class="hidden md:block border border-zinc-200 dark:border-zinc-800 rounded-xs overflow-hidden">

          <div class="overflow-x-auto">

            <table class="w-full text-sm text-left">

              <thead class="bg-zinc-50 dark:bg-zinc-900/70 border-zinc-200 dark:border-zinc-800 border-b">
                <tr>
                  <th class="px-5 py-3.5 font-medium text-zinc-500">
                    {{ t('users.list.results.headers.userInfo') }}
                  </th>
                  <th class="px-5 py-3.5 font-medium text-zinc-500">
                    {{ t('users.list.results.headers.organization') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-zinc-500">
                    {{ t('users.list.results.headers.status') }}
                  </th>

                  <th class="px-5 py-3.5 font-medium text-zinc-500">
                    {{ t('users.list.results.headers.actions') }}
                  </th>
                </tr>
              </thead>


              <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">

                <tr v-for="user in users" :key="user.id" index="id" class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors">
                  <!-- User -->
                  <td class="px-5 py-4">

                    <div class="flex items-center gap-3">

                      <div class="flex justify-center items-center bg-zinc-100 dark:bg-zinc-800 rounded-full w-12 h-12 text-zinc-600 dark:text-zinc-300 shrink-0">
                        <img v-if="user.avatar" :src="`${storageBase}/storage/users/avatars/${user.avatar}`" loading="lazy" class="rounded-full w-12 h-12 object-center" alt="avatar" />
                      </div>

                      <div class="min-w-0">

                        <p class="font-medium text-zinc-900 dark:text-zinc-100">
                          {{ user.name }}
                          {{ user.surname }}
                          {{ user.lastname }}
                        </p>

                        <p class="mt-0.5 text-emerald-500 text-xs">
                          <i>{{ getRole(user.role)?.label }}</i>
                        </p>

                        <p class="mt-0.5 text-zinc-500 text-xs">
                          @{{ user.username }}
                        </p>

                        <div class="mt-0.5 text-zinc-500 text-xs">
                          <div class="drop-shadow-lg blur-[5px]">
                            {{ user.email }}
                          </div>
                        </div>

                      </div>

                    </div>


                  </td>

                  <td class="px-5 py-4">
                    <p class="mt-0.5 text-zinc-500 text-xs">
                      <i class="bi bi-building"></i><b>{{ user.organization }}</b>
                    </p>
                  </td>



                  <!-- Status -->
                  <td class="px-5 py-4 text-zinc-500 text-xs">
                    <div class="flex items-center gap-3">

                      <div class="flex flex-wrap p-1">

                        <template v-if="user.active === '0'">

                          <div class="bg-red-500 p-2 rounded-sm text-zinc-200">
                            <b>{{ t('users.list.results.deactivated') }}</b>
                          </div>

                        </template>
                        <template v-else>
                          <div class="bg-emerald-500 p-2 rounded-sm text-zinc-200">
                            <b>{{ t('users.list.results.active') }}</b>
                          </div>
                        </template>
                      </div>

                      <div class="min-w-0">

                        <template v-if="user.blocked === '1'">

                          <div class="bg-red-500 p-2 rounded-sm text-zinc-200">
                            <b>{{ t('users.list.results.blocked') }}</b>
                          </div>

                        </template>
                      </div>

                    </div>


                  </td>

                  <!-- Actions -->
                  <td class="flex justify-center px-5 py-4">

                    <div class="flex flex-wrap gap-1">
                      <Button variant="ghost" :to="{ name: 'users.view', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.view')"><i class="bi bi-eye"></i></Button>
                      <Button variant="ghost" :to="{ name: 'users.edit', params: { uuid: user.uuid } }" :title="t('users.list.results.actions.edit')"><i class="bi bi-pencil-square"></i></Button>
                      <template v-if="(user.role !== '1' && user.role !== '2') && user.uuid !== authStore.sessionUser?.uuid">
                        <template v-if="user.active === '1'">
                          <Button variant="ghost" @click="handleDeactivate(user.uuid)" :title="t('users.list.results.actions.delete')"><i class="text-red-500 bi bi-toggle2-off"></i></Button>
                        </template>
                        <template v-else>
                          <Button variant="ghost" @click="handleActivate(user.uuid)" :title="t('users.list.results.actions.activate')"><i class="text-emerald-500 bi bi-toggle2-on"></i></Button>
                        </template>

                        <Button v-if="!user.blocked" variant="ghost" @click="handleBlock(user.uuid)" :title="t('users.list.results.actions.block')"><i class="text-red-400 bi-lock bi"></i></Button>
                        <Button v-else variant="ghost" @click="handleUnBlock(user.uuid)" :title="t('users.list.results.actions.unblock')"><i class="text-emerald-400 bi-unlock bi"></i></Button>
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
const storageBase = import.meta.env.VITE_API_URL
const { t } = useI18n()

const userStore = useUserStore()

const { users, loading, error } = storeToRefs(userStore)

const form = reactive({
  search: '',
  active: '',
  page: 1,
})

const activeOptions = computed(() => [
  {
    label: t('users.list.search.active.active'),
    value: '1',
  },
  {
    label: t('users.list.search.active.deactivated'),
    value: '0',
  },
])


const blockedOptions = computed(() => [
  {
    label: t('users.list.search.blocked.blocked'),
    value: '1',
  },
  {
    label: t('users.list.search.blocked.unblocked'),
    value: '0',
  },
])


async function handleSubmit() {
  await userStore.search(form)
}

async function handleDeactivate(id) {
  const deleted = await userStore.deactivateUser(id)
  if (deleted) handleSubmit()
}


async function handleActivate(id) {
  const activated = await userStore.activateUser(id)
  if (activated) handleSubmit()
}

async function handleBlock(id) {
  const blocked = await userStore.blockUser(id)
  if (blocked) handleSubmit()
}


async function handleUnBlock(id) {
  const unblocked = await userStore.unblockUser(id)
  if (unblocked) handleSubmit()
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