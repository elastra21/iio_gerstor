<template>
  <el-row class="edit-banner">
    <div class="header-section">
      <h2>Banner</h2>
    </div>
    <el-col :span="24">
      <el-card class="form">
        <el-form label-position="top">
          <el-form-item label="Título">
            <el-input v-model="title" :showError="false"></el-input>
          </el-form-item>

          <el-form-item label="Icono">
            <el-input v-model="icon" :showError="false"></el-input>
          </el-form-item>

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

          <!-- <el-form-item label="Link">
            <el-input v-model="link" :showError="false" />
          </el-form-item> -->
          <el-form-item label="Descripción">
            <el-input
              type="textarea"
              v-model="description"
              :showError="false"
            />
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
    <el-col :span="24" class="mt-5">
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>Vista Previa</span>
        </div>
        <v-banner
          class="banner"
          single-line
          style=" background-color:rgb(3,118,56) !important"
        >
          <v-icon slot="icon" color="warning" size="36" v-html="this.icon" />
          <p class="ma-0 white--text" style="text-align:initial">
            <b>{{ this.title }}: </b>{{ this.description }}
          </p>
          <template v-slot:actions>
            <v-icon color="white" size="26" v-html="'mdi-close'" />
          </template>
        </v-banner>
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
      title: "",
      description: "",
      icon: "",
      starts: "",
      ends: "",
      confirm: false
    };
  },
  methods: {
    saveData() {
      const { title, description, icon } = this;
      const starts = new Date(this.starts).toString();
      const ends = new Date(this.ends).toString();
      const formData = new FormData();
      formData.append("operation", "replace");
      formData.append("section", "home");
      formData.append("field", "banner");
      const json = { title, description, icon, starts, ends };
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
      this.saveData();
    },
    reset() {
      // this.title = "";
      // this.description = "";
      // this.icon = "";
      // this.starts = "";
      // this.ends = "";
      this.$forceUpdate();
      window.location.reload();
    }
  },
  beforeMount: function() {
    this.data = JSON.parse(JSON.stringify(this.$info));
    const banner = this.data.home.banner;
    this.title = banner.title;
    this.icon = banner.icon;
    this.starts = banner.starts;
    this.ends = banner.ends;
    this.description = banner.description;
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
