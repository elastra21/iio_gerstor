<template>
  <el-row class="edit-banner">
    <div class="header-section">
      <h2>Sobre nosotros</h2>
    </div>
    <el-col :span="24">
      <el-card class="form">
        <el-form label-position="top">
          <el-form-item label="Video Institucional">
            <el-input v-model="video_institucional" :showError="false"></el-input>
          </el-form-item>
          <el-form-item label="Facebook">
            <el-input v-model="facebook" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Instagram">
            <el-input v-model="instagram" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Linkedin">
            <el-input v-model="linkedin" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Link a Calendario">
            <el-input v-model="calendarLink" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Mensaje Jubilados">
            <el-input
              :autosize="{ minRows: 8, maxRows: 15 }"
              type="textarea"
              v-model="retiredText"
              :showError="false"
            ></el-input>
          </el-form-item>

          <el-form-item label="Historia">
            <el-input
              :autosize="{ minRows: 8, maxRows: 15 }"
              type="textarea"
              v-model="history"
              :showError="false"
            ></el-input>
          </el-form-item>

          <!-- <el-form-item label="Link">
            <el-input v-model="link" :showError="false" />
          </el-form-item> -->
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
import axios from "axios";
export default {
  name: "banner",
  data() {
    return {
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
      facebook: "",
      instagram: "",
      retiredText: "",
      video_institucional: "",
      linkedin: "",
      calendarLink: "",
      history: "",
      confirm: false
    };
  },
  methods: {
    saveData() {
      const { facebook, instagram, calendarLink, linkedin, history, video_institucional, retiredText } = this;
      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "about");
      formData.append("field", "information");
      const json = { calendarLink, facebook, instagram, linkedin, history, institutionalVideo: video_institucional, retiredText };
      formData.append("data", JSON.stringify(json));
      this.submitRequest(formData);
    },
    async submitRequest(formData) {
      const loading = this.$loading();
      axios.post("/admin/save_data.php", formData).then(
        response => {
          this.confirm = false;
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
    },
    submitForm() {
      this.saveData();
    },
    reset() {
      this.facebook = "";
      this.instagram = "";
      this.video_institucional = "";
      this.calendarLink = "";
      this.linkedin = "";
      this.retiredText = "";
      this.history = "";
      this.$forceUpdate();
    }
  },
  beforeMount: function() {
    this.data = JSON.parse(JSON.stringify(this.$info.about.information));
    this.facebook = this.data.facebook;
    this.video_institucional = this.data.institutionalVideo;
    this.instagram = this.data.instagram;
    this.linkedin = this.data.linkedin;
    this.retiredText = this.data.retiredText;
    this.history = this.data.history;
    this.calendarLink = this.data.calendarLink;
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
