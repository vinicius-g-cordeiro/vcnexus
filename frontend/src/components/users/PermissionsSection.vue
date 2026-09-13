<template>
  <Fieldset legend="Permissions" description="Fine-grained access, in addition to whatever the assigned roles already grant.">
    <div class="flex flex-col gap-4">
      <div v-for="group in permissionGroups" :key="group.key" class="flex flex-col gap-2.5">
        <div class="flex justify-between items-center gap-2">
          <p class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">
            {{ group.label }}
            <span class="ml-1.5 font-normal text-neutral-500 dark:text-neutral-400 text-xs">
              {{ groupSelectedCount(group) }}/{{ group.permissions.length }}
            </span>
          </p>
          <button
            type="button"
            :disabled="disabled"
            class="disabled:opacity-50 font-medium text-emerald-600 hover:text-emerald-700 disabled:hover:text-emerald-600 dark:hover:text-emerald-400 dark:disabled:hover:text-emerald-500 dark:text-emerald-500 text-xs transition-colors disabled:cursor-not-allowed"
            @click="toggleAll(group, !isGroupFullySelected(group))"
          >
            {{ isGroupFullySelected(group) ? 'Deselect all' : 'Select all' }}
          </button>
        </div>
        <div class="gap-2 grid grid-cols-1 sm:grid-cols-2">
          <BaseCheckbox
            :disabled="disabled"
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
      <Button :disabled="disabled" v-if="modelValue.length" size="sm" variant="ghost" modal="revoke-all-permissions" @open-modal="isConfirmOpen = true">
        Revoke all
      </Button>
    </div>

    <Modal v-model="isConfirmOpen" title="Revoke all permissions?" size="sm">
      <p class="text-neutral-700 dark:text-neutral-300 text-sm">
        This removes every individually-granted permission from this user. Access granted by
        their roles will not be affected. This can't be undone automatically.
      </p>
      <template #footer>
        <Button :disabled="disabled" variant="ghost" @click="isConfirmOpen = false">Cancel</Button>
        <Button :disabled="disabled" variant="danger" @click="revokeAll">Revoke all</Button>
      </template>
    </Modal>
  </Fieldset>
</template>

<script setup>
/**
 * PermissionsSection.vue — grouped permission checkboxes, with a per-group
 * "select all / deselect all" toggle, plus a global "revoke all" action
 * that confirms via Modal.vue before clearing everything.
 *
 * Usage:
 * <PermissionsSection v-model="form.permissions" :permission-groups="permissionGroups" />
 */
import { ref } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import BaseCheckbox from '@/components/BaseCheckbox.vue'
import Button from '@/components/Button.vue'
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
          { label: 'Create', value: 'users.new' },
          { label: 'View', value: 'users.view' },
          { label: 'Edit', value: 'users.edit' },
          { label: 'Edit Permissions', value: 'users.permissions' },
          { label: 'Edit Roles', value: 'users.roles' },
          { label: 'Deactivate', value: 'users.deactivate' },
          { label: 'Activate', value: 'users.activate' },
          { label: 'Block', value: 'users.block' },
          { label: 'Unblock', value: 'users.unblock' },
          { label: 'User\'s documents', value:'users.documents' },
          { label: 'User\'s reports', value:'users.reports' },
        ],
      },
      {
        key: 'tasks',
        label: 'Tasks',
        permissions: [
          { label: 'Create', value: 'tasks.new' },
          { label: 'View', value: 'tasks.view' },
          { label: 'Edit', value: 'tasks.edit' },
          { label: 'Deactivate', value: 'tasks.deactivate' },
          { label: 'Activate', value: 'tasks.activate' },
        ],
      },
      {
        key: 'tenants',
        label: 'Tenants',
        permissions: [
          { label: 'Create', value: 'tenants.new' },
          { label: 'View', value: 'tenants.view' },
          { label: 'Edit', value: 'tenants.edit' },
          { label: 'Deactivate', value: 'tenants.deactivate' },
          { label: 'Activate', value: 'tenants.activate' },
        ],
      },
      {
        key: 'schedule',
        label: 'Schedules',
        permissions: [
          { label: 'Calendar', value: 'schedules.calendar' },
          { label: 'Create', value: 'schedules.new' },
          { label: 'View', value: 'schedules.view' },
          { label: 'Edit', value: 'schedules.edit' },
          { label: 'Deactivate', value: 'schedules.deactivate' },
          { label: 'Activate', value: 'schedules.activate' },
        ],
      },
      {
        key: 'payments',
        label: 'Payments',
        permissions: [
          { label: 'View', value: 'payments.view' },
          { label: 'New', value: 'payments.new' },
          { label: 'Subscriptions', value: 'payments.subscription' },
          { label: 'Extract', value: 'payments.extract' },
          { label: 'Invoices', value: 'payments.invoices' },
        ],
      },
      {
        key: 'store',
        label: 'Store',
        permissions: [
          { label: 'View', value: 'store.view' },
          { label: 'Orders', value: 'store.orders' },
          { label: 'Suppliers', value: 'store.suppliers' },
          { label: 'Deliveries', value: 'store.deliveries' },
          { label: 'Inventory', value: 'store.inventory' },
        ],
      },
      {
        key: 'products',
        label: 'Store',
        permissions: [
          { label: 'View', value: 'products.view' },
          { label: 'New', value: 'products.new' },
          { label: 'Edit', value: 'products.edit' },
          { label: 'Deactivate', value: 'products.deactivate' },
          { label: 'Activate', value: 'products.activate' },
          { label: 'Manage Stock', value: 'products.stock' },
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
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const isConfirmOpen = ref(false)

function togglePermission(value, checked) {
  const next = checked
    ? [...props.modelValue, value]
    : props.modelValue.filter((v) => v !== value)
  emit('update:modelValue', next)
}

function groupSelectedCount(group) {
  const values = group.permissions.map((p) => p.value)
  return props.modelValue.filter((v) => values.includes(v)).length
}

function isGroupFullySelected(group) {
  return group.permissions.length > 0 && groupSelectedCount(group) === group.permissions.length
}

function toggleAll(group, checked) {
  const groupValues = group.permissions.map((p) => p.value)

  const next = checked
    // add every permission in this group that isn't already present
    ? [...props.modelValue, ...groupValues.filter((v) => !props.modelValue.includes(v))]
    // remove every permission that belongs to this group
    : props.modelValue.filter((v) => !groupValues.includes(v))

  emit('update:modelValue', next)
}

function revokeAll() {
  emit('update:modelValue', [])
  isConfirmOpen.value = false
}
</script>