<template>
  <v-container class="configuracion" fluid style="padding: 0 25px 25px;">
    <div class="header-section">
      <h2>Configuración</h2>
    </div>

    <v-row>
      <v-col cols="12">
        <el-card>
          <div style="text-align:left !important" slot="header">
            <b>Versiones</b>
          </div>
          <el-table :data="versions" style="width: 100%">
            <el-table-column prop="name" label="Nombre"> </el-table-column>
            <el-table-column align="right" label="Opciones">
              <template slot-scope="scope">
                <el-button
                  icon="el-icon-download"
                  size="mini"
                  @click="downloadItem(scope.row.name, scope.row.url)"
                >
                  Descargar
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
import $ from "jquery";
import axios from "axios";
export default {
  name: "configuracion",
  data() {
    return {
      versions: null
    };
  },
  methods: {
    loadVersions() {
      const middlePath =
        process.env.NODE_ENV === "development"
          ? "/versions/"
          : "/admin/versions/";
      this.publicPath = $(location).attr("origin") + middlePath;
      $.getJSON(this.publicPath, data => {
        this.data = data;
        this.versions = data.map(version => ({
          url: this.publicPath + version,
          name: version
        }));
        this.versions.reverse();
      });
    },
    downloadItem(label, url) {
      axios
        .get(url, { responseType: "blob" })
        .then(response => {
          const blob = new Blob([response.data], { type: "application/json" });
          const link = document.createElement("a");
          link.href = URL.createObjectURL(blob);
          link.download = label;
          link.click();
          URL.revokeObjectURL(link.href);
        })
        .catch(console.error);
    }
  },
  mounted: function() {
    this.loadVersions();
  }
};
</script>
<style lang="scss">
.configuracion {
  max-width: 100%;
  width: 100%;
  .el-card__body {
    max-width: 100% !important;
    height: 400px;
    max-height: 400px;
  }
}
</style>
