<template>
  <div class="fieldWrapper">
    <p class="inputTitle">{{ label }}</p>
    <!-- <v-combobox
      v-on:input="updateValue($event.target.value)"
      :items="items"
      :placeholder="promp"
      class="input"
      solo
      flat
      :class="[showError ? 'inputTextError' : 'inputText']"
      multiple
    ></v-combobox> -->
    <el-select
      v-on:input="updateValue($event.target.value)"
      multiple
      placeholder="Select"
    >
      <el-option
        v-for="item in options"
        :key="item.value"
        :label="item.label"
        :value="item.value"
      >
      </el-option>
    </el-select>
  </div>
</template>

<script>
export default {
  name: "ComboBox",
  props: {
    label: String,
    promp: String,
    showError: Boolean,
    items: [String]
  },
  methods: {
    updateValue: function(value) {
      this.$emit("input", value);
    }
  }
};
</script>
