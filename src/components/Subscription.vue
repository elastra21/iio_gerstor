<template>
  <el-row class="editor" :key="this.$route.params.page">
    <div class="header-section">
      <h2>
        {{ this.schema.title }}
      </h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card class="form">
        <json-editor ref="JsonEditor" :schema="schema" v-model="model">
          <el-button type="primary" @click="submit">Subscribe</el-button>
          <el-button type="reset" @click="reset">Reset</el-button>
        </json-editor>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Detalles</span>
        </div>
        <div class="json">
          <!--  d -->
        </div>
      </el-card>
      <br />

      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Resultado</span>
        </div>
        <pre class="json">{{ jsonString }}</pre>
      </el-card>
    </el-col>
  </el-row>
</template>

<script>
import JsonEditor from "vue-json-ui-editor";
import { Option } from "element-ui";

JsonEditor.setComponent("form", "el-form", ({ vm }) => {
  // vm is the JsonEditor VM

  const labelWidth = "120px";
  const model = vm.data;
  const rules = {};

  function parseField(fields) {
    Object.keys(fields).forEach(key => {
      if (key.indexOf("$") === 0 && key !== "$sub") return;
      const field = fields[key];
      if (field.$sub) {
        return parseField(field);
      }
      if (!field.name) return;
      rules[field.name] = [];
      // http://element.eleme.io/#/en-US/component/form#validation
      const type =
        field.schemaType === "array" && field.type === "radio"
          ? "string"
          : field.schemaType;
      const required = field.required;
      const message = field.title;
      const trigger = ["radio", "checkbox", "select"].includes(field.type)
        ? "change"
        : "blur";
      rules[field.name].push({ type, required, message, trigger });

      if (field.minlength !== undefined || field.maxlength !== undefined) {
        const max = field.maxlength || 255;
        const min = field.minlength || 0;
        const msg = `Length must between ${min} and ${max}`;
        rules[field.name].push({ min, max, message: msg, trigger });
      }
    });
  }

  parseField(vm.fields);

  // returning the form props
  return { labelWidth, rules, model };
});

// http://element.eleme.io/#/en-US/component/form#validation
JsonEditor.setComponent("label", "el-form-item", ({ field }) => ({
  prop: field.name
}));

JsonEditor.setComponent("datetime", "el-date-picker");
JsonEditor.setComponent("email", "el-input");
JsonEditor.setComponent("url", "el-input");
JsonEditor.setComponent("number", "el-input-number");
JsonEditor.setComponent("text", "el-input");
JsonEditor.setComponent("textarea", "el-input");
JsonEditor.setComponent("checkbox", "el-checkbox");
JsonEditor.setComponent("checkboxgroup", "el-checkbox-group");
JsonEditor.setComponent("radio", "el-radio");
JsonEditor.setComponent("select", "el-select");
JsonEditor.setComponent("switch", "el-switch");
JsonEditor.setComponent("color", "el-color-picker");
JsonEditor.setComponent("rate", "el-rate");

// You can also use the component object
JsonEditor.setComponent("option", Option);

// By default `<h1/>` is used to render the form title.
// To override this, use the `title` type:
JsonEditor.setComponent("title", "h5");

// By default `<p/>` is used to render the form title.
// To override this, use the `description` type:
JsonEditor.setComponent("description", "small");

// By default `<div/>` is used to render the message error.
// To override this, use the `error` type:
JsonEditor.setComponent("error", "el-alert", ({ vm }) => ({
  type: "error",
  title: vm.error
}));

export default {
  data: () => ({
    schema: require("@/schema/home"),
    model: {
      version: {
        edited: ""
      }
    }
  }),
  computed: {
    jsonString() {
      return JSON.stringify(this.model, null, 2).trim();
    }
  },
  props: {
    page: {
      type: Object,
      required: false
    }
  },
  methods: {
    submit() {
      this.$refs.JsonEditor.form().validate(valid => {
        if (valid) {
          // this.model contains the valid data according your JSON Schema.
          // You can submit your model to the server here

          // eslint-disable-next-line no-console
          console.log("model", JSON.stringify(this.model));
          this.$refs.JsonEditor.clearErrorMessage();
        } else {
          this.$refs.JsonEditor.setErrorMessage(
            "Please fill out the required fields"
          );
          return false;
        }
      });
    },
    reset() {
      this.$refs.JsonEditor.reset();
    }
  },
  beforeMount: function() {
    console.warn(this.$route.params.page);
    if (this.$route.params.page) {
      this.schema = require("@/schema/" + this.$route.params.page);
    }
  },
  components: { JsonEditor },
  watch: {
    "$route.params.page": {
      handler: function(data) {
        console.log(data);
        if (data === undefined) {
          this.schema = require("@/schema/home");
          this.model = {
            version: {
              edited: ""
            }
          };
        } else {
          console.log("corrio");
          this.schema = require("@/schema/" + data);
          this.model = {
            version: {
              edited: ""
            }
          };
        }
      },
      deep: true,
      immediate: true
    }
  }
};
</script>

<style>
h5 {
  display: none !important;
}
.editor {
  padding: 0 25px 25px;
}
.form {
  text-align: left;
  width: 100%;
  margin: 0 !important;
}

h2 {
  font-size: 1.7em;
  text-align: center;
  margin-top: 0;
  margin-bottom: 0.2em;
}

h2 + small {
  display: block;
  text-align: center;
  margin-bottom: 1.2em;
}

small {
  line-height: 20px;
  display: block;
}

.el-alert {
  margin-bottom: 15px;
}

.el-form .sub {
  margin: 0 5%;
}

.json {
  text-align: left;
}
</style>
