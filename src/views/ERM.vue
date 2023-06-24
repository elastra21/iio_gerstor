<template>
  <v-container fluid style="padding: 0 25px 25px;" class="erm">
    <div class="header-section">
      <h2>Estatutos, Reglamentos y Manuales</h2>
    </div>
    <v-row class="list-wrapper">
      <v-list subheader two-line style="width:100%">
        <!-- <h3>Informes</h3> -->
        <v-list-item v-for="erm in data" :key="erm" target="_blank">
          <v-list-item-avatar>
            <v-icon class="grey lighten-1" dark v-text="getMime(erm)" />
          </v-list-item-avatar>
          <v-list-item-content>
            <span class="list-text body-2 grey-2" v-text="erm.toLowerCase()" />
          </v-list-item-content>
          <v-list-item-action>
            <el-popconfirm
              title="¿Estas seguro que quieres borrar a este archivo?"
              v-on:confirm="deleteFiles(erm)"
            >
              <el-button
                class="ml-2"
                slot="reference"
                type="danger"
                plain
                size="mini"
              >
                Eliminar
              </el-button>
            </el-popconfirm>

            <!-- <v-btn icon>
              <v-icon color="grey lighten-1">mdi-delete-empty</v-icon>
            </v-btn> -->
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-row>
    <AddPDF path="manuales/" />
  </v-container>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import { AddPDF } from "../components/form";

export default {
  name: "ERM",
  components: { AddPDF },
  data: () => ({
    data: []
  }),
  methods: {
    deleteFiles(file) {
      const items_to_delete = [file];
      const formData = new FormData();
      formData.append("folder", "manuales");
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
          // this.loadImages();
        },
        error => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
    getMime: function(file) {
      const normalizedFile = file.toLowerCase();
      const dotIndex = normalizedFile.lastIndexOf(".");
      const mime = normalizedFile.substring(dotIndex, file.length);
      switch (mime) {
        case ".pdf":
          return "mdi-file-pdf-box";
        case ".jpg":
          return "mdi-file-image";
        case ".png":
          return "mdi-file-image";
        case ".pptx":
          return "mdi-file-powerpoint";
        default:
          return "mdi-file-document";
      }
    }
  },
  mounted: function() {
    // this.loading = this.$loading();
    this.publicPath = $(location).attr("origin") + "/archivos/manuales/";
    $.getJSON(this.publicPath, data => {
      data.map(image => {
        if (!image.match("index.php")) {
          this.data.push(image);
        }
      });
    });
  }
};
</script>
<style scoped lang="scss">
.erm {
  position: relative;
}
.list-text {
  text-align: left;
  text-transform: capitalize;
}
.list-wrapper {
  padding: 0 2vw;
}
</style>
