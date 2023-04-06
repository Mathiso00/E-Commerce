<template>
  <v-carousel show-arrows="hover">
    <v-carousel-item
        v-for="(item,i) in items"
        :key="i"
        :src="item.src"
        cover
    >
        <div class="d-flex fill-height justify-center align-center text-white font-weight-bold">
          <div class="text-h2">
            Display Slide
          </div>
        </div>
    </v-carousel-item>
  </v-carousel>
</template>

<script setup lang="ts">
import Bundle from "../assets/bundle.webp";
import Mac from "../assets/raw.jpeg";
import souris from "../assets/souris.webp";
import Pc from "../assets/new-pc.webp";

const items = [
  {src: Bundle},
  {src: Mac},
  {src: souris},
  {src: Pc}
/*    {src: "https://cdn.vuetifyjs.com/images/cards/docks.jpg"},
    {src: "https://cdn.vuetifyjs.com/images/cards/hotel.jpg"},
    {src: "https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"},*/
]
</script>
