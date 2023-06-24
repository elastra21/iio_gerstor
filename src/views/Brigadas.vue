<template>
  <v-container fluid style="padding: 0 25px 25px;" class="brigadas">
    <div class="header-section">
      <h2>Brigadas</h2>
    </div>
    <v-row class="pt-10 list-wrapper">
      <div id="pageContainer">
        <div id="viewer" class="pdfViewer"></div>
      </div>
    </v-row>
    <AddPDF path="brigadas/" :onComplete="reset" />
  </v-container>
</template>
<script>
import $ from "jquery";
import { AddPDF } from "../components/form";
import pdfjsLib from "pdfjs-dist/build/pdf";
import { PDFViewer } from "pdfjs-dist/web/pdf_viewer";
import "pdfjs-dist/web/pdf_viewer.css";

pdfjsLib.GlobalWorkerOptions.workerSrc =
  "https://cdn.jsdelivr.net/npm/pdfjs-dist@2.0.489/build/pdf.worker.min.js";

export default {
  name: "Brigadas",
  components: { AddPDF },
  data: () => ({
    data: []
  }),
  methods: {
    reset() {
      window.location.reload();
    },
    loadPdf: function() {
      const publicPath = $(location).attr("origin") + "/archivos/brigadas/";
      const files = [];
      $.getJSON(publicPath, data => {
        data.map(file => {
          if (!file.match("index.php")) files.push(publicPath + file + "?" + Math.random());
        });
        // console.warn(files[0]);
        // this.pdf = files[0];
        this.getPdf(files[0]);
      });
    },
    async getPdf(path) {
      let container = document.getElementById("pageContainer");
      let pdfViewer = new PDFViewer({ container: container });
      pdfjsLib.getDocument(path).then(pdf => {
        pdfViewer.setDocument(pdf);
        $(".textLayer").remove();
      });
    }
  },
  mounted: function() {
    this.loadPdf();
  }
};
</script>

<style lang="scss">
.brigadas {
  position: relative;
  #pageContainer {
    position: relative;
    margin: auto;
    width: 50%;
    // height: 80%;
  }

  div.page {
    width: 100% !important;
    height: auto !important;
    // height: 70vh !important;
    * {
      width: 100% !important;
      height: auto !important;
    }
    .textLayer {
      display: none !important;
    }
  }
}
</style>
