<template>
  <div class=" d-flex justify-center mx-auto">
    <form v-on:submit.prevent method="POST"
          :class="{ 'w-75': !smAndUp, 'md:w-50': mdAndUp }"
          class="my-5 pt-5 pb-5 w-50 bg-white bord">
      <div v-if="error" class="text-red text-center"> {{ error }}</div>
      <div v-if="success" class="text-green text-center">{{ success }}</div>
      <h1 class="text-center">Login here </h1>
      <v-text-field
          v-model="email"
          label="E-mail"
          type="email"
      ></v-text-field>
      <p v-if="errors.email" class="text-xs text-red mb-2">{{ errors.email}}</p>
      <v-text-field
          v-model="password"
          label="Password"
          type="password"
      ></v-text-field>
      <p v-if="errors.password" class="text-xs text-red mb-2">{{ errors.password }}</p>
      <div class=" d-flex justify-center py-4">
        <v-btn
            class="me-4"
            @click.prevent="loginUser"
        >
          submit
        </v-btn>
      </div>

    </form>
  </div>
</template>

<script setup lang="ts">
import {useDisplay} from "vuetify";
import {ref} from "@vue/reactivity";
import { Ref } from "vue";
import { useNuxtApp } from "#app/nuxt";
import {navigateTo} from "#app/composables/router";

const { $auth }= useNuxtApp();
const { smAndUp, mdAndUp } = useDisplay();
const email: Ref<string> = ref('');
const password: Ref<string> = ref('');
const loading = ref(false)
const success = ref('');
const error = ref('');
const errors = ref({
  email: '',
  password: ''
})

const validateForm = () => {
  let valid = true;

  // Validation of email field
  if (!email.value.trim()){
    valid = false;
    errors.value.email +='The email field is required.';
  }else if(!/.+@.+\..+/.test(email.value.trim())){
    valid = false;
    errors.value.email +='The email format is invalid';
  }

  // Validation of password field
  if (!password.value.trim()){
    valid = false;
    errors.value.password +='The password field is required.';
  }else if(!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,}$/.test(password.value.trim())){
    valid = false;
    errors.value.password +='Password must contain at least one uppercase letter, one lowercase letter, one digit, and be at least 6 characters long.';
  }
  return valid;
}

async function loginUser(){
  loading.value = true;
  console.log(email.value)
  try {
    if(validateForm()){
      let response = await $auth.loginWith('local', {body: { email: email.value, password: password.value }});
      success.value = 'You are login successfully.';
      navigateTo('/');
    }
  }catch (e) {
    console.log(e);
    error.value = "An error occurred during the operation";
  }finally {
    loading.value = false;
  }
}

</script>

<style>
.bord {
  box-shadow: -10px -10px #cdbe7c;
}
</style>