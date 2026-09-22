<template>
  <div :class="$attrs.class">
    <label v-if="label" class="form-label" :for="id">{{ label }}:</label>
    <input
      :id="id"
      ref="input"
      v-bind="{ ...$attrs, class: null }"
      class="form-input"
      :class="{ error: error }"
      type="date"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <div v-if="error" class="form-error">{{ error }}</div>
  </div>
</template>

<script>
let counter = 0

export default {
  name: 'DateInput',
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `date-input-${(counter += 1)}`
      },
    },
    error: String,
    label: String,
    modelValue: String,
  },
  emits: ['update:modelValue'],
  methods: {
    focus() {
      this.$refs.input.focus()
    },
  },
}
</script>
