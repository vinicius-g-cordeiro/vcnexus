<script setup="js">
const props = defineProps({
    modelValue: { type: String, required: true },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
    name: { type: String, default: '' },
    id: { type: String, default: '' },
    classes: { type: String, default: '' },
    options: { type: Array, required: true }
})

const emit = defineEmits(['update:modelValue', 'change'])

const updateValue = (e) => {
    emit('update:modelValue', e.target.value)
    emit('change', e)
}
</script>

<template>
    <div class="flex flex-col gap-1 m-2">
        <label :for="id" class="flex flex-row justify-start items-center gap-1 font-semibold text-zinc-900 dark:text-zinc-50 text-xs align-baseline whitespace-nowrap" :title="label" alt="label" >
            <template v-if="label">
                {{ label }}: <span v-if="required" class="font-bold text-[0.3rem] text-tomato-500"><i class="bi bi-asterisk"></i> </span>
            </template>
            <template v-else>
                <slot name="label">
                    {{ label }}
                </slot>
            </template>
        </label>

        <div class="relative">
            <select :value="modelValue" :name="name" :id="id" :disabled="disabled" class="bg-zinc-100 dark:bg-zinc-800 px-3 py-2 border border-olive-wood-500 dark:border-zinc-600 rounded-md focus:outline-olive-wood-500 w-full text-xs" :class="classes" :required="required" :autofocus="autofocus" @change="updateValue">
                <option value="" selected>{{ placeholder }}</option>
                <option v-for="option in options" :key="option.value" :value="option.value">
                    {{ option.label }}
                </option>
            </select>
        </div>
        
        <p v-if="error" class="text-olive-wood-500 text-xs">
            {{ error }}
        </p>
    </div>
</template>