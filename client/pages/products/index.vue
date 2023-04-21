<template>
  <v-container>
    <h1>List of products</h1>
  </v-container>
</template>

<script setup lang="ts">
import {getProducts} from "~/composables/useProduct";
import {getProduct} from "~/composables/useProduct";
const data = getProducts();
const infoProduct = getProduct(1);
console.log("un produit: ", infoProduct)
console.log("Les produits: ", data)
</script>
