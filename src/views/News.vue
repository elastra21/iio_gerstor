<template>
  <el-row class="news">
    <div class="header-section">
      <h2>
        Noticias
      </h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card class="form">
        <div class="photoWrapper">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
        </div>
        <el-form label-position="top">
          <el-form-item label="Título">
            <el-input v-model="title" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Tag">
            <el-input v-model="tag" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Fecha">
            <el-date-picker
              v-model="date"
              type="date"
              placeholder="Pick a day"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
          </el-form-item>

          <!-- <el-form-item label="Fecha">
            <el-input v-model="date" :showError="false"></el-input>
          </el-form-item> -->

          <!-- <el-form-item label="Link">
            <el-input v-model="link" :showError="false" />
          </el-form-item> -->
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
          <span>Noticias</span>
        </div>
        <el-table
          :default-sort="{ prop: 'date', order: 'descending' }"
          :data="data.home.noticias"
          style="width: 100%"
        >
          <el-table-column prop="title" label="Título" />
          <el-table-column
            sortable
            prop="date"
            label="Fecha"
            :sort-method="sortNews"
          />

          <el-table-column align="right" label="Operaciones">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="handleEdit(scope.row.id, scope.row)"
              >
                Editar
              </el-button>
              <el-button
                size="mini"
                type="danger"
                @click="confirmDelete(scope.row.id)"
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
import JsonEditor from "vue-json-ui-editor";
import { Option } from "element-ui";
import data from "../data/home.json";
import { InputText, ComboBox, ImageInput } from "../components/form";
import axios from "axios";

export default {
  data: () => ({
    schema: require("@/schema/home"),
    pickerOptions: {
      disabledDate: time => time.getTime() >= Date.now(),
      shortcuts: [
        {
          text: "Hoy",
          onClick(picker) {
            picker.$emit("pick", new Date());
          }
        },
        {
          text: "Ayer",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit("pick", date);
          }
        },
        {
          text: "Antier",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 2);
            picker.$emit("pick", date);
          }
        }
      ]
    },
    data: null,
    editing: false,
    editingItem: -1,
    editImage: null,
    image: null,
    title: "",
    description: "",
    link: "",
    date: null,
    tag: null,
    confirm: false,
    model: {
      version: {
        edited: ""
      }
    }
  }),
  computed: {},
  methods: {
    sortNews(a, b) {
      const { normalizeDate } = this;
      const aDate = new Date(a);
      const bDate = new Date(b);
      if (aDate < bDate) return 1;
      if (aDate > bDate) return -1;
      return 0;
    },
    // normalizeDate(row, column) {
    //   const date = new Date(row.date);
    //   console.warn(date.getFullYear());
    //   // return `${date.getFullYear()}`;
    //   // return date.toLocaleDateString();
    //   return row.date
    //     .split("/")
    //     .reverse()
    //     .join("/");
    // },
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
      if (editingItem > -1) {
        const { title } = data.home.noticias[editingItem];
        return title;
      } else return "";
    },
    confirmDelete(index) {
      this.editingItem = index;
      this.confirm = true;
    },
    cancelDelete() {
      this.confirm = false;
      this.editingItem = -1;
    },
    validateFields() {
      return (
        this.title.length > 0 &&
        this.description.length > 0 &&
        this.date.toLocaleDateString().length > 7 &&
        (this.image != null || this.editing)
      );
    },

    saveData() {
      const { title, description, tag, date, image, normalizeString } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "home");
      formData.append("field", "noticias");
      const json = { title, content: description, tag, date };
      if (image !== null) {
        formData.append("image", image);
        json.image = normalizeString(`${title}.jpg`);
      } else json.image = "";
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    editData() {
      const { editingItem, normalizeString } = this;
      const { title, description, tag, date, image } = this;
      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "home");
      formData.append("field", "noticias");
      formData.append("index", editingItem);
      const json = { title, content: description, tag, date };
      if (image !== null) {
        formData.append("image", image);
        json.image = normalizeString(`${title}.jpg`);
      } else {
        const noticia = this.data.home.noticias[editingItem];
        json.image = noticia.image.length > 0 ? noticia.image : "";
      }
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    deleteData(index) {
      const { editingItem } = this;
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "home");
      formData.append("field", "noticias");
      formData.append("index", editingItem);
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
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
      console.warn(this.date.toLocaleDateString());
      if (this.validateFields()) {
        if (!this.editing) {
          const { title, description, tag, date } = this;
          this.saveData();
          this.model.noticias.push({ title, content: description, date, tag });
          this.data.home = { ...this.data.home, ...this.model };
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
      this.editingItem = -1;
      this.title = "";
      this.image = null;
      this.editImage = null;
      this.description = "";
      this.tag = "";
      this.date = "";
      this.$forceUpdate();
    },
    handleEdit(index, slide) {
      this.editing = true;
      this.title = slide.title;
      this.description = slide.content;
      this.tag = slide.tag;
      this.date = new Date(slide.date);
      this.editingItem = index;
      slide.image.length > 0
        ? (this.editImage =
            $(location).attr("origin") + "/archivos/noticias/" + slide.image + "?" + new Date().getTime())
        : (this.editImage = null);
    }
  },
  beforeMount: function() {
    this.data = JSON.parse(JSON.stringify(this.$info));
    //console.warn(this.$route.params.page);
    if (this.$route.params.page) {
      this.schema = require("@/schema/" + this.$route.params.page);
    }
    const news = [...this.data.home.noticias];
    news.forEach((noticia, i) => {
      noticia.id = i;
    });
    this.model.news = news;
    //console.warn(this.model);
  },
  components: { JsonEditor, InputText, ComboBox, ImageInput }
};
</script>

<style lang="scss">
.news {
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
