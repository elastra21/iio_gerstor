<template>
  <div
    class="base-image-input"
    :style="{ 'background-image': `url(${imageData})` }"
    @click="chooseImage"
  >
    <div v-if="!imageData" class="placeholder">
      <img
        style="margin-bottom:20px; width:80px"
        src="@/assets/upload-photo.svg"
      />
      Choose an Image
    </div>
    <input
      class="file-input"
      ref="fileInput"
      type="file"
      accept="image/jpeg"
      @input="onSelectFile"
    />
    <v-dialog v-model="errorDialog" max-width="300">
      <v-card class="pt-5 pb-2 px-3">
        <v-card-text class="subheading">{{ errorText }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="closeModal" text>Aceptar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
  name: "ImageInput",
  data() {
    return {
      imageData: null,
      errorDialog: false,
      errorText: ""
    };
  },
  props: {
    setData: String
  },
  watch: {
    setData: function(value) {
      this.imageData = value;
      // console.warn(value);
    }
  },

  methods: {
    reset() {
      this.imageData = null;
      this.$refs.fileInput.value = "";
    },
    closeModal() {
      this.errorDialog = false;
    },
    chooseImage() {
      this.$refs.fileInput.click();
    },
    onSelectFile() {
      const input = this.$refs.fileInput;
      const files = input.files;
      const maxSize = 1024;
      if (files && files[0]) {
        let size = files[0].size / maxSize / maxSize;
        console.warn(files[0].type);
        if (!files[0].type.match("image/jpeg")) {
          // check whether the upload is an image
          this.errorDialog = true;
          this.errorText = "Por favor eliga una imagen en formato 'jpg'";
          return;
        } else if (size > 1) {
          // check whether the size is greater than the size limit
          this.errorDialog = true;
          this.errorText =
            "La imágen es muy grande, Por favor seleccione una imagen menor a 1MB";
          return;
        }
        const reader = new FileReader();
        reader.onload = e => {
          this.imageData = e.target.result;
        };
        reader.readAsDataURL(files[0]);
        this.$emit("input", files[0]);
      }
    }
  }
};
</script>

<style scoped>
.base-image-input {
  display: flex;
  width: 200px;
  height: 200px;
  cursor: pointer;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  border-radius: 18px;
  border: solid 1px #cbd0d9;
  overflow: hidden;
}
.placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #cbd0d9;
  font-size: 18px;
  font-family: Helvetica;
}
.base-image-input:hover {
  opacity: 0.7;
}
.file-input {
  display: none;
}
</style>
