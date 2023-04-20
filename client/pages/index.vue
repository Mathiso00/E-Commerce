<template>
  <div>
    <Carousel />
    <div >
      <h1 class="text-center font-weight-light my-5">The most popular products</h1>
      <v-container
          class="mb-10"
      >
        <v-fade-transition mode="out-in">
          <v-row>
            <v-col
                v-for="product in products"
                :key="product"
                :cols="smAndUp ? mdAndUp ? lgAndUp ? 3 : 4: 6 : 12"
            >
              <v-card class="text-center">
                <div class="float-right px-2 ml-2 bg-black">{{ product.price }} €</div>
                <v-img height="125" cover class="bg-grey-lighten-2 " :src="product.img" />
                <v-card-title class="text-h6">{{ product.name }}</v-card-title>
                <v-card-subtitle class="text-body-1 mb-3">{{ product.description }}</v-card-subtitle>
<!--                <v-card-text class="text-body-2">{{ product.price }} €</v-card-text>-->
              </v-card>
            </v-col>

          </v-row>
        </v-fade-transition>
      </v-container>
    </div>
  </div>
</template>

<script setup lang="ts">
import Carousel from "~/components/Carousel.vue";
import Bundle from "../assets/bundle.webp";
import Mac from "../assets/mac.jpeg";
import Souris from "../assets/souris.webp";
import Pc from "../assets/new-pc.webp";
import Souris2 from  '../assets/souris2.webp'
import Boitier from '../assets/Boitier-PC-7.jpg'
import {useDisplay} from "vuetify";

const { smAndUp, mdAndUp, lgAndUp } = useDisplay();

const products: { name: string, description: string, price: number, img: string }[] = [
  {
    name: "IMac Pro",
    description: "A powerful and stylish laptop for professionals and creatives.",
    price: 1699,
    img: Mac
  },
  {
    name: "Surface Laptop 4",
    description: "A lightweight, high-performance laptop with a touch screen for maximum productivity.",
    price: 999,
    img: Pc
  },
  {
    name: "Computer mice",
    description: "An elegant and modern design, with a high quality finish.",
    price: 39,
    img: Souris

  },
  {
    name: "PC monitor",
    description: "An ultra-thin laptop with impressive battery life and an AMOLED display.",
    price: 999,
    img: Bundle
  },
  {
    name: "Dell XPS 13",
    description: "Elegant and modern design, fits perfectly in any office or home environment",
    price: 1099,
    img: Boitier
  },
  {
    name: "Computer mice",
    description: "An elegant and modern design, with a high quality finish.",
    price: 899,
    img: Souris2
  }
];

console.log(products)

</script>
