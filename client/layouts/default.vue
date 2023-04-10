<template>
  <v-app style="background-color: #e2e2e2">
    <Navbar />
    <v-main>
      <slot />
    </v-main>
    <Footeer />
  </v-app>
</template>

<script setup lang="ts">
import Navbar from "~/components/Navbar.vue";
import Footeer from "~/components/Footer.vue";
</script>