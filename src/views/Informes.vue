<template>
  <v-container fluid style="padding: 0 25px 25px;" class="informes">
    <div class="header-section">
      <h2>Informes</h2>
    </div>
    <v-row>
      <v-col
        class="col-md-4 col-lg-3 col-xl-2 col-6 thumbnail-container"
        v-for="(pdf, index) in data"
        :key="index"
      >
        <v-card
          height="280"
          width="200"
          hover
          class="thumbnail"
          style="margin:10px; padding:10px; position:relative"
        >
          <el-popconfirm
            title="¿Estas seguro que quieres borrar a este archivo?"
            v-on:confirm="deleteFiles(pdf)"
          >
            <!-- <el-button
              
              type="danger"
              plain
              size="mini"
            >
              Eliminar
            </el-button> -->

            <el-button
              class="ml-2 delete-button"
              slot="reference"
              type="danger"
              icon="el-icon-delete"
              circle
            ></el-button>
          </el-popconfirm>
          <div class="cover-container" @click="goTo(pdf)">
            <canvas class="pdf" :id="`pdf-${index}`" />
          </div>
          <div class="name">
            {{ pdf }}
          </div>
        </v-card>
      </v-col>
    </v-row>
    <AddPDF path="informes/" :onComplete="loadPdfs" />
  </v-container>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import { AddPDF } from "../components/form";

export default {
  name: "Informes",
  components: { AddPDF },
  props: {},
  data() {
    return {
      publicPath: null,
      data: [],
      loading: null,
      width: 200,
      height: 200,
    };
  },
  methods: {
    deleteFiles(file) {
      const items_to_delete = [file];
      const formData = new FormData();
      formData.append("folder", "informes");
      formData.append("files", JSON.stringify(items_to_delete));
      this.submitRequest(formData);
      // this.selected.forEach((images, i) => {
      //   items_to_delete.push(this.data[images]);
      // });
      // this.$confirm(
      //   `Desea Borrar los siguientes archivos?, ${items_to_delete.join(", ")}`,
      //   "Confirmar"
      // )
      //   .then(_ => {
      //
      //   })
      //   .catch(_ => {});
    },
    submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/delete_images.php", formData).then(
        (response) => {
          this.confirm = false;
          //console.log(response.data);
          setTimeout(() => {
            loading.close();
            window.location.reload();
          }, 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
          // this.loadImages();
        },
        (error) => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    loadPdfs() {
      this.data = [];
      this.loading = this.$loading();
      this.publicPath = $(location).attr("origin") + "/archivos/informes/";
      $.getJSON(this.publicPath, (data) => {
        data.map((image) => {
          if (!image.match("index.php")) {
            this.data.push(image);
          }
        });
      }).then(() =>
        this.data.map((pdf_url, index) => {
          this.renderThumbnails(this.publicPath + pdf_url, index);
        })
      );
    },
    goTo(pdf_path) {
      window.location = this.publicPath + pdf_path;
    },
    async renderThumbnails(pdf_url, index) {
      import("pdfjs-dist").then(async (pdfjsLib) => {
        pdfjsLib.GlobalWorkerOptions.workerSrc =
          "https://cdn.jsdelivr.net/npm/pdfjs-dist@2.0.489/build/pdf.worker.min.js";
        let pdf = await pdfjsLib.getDocument({ url: pdf_url });
        const page = await pdf.getPage(1);
        let viewport = page.getViewport({ scale: 1.0 });
        viewport = page.getViewport({ scale: this.width / 612 });
        let canvas = document.getElementById(`pdf-${index}`);
        canvas.height = viewport.viewBox[3];
        canvas.width = viewport.viewBox[2];
        const context = canvas.getContext("2d");
        await page
          .render({ canvasContext: context, viewport: viewport })
          .then(() => {
            if (index == this.data.length - 1) {
              this.loading.close();
            }
          });
      });
    },
  },
  mounted: function() {
    this.loadPdfs();
  },
};
</script>
<style lang="scss" scoped>
.informes {
  position: relative;
}
.delete-button {
  position: absolute;
  top: -20px;
  right: -20px;
  width: 40px;
  height: 40px;
}
.thumbnail-container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.thumbnail {
  display: flex;
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
