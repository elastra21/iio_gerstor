<template>
  <v-app class="pa-0 ma-0" id="app">
    <el-menu
      default-active="/"
      :collapse="isTablet"
      class="el-menu-vertical"
      @select="handleSelect"
      :router="true"
      background-color="#545c64"
      text-color="#fff"
    >
      <el-image class="logo" :src="require('@/assets/logo_iio.svg')" />

      <el-submenu index="/edit/home">
        <template slot="title">
          <i class="el-icon-menu"></i>
          <span>Inicio</span>
        </template>
        <!-- <el-menu-item index="/edit/home" :route="{ path: '/edit/home' }">
          <i class="el-icon-bell"></i>
          <span>Anuncios</span>
        </el-menu-item> -->
        <el-submenu class="" index="/edit/anuncios">
          <template slot="title">
            <i class="el-icon-bell"></i> <span>Anuncios</span>
          </template>
          <el-menu-item index="/edit-banner" :route="{ path: '/edit-banner' }">
            Banner
          </el-menu-item>
          <el-menu-item
            index="/edit/edit-announcement"
            :route="{ path: '/edit-announcement' }"
          >
            Pantalla completa
          </el-menu-item>
        </el-submenu>
        <el-menu-item
          index="/edit/edit-slider"
          :route="{ path: '/edit-slider' }"
        >
          <i class="el-icon-picture-outline"></i>
          Slider
        </el-menu-item>
        <el-menu-item index="/edit/edit-news" :route="{ path: '/edit-news' }">
          <i class="el-icon-date"></i>
          Noticias
        </el-menu-item>
        <el-menu-item
          index="/edit/edit-galery"
          :route="{ path: '/edit-galery' }"
        >
          <i class="el-icon-camera"></i>
          Galería
        </el-menu-item>
      </el-submenu>

      <el-submenu index="/edit/about">
        <template slot="title">
          <i class="el-icon-location"></i>
          <span>Nosotros</span>
        </template>
        <el-menu-item index="/edit/about" :route="{ path: '/edit-about-us' }">
          <i class="el-icon-s-management"></i>
          Conócenos
        </el-menu-item>
        <el-menu-item
          index="/edit-personal"
          :route="{ path: '/edit-personal' }"
        >
          <i class="el-icon-s-custom"></i>
          Personal
        </el-menu-item>
        <el-menu-item index="/retired" :route="{ path: '/retired' }">
          <i class="el-icon-user"></i>
          Retirados
        </el-menu-item>
        <!-- <el-menu-item index="/edit-retired" :route="{ path: '/edit-retired' }">
          <i class="el-icon-user"></i>
          Antiguo personal
        </el-menu-item> -->
      </el-submenu>

      <el-submenu class="" index="/transparencia">
        <template slot="title">
          <i class="el-icon-view" />
          <span>Transparencia</span>
        </template>

        <el-menu-item
          index="/transparencia/informes"
          :route="{ path: '/transparencia/informes' }"
        >
          <i class="el-icon-document" />
          Informes
        </el-menu-item>
        <el-menu-item
          index="/transparencia/cca"
          :route="{ path: '/transparencia/cca' }"
        >
          <i class="el-icon-school" />
          {{ isTablet ? "Cuerpos, Comités y Actas" : "Cuerpos, Comités y ..." }}
        </el-menu-item>
        <el-menu-item
          index="/transparencia/ERM"
          :route="{ path: '/transparencia/ERM' }"
        >
          <i class="el-icon-collection" />
          {{
            isTablet
              ? "Estatutos, Reglamentos y Manuales"
              : "Estatutos, Reglamen..."
          }}
        </el-menu-item>
        <el-menu-item
          index="/transparencia/brigadas"
          :route="{ path: '/transparencia/brigadas' }"
        >
          <i class="el-icon-bangzhu" />
          Brigadas de Seguridad
        </el-menu-item>
      </el-submenu>

      <!-- <el-menu-item index="3">
        <i class="el-icon-document"></i>
        <span>Transparencia</span>
      </el-menu-item> -->
      <el-menu-item index="/investigacion" :route="{ path: '/investigacion' }">
        <i class="el-icon-search"></i>
        <span>Investigación</span>
      </el-menu-item>

      <el-menu-item
        index="/edit-infografias"
        :route="{ path: '/edit-infografias' }"
      >
        <i class="el-icon-picture"></i>
        <span>Infografías</span>
      </el-menu-item>

      <!-- edit seminarios -->

      <el-menu-item
        index="/edit-seminarios"
        :route="{ path: '/edit-seminarios' }"
      >
        <i class="el-icon-video-camera"></i>
        <span>Seminarios</span>
      </el-menu-item>

      <el-menu-item
        index="/incidencia-social"
        :route="{ path: '/incidencia-social' }"
      >
        <i class="el-icon-connection"></i>
        <span>Incidencia</span>
      </el-menu-item>

      <!-- <el-menu-item
        index="estaciones-meteorologicas"
        :route="{ path: '/estaciones-meteorologicas' }"
      >
        <i class="el-icon-cloudy-and-sunny"></i>
        <span>Estaciones</span>
      </el-menu-item> -->


      <el-submenu index="estaciones-meteorologicas">
        <template slot="title">
          <i class="el-icon-cloudy-and-sunny"></i>
          <span>Estaciones</span>
        </template>
        <el-menu-item index="/estaciones-meteorologicas/informacion" :route="{ path: '/estaciones-meteorologicas' }">
          <i class="el-icon-s-management"></i>
          Información
        </el-menu-item>
        <el-menu-item
        index="/estaciones-meteorologicas/estaciones"
          :route="{ path: '/estaciones' }"
        >
          <i class="el-icon-s-custom"></i>
          Estaciones
        </el-menu-item>
        <el-menu-item index="/calidad-del-aire" :route="{ path: '/calidad-del-aire' }">
          <i class="el-icon-user"></i>
          Calidad del Aire
        </el-menu-item>
      </el-submenu>




      <el-menu-item index="/configuracion" :route="{ path: '/configuracion' }">
        <i class="el-icon-setting"></i>
        <span>Configuración</span>
      </el-menu-item>

      <small v-if="!isTablet" class="water-mark">
        Dessarollado por <br />
        <b class="hacktronicks">Hacktronicks</b> Dev Team
      </small>
    </el-menu>
    <router-view id="router" />
  </v-app>
</template>

<script>
import Subscription from "./components/Subscription.vue";
export default {
  name: "app",
  components: { Subscription },
  data() {
    return {
      activeIndex: "/",
      isActive: false,
      isTablet: window.innerWidth <= 1400,
    };
  },
  mounted: function() {
    window.addEventListener("resize", this.onresize);
  },
  unmounted() {
    window.removeEventListener("resize", this.onresize);
  },
  methods: {
    handleSelect(key, keyPath) {
      console.log(key, keyPath);
    },
    select: function() {
      this.isActive = !this.isActive;
    },
    onresize: function() {
      this.isTablet = window.innerWidth <= 1400;
      console.warn(window.innerWidth <= 1400);
    },
  },
};
</script>

<style lang="scss">
@font-face {
  font-family: "neon";
  src: url("./assets/fonts/beon.ttf");
}
.el-menu--collapse {
  width: 64px !important;
}
body {
  margin: 0px !important;
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.logo {
  margin-top: 20px;
  margin-bottom: 20px;
  min-height: 50px;
  height: 50px;
  align-self: center !important;
  opacity: 0.9;
  padding: 0 10px;
}
#app {
  width: 100%;
  height: 100vh;
  display: flex !important;
  flex-direction: row !important;
  text-align: center;
  color: #2c3e50;
}
.v-application--wrap {
  flex-direction: row !important;
}
ul {
  padding-left: 0 !important;
}
.el-menu-vertical {
  position: relative;
  display: flex;
  flex-direction: column;
  width: 237px;
  // min-width: 237px;
  text-align: left;
  height: 100%;
  padding-bottom: 75px;
  overflow-y: scroll;
}
// .el-menu--collapse {
//   max-width: 64px;
//   min-width: 64px !important;
// }
.water-mark {
  color: white;
  position: fixed;
  width: 225px !important;
  height: 70px;
  background-color: #545c64;
  bottom: 0px;
  padding-top: 10px;
  text-align: center;
  .hacktronicks {
    font-family: "neon";
    color: #ffffff !important;
    text-decoration: none !important;
    opacity: 0.9;
  }
}
#router {
  flex: 1;
  height: 100%;
  overflow-y: scroll;
}
</style>
