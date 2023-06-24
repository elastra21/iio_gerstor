<template>
  <el-row class="news">
    <div class="header-section">
      <h2>Antiguos Colaboradores</h2>
    </div>
    <el-col :span="12" style="padding-right: 25px;">
      <el-card class="form">
        <el-form label-position="top">
          <el-form-item label="Miembro">
            <div class="d-flex">
              <el-input
                readonly
                :value="getName(getWorker(integrante.id)) || ''"
                :showError="false"
              />
              <el-button
                type="text"
                class="pl-4"
                v-if="!editingIntegrante"
                @click="dialogVisible = true"
              >
                <i class="el-icon-search" />
              </el-button>
            </div>
          </el-form-item>
          <el-form-item label="fecha de retiro">
            
            
            <el-date-picker
              v-model="integrante.fecha"
              type="date"
              placeholder="Pick a day"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
          
              
          </el-form-item>
          
          <el-form-item label="Biografía">
            <el-input
              type="textarea"
              v-model="integrante.biografia"
              :showError="false"
            />
          </el-form-item>

          <el-form-item>
            <transition name="el-fade-in-linear">
              <el-button
                :disabled="typeof integrante.id === 'string'"
                style="width:120px; float:right"
                type="success"
                :icon="`el-icon-${editingIntegrante ? 'edit' : 'plus'}`"
                @click="submitForm()"
              >
                {{ editingIntegrante ? "Guardar" : "Agregar" }}
              </el-button>
            </transition>
            <transition name="el-fade-in-linear">
              <el-button v-show="editingIntegrante" @click="reset()">
                Cancelar
              </el-button>
            </transition>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
    <el-col :span="12">
      <el-card class="box-card list">
        <div slot="header" class="clearfix">
          <span>Antiguos Colaboradores</span>
        </div>
        <el-table :data="data">
          <el-table-column label="Nombre">
            <template slot-scope="scope">
              {{ getName(getWorker(scope.row.id)) }}
            </template>
          </el-table-column>
          <el-table-column align="right" label="Operaciones">
            <template slot-scope="scope">
              <el-button
                size="mini"
                @click="handleEdit(scope.$index, scope.row)"
              >
                Editar
              </el-button>
              <el-popconfirm
                title="¿Estas seguro que quieres borrar a este retirado?"
                v-on:confirm="deleteData(scope.$index)"
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
            </template>
          </el-table-column>
        </el-table>
        <!-- <div style="margin:15px 7px; float:right">
          <el-button
            size="mini"
            type="success"
            icon="el-icon-plus"
            @click="resetDateFilter"
          >
            Agregar
          </el-button>
        </div> -->
      </el-card>
      <br />
    </el-col>
    <el-dialog
      title="Agregar Jubilado"
      :visible.sync="dialogVisible"
      width="30%"
    >
      <el-select
        v-model="integrante.id"
        filterable
        remote
        reserve-keyword
        placeholder="Buscar miembro"
        :remote-method="remoteMethod"
        :loading="loading"
      >
        <el-option
          v-for="item in options"
          :key="item.id"
          :label="item.name + ' ' + item.lastName"
          :value="item.id"
        >
        </el-option>
      </el-select>
      <el-button
        :disabled="typeof integrante.id === 'string'"
        class="ml-3"
        type="primary"
        @click="dialogVisible = false"
      >
        Ok
      </el-button>
    </el-dialog>
  </el-row>
</template>

<script>
import axios from "axios";
export default {
  data: () => ({
    data: [],
    editingIntegrante: false,
    editingIndex: null,
    workers: [],
    loading: false,
    integrante: { id: "", biografia: "", fecha: "" },
    dialogVisible: false,
    options: null
  }),
  computed: {
    pickerOptions: {
      disabledDate: time => time.getTime() >= Date.now(),
      shortcuts: [
        {
          text: "Hoy",
          onClick(picker) {
            picker.$emit("pick", new Date());
          }
        },
        {
          text: "Ayer",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit("pick", date);
          }
        },
        {
          text: "Antier",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 2);
            picker.$emit("pick", date);
          }
        }
      ]
    },
  },
  methods: {
    getName(worker) {
      return worker ? worker.name + " " + worker.lastName : null;
    },
    getWorker(id) {
      //console.warn(id);
      return this.workers.find(worker => id === worker.id) || null;
    },
    // addRetired() {
    //   this.data.push(this.integrante);
    //   this.reset();
    // },
    reset() {
      this.editingIntegrante = false;
      this.loading = false;
      this.integrante = { id: "", biografia: "" };
      this.dialogVisible = false;
      this.editingIndex = null;
    },
    handleEdit(index, employee) {
      this.integrante = this.data[index];
      this.editingIndex = index;
      this.editingIntegrante = true;
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
    editData() {
      const { editingIndex, integrante } = this;
      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "about");
      formData.append("field", "retired");
      formData.append("index", editingIndex);
      const json = { ...integrante };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    deleteData(index) {
      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "about");
      formData.append("field", "retired");
      formData.append("index", index);
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
    },
    submitForm() {
      if (!this.editingIntegrante) this.saveData();
      else this.editData();
      this.reset();
    },

    remoteMethod(query) {
      // dialogVisible
      // agregarTA
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
          var filteredWorkers = [];

          filteredWorkers = this.workers.filter(
            el => !this.data.includes(el.id)
          );

          this.options = filteredWorkers.filter(item => {
            return (
              `${item.name} ${item.lastName}`
                .toLowerCase()
                .indexOf(query.toLowerCase()) > -1
            );
          });
        }, 200);
      } else {
        this.options = [];
      }
    }
  },
  beforeMount: function() {
    // console.warn(this.$route.params.page);
    // if (this.$route.params.page) {
    //   this.schema = require("@/schema/" + this.$route.params.page);
    // }
    this.data = [...this.$info.about.retired];
    this.workers = this.$info.about.personal;
    // console.warn(this.model);
  }
};
</script>

<style lang="scss">
.news {
  padding: 0 25px 25px;
  .form {
    text-align: left;
    width: 100%;
    margin: 0 !important;
  }
  .el-table {
    max-height: 450px;
    overflow-y: scroll;
  }
  .cell {
    height: 28px;
  }
  .list {
    .el-card__body {
      padding-top: 0px;
    }
    .el-card__header {
      border-bottom: none;
      padding-bottom: 0;
    }
  }

  h2 {
    font-size: 1.7em;
    text-align: center;
    margin-top: 0;
    margin-bottom: 0.2em;
  }

  h2 + small {
    display: block;
    text-align: center;
    margin-bottom: 1.2em;
  }

  small {
    line-height: 20px;
    display: block;
  }

  .el-alert {
    margin-bottom: 15px;
  }

  .el-form .sub {
    margin: 0 5%;
  }

  .json {
    text-align: left;
    overflow-x: scroll;
    overflow-y: scroll;
    max-height: 450px;
  }
  .photoWrapper {
    margin-top: 15px;
    display: flex;
    justify-content: center;
  }
}
</style>
