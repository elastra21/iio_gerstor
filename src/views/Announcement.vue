<template>
  <el-row class="edit-banner">
    <div class="header-section">
      <h2>Anuncio</h2>
    </div>
    <el-col :span="24">
      <el-card class="form">
        <div class="photoWrapper">
          <ImageInput ref="refImage" v-model="image" :setData="editImage" />
        </div>
        <el-form label-position="top">
          <el-form-item label="Desde">
            <el-date-picker
              v-model="starts"
              type="datetime"
              placeholder="Pick a day"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
          </el-form-item>

          <el-form-item label="Hasta">
            <el-date-picker
              v-model="ends"
              type="datetime"
              placeholder="Pick a day"
              :picker-options="pickerOptions"
            >
            </el-date-picker>
          </el-form-item>

          <el-form-item label="Link">
            <el-input v-model="link" :showError="false"></el-input>
          </el-form-item>

          <el-form-item>
            <transition name="el-fade-in-linear">
              <el-button
                style="width:120px"
                type="success"
                :icon="'el-icon-edit'"
                @click="submitForm('ruleForm')"
              >
                Guardar
              </el-button>
            </transition>
          </el-form-item>
        </el-form>
      </el-card>
    </el-col>
  </el-row>
</template>
<script>
import $ from "jquery";
import axios from "axios";
import { ImageInput } from "../components/form";

export default {
  name: "banner",
  components: { ImageInput },
  data: () => ({
    pickerOptions: {
      disabledDate: time => time.getTime() <= Date.now(),
      shortcuts: [
        {
          text: "Hoy",
          onClick(picker) {
            picker.$emit("pick", new Date());
          }
        },
        {
          text: "Mañana",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() + 3600 * 1000 * 24);
            picker.$emit("pick", date);
          }
        },
        {
          text: "Prox. Semana",
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() + 3600 * 1000 * 24 * 7);
            picker.$emit("pick", date);
          }
        }
      ]
    },
    data: null,
    editImage: null,
    image: null,
    starts: "",
    ends: "",
    link: "",
    confirm: false
  }),
  methods: {
    validateField() {
      return this.image != null;
    },
    saveData() {
      const { image, link } = this;
      const starts = new Date(this.starts).toString();
      const ends = new Date(this.ends).toString();
      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "home");
      formData.append("field", "announcement");
      const json = { link, starts, ends };
      if (image !== null) {
        formData.append("image", image);
        json.image = "announcement.jpg";
      }
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
          this.reset();
          // console.log(response.data);
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
      if (this.validateField()) {
        this.saveData();
      } else {
        this.$notify.error("Favor de agregar imagen");
      }
    },
    reset() {
      // this.$refs.refImage.reset();
      // this.title = "";
      // this.description = "";
      // this.icon = "";
      // this.starts = "";
      // this.ends = "";
      this.$forceUpdate();
      window.location.reload();
    }
  },
  mounted: function() {
    this.data = JSON.parse(JSON.stringify(this.$info));
    const announcement = this.data.home.announcement;
    this.link = announcement.link;
    this.starts = announcement.starts;
    this.ends = announcement.ends;
    announcement.image.length > 0
      ? (this.editImage =
          $(location).attr("origin") +
          "/archivos/announcement/" +
          announcement.image + "?" + new Date().getTime())
      : null;
  },
  watch: {
    editImage: function(value) {
      // console.warn(value);
    }
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
.edit-banner {
  padding: 0 25px 25px;
  .el-card__body {
    max-width: 5000px !important;
  }
}
</style>
