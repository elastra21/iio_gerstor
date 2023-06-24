<template>
  <div class="fieldWrapper">
    <p class="inputTitle">{{ label }}</p>
    <input
      :type="type"
      :placeholder="promp"
      class="input"
      v-on:input="updateValue($event.target.value)"
      :class="[showError ? 'inputTextError' : 'inputText']"
    />
  </div>
</template>

<script>
export default {
  name: "InputText",
  props: {
    type: String,
    vmodel: String,
    label: String,
    promp: String,
    showError: Boolean
  },
  methods: {
    updateValue: function(value) {
      this.$emit("input", value);
    }
  }
};
</script>
