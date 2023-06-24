<template>
  <v-container class="galeria" fluid style="padding: 0 25px 25px;">
    <div class="header-section">
      <h2>Infografías</h2>
      <div style="position:absolute; right:10px">
        <h3 style="pad">{{ gallery.length }}</h3>
        Imagenes
      </div>
    </div>
    <v-item-group v-model="selected" multiple>
      <v-layout row wrap style="padding-top:10px;">
        <v-flex
          style="display:flex; align-items:center; justify-content:center;"
          class=" xs12 sm6 md3 lg2 xl2"
          v-for="(image, index) in gallery"
          :key="index"
        >
          <v-item v-slot="{ active, toggle }">
            <v-card
              height="200"
              width="200"
              hover
              style="margin:10px; padding:10px; position:relative"
              @click="toggle"
            >
              <v-img aspect-ratio="1" position="top center" :src="image.url" />
              <el-checkbox fill="red" class="delete-select" :value="active" />
            </v-card>
          </v-item>
        </v-flex>
        <div
          class="d-flex align-center justify-center"
          style="height:200px; width:100%"
          v-if="gallery.length == 0"
        >
          <h6 class="light">No hay Información para mostrar</h6>
        </div>
      </v-layout>
    </v-item-group>
    <v-fab-transition>
      <v-btn
        class="ma-2 delete-button"
        fab
        dark
        v-show="selected.length > 0"
        target="_blank"
        @click.stop="deleteFiles()"
        color="red"
      >
        <v-badge light :content="selected.length">
          <v-icon>mdi-delete-empty</v-icon>
        </v-badge>
      </v-btn>
    </v-fab-transition>
    <AddImage :onComplete="loadImages" path="infografias/" />
  </v-container>
</template>

<script>
import $ from "jquery";
import axios from "axios";
import { AddImage } from "../components/form";
export default {
  name: "Infografias",
  components: { AddImage },
  data() {
    return {
      publicPath: null,
      gallery: null,
      dialogVisible: false,
      data: null,
      selected: [],
    };
  },
  methods: {
    loadImages() {
      this.publicPath = $(location).attr("origin") + "/archivos/infografias/";
      $.getJSON(this.publicPath, (data) => {
        this.data = data;
        const filtered_data = data.filter(
          (image) =>
            image.includes(".jpeg") ||
            image.includes(".jpg") ||
            image.includes(".png")
        );
        this.gallery = filtered_data.map((image) => ({
          url: this.publicPath + image,
        }));
      });
      this.selected = [];
    },
    handleClose(done) {
      this.$confirm("Are you sure to close this dialog?")
        .then((_) => {
          done();
        })
        .catch((_) => {});
    },
    deleteFiles() {
      const items_to_delete = [];
      this.selected.forEach((images, i) => {
        items_to_delete.push(this.data[images]);
      });
      this.$confirm(
        `Desea Borrar los siguientes archivos?, ${items_to_delete.join(", ")}`,
        "Confirmar"
      )
        .then((_) => {
          const formData = new FormData();
          formData.append("folder", "infografias");
          formData.append("files", JSON.stringify(items_to_delete));
          this.submitRequest(formData);
        })
        .catch((_) => {});
    },
    submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/delete_images.php", formData).then(
        (response) => {
          this.confirm = false;
          //console.log(response.data);
          setTimeout(() => loading.close(), 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
          this.loadImages();
        },
        (error) => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },
  },
  computed: {},
  beforeMount: function() {
    this.loadImages();
  },
  watch: {
    selected: function(value) {
      //  console.warn(value);
    },
  },
};
</script>
<style lang="scss">
.galeria {
  .el-checkbox__inner {
    border: 1px solid gray;
    width: 18px;
    height: 18px;
  }
  .el-checkbox__inner::after {
    margin-left: 1.5px;
    margin-top: 1px;
    height: 8px;
    width: 4px;
  }
  .v-badge__badge {
    width: 25px;
    height: 25px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 12.5px;
    font-size: 14px;
  }
}
.delete-select {
  position: absolute !important;
  right: 10px;
  top: 7px;
}
.delete-button {
  z-index: 100;
  position: fixed;
  bottom: 120px;
  right: 25px;
}
</style>
