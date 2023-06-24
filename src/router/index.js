import Vue from "vue";
import VueRouter from "vue-router";
import Subscription from "../components/Subscription.vue";
import {
  AboutUs,
  Slider,
  News,
  Personal,
  Retired,
  ERM,
  CCA,
  Brigadas,
  Vinculacion,
  Gallery,
  Banner,
  Announcement,
  Informes,
  Configuration,
  Infografias,
  Seminarios,
  WeatherStations
} from "../views";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Home",
    component: Configuration
  },
  {
    path: "/edit-about-us",
    name: "About-us",
    component: AboutUs
  },
  {
    path: "/edit-banner",
    name: "banner",
    component: Banner
  },
  {
    path: "/edit-announcement",
    name: "announcement",
    component: Announcement
  },

  {
    path: "/edit-galery",
    name: "gallery",
    component: Gallery
  },
  {
    path: "/edit-infografias",
    name: "infografias",
    component: Infografias
  },
  {
    path: "/edit-seminarios",
    name: "seminarios",
    component: Seminarios
  },
  {
    path: "/edit-slider",
    name: "slider",
    component: Slider
  },
  {
    path: "/edit-news",
    name: "news",
    component: News
  },
  {
    path: "/edit-personal",
    name: "personal",
    component: Personal
  },
  {
    path: "/retired",
    name: "retired",
    component: Retired
  },
  {
    path: "/transparencia/informes",
    name: "informes",
    component: Informes
  },
  {
    path: "/transparencia/ERM",
    name: "ERM",
    component: ERM
  },
  {
    path: "/transparencia/cca",
    name: "CCA",
    component: CCA
  },
  {
    path: "/transparencia/brigadas",
    name: "Brigadas",
    component: Brigadas
  },
  {
    path: "/investigacion",
    name: "investigacion",
    component: () => import("../views/Investigacion.vue")
  },
  {
    path: "/incidencia-social",
    name: "Incidencia",
    component: Vinculacion
  },
  {
    path: "/configuracion",
    name: "Configuración",
    component: Configuration
  },
  {
    path: "/estaciones-meteorologicas",
    name: "WeatherStation",
    component: WeatherStations
  },
  {
    path: "/estaciones",
    name: "Estaciones",
    component: () => import("../views/Estaciones.vue")
  },
  {
    path: "/calidad-del-aire",
    name: "Calidad del aire",
    component: () => import("../views/CalidadAire.vue")
  }
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes
});

export default router;
