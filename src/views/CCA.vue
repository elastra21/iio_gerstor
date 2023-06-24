<template>
  <v-container fluid style="padding: 0 25px 25px;" class="cca">
    <div class="header-section">
      <h2>Cuerpos, Comités y Actas</h2>
    </div>
    <el-tabs value="first">
      <el-tab-pane label="Cuerpos y Comités" name="first">
        <el-row>
          <el-col :span="24" class="d-flex align-start  mb-4">
            <el-select v-model="ccSelected" placeholder="Cuerpos y Comités">
              <el-option
                v-for="(item, index) in cc"
                :key="item.name"
                :label="item.name"
                :value="index"
              >
              </el-option>
            </el-select>
            <el-button
              size="small"
              type="primary"
              icon="el-icon-plus"
              circle
              class="ml-4 mt-1"
              @click="agregarCC = true"
            />
          </el-col>

          <el-col :span="24" class="d-flex align-center">
            <el-button
              v-if="cc.length > 0"
              class="ml-0"
              type="text"
              @click="editCASName()"
            >
              <i class="el-icon-edit" style="font-size:22px !important" />
            </el-button>
            <h2 class="d-flex my-5 pl-3" style="flex:1" v-if="cc.length > 0">
              <input
                style="flex:1"
                ref="cas"
                type="text"
                :disabled="!editCCName"
                v-model="cc[ccSelected].name"
                v-on:blur="ccNameEdited"
              />
            </h2>

            <!-- <h2 {{  }}</h2> -->
          </el-col>

          <el-col :span="12" style="padding-right: 25px;">
            <el-card class="form">
              <el-form label-position="top">
                <el-form-item label="Nombre de Integrante">
                  <el-input v-model="name" :showError="false" />
                </el-form-item>
                <el-form-item label="Puesto">
                  <el-input v-model="position" :showError="false" />
                </el-form-item>
                <el-form-item>
                  <transition name="el-fade-in-linear">
                    <el-button
                      :disabled="name.length < 1"
                      style="width:120px; float:right"
                      type="success"
                      :icon="`el-icon-${editingIntegrante ? 'edit' : 'plus'}`"
                      @click="addIntegrante()"
                    >
                      {{ editingIntegrante ? "Guardar" : "Agregar" }}
                    </el-button>
                  </transition>
                  <transition name="el-fade-in-linear">
                    <el-button
                      class="ml-0"
                      v-show="editingIntegrante"
                      @click="resetEdit()"
                    >
                      Cancelar
                    </el-button>
                  </transition>
                </el-form-item>
              </el-form>
            </el-card>
          </el-col>
          <el-col :span="12">
            <el-card class="box-card list">
              <el-table v-if="cc.length > 0" :data="cc[ccSelected].integrantes">
                <el-table-column prop="name" label="Nombre"> </el-table-column>
                <el-table-column align="right" label="Operaciones">
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      @click="handleEdit(scope.$index, scope.row)"
                    >
                      Editar
                    </el-button>
                    <el-popconfirm
                      title="¿Estas seguro que quieres borrar este integrante?"
                      v-on:confirm="handleDelete(scope.$index)"
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
                  </template>
                </el-table-column>
              </el-table>
            </el-card>
          </el-col>
          <el-col class="d-flex mt-5 justify-end" :span="24">
            <el-button class="mt-3 " type="primary" @click="replaceData()">
              Guardar Cambios
            </el-button>
          </el-col>
        </el-row>
      </el-tab-pane>
      <el-tab-pane label="Actas" name="second">
        <v-row class="list-wrapper">
          <v-list subheader two-line style="width:100%">
            <!-- <h3>Informes</h3> -->
            <v-list-item v-for="erm in data" :key="erm" target="_blank">
              <v-list-item-avatar>
                <v-icon class="grey lighten-1" dark v-text="getMime(erm)" />
              </v-list-item-avatar>
              <v-list-item-content>
                <span
                  class="list-text body-2 grey-2"
                  v-text="erm.toLowerCase()"
                />
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
      </el-tab-pane>
    </el-tabs>
    <el-dialog
      title="Agregar CC"
      :visible.sync="agregarCC"
      width="30%"
      v-on:close="newCC = ''"
    >
      <span class="d-flex mb-3">Nombre del CC</span>
      <el-input v-model="newCC" class="pr-6" :showError="false" />

      <el-button
        :disabled="newCC.length < 1"
        class="mt-3 "
        type="primary"
        @click="addCC()"
      >
        Guardar
      </el-button>
    </el-dialog>
    <AddPDF path="actas/" />
  </v-container>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import { AddPDF } from "../components/form";

export default {
  name: "CCA",
  components: { AddPDF },
  data: () => ({
    data: [],
    ccSelected: 0,
    editingIndex: -1,
    name: "",
    position: "",
    agregarCC: false,
    newCC: "",
    editingIntegrante: false,
    editCCName: false,
    cc: []
  }),
  methods: {
    deleteFiles(file) {
      const items_to_delete = [file];
      const formData = new FormData();
      formData.append("folder", "actas");
      formData.append("files", JSON.stringify(items_to_delete));
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
        },
        error => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
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
    editCASName() {
      // this.casToEdit = this.cas.name;
      this.editCCName = true;
      this.$nextTick(() => this.$refs.cas.select());
    },
    addIntegrante() {
      const { name, position, ccSelected, editingIndex } = this;
      if (this.editingIntegrante) {
        const this_mother_fucker = this.cc[ccSelected].integrantes[
          editingIndex
        ];
        this_mother_fucker.name = name;
        position ? (this_mother_fucker.positions = position) : null;
        this.editingIndex = -1;
        this.editingIntegrante = false;
      } else {
        const data_to_add = { name };
        position ? (data_to_add.positions = position) : null;
        this.cc[ccSelected].integrantes.push(data_to_add);
      }
      this.name = "";
      this.position = null;
    },
    ccNameEdited() {
      // this.data.ca.find(item =>
      //   item.name === this.casToEdit ? (item.name = this.casToEdit) : null
      // );
      this.editCaName = false;
      // this.casToEdit = "";
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
    },
    addCC() {
      this.cc.push({ name: this.newCC, integrantes: [] });
      const { newCC } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "transparencia");
      formData.append("field", "cca");
      const json = { name: this.newCC, integrantes: [] };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    handleEdit(index, worker) {
      const the_mother_fucker = this.cc[this.ccSelected].integrantes[index];
      this.name = the_mother_fucker.name;
      this.position = the_mother_fucker.positions;
      this.editingIntegrante = true;
      this.editingIndex = index;
    },
    replaceData() {
      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "transparencia");
      formData.append("field", "cca");
      const json = [...this.cc];
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    handleDelete(index) {
      const { ccSelected } = this;
      this.name = "";
      this.position = null;
      if (index !== undefined) this.cc[ccSelected].integrantes.splice(index, 1);
    },
    resetEdit() {
      this.editingIntegrante = false;
      this.name = "";
      this.position = null;
    },
    saveData() {
      const { integrante } = this;
      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "about");
      formData.append("field", "retired");
      const json = { ...integrante };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    deleteData(index) {
      const { editingItem } = this;
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "transparencia");
      formData.append("field", "cca");
      formData.append("index", this.ccSelected);
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
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
    }
  },
  mounted: function() {
    // this.loading = this.$loading();
    this.publicPath = $(location).attr("origin") + "/archivos/actas/";

    this.cc = [...this.$info.transparencia.cca];
    // const cca_consolidado = this.$info.transparencia.cca;
    //
    // cca_consolidado.forEach((cca, i) => {
    //   const category = cca.name;
    //   const tagedData = cca.integrantes.map(integrante => ({
    //     ...integrante,
    //     category,
    //     positions: integrante.positions || "N/A"
    //   }));
    //   this.data = [...this.data, ...tagedData];
    // });
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
.cca {
  position: relative;
}
.list-text {
  text-align: left;
  text-transform: capitalize;
}
.list-wrapper {
  // padding: 0 2vw;
}
</style>
