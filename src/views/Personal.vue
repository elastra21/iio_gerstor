<template>
  <el-row class="news">
    <div class="header-section">
      <h2>
        Personal
      </h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card class="form">
        <div class="photoWrapper">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
        </div>
        <el-form label-position="top">
          <el-form-item label="Nombre">
            <el-input v-model="name" :showError="false"></el-input>
          </el-form-item>
          <el-form-item label="Apellido">
            <el-input v-model="lastName" :showError="false"></el-input>
          </el-form-item>
          <el-form-item label="Nivel de SNI">
            <el-input v-model="sni" :showError="false"></el-input>
          </el-form-item>
          <el-form-item label="Email">
            <el-input v-model="email" :showError="false"></el-input>
          </el-form-item>
          <el-form-item label="Posición">
            <el-input v-model="position" :showError="false" />
          </el-form-item>
          <el-form-item label="Nombramiento">
            <el-input v-model="nombramiento" :showError="false" />
          </el-form-item>
          <el-form-item label="Teléfono">
            <el-input v-model="phone" :showError="false" />
          </el-form-item>
          <el-form-item label="Extensión">
            <el-input v-model="ext" :showError="false" />
          </el-form-item>
          <el-form-item label="Cubículo">
            <el-input v-model="workStation" :showError="false" />
          </el-form-item>
          <el-form-item label="Área de Enfoque">
            <el-input v-model="principal" :showError="false" />
          </el-form-item>
          <el-form-item label="Líneas de Investigación">
            <el-select
              v-model="investigationLine"
              filterable
              allow-create
              multiple
              placeholder="Agrega Las líneas"
            >
            </el-select>
          </el-form-item>
          <el-form-item label="Tag">
            <el-select
              v-model="tag"
              filterable
              allow-create
              placeholder="Elige una Etiqueta"
            >
              <el-option
                v-for="item in tags"
                :key="item"
                :label="item"
                :value="item"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="ORCID">
            <el-input v-model="orcid" :showError="false" />
          </el-form-item>
          <el-form-item label="Google Académico">
            <el-input v-model="googleAcademic" :showError="false" />
          </el-form-item>
          <el-form-item label="Researchgate">
            <el-input v-model="researchgate" :showError="false" />
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
          <span>Personal</span>
        </div>
        <el-table :data="data.about.personal">
          <el-table-column label="Nombre">
            <template slot-scope="scope">
              {{ scope.row.name + " " + scope.row.lastName }}
            </template>
          </el-table-column>
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
        <!-- <div style="margin:15px 7px; float:right">
          <el-button
            size="mini"
            type="success"
            icon="el-icon-plus"
            @click="resetDateFilter"
          >
            Agregar
          </el-button>
        </div> -->
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
import JsonEditor from "vue-json-ui-editor";
import { Option } from "element-ui";
import data from "../data/home.json";
import { InputText, ComboBox, ImageInput } from "../components/form";
import axios from "axios";
import $ from "jquery";

export default {
  data: () => ({
    schema: require("@/schema/home"),
    data: null,
    editing: false,
    editingItem: null,
    editImage: null,
    image: null,
    name: "",
    lastName: "",
    position: "",
    email: "",
    phone: "",
    sni: "",
    ext: "",
    tags: [],
    workStation: "",
    investigationLine: [],
    orcid: "",
    googleAcademic: "",
    researchgate: "",
    nombramiento: "",
    principal: "",
    tag: "",
    confirm: false,
    model: {
      version: {
        edited: ""
      }
    }
  }),
  computed: {},
  props: {
    page: {
      type: Object,
      required: false
    }
  },
  methods: {
    validateExtraFields() {
      const json = {
        workStation: this.workStation,
        investigationLine: this.investigationLine,
        orcid: this.orcid,
        googleAcademic: this.googleAcademic,
        researchgate: this.researchgate,
        nombramiento: this.nombramiento,
        principal: this.principal,
        tag: this.tag,
        sni: this.sni
      };
      const keys = Object.keys(json);
      const validated_json = {};
      keys.forEach((key, i) => {
        if (json[key] != null) {
          if (json[key].length > 0) {
            validated_json[key] = json[key];
          }
        }
      });
      return validated_json;
    },
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
        const { name, lastName } = data.about.personal[editingItem];
        return name + " " + lastName;
      } else return "";
    },
    confirmDelete(index) {
      this.editingItem = index;
      this.confirm = true;
    },
    cancelDelete() {
      this.confirm = false;
      this.editingItem = null;
    },
    saveData() {
      const { name, lastName, position, email, phone, ext, tag, image } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "about");
      formData.append("field", "personal");
      var json = { name, lastName, position, email, phone, ext, tag };
      if (image !== "") {
        formData.append("image", image);
        json.image = this.normalizeString(name + " " + lastName) + ".jpg";
      } else json.image = "";
      json = { ...json, ...this.validateExtraFields() };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    editData() {
      const { editingItem } = this;
      const { name, lastName, position, email, phone, ext, tag, image } = this;
      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "about");
      formData.append("field", "personal");
      formData.append("index", editingItem);
      var json = { name, lastName, position, email, phone, ext, tag };
      const worker = this.data.about.personal[editingItem];
      json.id = worker.id;
      if (image !== "") {
        formData.append("image", image);
        json.image = this.normalizeString(name + " " + lastName) + ".jpg";
      } else {
        json.image = worker.image.length > 0 ? worker.image : "";
      }
      json = { ...json, ...this.validateExtraFields() };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    validateFields() {
      //eslint-disable-next-line
      const regexExp = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return (
        this.name.length > 1 &&
        this.lastName.length > 1 &&
        this.tag.length > 2 &&
        regexExp.test(this.email) &&
        this.phone.length > 8
      );
    },
    deleteData(index) {
      const { editingItem } = this;
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "about");
      formData.append("field", "personal");
      formData.append("index", editingItem);
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          //console.log(response.data);
          this.reset();
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
          const { name, lastName, position, email, phone, ext } = this;
          const { tag, image } = this;
          this.saveData();
          this.model.personal.push({
            name,
            lastName,
            position,
            email,
            phone,
            ext
          });
          this.data.about = { ...this.data.about, ...this.model };
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
      this.name = "";
      this.lastName = "";
      this.email = "";
      this.phone = "";
      this.ext = "";
      this.editImage = "";
      this.nombramiento = "";
      this.position = "";
      this.tag = "";
      this.workStation = "";
      this.investigationLine = [];
      this.orcid = "";
      this.googleAcademic = "";
      this.researchgate = "";
      this.principal = "";
      this.tag = "";
      this.sni = "";
      this.image = "";
      this.$forceUpdate();
    },
    handleEdit(index, employee) {
      this.reset();
      this.editing = true;
      this.editingItem = index;
      this.name = employee.name;
      this.lastName = employee.lastName;
      this.email = employee.email;
      this.phone = employee.phone;
      this.ext = employee.ext;
      this.position = employee.position;
      this.tag = employee.tag;
      this.workStation = employee.workStation;
      this.investigationLine = employee.investigationLine;
      this.orcid = employee.orcid;
      this.googleAcademic = employee.googleAcademic;
      this.researchgate = employee.researchgate;
      this.nombramiento = employee.nombramiento;
      this.principal = employee.principal;
      this.tag = employee.tag;
      this.sni = employee.sni;
      employee.image.length > 0
        ? (this.editImage =
            $(location).attr("origin") + "/archivos/personal/" + employee.image + "?" + new Date().getTime())
        : null;
    }
  },
  beforeMount: function() {
    this.data = JSON.parse(JSON.stringify(this.$info));
    this.tags = [
      ...new Set(
        this.$info.about.personal.map(worker =>
          worker.tag.toLowerCase().replace(/^\w/, c => c.toUpperCase())
        )
      )
    ];
    // console.warn(this.$route.params.page);
    if (this.$route.params.page) {
      this.schema = require("@/schema/" + this.$route.params.page);
    }
    this.model.personal = [...this.data.about.personal];
    // console.warn(this.model);
  },
  components: { JsonEditor, InputText, ComboBox, ImageInput },
  watch: {
    image: function(value) {
      // console.warn(value);
    },
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
.el-table::before {
  height: 0px !important;
}
.header-section {
  display: flex;
  padding: 9px 20px;
  position: sticky;
  top: 0px;
  width: calc(100% + 10px);
  margin-left: -25px;
  margin-bottom: 25px;
  background-color: rgba(255, 255, 255, 0.9);
  z-index: 50;
  // box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
  * {
    margin: 0 !important;
  }
}
.news {
  padding: 0 25px 25px;
  .form {
    text-align: left;
    width: 100%;
    margin: 0 !important;
  }
  .el-table {
    max-height: 450px;
    overflow-y: scroll;
  }
  .cell {
    height: 28px;
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
    text-align: left;
    overflow-x: scroll;
    overflow-y: scroll;
    max-height: 450px;
  }
  .photoWrapper {
    margin-top: 15px;
    display: flex;
    justify-content: center;
  }
}
</style>
