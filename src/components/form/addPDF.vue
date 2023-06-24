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
            <v-icon>mdi-plus</v-icon>
            <input
              class="file-input"
              type="file"
              ref="file"
              accept="application/pdf"
              @input="onFileChange($event.target.name, $event.target.files)"
            />
          </v-btn>
        </v-fab-transition>
      </template>
      <span class="header-4-alt white--text">Agregar archivo</span>
    </v-tooltip>
    <v-dialog persistent v-model="errorDialog" max-width="300">
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
        <h3 class="mb-5">Confirmar Documemto</h3>
        <v-card
          height="280"
          width="200"
          hover
          class="thumbnail"
          style="margin:10px; padding:10px; position:relative"
        >
          <div class="cover-container">
            <canvas class="pdf" :id="`pdf-preview`" />
          </div>
          <div class="name">
            {{ this.imageName }}
          </div>
        </v-card>
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
    imageName: "",
    imageFile: null,
    confirmUpload: false
  }),
  props: {
    value: Object,
    path: String,
    onComplete: Function
  },
  mounted: function() {
    this.initialState = this.$refs.file.files;
  },
  methods: {
    saveData() {
      const { files } = this.$refs.file;
      const { imageName, path } = this;
      this.confirmUpload = false;
      const formData = new FormData();
      const fileName =
        path === "brigadas/" ? "BRIGADA_DE_SEGURIDAD" : imageName;
      formData.append("pdfName", fileName);
      formData.append("document", files[0]);
      formData.append("path", path);
      this.submitRequest(formData);
    },
    closeModal() {
      this.errorDialog = false;
    },
    launchFilePicker() {
      this.$refs.file.click();
    },
    reset() {
      this.$refs.file.files = this.initialState;
      this.confirmUpload = false;
    },
    async loadPreview() {
      import("pdfjs-dist").then(async pdfjsLib => {
        let pdf = await pdfjsLib.getDocument({ url: this.imageFile });
        const page = await pdf.getPage(1);
        let viewport = page.getViewport({ scale: 1.0 });
        viewport = page.getViewport({ scale: this.width / 612 });
        let canvas = document.getElementById(`pdf-preview`);
        canvas.height = viewport.viewBox[3];
        canvas.width = viewport.viewBox[2];
        const context = canvas.getContext("2d");
        await page
          .render({ canvasContext: context, viewport: viewport })
          .then(() => {
            // this.loading.close();
          });
      });
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      formData.forEach((value, key) => {
        console.log(key + " " + value);
      });

      axios.post("/admin/upload_image.php", formData).then(
        response => {
          this.confirm = false;
          console.log(response.data);
          setTimeout(() => {
            loading.close();
            this.onComplete();
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
    onFileChange(fieldName, file) {
      let imageFile = file[0];

      if (file.length > 0) {
        if (!imageFile.type.match("application/pdf")) {
          // check whether the upload is an image
          this.errorDialog = true;
          this.errorText = "Por favor seleccione un Archivo en formato PDF";
        } else {
          const reader = new FileReader();
          reader.onload = e => {
            this.imageFile = e.target.result;
            this.imageName = imageFile.name;
            this.loadPreview();
            console.warn(this.imageName);
          };
          reader.readAsDataURL(file[0]);
          this.confirmUpload = true;
          this.$emit("input", imageFile);
          // this.$emit("input", { imageFile, imageURL });
        }
      }
    }
  }
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
.thumbnail {
  display: flex;
  align-self: center;
  justify-content: center;
  max-width: 200px;
  max-height: 280px;
  .cover-container {
    display: flex;
    justify-content: center;
    overflow: hidden;
    max-width: 155px;
    border-radius: 2px;
    height: 200px;
    border: 0.5px lightgrey solid;
  }
  .name {
    position: absolute;
    bottom: 20px;
    left: 0px;
    right: 0px;
    margin: 0 auto;
    font-size: 14px;
  }
}
.pdf {
  opacity: 0.8;
  transform: rotate(180deg) scale(-1, 1);
}
</style>
