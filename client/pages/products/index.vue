<template>
  <v-container>
    <h1>List of products</h1>
    <v-text-field
        prepend-icon="mdi-magnify"
        single-line
    ></v-text-field>
    <v-row>
      <v-col
          v-for="product in products"
          :key="product.id"
          :cols="smAndUp ? mdAndUp ? lgAndUp ? 3 : 4: 6 : 12"
      >
        <ProductCard :product="product" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import {getProducts} from "~/composables/useProduct";
import ProductCard from "~/components/ProductCard.vue";
import {useDisplay} from "vuetify";
import {ref} from "@vue/reactivity";
import {IProduct} from "~/types/IProduct";

const { smAndUp, mdAndUp, lgAndUp } = useDisplay();
const products = ref<Array<IProduct>>([]);

async function fetchProducts() {
  const response = await getProducts();
  console.log("Les produits: ", response)
  products.value = response.products;
}

fetchProducts();

console.log("Les produits: ", products.value)
</script>
