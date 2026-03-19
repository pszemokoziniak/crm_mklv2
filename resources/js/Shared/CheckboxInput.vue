<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <div class="flex items-center mt-2">
      <input :id="id" ref="input" v-bind="{ ...$attrs, class: null }" class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out" type="checkbox" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)" />
      <span v-if="description" class="ml-2 text-sm text-gray-600">{{ description }}</span>
    </div>
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'

export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `checkbox-input-${uuid()}`
      },
    },
    modelValue: [Boolean, Number],
    label: String,
    description: String,
    error: String,
  },
  emits: ['update:modelValue'],
}
</script>
