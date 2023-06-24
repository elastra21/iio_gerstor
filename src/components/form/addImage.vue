<template id="">
  <div class="">
    <v-tooltip left>
      <template v-slot:activator="{ on, attrs }">
        <v-fab-transition>
          <v-btn
            v-bind="attrs"
            v-on="on"
            fab
            class="ma-2 button"
            target="_blank"
            @click="launchFilePicker()"
          >
            <v-icon>{{ edit ? "mdi-pencil" : "mdi-plus" }}</v-icon>
            <input
              class="file-input"
              type="file"
              ref="file"
              accept="image/jpeg"
              @input="onFileChange($event.target.name, $event.target.files)"
            />
          </v-btn>
        </v-fab-transition>
      </template>
      <span class="header-4-alt white--text">Agregar Imagen</span>
    </v-tooltip>
    <v-dialog v-model="errorDialog" persistent max-width="300">
      <v-card class="pt-5 pb-2 px-3">
        <v-card-text class="subheading">{{ errorText }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="closeModal()" text>Aceptar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog
      v-model="confirmUpload"
      @keydown.esc="reset()"
      @click:outside="reset()"
      max-width="450"
    >
      <v-card class="pt-5 pb-2 px-3 d-flex flex-column align-start">
        <h3 class="mb-5">Confirmar Imágen</h3>
        <div
          class="base-image-input"
          :style="{ 'background-image': `url(${imageFile})` }"
        />
        <v-card-actions class="mt-3" style="align-self:flex-end">
          <v-btn @click="reset()" text>Cancelar</v-btn>
          <v-btn @click="saveData()" text>Aceptar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "ScrollToTop",
  data: () => ({
    errorDialog: null,
    errorText: "",
    uploadFieldName: "file",
    maxSize: 1024,
    initialState: null,
    imageURL: null,
    imageFile: null,
    confirmUpload: false,
  }),
  props: {
    value: Object,
    onComplete: Function,
    edit: {
      type: Boolean,
      default: false,
    },
    path: {
      type: String,
      default: "galeria/",
    },
    forcedName: {
      type: String,
      default: null,
    },
  },
  mounted: function() {
    this.initialState = this.$refs.file.files;
  },
  methods: {
    saveData() {
      const { forcedName, path } = this;
      const { files } = this.$refs.file;

      this.confirmUpload = false;

      const formData = new FormData();
      forcedName ? formData.append("imageName", forcedName) : null;
      formData.append("image", files[0]);
      formData.append("path", path);

      this.submitRequest(formData);
    },
    closeModal() {
      this.errorDialog = false;
    },
    launchFilePicker() {
      this.$refs.file.click();
      console.warn(this.$refs.file);
    },
    reset() {
      this.$refs.file.files = this.initialState;
      this.confirmUpload = false;
    },
    async submitRequest(formData) {
      formData.forEach((value, key) => {
        console.log(key + " " + value);
      });
      const loading = this.$loading();
      axios.post("/admin/upload_image.php", formData).then(
        (response) => {
          this.confirm = false;
          console.log(response.data);
          setTimeout(() => {
            loading.close();
            this.reset();
            this.onComplete();
          }, 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
        },
        (error) => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => {
            loading.close();
          }, 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    onFileChange(fieldName, file) {
      const { maxSize } = this;
      let imageFile = file[0];
      if (file.length > 0) {
        let size = imageFile.size / maxSize / maxSize;
        if (!imageFile.type.match("image/jpeg")) {
          // check whether the upload is an image
          this.errorDialog = true;
          this.errorText = "Por favor seleccione una imagen en formato PNG";
        } else if (size > 1) {
          // check whether the size is greater than the size limit
          this.errorDialog = true;
          this.errorText =
            "La imágen es muy pesada, por favor seleccione una imágen menor a 1MB o comprima la imágen";
          this.reset();
        } else {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.imageFile = e.target.result;
          };
          reader.readAsDataURL(file[0]);
          this.confirmUpload = true;
          this.$emit("input", imageFile);
          // this.$emit("input", { imageFile, imageURL });
        }
      }
    },
  },
};
</script>
<style lang="scss" scoped>
.button {
  z-index: 100;
  position: fixed;
  bottom: 45px;
  right: 25px;
  color: "darkblue";
}
.file-input {
  display: none;
}
.base-image-input {
  display: flex;
  align-self: center;
  width: 300px;
  height: 250px;
  cursor: pointer;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 18px;
  overflow: hidden;
}
</style>
