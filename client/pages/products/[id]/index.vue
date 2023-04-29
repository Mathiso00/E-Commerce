<template>
<h1> Detail product</h1>
  <v-container>
    <v-row>
      <v-col>
        <v-card>
          <v-col class="d-flex justify-lg-space-between">
            <div>
              <v-card-title class="text-uppercase font-weight-light">{{product.name }}</v-card-title>
              <v-img height="300" cover :src="product.photo" :alt="product.name" />
              <v-pagination
                  v-model="page"
                  :length="4"
                  rounded="0"
              ></v-pagination>
            </div>
            <v-card-text>{{product.description}}</v-card-text>
            <div class="d-flex flex-column my-5 border py-4 px-5">
              <div class=" px-2 text-center font-weight-bold">{{product.price}} €</div>
              <v-select
                  :items="items"
                  density="compact"
                  class="bg-white my-5 "
              ></v-select>
              <v-btn class="rounded px-4 text-center mb-5 text-white" style="background-color: #1d1d1b"> Add to cart </v-btn>
              <v-btn class="rounded px-4 text-center text-white" style="background-color: #BAA64F"> BUY </v-btn>

            </div>
          </v-col>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup lang="ts">
import {getProduct} from "~/composables/useProduct";
import {IProduct} from "~/types/IProduct";
import {ref} from "@vue/reactivity";

const route = useRoute();

  const data = await getProduct(parseInt(route.params.id, 10));
  const product = data.product
  console.log(data.product)
  const arrayImage = [
    {
      url: product.photo,
    },
    {
      url: product.photo,
    },
    {
      url: product.photo,
    },
    {
      url: product.photo,
    }
  ]

</script>

<style scoped>

</style>