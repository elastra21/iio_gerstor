<template>
  <div>
    <el-button
      size="small"
      type="primary"
      icon="el-icon-plus"
      circle
      class="ml-4"
      @click="agregarSeminario = true"
    />
    <el-dialog
      @keydown.esc="reset()"
      title="Agregar nuevo seminario"
      :visible.sync="agregarSeminario"
      width="30%"
      v-on:close="reset()"
    >
      <div>
        <div class="photoWrapper">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
        </div>
        <el-form label-position="top">
          <el-form-item class="text-left" label="Nombre del Seminario">
            <el-input v-model="seminario.name" :showError="false"></el-input>
          </el-form-item>
          <el-form-item class="text-left" label="description">
            <el-input
              v-model="seminario.description"
              :showError="false"
            ></el-input>
          </el-form-item>

          <el-form-item>
            <transition name="el-fade-in-linear">
              <el-button
                :disabled="!valid"
                style="width:120px"
                type="success"
                :icon="'el-icon-save'"
                @click="saveData()"
              >
                Guardar
              </el-button>
            </transition>
          </el-form-item>
        </el-form>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import axios from "axios";
import { ImageInput } from "./";

export default {
  name: "addSeminario",
  components: { ImageInput },
  data() {
    return {
      valid: false,
      agregarSeminario: false,
      errorDialog: null,
      errorText: "",
      image: null,
      editImage: null,
      seminario: {
        name: "",
        image: null,
        path: "",
        description: "",
        videos: [],
      },
    };
  },
  props: {
    value: Object,
  },
  methods: {
    reset() {
      this.agregarSeminario = false;
      this.$refs.refImage.reset();
      this.editImage = null;
      this.image = null;
      this.seminario = {
        name: "",
        image: null,
        path: "",
        description: "",
        videos: [],
      };
    },
    saveData() {
      const { image } = this;
      const seminario = { ...this.seminario };
      // auto generate url path from name that replace spacial caracters and replace accented characters with their non-accented equivalents
      seminario.path = seminario.name
        .toLowerCase()
        .replace(/ /g, "-")
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "");

      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "seminarios");
      formData.append("field", "seminarios");
      formData.append("image", image);
      seminario.image = `${seminario.path}.jpg`;
      formData.append("data", JSON.stringify(seminario));

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
          this.reset();
          console.log(response.data);
          setTimeout(() => {
            loading.close();
            this.$emit("complete");
          }, 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
        },
        (error) => {
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
  },
  watch: {
    seminario: {
      handler(value) {
        this.valid =
          value.name.length > 1 && this.image && value.description.length > 1;
      },
      deep: true,
    },
    image: {
      handler(value) {
        this.valid =
          value &&
          this.seminario.name.length > 1 &&
          this.seminario.description.length > 1;
      },
    },
  },
};
</script>
