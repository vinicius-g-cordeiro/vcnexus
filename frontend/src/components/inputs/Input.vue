<script setup="js">
import { ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        required: true
    },
    type: {
        type: String,
        required: true,
        default: 'text'
    },
    placeholder: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    readonly: {
        type: Boolean,
        default: false
    },
    autocomplete: {
        type: String,
        default: 'off'
    },
    autofocus: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    label: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    id: {
        type: String,
        default: ''
    },
    classes: {
        type: String,
        default: ''
    },
    showPassword: {
        type: Boolean,
        default: false
    },
    passwordMeter: {
        type: Boolean,
        default: false
    },
    maxlength: {
        type: Number,
        default: null,
        required: false
    },
    minlength: {
        type: Number,
        default: null,
        required: false
    }
})

const input = ref(null)
let value = ref(props.modelValue)

const emit = defineEmits(['update:modelValue', 'change'])

const updateValue = (e) => {
    value.value = e.target.value
    emit('update:modelValue', value.value)
    emit('change', e)
}


const isPasswordVisible = ref(false)
const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value
    input.value.type = isPasswordVisible.value ? 'text' : 'password'
}

const meter = ref(null)
const meterText = ref(null)

const updatePasswordMeter = () => {
    // check passwrod strength: at least 8 characters, at least one uppercase letter, at least one lowercase letter, at least one number, at least one special character
    const weights = [{
        regex: /(?=.*[a-z])/,
        weight: 25
    },
    {
        regex: /(?=.*[A-Z])/,
        weight: 25
    },
    {
        regex: /(?=.*\d)/,
        weight: 10
    },
    {
        regex: /(?=.*[@$!%*?&])/,
        weight: 15
    },
    {
        regex: /(?=.{12,})/,
        weight: 25
    },
    ]

    let strength = 0
    weights.forEach(weight => {
        if (weight.regex.test(value.value)) {
            strength += weight.weight
        }
    })

    meter.value.style.width = `${strength}%`


    const colors = [{
        min: 0,
        max: 25,
        color: 'bg-tomato-500'
    },
    {
        min: 25,
        max: 50,
        color: 'bg-tomato-400'
    },
    {
        min: 50,
        max: 75,
        color: 'bg-orange-300'
    },
    {
        min: 75,
        max: 100,
        color: 'bg-emerald-500'
    }
    ]

    colors.map(color => {
        if (strength >= color.min && strength <= color.max) {
            meter.value.classList.add(color.color)
        } else {
            meter.value.classList.remove(color.color)
        }
    })


    meterText.value.textContent = strength >= 75 ? 'Strong' : strength >= 50 ? 'Medium' : strength >= 25 ? 'Weak' : 'Very Weak'
}

</script>

<template>
    <div class="flex flex-col gap-1 m-2">
        <label :for="id" class="flex flex-row justify-start items-center gap-1 font-semibold text-zinc-900 dark:text-zinc-50 text-xs align-baseline whitespace-nowrap" :title="label" alt="label">
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
            <input @keypress="type === 'password' && showPassword ? updatePasswordMeter() : null" class="bg-zinc-100 dark:bg-zinc-800 p-1 border border-olive-wood-500 dark:border-zinc-600 focus:outline-olive-wood-500 w-full text-xs" ref="input"
                :type="showPassword ? (isPasswordVisible ? 'text' : 'password') : type" :placeholder="placeholder" :disabled="disabled" :readonly="readonly" :autocomplete="autocomplete" :autofocus="autofocus" :required="required" :name="name" :id="id" :class="classes" :value="modelValue"
                @input="updateValue" :maxlength="maxlength" :minlength="minlength" />
            <template v-if="showPassword">
                <button type="button" @click="togglePasswordVisibility" class="top-1/2 right-2 absolute text-zinc-600 dark:text-zinc-400 -translate-y-1/2 cursor-pointer transform">
                    <i v-if="isPasswordVisible" class="bi bi-eye-slash-fill"></i>
                    <i v-else class="bi bi-eye-fill"></i>
                </button>
            </template>
        </div>
        <template v-if="passwordMeter">
            <canvas class="right-0 bottom-1 left-0 h-1 transition-all duration-500 ease-in-out" ref="meter" @input="updatePasswordMeter"></canvas>
            <span ref="meterText" class="text-zinc-600 dark:text-zinc-400 text-xs">Password strength: <span class="font-semibold text-zinc-900 dark:text-zinc-50">Weak</span></span>
        </template>
        <p v-if="error" class="text-olive-wood-500 text-xs">
            {{ error }}
        </p>
    </div>
</template>