<template>
  <div class="vue-file-upload" data-v-image-upload>
    <div class="row">
      <div class="col-lg-3">
        <div class="image-preview" v-show="url">
          <span class="delete-button"></span>
          <img :src="url" class="img-thumbnail" width="200" height="200" :alt="label">
        </div>
      </div>
      <div class="col-lg-9">
        <span class="filename"></span>
        <input @input="handleFileSelection" type="file" id="image" name="image" class="form-control" accept="image/*" data-fork-cms-role="image-field" aria-describedby="helpImage">
        <small class="form-text text-muted" id="helpImage">Only jp(e)g, gif or png-files are allowed, maximum filesize: 2MB.</small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VueImageUpload',
  props: {
    label: {
      type: String
    },
    imageUlr: {
      type: String
    }
  },
  data () {
    return {
      file: null,
      url: null
    }
  },
  computed: {},
  methods: {
    handleFileSelection (event) {
      this.file = event.target.files[0]
      this.url = URL.createObjectURL(event.target.files[0])
    }
  },
  watch: {}
}
</script>

<style scoped lang="scss">

</style>