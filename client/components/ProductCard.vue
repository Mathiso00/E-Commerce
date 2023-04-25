<template>
  <v-container>
    <v-card class="text-center bord rounded-0 bg-grey-lighten-3 p-2">
      <nuxt-link :to="`/products/${product.id}`">
        <v-img height="150" cover class="bg-grey-lighten-2 position-relative d-flex" :src="product.photo" :alt="product.name">
          <div class=" px-2 ml-2 bg-black position-absolute align-price">{{product.price}} €</div>
        </v-img>
      </nuxt-link>
      <v-card-title class="text-h6">{{product.name}}</v-card-title>
      <v-card-subtitle class="text-body-1 mb-3">{{product.description}}</v-card-subtitle>
      <v-card-actions class="d-flex justify-center">
        <v-btn color="primary text-xs">Add to Cart</v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import {IProduct} from "~/types/IProduct";

const props = defineProps<{
  product: IProduct
}>();

</script>

<style scoped>
.bord {
  box-shadow: -10px -10px #cdbe7c;
}
.align-price{
  right: 0;
}
</style>