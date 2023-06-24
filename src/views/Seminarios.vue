<template>
  <el-row class="edit-banner investigacion">
    <div class="header-section mb-1">
      <h2>Seminarios</h2>
    </div>
    <el-col :span="24" class="d-flex align-center mb-4"> </el-col>

    <el-col :span="24">
      <el-form label-position="top">
        <el-row>
          <el-col :span="24" class="mb-4 d-flex align-center ">
            <el-select
              v-model="seminario_index"
              class="pr-0"
              placeholder="Cuerpo Académico"
              @change="onSeminarioChanged($event)"
            >
              <el-option
                v-for="(seminario, index) in seminarios"
                :key="seminario.name"
                :label="seminario.name"
                :value="index"
              >
              </el-option>
            </el-select>

            <addSeminario v-on:complete="onSeminarioAdded()"/>
          </el-col>
        </el-row>
        <el-row>
          <el-col :span="24" class="d-flex align-center">
            <div class="photoWrapper">
              <img class="image-edit" :src="editImage" alt="" />
              <AddImage
                class="obligame-perro"
                edit
                path="seminarios/"
                :onComplete="onImageEdited"
                :forced-name="seminario.image"
              />
            </div>
          </el-col>
          <el-col :span="24" class="d-flex align-center">
            <el-button
              v-if="seminario"
              class="ml-0"
              type="text"
              @click="editSeminarioName()"
            >
              <i class="el-icon-edit" style="font-size:22px !important" />
            </el-button>
            <h2 class="d-flex my-5 pl-3" style="flex:1" v-if="seminario.name">
              <input
                style="flex:1"
                ref="seminario"
                type="text"
                :disabled="!editing_seminario"
                v-model="seminario.name"
                v-on:blur="seminarioNameEdited"
              />
            </h2>
          </el-col>

          <el-col :span="24" class="d-flex">
            <el-card style="width: 100%;" class="mb-3">
              <el-form-item style="text-align: initial;" label="Descripción">
                <el-input
                  type="textarea"
                  v-model="seminario.description"
                  :showError="false"
                />
              </el-form-item>
            </el-card>
          </el-col>
          <el-col :span="12" style="padding-right: 25px;">
            <el-card class="form">
              <!-- Card Title -->
              <h3 class="mb-3">Agregar Video</h3>
              <el-form-item label="Título">
                <div class="d-flex">
                  <el-input
                    v-model="video.title"
                    :value="''"
                    :showError="false"
                  />
                </div>
              </el-form-item>

              <el-form-item label="URL">
                <div class="d-flex">
                  <el-input
                    v-model="video.link"
                    :value="''"
                    :showError="false"
                  />
                </div>
              </el-form-item>
              <el-form-item label="Fecha">
                <el-date-picker
                  v-model="video.date"
                  type="date"
                  placeholder="Pick a day"
                  :picker-options="pickerOptions"
                >
                </el-date-picker>
              </el-form-item>

              <el-form-item>
                <transition name="el-fade-in-linear">
                  <el-button
                    :disabled="invalidVideoForm"
                    style="width:120px; float:right"
                    type="success"
                    :icon="`el-icon-${editingVideo ? 'edit' : 'plus'}`"
                    @click="addVideo()"
                  >
                    {{ editingVideo ? "Guardar" : "Agregar" }}
                  </el-button>
                </transition>
                <transition name="el-fade-in-linear">
                  <el-button v-show="editingVideo" @click="resetEdit()">
                    Cancelar
                  </el-button>
                </transition>
              </el-form-item>
            </el-card>
          </el-col>
          <el-col :span="12">
            <el-card class="box-card list">
              <div slot="header" class="clearfix">
                <h3>Videos</h3>
              </div>
              <el-table
                max-height="550px"
                v-if="seminario.videos"
                :data="seminario.videos"
              >
                <el-table-column label="Nombre">
                  <template slot-scope="scope">
                    <!-- Small image of video -->
                    <div class="name-video-wrapper">
                      <img
                        style="min-width: 75px;"
                        v-if="scope.row.link"
                        :src="scope.row.link | extractThumbnail(seminarImage())"
                      />
                      {{ scope.row.title }}
                    </div>
                  </template>
                </el-table-column>
                <el-table-column
                  width="180px"
                  align="right"
                  label="Operaciones"
                >
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      @click="handleEditResource(scope.$index)"
                    >
                      Editar
                    </el-button>
                    <el-popconfirm
                      title="¿Estas seguro que quieres borrar esta estación?"
                      v-on:confirm="deleteVideo(scope.$index)"
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
        </el-row>
        <el-form-item class="mt-6 d-flex flex-row-reverse">
          <el-button
            style="width:120px"
            type="primary"
            :icon="'el-icon-edit'"
            @click="saveData('ruleForm')"
          >
            Guardar
          </el-button>
          <!-- delete seminario btn with confirmation -->
          <el-button
            style="width:120px"
            type="danger"
            :icon="'el-icon-delete'"
            @click="confirmDeleteSeminario()"
          >
            Eliminar
          </el-button>
        </el-form-item>
      </el-form>
    </el-col>

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

    <!-- //////////////////Dialog Confirmar delete seminario/////////////  -->
    <el-dialog width="350px" :visible.sync="confirm_delete_seminaio">
      <div slot="title" class="clearfix">
        <p style="text-align:left; margin:0">Eliminar Seminario</p>
      </div>
      <p style="text-align:left; margin:0">
        Estás seguro que deseas borrar
        <b style="color:red !important" v-html="seminario.name" />
        esta es una acción irreversible, se eliminarán todos los videos
        asociados a este seminario
      </p>
      <span slot="footer" class="dialog-footer">
        <el-button @click="confirm_delete_seminaio = false">Cancelar</el-button>
        <el-button type="primary" @click="handleDeleteSeminario()">
          Aceptar
        </el-button>
      </span>
    </el-dialog>
  </el-row>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import addSeminario from "../components/form/addSeminario.vue";
import { ImageInput, AddImage } from "../components/form";

export default {
  name: "banner",
  components: { ImageInput, AddImage, addSeminario },
  data: () => ({
    invalidVideoForm:true,
    editingIndex: null,
    confirmList: null,
    seminarios: [],
    seminario_index: 0,
    loading: false,
    workers: null,
    seminario: {
      name: "",
      image: "",
      path: "",
      description: "",
      videos: [],
    },
    description: "",
    editing_seminario: false,
    name: "",
    // casToEdit: "",
    editingItem: null,
    video: {
      title: "",
      image: "",
      link: "",
      date: "",
    },
    editingVideo: false,
    confirm: false,
    confirm_delete_seminaio: false,
    editImage: null,
  }),
  computed: {
    pickerOptions: () => ({
      disabledDate: (time) => time.getTime() >= Date.now(),
      shortcuts: [
        {
          text: "Hoy",
          onClick(picker) {
            picker.$emit("pick", new Date());
          },
        },
        {
          text: "Ayer",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit("pick", date);
          },
        },
        {
          text: "Antier",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 2);
            picker.$emit("pick", date);
          },
        },
      ],
    }),
    videoStructure: () => ({
      id: -1,
      title: "",
      image: "",
      link: "",
      date: "",
    }),
  },
  methods: {
    seminarImage() {
      return (
        $(location).attr("origin") +
        "/archivos/seminarios/" +
        this.seminario.image + "?v=" + Math.random()
      );
    },
    onImageEdited() {
      this.reset();
    },
    resetVideoForm() {
      this.video = { ...this.videoStructure };
    },
    onSeminarioAdded() {
      this.reset();
    },

    confirmDeleteSeminario() {
      this.confirm_delete_seminaio = true;
    },

    handleDeleteSeminario() {
      // post to delete seminario
      console.warn(this.seminario.name);
      // TODO: delete seminario

      const { seminario_index } = this;

      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "seminarios");
      formData.append("field", "seminarios");
      formData.append("index", seminario_index);

      // formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    editSeminarioName() {
      // this.casToEdit = this.cas.name;
      this.editing_seminario = true;
      this.$nextTick(() => this.$refs.seminario.select());
    },

    seminarioNameEdited() {
      // this.data.ca.find(item =>
      //   item.name === this.casToEdit ? (item.name = this.casToEdit) : null
      // );
      this.editing_seminario = false;
      // this.casToEdit = "";
    },

    onSeminarioChanged(evt) {
      this.loadData();
    },

    addVideo() {
      if (this.editingVideo) {
        this.editVideo();
        this.editingVideo = false;
        return;
      }
      const { video, seminario_index } = this;

      const formData = new FormData();
      formData.append("operation", "add");
      formData.append("section", "seminarios");
      formData.append("field", seminario_index);
      formData.append("subField", "videos");

      formData.append("data", JSON.stringify(video));
      this.submitRequest(formData);
    },

    editVideo() {
      const { video, seminario_index, editingIndex } = this;

      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "seminarios");
      formData.append("field", seminario_index);
      formData.append("subField", "videos");
      formData.append("index",editingIndex);

      formData.append("data", JSON.stringify(video));
      this.submitRequest(formData);
    },

    resetEdit() {
      this.editingVideo = false;
      this.resetVideoForm();
    },

    handleEditResource(index) {
      this.video = { ...this.seminario.videos[index] };
      this.editingIndex = index;
      this.editingVideo = true;
    },

    deleteVideo(index) {
      const { seminario_index } = this;

      const formData = new FormData();
      formData.append("operation", "remove");
      formData.append("section", "seminarios");
      formData.append("field", seminario_index);
      formData.append("subField", "videos");
      formData.append("index", index);

      this.submitRequest(formData);
    },

    getDeleteName() {
      const { editingItem } = this;
      console.warn(editingItem);
      if (editingItem !== null) {
        return editingItem.title;
      } else return null;
    },

    saveData() {
      const { name, path, description, image,videos } = this.seminario;
      const { seminario_index } = this;

      const formData = new FormData();
      formData.append("operation", "edit");
      formData.append("section", "seminarios");
      formData.append("field", "seminarios");
      formData.append("index", seminario_index);
      const json = {
        name,
        image,
        path,
        description,
        videos,
      };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },

    async submitRequest(formData) {
      formData.forEach((value, key) => {
        console.log(key + " " + value);
      });

      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        (response) => {
          this.confirm = false;
          this.reset();
          //console.log(response.data);
          setTimeout(() => loading.close(), 1000);
          this.$notify({
            message: "Información guardada con éxito.",
            type: "success",
          });
        },
        (error) => {
          this.confirm = false;
          console.log(error);
          setTimeout(() => loading.close(), 1000);
          this.$notify.error("Ha habido un error.");
        }
      );
    },

    reset() {
      window.location.reload();
    },

    loadData() {
      this.seminario = { ...this.seminarios[this.seminario_index] };
      this.editImage = this.seminarImage();
      this.resetVideoForm();
      this.editingVideo = false;
      this.editImage = this.seminarImage();
      this.resetVideoForm();
    },
  },
  filters: {
    // https://codepen.io/srestraj/details/gOvavEw
    extractThumbnail: function(image, default_image) {
      if (!image) return "";
      // regex to check the URL
      const regex = /^(http(s)??\:\/\/)?(www\.)?((youtube\.com\/watch\?v=)|(youtu.be\/)|(vimeo\.com\/)|(youtube\.com\/shorts\/)|(dailymotion\.com\/))?/i;
      let vimeoURL = "vimeo.com";
      let dailymotionURL = "dailymotion.com";
      let facebookWatchURL_1 = "facebook.com/watch";
      let facebookWatchURL_2 = "fb.watch";
      let path = "";

      if (image.includes(vimeoURL)) {
        path = "https://" + image.replace(regex, "vumbnail.com/");
        return path + ".jpg";
      } else if (image.includes(dailymotionURL)) {
        path = image.replace(regex, "https://www.dailymotion.com/thumbnail/");
        return path;
      } else if (
        image.includes(facebookWatchURL_1) ||
        image.includes(facebookWatchURL_2)
      ) {
        return default_image;
      } else {
        path = "https://" + image.replace(regex, "img.youtube.com/vi/");
        return path + "/hqdefault.jpg"; // for high quality videos, you can use maxresdefault.jpg
      }
    },
  },
  mounted: function() {
    this.seminarios = JSON.parse(JSON.stringify(this.$info.seminarios));
    this.seminario_index = 0;
    this.loadData();
  },
  watch: {
    editImage: function(value) {
      console.log(value);
    },
    // check if video form is valid
    video: {
      handler: function(value) {
        if (value.title && value.link && value.date) {
          this.invalidVideoForm = false;
        } else {
          this.invalidVideoForm = true;
        }
      },
      deep: true,
    },
  },
};
</script>
<style lang="scss">
.obligame-perro {
  height: 0px !important;
  button {
    height: 45px !important;
    width: 45px !important;
    position: relative !important;
    right: -100px !important;
  }
}
</style>
<style lang="scss" scoped>
.name-video-wrapper {
  display: flex;
  align-items: center;
  img {
    width: 75px;
    height: 75px;
    object-fit: cover;
    margin-right: 15px;
    border-radius: 5px;
  }
}
.image-edit {
  display: flex;
  width: 200px;
  height: 200px;
  cursor: pointer;
  object-fit: contain;
  border-radius: 18px;
  border: solid 1px #cbd0d9;
  overflow: hidden;
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
