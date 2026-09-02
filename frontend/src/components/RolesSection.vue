<template>
  <Fieldset legend="Roles" description="Roles determine which parts of the system this user can access.">
    <Select
      :model-value="modelValue"
      multiple
      label="Assigned roles"
      placeholder="Add roles..."
      :options="roleOptions"
      @update:model-value="$emit('update:modelValue', $event)"
    />

    <ul v-if="selectedRoleDetails.length" class="flex flex-col gap-2 mt-1">
      <li
        v-for="role in selectedRoleDetails"
        :key="role.value"
        class="flex items-start gap-3 bg-neutral-300/50 dark:bg-neutral-700/50 px-3 py-2.5 rounded-md"
      >
        <span class="bg-emerald-500 mt-1.5 rounded-full w-2 h-2 shrink-0" />
        <div>
          <p class="font-medium text-neutral-900 dark:text-neutral-50 text-sm">{{ role.label }}</p>
          <p v-if="role.description" class="text-neutral-500 dark:text-neutral-400 text-xs">
            {{ role.description }}
          </p>
        </div>
      </li>
    </ul>
  </Fieldset>
</template>

<script setup>
/**
 * RolesSection.vue — multi-select role assignment with a summary
 * list of what each selected role means.
 *
 * Usage:
 * <RolesSection v-model="form.roles" :role-options="roles" />
 */
import { computed } from 'vue'
import Fieldset from '@/components/Fieldset.vue'
import Select from '@/components/Select.vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [], // array of role values
  },
  roleOptions: {
    type: Array,
    default: () => [
      { label: 'Administrator', value: 'admin', description: 'Full access to every area of the system.' },
      { label: 'Manager', value: 'manager', description: 'Can manage workers and view reports.' },
      { label: 'Worker', value: 'worker', description: 'Can view and update assigned jobs.' },
      { label: 'Viewer', value: 'viewer', description: 'Read-only access.' },
    ],
  },
})

defineEmits(['update:modelValue'])

const selectedRoleDetails = computed(() =>
  props.roleOptions.filter((r) => props.modelValue.includes(r.value))
)
</script>
