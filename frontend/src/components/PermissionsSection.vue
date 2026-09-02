<template>
  <Fieldset legend="Permissions" description="Fine-grained access, in addition to whatever the assigned roles already grant.">
    <div class="flex flex-col gap-4">
      <div
        v-for="group in permissionGroups"
        :key="group.key"
        class="flex flex-col gap-2.5"
      >
        <p class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">{{ group.label }}</p>
        <div class="gap-2 grid grid-cols-1 sm:grid-cols-2">
          <BaseCheckbox
            v-for="perm in group.permissions"
            :key="perm.value"
            :model-value="modelValue.includes(perm.value)"
            :label="perm.label"
            @update:model-value="togglePermission(perm.value, $event)"
          />
        </div>
      </div>
    </div>

    <div class="flex justify-between items-center pt-2 border-neutral-300 dark:border-neutral-700 border-t">
      <p class="text-neutral-500 dark:text-neutral-400 text-xs">
        {{ modelValue.length }} permission{{ modelValue.length === 1 ? '' : 's' }} granted
      </p>
      <AppButton
        v-if="modelValue.length"
        size="sm"
        variant="ghost"
        modal="revoke-all-permissions"
        @open-modal="isConfirmOpen = true"
      >
        Revoke all
      </AppButton>
    </div>

    <Modal v-model="isConfirmOpen" title="Revoke all permissions?" size="sm">
      <p class="text-neutral-700 dark:text-neutral-300 text-sm">
        This removes every individually-granted permission from this user. Access granted by
        their roles will not be affected. This can't be undone automatically.
      </p>
      <template #footer>
        <AppButton variant="ghost" @click="isConfirmOpen = false">Cancel</AppButton>
        <AppButton variant="danger" @click="revokeAll">Revoke all</AppButton>
      </template>
    </Modal>
  </Fieldset>
</template>

<script setup>
/**
 * PermissionsSection.vue — grouped permission checkboxes, plus a
 * "revoke all" action that confirms via Modal.vue before clearing.
 *
 * Usage:
 * <PermissionsSection v-model="form.permissions" :permission-groups="permissionGroups" />
 */
import { ref } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseCheckbox from '@/components/BaseCheckbox.vue'
import AppButton from '@/components/AppButton.vue'
import Modal from '@/components/Modal.vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [], // array of permission values
  },
  permissionGroups: {
    type: Array,
    default: () => [
      {
        key: 'users',
        label: 'Users',
        permissions: [
          { label: 'View users', value: 'users.view' },
          { label: 'Edit users', value: 'users.edit' },
          { label: 'Delete users', value: 'users.delete' },
          { label: 'Invite users', value: 'users.invite' },
        ],
      },
      {
        key: 'billing',
        label: 'Billing',
        permissions: [
          { label: 'View invoices', value: 'billing.view' },
          { label: 'Manage payment methods', value: 'billing.manage' },
        ],
      },
      {
        key: 'reports',
        label: 'Reports',
        permissions: [
          { label: 'View reports', value: 'reports.view' },
          { label: 'Export reports', value: 'reports.export' },
        ],
      },
    ],
  },
})

const emit = defineEmits(['update:modelValue'])

const isConfirmOpen = ref(false)

function togglePermission(value, checked) {
  const next = checked
    ? [...props.modelValue, value]
    : props.modelValue.filter((v) => v !== value)
  emit('update:modelValue', next)
}

function revokeAll() {
  emit('update:modelValue', [])
  isConfirmOpen.value = false
}
</script>
