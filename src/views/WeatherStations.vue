<template>
  <el-row class="weather-container">
    <div class="header-section">
      <h2>Estaciones Meteorológicas</h2>
    </div>
    <el-col :span="24" style="padding-bottom: 25px;">
      <h3 class="mb-3 text-left">Investigación</h3>
      <el-card class="form">
        <el-form label-position="top">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
          <el-form-item label="Investigador">
            <el-input v-model="investigador.name" />
          </el-form-item>
          <el-form-item label="Descripción">
            <el-input type="textarea" v-model="investigador.description" />
          </el-form-item>
          <el-form-item label="Historia">
            <el-input type="textarea" v-model="investigador.history" />
          </el-form-item>
          <el-form-item>
            <el-button :disabled="formValid(investigador)" @click="saveData" type="primary">Guardar</el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
  </el-row>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import ImageInput from "../components/form/imageInput.vue";

export default {
  name: "EstacionesMeteorologicas",
  components: { ImageInput },
  data: () => ({
    editImage: null,
    image: null,
    investigador: {
      name: "",
      description: "",
      history: "",
    },
    estacionesMeteorologicas: {
      description: "",
      locations: [],
    },
    calidadDelAire: {
      description: "",
      urls: [],
    },
  }),
  computed: {
    static_path() {
      return $(location).attr("origin") + "/archivos/estaciones/";
    },
  },
  methods: {
    formValid(data) {
      return !data.name || !data.description || data.history.length < 1;
    },
    saveData() {
      const { investigador, image } = this;
      const starts = new Date(this.starts).toString();
      const ends = new Date(this.ends).toString();
      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "estaciones");
      formData.append("field", "investigador");
      formData.append("path", "../archivos/estaciones/investigador.jpeg");
      const json = { ...investigador};
      if (image !== null) {
        formData.append("image", image);
      }
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },

    async submitRequest(formData) {
    // check form data
    formData.forEach((value, key) => {
      console.log(key + " " + value);

    });
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          // this.reset();
          console.log(response.data);
          setTimeout(() => loading.close(), 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success"
          });
        },
        error => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    reloadWindow() {
      window.location.reload();
    },
  },
  mounted: function() {
    const data = JSON.parse(JSON.stringify(this.$info.estaciones));
    console.warn(data);

    this.investigador = data.investigador;
    this.editImage = this.static_path + data.investigador.image + "?" + new Date().getTime();
    // this.history = data.history;
  },
};
</script>
<style lang="scss" >
.weather-container {
  padding: 0 25px 25px;
  .el-card__body {
    max-width: 100% !important;
  }
}
</style>
