<template>
  <el-row class="weather-container">
    <div class="header-section">
      <h2>Estaciones Meteorológicas</h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card>
        <el-form label-position="top">
          <el-form-item class="text-left" label="Nombre">
            <el-input v-model="estacion.name" />
          </el-form-item>
          <el-form-item class="text-left" label="URL">
            <el-input v-model="estacion.url" />
          </el-form-item>
          <el-form-item class="text-left" label="Latitud">
            <!-- <el-input  v-model="estacion.location.lat" /> -->
            <div class="el-input">
              <input
                autocomplete="off"
                class="el-input__inner"
                @keydown="validateNumber"
                v-model="estacion.location.lat"
              />
            </div>
          </el-form-item>
          <el-form-item class="text-left" label="Longitud">
            <!-- <el-input @change="validateNumber" v-model="estacion.location.lng" /> -->
            <div class="el-input">
              <input
                autocomplete="off"
                class="el-input__inner"
                @keydown="validateNumber"
                v-model="estacion.location.lng"
              />
            </div>
          </el-form-item>
          <el-form-item>
            <el-button @click="cancelEdit" v-if="isEditing" type="danger">
              Cancelar
            </el-button>
            <el-button :disabled="formValid" @click="saveData" type="primary">
              Guardar
            </el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="box-card list">
        <div slot="header" class="clearfix">
          <h3>Estaciones Meteorológicas</h3>
        </div>
        <el-table v-if="estaciones" :data="estaciones">
          <el-table-column prop="name" label="Nombre">
            <template slot-scope="scope">
              <a :href="scope.row.url" target="_blank">{{ scope.row.name }}</a>
            </template>
          </el-table-column>
          <!-- <el-table-column prop="url" label="URL"></el-table-column> -->
          <el-table-column
            prop="location.lat"
            label="Latitud"
          ></el-table-column>
          <el-table-column
            prop="location.lng"
            label="Longitud"
          ></el-table-column>
          <el-table-column label="opciones" align="right">
            <template slot-scope="scope">
              <el-popconfirm
                title="¿Estas seguro que quieres borrar esta estación?"
                v-on:confirm="deleteData(scope.$index)"
              >
                <el-button
                  slot="reference"
                  type="danger"
                  plain
                  size="mini"
                  class="ml-2"
                >
                  Eliminar
                </el-button>
              </el-popconfirm>
              <el-button
                @click="editEstacion(scope.$index)"
                type="primary"
                size="mini"
              >
                Editar
              </el-button>
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
  name: "Estaciones",
  data: () => ({
    formValid: true,
    estaciones: null,
    isEditing: false,
    editingItemIndex: -1,
    estacion: {
      name: "",
      url: "",
      location: {
        lat: "",
        lng: "",
      },
    },
  }),
  computed: {
    locationStructure: () => ({
      name: "",
      url: "",
      location: {
        lat: "",
        lng: "",
      },
    }),
  },
  methods: {
    validateNumber(e) {
      const { key } = e;
      const { value } = e.target;
      // if delete or backspace, allow input
      if (key === "Backspace" || key.length > 1) {
        return;
      }
      if (!/^\d*\.?\d*$/.test(value + key)) {
        e.preventDefault();
        console.log("Invalid input: Not a number!");
        // Additional actions or error handling can be added here
      } else {
        console.log("Valid input: Number detected!");
        // Additional actions for valid input can be added here
      }
    },
    deleteEstacion(index) {
      console.warn(index);
    },
    editEstacion(index) {
      this.editingItemIndex = index;
      this.estacion = { ...this.estaciones[index] };
      this.isEditing = true;
    },
    resetForm() {
      this.editingItem = -1;
      this.estacion = JSON.parse(JSON.stringify(this.locationStructure));
    },
    saveData() {
      if (this.isEditing) {
        this.editData();
        return;
      }
      const { estacion } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "estaciones");
      formData.append("field", "estacionesMeteorologicas");
      formData.append("subField", "locations");

      formData.append("data", JSON.stringify(estacion));
      this.submitRequest(formData);
    },
    editData() {
      const { editingItemIndex, estacion } = this;
      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "estaciones");
      formData.append("field", "estacionesMeteorologicas");
      formData.append("subField", "locations");
      formData.append("index", editingItemIndex);

      formData.append("data", JSON.stringify(estacion));
      this.submitRequest(formData);
    },

    deleteData(index) {
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "estaciones");
      formData.append("field", "estacionesMeteorologicas");
      formData.append("subField", "locations");
      formData.append("index", index);
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      // check form data
      formData.forEach((value, key) => {
        console.log(key + " " + value);
      });
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        (response) => {
          this.confirm = false;
          // this.reset();
          console.log(response.data);
          setTimeout(() => {
            loading.close();
            this.reloadWindow();
          }, 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
          this.resetForm();
          this.loadData();
        },
        (error) => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    cancelEdit() {
      this.isEditing = false;
      this.resetForm();
    },
    reloadWindow() {
      window.location.reload();
    },
    loadData() {
      const data = JSON.parse(JSON.stringify(this.$info.estaciones));

      this.estaciones = data.estacionesMeteorologicas.locations;
      console.warn(this.estaciones);
    },
  },
  beforeMount: function() {
    this.loadData();
  },
  watch: {
    estacion: {
      handler: function(val, oldVal) {
        this.formValid =
          !val.name || !val.url || !val.location.lat || !val.location.lng;
      },
      deep: true,
    },
  },
};
</script>
<style lang="scss" scoped>
.weather-container {
  padding: 0 25px 25px;
}
</style>
