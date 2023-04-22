<template>
    <v-app-bar
        class="text-yellow-lighten-4 "
        style="background-color: #1d1d1b"
    >
      <v-toolbar-title>Name</v-toolbar-title>
      <v-spacer></v-spacer>
      <div class="d-none d-md-flex">
        <v-list style="background-color: #1d1d1b" >
          <v-list-item >
            <nuxt-link to="/" class="mx-4 font-weight-light text-decoration-none text-yellow-lighten-4">Home</nuxt-link>
            <nuxt-link to="/products" class="mx-4 font-weight-light text-decoration-none text-yellow-lighten-4">Products</nuxt-link>
            <nuxt-link to="/" class="mx-4  font-weight-light text-decoration-none text-yellow-lighten-4">Contact</nuxt-link>
          </v-list-item>
        </v-list>
      </div>
      <v-spacer></v-spacer>
      <div>
        <v-menu v-model="menu" offset-x close-on-content-click>
          <template v-slot:activator=" { on, props } ">
            <v-col class="flex flex-row">
              <v-icon v-if="$auth.loggedIn" icon="mdi-account" @click="menu=!menu" v-bind="props"  />
            </v-col>
          </template >
          <v-list>
            <v-list-item-title>
              <nuxt-link to="/" class="mx-4 text-blue">Profile</nuxt-link>
            </v-list-item-title>
            <v-list-item-title>
              <nuxt-link @click="logout" class="mx-4 text-blue">Logout</nuxt-link>
            </v-list-item-title>
          </v-list>
        </v-menu>
      </div>
      <nuxt-link v-if="!$auth.loggedIn" to="/login" class="mx-4 font-weight-light text-decoration-none text-yellow-lighten-4">Login</nuxt-link>
      <nuxt-link v-if="!$auth.loggedIn" to="/register" class="mx-4 font-weight-light text-decoration-none text-yellow-lighten-4">Register</nuxt-link>
      <v-icon icon="mdi-cart" class="mx-4" />
      <v-app-bar-nav-icon class="d-md-none d-lg-none mx-2" variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    </v-app-bar>
    <v-navigation-drawer
        v-model="drawer"
        location="top"
        class="hidden-lg"
        temporary
    >
      <v-list>
        <v-list-item >
          <nuxt-link @click="drawer = false" to="/" class="mx-4 font-weight-light text-decoration-none">Home</nuxt-link>
        </v-list-item>
        <v-list-item>
          <nuxt-link @click="drawer = false" to="/products" class="mx-4 font-weight-light text-decoration-none ">Products</nuxt-link>
        </v-list-item>
        <v-list-item>
          <nuxt-link @click="drawer = false" to="/" class="mx-4  font-weight-light text-decoration-none">Contact</nuxt-link>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
</template>

<script setup lang="ts">
import {ref} from "@vue/reactivity";
import {useNuxtApp} from "#app/nuxt";
import {navigateTo} from "#app/composables/router";

const menu = ref(false);
const drawer = ref(false);
const loading = ref(false);
const { $auth }=useNuxtApp();
console.log($auth.loggedIn)

async function logout (){
  loading.value = true;
  try {
    await $auth.logout();
    navigateTo('/login');
  }catch (e) {
    console.log(e)
  }finally {
    loading.value = false;
  }
}


</script>