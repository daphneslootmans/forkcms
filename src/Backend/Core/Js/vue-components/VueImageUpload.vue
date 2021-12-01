<template>
  <div class="vue-file-upload" data-v-image-upload>
    <div class="row">
      <div class="col-lg-3">
        <div class="image-preview">
          <span class="delete-button" @click="removeFile" v-if="url"><i class="fa fa-times"></i></span>
          <img :src="getPreviewUrl" class="img-thumbnail" width="200" height="200" :alt="label">
        </div>
      </div>
      <div class="col-lg-9">
        <span class="filename"></span>
        <input @input="handleFileSelection"
               type="file"
               id="image"
               name="image"
               :class="['form-control', {error: fileSizeError}]"
               accept="image/*"
               data-fork-cms-role="image-field"
               aria-describedby="helpImage"
               ref="imageInput"
        >
        <small :class="['form-text', {'text-danger': fileSizeError}, {'text-muted': !fileSizeError}]" id="helpImage">
          Only jp(e)g, gif or png-files are allowed, maximum filesize: 2MB.<br>
          <span v-if="file">filesize: {{ getFileSize }}</span>
        </small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VueImageUpload',
  props: [
    'label',
    'imageUrl',
    'backendCoreUrl'
  ],
  data () {
    return {
      file: null,
      url: null,
      maxFileSize: 2e+6,
      fileSizeError: false
    }
  },
  computed: {
    getPreviewUrl () {
      return this.url ? this.url : this.backendCoreUrl + '/Layout/Images/image-upload-placeholder.jpg'
    },
    getFileSize () {
      return this.file ? this.bytesToSize(this.file.size) : 0
    }
  },
  methods: {
    handleFileSelection (event) {
      this.file = event.target.files[0]
      this.url = this.file ? URL.createObjectURL(event.target.files[0]) : null
      if (this.file?.size > this.maxFileSize) {
        this.fileSizeError = true
      } else {
        // TODO:  add backend call to add file
      }
    },
    removeFile () {
      // TODO: add backend call to delete file
      this.file = null
      this.url = null
      this.fileSizeError = false
      this.$refs.imageInput.value = ''
    },
    bytesToSize (bytes) {
      const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
      if (bytes === 0) return 'n/a'
      const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10)
      if (i === 0) return `${bytes} ${sizes[i]})`
      return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`
    }
  },
  watch: {},
  mounted () {
    if (this.imageUrl) this.url = this.imageUrl
  }
}
</script>

<style scoped lang="scss">
</style>