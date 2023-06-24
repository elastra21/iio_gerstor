<template>
  <el-row class="array-editor">
    <div class="header-section">
      <h2>
        Slider
      </h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card class="form">
        <div class="photoWrapper">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
        </div>
        <el-form>
          <el-form-item label="Título">
            <el-input v-model="title" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Link">
            <el-input v-model="link" :showError="false" />
          </el-form-item>
          <el-form-item label="Descripción">
            <el-input
              type="textarea"
              v-model="description"
              :showError="false"
            />
          </el-form-item>
          <el-form-item>
            <transition name="el-fade-in-linear">
              <el-button
                style="width:120px"
                type="success"
                :icon="editing ? 'el-icon-edit' : 'el-icon-plus'"
                @click="submitForm('ruleForm')"
              >
                {{ editing ? "Guardar" : "Agregar" }}
              </el-button>
            </transition>
            <transition name="el-fade-in-linear">
              <el-button v-show="editing" @click="reset()">
                Cancelar
              </el-button>
            </transition>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="box-card list">
        <div slot="header" class="clearfix">
          <span>Páginas</span>
        </div>
        <el-table :data="data.home.slider" style="width: 100%">
          <el-table-column prop="title" label="Título"> </el-table-column>
          <el-table-column align="right" label="Operaciones">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="handleEdit(scope.$index, scope.row)"
              >
                Editar
              </el-button>
              <el-button
                size="mini"
                type="danger"
                @click="confirmDelete(scope.$index)"
              >
                Eliminar
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
      <br />
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Resultado</span>
        </div>
        <pre class="json">{{ JSON.stringify(this.model, null, 2).trim() }}</pre>
      </el-card>
    </el-col>
    <el-dialog width="350px" :visible.sync="confirm">
      <div slot="title" class="clearfix">
        <p style="text-align:left; margin:0">Guardar Cambios</p>
      </div>
      <p style="text-align:left; margin:0">
        Estás seguro que deseas borrar a <br />
        <b v-html="getName(editingItem)" />
      </p>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelDelete()">Cancelar</el-button>
        <el-button type="primary" @click="deleteData()">
          Aceptar
        </el-button>
      </span>
    </el-dialog>
  </el-row>
</template>

<script>
import $ from "jquery";
import axios from "axios";
import { Option } from "element-ui";
import data from "../data/home.json";
import JsonEditor from "vue-json-ui-editor";
import { InputText, ComboBox, ImageInput } from "../components/form";

export default {
  data: () => ({
    schema: require("@/schema/home"),
    editImage: null,
    image: null,
    data: null,
    editing: false,
    editingItem: null,
    title: "",
    description: "",
    link: "",
    confirm: false,
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
    normalizeString(name) {
      return name
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace("dr. ", "")
        .replace("dra. ", "")
        .replace(/ /g, "_");
    },
    getName() {
      const { editingItem, data } = this;
      if (editingItem) {
        const { title } = data.home.slider[editingItem];
        return title;
      } else return "";
    },
    confirmDelete(index) {
      this.editingItem = index;
      this.confirm = true;
    },
    validateFields() {
      return (
        (this.title.length > 0 || this.description.length > 0) &&
        this.link.length > 0 &&
        (this.image != null || this.editing)
      );
    },
    cancelDelete() {
      this.confirm = false;
      this.editingItem = null;
    },
    saveData() {
      const { title, description, link, image } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "home");
      formData.append("field", "slider");
      const json = { title, description, link };
      if (image !== null) {
        formData.append("image", image);
        json.image = this.normalizeString(title) + ".jpg";
      } else json.image = "";
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    editData() {
      const { editingItem } = this;
      const { title, description, link, image } = this;
      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "home");
      formData.append("field", "slider");
      formData.append("index", editingItem);
      const json = { title, description, link };
      if (image !== null) {
        formData.append("image", image);
        json.image = this.normalizeString(title) + ".jpg";
      } else {
        const slide = this.data.home.slider[editingItem];
        json.image = slide.image.length > 0 ? slide.image : "";
      }
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    deleteData(index) {
      const { editingItem } = this;
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "home");
      formData.append("field", "slider");
      formData.append("index", editingItem);
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          this.reset();
          //console.log(response.data);
          setTimeout(() => {
            loading.close();
            window.location.reload();
          }, 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success"
          });
        },
        error => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => {
            loading.close();
          }, 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    submitForm() {
      if (this.validateFields()) {
        if (!this.editing) {
          const { title, description, link } = this;
          this.saveData();
          this.model.slider.push({ title, description, link });
          // console.warn(this.model);
          this.data.home = { ...this.data.home, ...this.model };
          //console.warn(this.data);
          this.reset();
        } else {
          this.editData();
          this.reset();
        }
      } else this.$notify.error("Favor de llenar todos los campos.");
    },
    reset() {
      this.$refs.refImage.reset();
      this.editing = false;
      this.editingItem = null;
      this.title = "";
      this.description = "";
      this.link = "";
      this.editImage = null;
      this.image = null;
    },
    handleEdit(index, slide) {
      this.editing = true;
      this.editingItem = index;
      this.title = slide?.title;
      this.description = slide?.description;
      this.link = slide?.link;
      slide.image.length > 0
        ? (this.editImage =
            $(location).attr("origin") + "/archivos/slider/" + slide.image + "?" + new Date().getTime())
        : null;
    }
  },
  beforeMount: function() {
    this.data = JSON.parse(JSON.stringify(this.$info));
    if (this.$route.params.page) {
      this.schema = require("@/schema/" + this.$route.params.page);
    }
    this.model.slider = [...this.data.home.slider];
    // console.warn(this.model);
  },
  components: { JsonEditor, InputText, ComboBox, ImageInput },
  watch: {
    "$route.params.page": {
      handler: function(data) {
        //console.log(data);
        if (data === undefined) {
          this.schema = require("@/schema/home");
          this.model = {
            version: {
              edited: ""
            }
          };
        } else {
          //console.log("corrio");
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

<style lang="scss">
.el-card__body {
  max-width: 1000px;
  overflow-x: scroll;
}
.array-editor {
  max-width: 100%;
  position: relative;
  padding: 0 25px 25px;

  .form {
    text-align: left;
    width: 100%;
    margin: 0 !important;
  }
  .list {
    .el-card__body {
      padding-top: 0px;
    }
    .el-card__header {
      border-bottom: none;
      padding-bottom: 0;
    }
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
    text-align: left !important;
    overflow-x: scroll !important;
    overflow-y: scroll !important;
  }
  .photoWrapper {
    margin-top: 15px;
    display: flex;
    justify-content: center;
  }
}
</style>
