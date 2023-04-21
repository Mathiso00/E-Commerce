<template>
  <RegisterForm class="my-16"/>
</template>

<script setup lang="ts">
import RegisterForm from "~/components/RegisterForm.vue";
import {useNuxtApp} from "#app/nuxt";

const { $auth } = useNuxtApp();

definePageMeta({
  title: 'Register',
  auth: false
})
</script>


