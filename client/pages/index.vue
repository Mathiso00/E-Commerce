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
                :key="product.id"
                :cols="smAndUp ? mdAndUp ? lgAndUp ? 3 : 4: 6 : 12"
            >
              <ProductCard :product="product" />
            </v-col>

          </v-row>
        </v-fade-transition>
      </v-container>
    </div>
  </div>
</template>

<script setup lang="ts">
import {useDisplay} from "vuetify";
import {getProducts} from "~/composables/useProduct";
import {ref} from "@vue/reactivity";
import {IProduct} from "~/types/IProduct";

const { smAndUp, mdAndUp, lgAndUp } = useDisplay();
const products = ref<Array<IProduct>>([]);

async function fetchProducts() {
  const response = await getProducts();
  products.value = response.products.slice(0, 6);
}

fetchProducts();

definePageMeta({
  title: 'Home',
  auth: false
})

</script>
