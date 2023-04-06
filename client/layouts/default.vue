<template>
  <v-app>
    <Navbar />
    <v-main>
      <slot />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import Navbar from "~/components/Navbar.vue";
</script>