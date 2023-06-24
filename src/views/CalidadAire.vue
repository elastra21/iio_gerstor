<template>
  <el-row class="weather-container">
    <div class="header-section">
      <h2>Calidad del Aire</h2>
    </div>
    <el-col :span="24">
        <h3 class="text-left mb-4">Agregar Nueva estación</h3>
      <el-card>
        <el-form label-position="top">
          <el-form-item  class="text-left" label="Nuevo URL">
            <el-input type="input" v-model="newURL" />
          </el-form-item>
          <el-form-item >
            <el-button
              type="primary"
              @click="saveData"
              :disabled="!newURL"
            >
              Agregar
            </el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
    <el-col class="mt-8" :span="24">
        <h3 class="text-left mb-4">Lista de estaciones </h3>
      <el-card>
        <el-table :data="urls">
          <el-table-column prop="url" label="URL">
            <template slot-scope="scope">
              <a :href="scope.row.url" target="_blank">{{ scope.row }}</a>
            </template>
          </el-table-column>
          <el-table-column label="opciones" align="right">
            <template slot-scope="scope">
              <el-popconfirm
                title="¿Estas seguro que quieres borrar esta estación?"
                v-on:confirm="deleteData(scope.$index)"
              >
                <el-button slot="reference" type="danger" plain size="mini">
                  Eliminar
                </el-button>
              </el-popconfirm>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
    </el-col>
  </el-row>
</template>
<script>
import axios from "axios";

export default {
  name: "CalidadAire",
  data: () => ({
    newURL: "",
    urls: [],
  }),
  methods: {
    deleteURL(index) {
      this.urls.splice(index, 1);
    },
    addEstacion() {
        this.urls.push(this.newURL);
    //   this.$info.estaciones.calidadDelAire.urls = this.urls;
        this.resetForm();
    },
    
    resetForm() {
      this.newURL = "";
    },
    saveData() {
      const { newURL} = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "estaciones");
      formData.append("field", "calidadDelAire");
      formData.append("subField", "urls");
      // const json = { newURL};
      formData.append("data", newURL);
      this.submitRequest(formData);
    },

    deleteData(index) {
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "estaciones");
      formData.append("field", "calidadDelAire");
      formData.append("subField", "urls");
      formData.append("index", index);
      this.submitRequest(formData);
    },

    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          // this.reset();
          // console.log(response.data);
          this.reloadWindow();
          setTimeout(() => {
            loading.close();
            this.reloadWindow();
          }, 1000);
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
  beforeMount: function() {
    const data = JSON.parse(
      JSON.stringify(this.$info.estaciones.calidadDelAire)
    );

    this.urls = data.urls;
  },
};
</script>
<style lang="scss" scoped>
.weather-container {
  padding: 0 25px 25px;
}
</style>
<style lang="scss">
.weather-container {
  padding: 0 25px 25px;
  .el-card__body{
    max-width: 100% !important;
  }
}
</style>
