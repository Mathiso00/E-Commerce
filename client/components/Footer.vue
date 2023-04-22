<template>
<!--  <v-footer class="d-flex flex-column bg-grey-lighten-1 no-gutters px-0 py-0">-->
  <div>
    <div class="bg-grey-lighten-1 d-flex w-100 align-center px-4 py-5">
      <strong>Get connected with us on social networks!</strong>

      <v-spacer></v-spacer>

      <v-btn
          v-for="icon in icons"
          :key="icon"
          class="mx-2"
          :icon="icon"
          variant="plain"
          size="small"
      ></v-btn>
    </div>

    <div style="background-color: #1d1d1b" class="text-center text-yellow-lighten-4 w-100 mx-0 py-4 ">
      {{ new Date().getFullYear() }} — <strong>Name</strong>
    </div>
  </div>

<!--  </v-footer>-->
</template>

<script setup lang="ts">
const icons = [
    'mdi-facebook',
    'mdi-twitter',
    'mdi-linkedin',
    'mdi-instagram',
  ]
</script>

<style scoped>

</style>