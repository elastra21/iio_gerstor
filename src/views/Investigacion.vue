<template>
  <el-row class="edit-banner investigacion">
    <div class="header-section mb-1">
      <h2>Investigación</h2>
    </div>
    <el-col :span="24" class="d-flex align-center mb-4">
      <el-select
        v-model="dataIndex"
        value-key="title"
        class="pr-0"
        placeholder="Investigación"
        v-on:change="onResearchChange"
      >
        <el-option
          v-for="(item, index) in investigaciones"
          :key="item.title"
          :label="item.title"
          :value="index"
        >
        </el-option>
      </el-select>
    </el-col>

    <el-col :span="24">
      <el-form label-position="top">
        <el-tabs
          type="border-card"
          @tab-click="handleTabClick"
          style="min-height:80vh;"
        >
          <el-tab-pane label="Información" class="form">
            <el-form-item label="Título">
              <el-input v-model="title" :showError="false"></el-input>
            </el-form-item>
            <el-form-item label="Descripción">
              <el-input
                type="textarea"
                v-model="description"
                :showError="false"
              />
            </el-form-item>
            <el-form-item label="Visión">
              <el-input type="textarea" v-model="vision" :showError="false" />
            </el-form-item>
            <el-form-item label="Misión">
              <el-input type="textarea" v-model="mision" :showError="false" />
            </el-form-item>
          </el-tab-pane>
          <el-tab-pane label="Cuerpos Académicos">
            <h2 class="d-flex">Cuerpos Académicos</h2>
            <el-row>
              <el-col :span="24" class="mb-4 d-flex align-center ">
                <el-select
                  v-model="indexCAS"
                  class="pr-0"
                  placeholder="Cuerpo Académico"
                  @change="onCAChange($event)"
                >
                  <el-option
                    v-for="(item, index) in data.ca"
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
                  class="ml-4"
                  @click="agregarCA = true"
                />
              </el-col>
            </el-row>
            <el-row>
              <el-col :span="24" class="d-flex align-center">
                <el-button
                  v-if="cas"
                  class="ml-0"
                  type="text"
                  @click="editCASName()"
                >
                  <i class="el-icon-edit" style="font-size:22px !important" />
                </el-button>
                <h2 class="d-flex my-5 pl-3" style="flex:1" v-if="cas.name">
                  <input
                    style="flex:1"
                    ref="cas"
                    type="text"
                    :disabled="!editCaName"
                    v-model="cas.name"
                    v-on:blur="casNameEdited"
                  />
                </h2>

                <!-- <h2 {{  }}</h2> -->
              </el-col>
              <el-col :span="12" style="padding-right: 25px;">
                <el-card class="form">
                  <el-form-item label="Investigador">
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
                        :disabled="
                          typeof integrante.id === 'string' || cas.length == 0
                        "
                        style="width:120px; float:right"
                        type="success"
                        :icon="`el-icon-${editingIntegrante ? 'edit' : 'plus'}`"
                        @click="AddIntegrante()"
                      >
                        {{ editingIntegrante ? "Guardar" : "Agregar" }}
                      </el-button>
                    </transition>
                    <transition name="el-fade-in-linear">
                      <el-button
                        v-show="editingIntegrante"
                        @click="resetEdit()"
                      >
                        Cancelar
                      </el-button>
                    </transition>
                  </el-form-item>
                </el-card>
              </el-col>
              <el-col :span="12">
                <el-card class="box-card list">
                  <div slot="header" class="clearfix">
                    <span>Integrantes</span>
                  </div>
                  <el-table v-if="cas.integrantes" :data="cas.integrantes">
                    <el-table-column label="Nombre">
                      <template slot-scope="scope">
                        {{ getName(getWorker(scope.row.id)) }}
                      </template>
                    </el-table-column>
                    <el-table-column align="right" label="Operaciones">
                      <template slot-scope="scope">
                        <el-button
                          size="mini"
                          @click="handleWorkerEdit(scope.$index, scope.row)"
                        >
                          Editar
                        </el-button>
                        <el-button
                          size="mini"
                          type="danger"
                          @click="confirmDelete(scope.$index, 'ca')"
                        >
                          Eliminar
                        </el-button>
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
                </div>
              </el-card>
              <br />

              <el-card class="box-card">
                <div slot="header" class="clearfix">
                  <span>Resultado</span>
                </div>
                <pre class="json">{{ JSON.stringify(this.model, null, 2).trim() }}</pre> -->
                </el-card>
              </el-col>
            </el-row>
          </el-tab-pane>
          <el-tab-pane label="Técnicos Académicos">
            <el-col :span="24" class="d-flex align-center">
              <h2>Técnicos Académicos</h2>
              <el-button
                size="small"
                type="primary"
                icon="el-icon-plus"
                circle
                class="ml-4"
                @click="agregarTA = true"
              />
            </el-col>
            <el-col :span="24">
              <el-card class="box-card list">
                <!-- <div slot="header" class="d-flex align-center">

                </div> -->
                <el-table v-if="tas" :data="tas">
                  <el-table-column label="Nombre">
                    <template slot-scope="scope">
                      {{ getName(getWorker(scope.row)) }}
                    </template>
                  </el-table-column>
                  <el-table-column align="right" label="Operaciones">
                    <template slot-scope="scope">
                      <el-button
                        size="mini"
                        type="danger"
                        @click="confirmDelete(scope.$index, 'ta')"
                      >
                        Eliminar
                      </el-button>
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
                </div>
              </el-card>
              <br />

              <el-card class="box-card">
                <div slot="header" class="clearfix">
                  <span>Resultado</span>
                </div>
                <pre class="json">{{ JSON.stringify(this.model, null, 2).trim() }}</pre> -->
              </el-card>
            </el-col>
          </el-tab-pane>
          <el-tab-pane label="Tesistas Activos">
            <h2 class="d-flex">Tesistas Activos</h2>
            <el-col :span="12" style="padding-right: 25px;">
              <el-card
                class="d-flex px-12 py-5"
                style="text-align:left; max-height:900px; overflow-y:scroll;"
              >
                <ul>
                  <li v-for="(item, index) in tesistas" :key="item">
                    {{ item }}
                    <div class="d-flex mt-3 justify-end">
                      <el-button
                        class="mr-2"
                        size="mini"
                        style="width:70px;"
                        @click="handleEditList(index, 'tesistas')"
                      >
                        Editar
                      </el-button>
                      <el-popconfirm
                        title="¿Estas seguro que quieres borrar este articulo?"
                        v-on:confirm="handleDeleteList(index, 'tesistas')"
                      >
                        <el-button
                          slot="reference"
                          type="danger"
                          plain
                          size="mini"
                          style="width:70px;"
                        >
                          Eliminar
                        </el-button>
                      </el-popconfirm>
                    </div>
                  </li>
                </ul>
              </el-card>
            </el-col>
            <el-col :span="12">
              <el-card class="form">
                <h3>Agregar Tesista</h3>
                <el-form-item label="Tesista e información">
                  <el-input
                    type="textarea"
                    v-model="tesista"
                    :showError="false"
                  />
                </el-form-item>

                <transition name="el-fade-in-linear">
                  <el-button
                    :disabled="tesista.length < 1"
                    style="width:120px; float:right"
                    type="success"
                    :icon="`el-icon-${editingtesista ? 'edit' : 'plus'}`"
                    @click="handleAddList('tesista', 'tesistas')"
                  >
                    {{ editingtesista ? "Guardar" : "Agregar" }}
                  </el-button>
                </transition>
                <transition name="el-fade-in-linear">
                  <el-button
                    v-show="editingtesista"
                    @click="handleCancelList('tesista')"
                  >
                    Cancelar
                  </el-button>
                </transition>
              </el-card>
            </el-col>
          </el-tab-pane>
          <el-tab-pane label="Proyectos Vigentes">
            <h2 class="d-flex">Proyectos Vigentes</h2>
            <el-col :span="12" style="padding-right:25px">
              <el-card
                class="d-flex px-9 py-5"
                style="text-align:left; max-height:900px; overflow-y:scroll;"
              >
                <ul>
                  <li v-for="(item, index) in proyectos" :key="item">
                    {{ item }}
                    <div class="d-flex mt-3 justify-end">
                      <el-button
                        class="mr-2"
                        size="mini"
                        style="width:70px;"
                        @click="handleEditList(index, 'proyectos')"
                      >
                        Editar
                      </el-button>
                      <el-popconfirm
                        title="¿Estas seguro que quieres borrar este articulo?"
                        v-on:confirm="handleDeleteList(index, 'proyectos')"
                      >
                        <el-button
                          slot="reference"
                          type="danger"
                          plain
                          size="mini"
                          style="width:70px;"
                        >
                          Eliminar
                        </el-button>
                      </el-popconfirm>
                    </div>
                  </li>
                </ul>
              </el-card>
            </el-col>
            <el-col :span="12">
              <el-card class="form">
                <h3>Agregar Proyecto</h3>
                <el-form-item label="Proyecto e información">
                  <el-input
                    type="textarea"
                    v-model="proyecto"
                    :showError="false"
                  />
                </el-form-item>

                <transition name="el-fade-in-linear">
                  <el-button
                    :disabled="proyecto.length < 1"
                    style="width:120px; float:right"
                    type="success"
                    :icon="`el-icon-${editingproyecto ? 'edit' : 'plus'}`"
                    @click="handleAddList('proyecto', 'proyectos')"
                  >
                    {{ editingproyecto ? "Guardar" : "Agregar" }}
                  </el-button>
                </transition>
                <transition name="el-fade-in-linear">
                  <el-button
                    v-show="editingproyecto"
                    @click="handleCancelList('proyecto')"
                  >
                    Cancelar
                  </el-button>
                </transition>
              </el-card>
            </el-col>
          </el-tab-pane>
          <!-- <el-tab-pane label="Servicios y Labs"> </el-tab-pane> -->
          <!-- <el-tab-pane label="Task">Task</el-tab-pane> -->

          <el-tab-pane label="Publicaciones">
            <h2 class="d-flex">Publicaciones</h2>
            <el-col :span="12" style="padding-right:25px">
              <el-card
                class="d-flex px-9 py-5"
                style="text-align:left; max-height:900px; overflow-y:scroll;"
              >
                <ul>
                  <li v-for="(item, index) in publicacions" :key="item">
                    {{ item }}
                    <div class="d-flex mt-3 justify-end">
                      <el-button
                        class="mr-2"
                        size="mini"
                        style="width:70px;"
                        @click="handleEditList(index, 'publicacions')"
                      >
                        Editar
                      </el-button>
                      <el-popconfirm
                        title="¿Estas seguro que quieres borrar este articulo?"
                        v-on:confirm="handleDeleteList(index, 'publicacions')"
                      >
                        <el-button
                          slot="reference"
                          type="danger"
                          plain
                          size="mini"
                          style="width:70px;"
                        >
                          Eliminar
                        </el-button>
                      </el-popconfirm>
                    </div>
                  </li>
                </ul>
              </el-card>
            </el-col>
            <el-col :span="12">
              <el-card class="form">
                <h3>Agregar Publicacion</h3>
                <el-form-item label="Proyecto e información">
                  <el-input
                    type="textarea"
                    v-model="publicacion"
                    :showError="false"
                  />
                </el-form-item>

                <transition name="el-fade-in-linear">
                  <el-button
                    :disabled="publicacion.length < 1"
                    style="width:120px; float:right"
                    type="success"
                    :icon="`el-icon-${editingpublicacion ? 'edit' : 'plus'}`"
                    @click="handleAddList('publicacion', 'publicacions')"
                  >
                    {{ editingpublicacion ? "Guardar" : "Agregar" }}
                  </el-button>
                </transition>
                <transition name="el-fade-in-linear">
                  <el-button
                    v-show="editingpublicacion"
                    @click="handleCancelList('publicacion')"
                  >
                    Cancelar
                  </el-button>
                </transition>
              </el-card>
            </el-col>
          </el-tab-pane>
        </el-tabs>
        <el-form-item class="mt-6">
          <el-button
            style="width:120px"
            type="success"
            :icon="'el-icon-edit'"
            @click="submitForm('ruleForm')"
          >
            Guardar
          </el-button>
        </el-form-item>
      </el-form>
    </el-col>
    <!-- //////////////Dialog Agregar Integrante a Cuerpo Académico/////////  -->
    <el-dialog
      title="Agregar Integrante"
      :visible.sync="dialogVisible"
      width="30%"
    >
      <el-select
        v-model="integrante.id"
        filterable
        remote
        reserve-keyword
        placeholder="Buscar Investigador"
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

    <!-- //////////////////Dialog Agregar Técnico Académico/////////////  -->
    <el-dialog title="Agregar Técnico" :visible.sync="agregarTA" width="30%">
      <el-select
        v-model="id_tecnico"
        filterable
        remote
        reserve-keyword
        placeholder="Nombre del Investigador"
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
        :disabled="id_tecnico === null"
        class="ml-3"
        type="primary"
        @click="addTA()"
      >
        Guardar
      </el-button>
    </el-dialog>

    <!-- //////////////////Dialog Agregar Cuerpo Académico/////////////  -->
    <el-dialog
      title="Agregar Cuerpo Académico"
      :visible.sync="agregarCA"
      width="30%"
      v-on:close="newCA = ''"
    >
      <span class="d-flex mb-3">Nombre del CA</span>
      <el-input v-model="newCA" class="pr-6" :showError="false" />

      <el-button
        :disabled="newCA.length < 1"
        class="mt-3 "
        type="primary"
        @click="addCA()"
      >
        Guardar
      </el-button>
    </el-dialog>

    <!-- //////////////////Dialog Confirmar/////////////  -->

    <el-dialog width="350px" :visible.sync="confirm">
      <div slot="title" class="clearfix">
        <p style="text-align:left; margin:0">Guardar Cambios</p>
      </div>
      <p style="text-align:left; margin:0">
        Estás seguro que deseas borrar a <br />
        <b v-html="getDeleteName(editingItem)" />
      </p>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelDelete()">Cancelar</el-button>
        <el-button type="primary" @click="handleDelete()">
          Aceptar
        </el-button>
      </span>
    </el-dialog>

    <!-- //////////////////Dialog Confirmar lists/////////////  -->

    <el-dialog width="350px" :visible.sync="confirmList">
      <div slot="title" class="clearfix">
        <p style="text-align:left; margin:0">Guardar Cambios</p>
      </div>
      <p style="text-align:left; margin:0">
        Estás seguro que deseas borrar a <br />
        <b v-html="getDeleteName(editingItem)" />
      </p>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelDelete()">Cancelar</el-button>
        <el-button type="primary" @click="handleDelete()">
          Aceptar
        </el-button>
      </span>
    </el-dialog>
  </el-row>
</template>
<script>
import axios from "axios";
export default {
  name: "banner",
  data: () => ({
    indexCAS: 0,
    newCA: "",
    dataIndex: 0,
    confirmList: false,
    investigaciones: [],
    proyecto: "",
    tesista: "",
    editingproyecto: false,
    editingtesista: false,
    editingpublicacion: false,
    publicacionToEdit: -1,
    proyectoToEdit: -1,
    tesistaToEdit: -1,
    tesistas: [],
    options: null,
    dialogVisible: false,
    loading: false,
    data: null,
    workers: null,
    title: "",
    cas: "",
    tas: null,
    id_tecnico: null,
    description: "",
    mision: "",
    vision: "",
    editCaName: false,
    proyectos: [],
    publicacions: [],
    publicacion: "",
    // casToEdit: "",
    agregarTA: false,
    agregarCA: false,
    editingItem: null,
    editingField: "",
    integrante: { id: "", biografia: "" },
    editingIntegrante: false,
    activeName: "first",
    confirm: false
  }),
  methods: {
    handleAddList(item, field) {
      if (this[`editing${item}`] === true) {
        this[`editing${item}`] = false;
        // console.warn(this[field][this[`${item}ToEdit`]]);
        this[field][this[`${item}ToEdit`]] = this[item];
        this[`${item}ToEdit`] = -1;
      } else this[field].push(this[item]);
      this[item] = "";
    },
    handleCancelList(field) {
      this[`editing${field}`] = false;
      this[field] = "";
    },
    handleEditList(index, field) {
      const textField = field.slice(0, -1);
      this[`editing${textField}`] = true;
      this[`${textField}ToEdit`] = index;
      this[textField] = this[field][index];
    },
    handleDeleteList(index, field) {
      this[field].splice(index, 1);
    },
    addCA() {
      this.data.ca.push({ name: this.newCA, integrantes: [] });
      this.newCA = "";
      this.agregarCA = false;
    },
    addTA() {
      this.data.ta.push(this.id_tecnico);
      this.id_tecnico = null;
      this.agregarTA = false;
    },
    onResearchChange(value) {
      this.data = JSON.parse(JSON.stringify(this.investigaciones[value]));
      this.loadData();
    },
    editCASName() {
      // this.casToEdit = this.cas.name;
      this.editCaName = true;
      this.$nextTick(() => this.$refs.cas.select());
    },
    casNameEdited() {
      // this.data.ca.find(item =>
      //   item.name === this.casToEdit ? (item.name = this.casToEdit) : null
      // );
      this.editCaName = false;
      // this.casToEdit = "";
    },
    remoteMethod(query) {
      // dialogVisible
      // agregarTA
      if (query !== "") {
        this.loading = true;
        setTimeout(() => {
          this.loading = false;
          var filteredWorkers = [];

          if (this.dialogVisible) {
            const integrantesIDS = this.cas.integrantes.map(
              integrante => integrante.id
            );
            filteredWorkers = this.workers.filter(
              el => !integrantesIDS.includes(el)
            );
          } else
            filteredWorkers = this.workers.filter(
              el => !this.tas.includes(el.id)
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
    },
    onCAChange(evt) {
      // console.warn(this.cas);
      this.cas = this.data.ca[this.indexCAS];
      // console.warn(evt);
      this.integrante = {
        id: "",
        biografia: ""
      };
      this.editingIntegrante = false;
    },
    AddIntegrante() {
      if (this.editingIntegrante) {
        this.resetEdit();
      } else {
        this.cas.integrantes.push(this.integrante);
        this.integrante = {
          id: "",
          biografia: ""
        };
      }
    },
    resetEdit() {
      this.editingIntegrante = false;
      this.integrante = { id: "", biografia: "" };
    },
    handleWorkerEdit(index, worker) {
      this.integrante = this.cas.integrantes[index];
      this.editingIntegrante = true;
    },
    confirmDelete(index, field) {
      this.editingItem = index;
      this.editingField = field;
      this.confirm = true;
    },
    cancelDelete() {
      this.confirm = false;
      this.editingField = "";
      this.editingItem = null;
    },
    handleDelete() {
      const { editingItem, editingField, data, cas, getCAIntegrante } = this;
      if (editingField == "ta") {
        //-------> is TA
        data.ta.splice(editingItem, 1);
      } else {
        // -------------------------> is CA
        const investigador = getCAIntegrante(cas);
        const index = cas.integrantes.findIndex(
          item => item.id === investigador
        );
        if (index !== undefined) cas.integrantes.splice(index, 1);
      }
      this.confirm = false;
      this.editingField = "";
    },
    getDeleteName() {
      const { editingItem, data, editingField, cas, getCAIntegrante } = this;

      if (editingItem !== null) {
        const id =
          editingField == "ca" ? getCAIntegrante(cas) : data.ta[editingItem];
        return this.getName(this.getWorker(id));
      } else return null;
    },
    getCAIntegrante(ca) {
      const { data, editingItem } = this;
      const caIndex = data.ca.findIndex(item => item.name == ca.name);
      return data.ca[caIndex].integrantes[editingItem].id;
    },
    getName(worker) {
      return worker ? worker.name + " " + worker.lastName : null;
    },
    getWorker(id) {
      return this.workers.find(worker => id === worker.id) || null;
    },
    handleTabClick(tab, event) {
      // console.log(tab, event);
    },
    saveData() {
      const { name, serv_y_lab, link, ca } = this.data;
      const { title, tas, proyectos, tesistas, mision, dataIndex } = this;
      const { vision, description, publicacions } = this;

      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "investigaciones");
      formData.append("field", dataIndex);
      const json = {
        name,
        serv_y_lab,
        link,
        title,
        ca,
        ta: tas,
        mision,
        vision,
        description,
        proyectos_vigentes: this.encondeList(proyectos),
        tesistas: this.encondeList(tesistas),
        publicaciones: this.encondeList(publicacions)
      };
      // console.warn(Object.keys(json));
      // console.warn("/n");
      // console.warn(json);
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          this.reset();
          //console.log(response.data);
          setTimeout(() => loading.close(), 1000);
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
    },
    submitForm() {
      this.saveData();
    },
    reset() {
      // this.title = "";
      // this.description = "";
      // this.mision = "";
      // this.vision = "";
      this.$forceUpdate();
      window.location.reload();
    },
    decodeList(list) {
      if (list != null) {
        const decoded = list
          .replaceAll("<ul>", "")
          .replaceAll("</ul>", "")
          .replaceAll("<li>", "")
          .split("</li>");
        decoded.pop();
        return decoded;
      } else return [];
    },
    encondeList(list) {
      if (list != null) {
        var htmlList = "<ul>";
        list.forEach((item, i) => (htmlList += `<li>${item}</li>`));
        htmlList += "</ul>";
        return htmlList;
      } else return null;
    },
    loadData() {
      // console.warn(Object.keys(this.data));
      this.tas = this.data.ta;
      this.workers = this.$info.about.personal;
      this.title = this.data.title;
      this.description = this.data.description;
      this.mision = this.data.mision;
      this.vision = this.data.vision;
      this.integrante = { id: "", biografia: "" };
      this.cas = this.data.ca[0];
      this.editingIntegrante = false;
      this.proyecto = "";
      this.tesista = "";
      this.options = null;
      this.id_tecnico = null;
      this.proyectos = this.decodeList(this.data.proyectos_vigentes);
      this.tesistas = this.decodeList(this.data.tesistas);
      this.publicacions = this.decodeList(this.data.publicaciones);
    }
  },
  beforeMount: function() {
    this.investigaciones = JSON.parse(
      JSON.stringify(this.$info.investigaciones)
    );
    this.data = JSON.parse(JSON.stringify(this.investigaciones[0]));
    this.loadData();
    
  }
};
</script>
<style lang="scss" scoped>
.banner {
  width: 100%;
  background-color: white !important;
}
</style>
<style lang="scss">
.investigacion {
  li {
    padding: 15px 0;
    border-bottom: 0.1px rgba(0, 0, 0, 0.1) solid;
    // border-top: 0.1px rgba(0, 0, 0, 0.1) solid;
  }
}
.edit-banner {
  padding: 0 25px 25px;
  .el-card__body {
    max-width: 5000px !important;
  }
}
</style>
