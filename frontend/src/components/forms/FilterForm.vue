<script setup>
import { reactive, watch } from 'vue'
import Input from '@/components/inputs/Input.vue'
import Select from '@/components/inputs/Select.vue'

const props = defineProps({
  fields: { type: Array, required: true },
  modelValue: { type: Object, default: () => ({}) },
})
const emit = defineEmits(['update:modelValue', 'search', 'reset'])

const initial = () =>
  Object.fromEntries(props.fields.map(f => [f.name, props.modelValue[f.name] ?? f.default ?? '']))

const form = reactive(initial())

watch(form, v => emit('update:modelValue', { ...v }), { deep: true })

function reset() {
  Object.assign(form, Object.fromEntries(props.fields.map(f => [f.name, f.default ?? ''])))
  emit('reset')
}
</script>

<template>
  <form class="min-w-0" @submit.prevent="emit('search', { ...form })">
    <div class="items-end gap-x-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
      <div v-for="f in fields" :key="f.name" :class="f.classes">
        <Select v-if="f.type === 'select'" v-model="form[f.name]" :name="f.name" :id="f.name" :label="f.label" :options="f.options" :required="f.required" :placeholder="f.placeholder || 'All'" :autofocus="f.autofocus" :disabled="f.disabled" />
        <Input v-else v-model="form[f.name]" :name="f.name" :id="f.name" :label="f.label" :type="f.type || 'text'" :required="f.required" :placeholder="f.placeholder" :autocomplete="f.autocomplete" :autofocus="f.autofocus" :disabled="f.disabled" :readonly="f.readonly" />
      </div>

      <div class="flex gap-2 m-2">
        <button type="submit" class="bg-olive-wood-500 hover:bg-olive-wood-600 px-4 py-2 rounded-md font-medium text-white text-xs transition">Search</button>
        <button type="button" class="hover:bg-zinc-200 dark:hover:bg-zinc-700 px-4 py-2 border border-zinc-300 dark:border-zinc-600 rounded-md font-medium text-xs transition" @click="reset">Clear</button>
      </div>
    </div>
  </form>
</template>