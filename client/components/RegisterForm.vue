<template>
  <div class=" d-flex justify-center mx-auto">
    <form v-on:submit.prevent method="POST" :class="{ 'w-75': !smAndUp, 'md:w-50': mdAndUp }"
          class="my-5 pt-5 pb-5 w-50 bg-white bord">
      <div v-if="error" class="text-red"> {{ error }}</div>
      <div v-if="success" class="text-green text-center">{{ success }}</div>
      <h1 class="text-center">Registration</h1>
      <v-text-field
          v-model="firstname"
          label="Firstname"
          type="text"
      ></v-text-field>
      <p v-if="errors.firstname" class="text-xs text-red mb-2">{{ errors.firstname }}</p>
      <v-text-field
          v-model="lastname"
          label="Lastname"
          type="text"
      ></v-text-field>
      <p v-if="errors.lastname" class="text-xs text-red mb-2">{{ errors.lastname}}</p>
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
            @click.prevent="signUser"
        >
          submit
        </v-btn>
      </div>
    </form>

  </div>
</template>

<script setup lang="ts">
import {useDisplay} from "vuetify";
import { Ref } from 'vue'
import { ref } from "@vue/reactivity";
import {signUp} from "~/composables/useAuth";
import {navigateTo} from "#app/composables/router";

const { smAndUp, mdAndUp } = useDisplay();
const firstname: Ref<string> = ref('');
const lastname: Ref<string> = ref('');
const email: Ref<string> = ref('');
const password: Ref<string> = ref('');

const success = ref('')
const loading = ref(false);
const error = ref('');
const errors = ref({
  firstname: '',
  lastname: '',
  email: '',
  password: ''
})

const validate = () => {
  let valid = true;

  // Validation of firstname field
  if (!firstname.value.trim()){
    valid = false;
    errors.value.firstname +='The firstname field is required.';
  }

  // Validation of lastname field
  if (!lastname.value.trim()){
    valid = false;
    errors.value.lastname +='The lastname field is required.';
  }

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
  }else if(password.value.trim().length < 4){
    valid = false;
    errors.value.password +='The password must contain at least 4 characters';
  }

  return valid;
}

async function signUser() {
  loading.value = true;
  try {
    if(validate()){
      console.log(firstname.value)
      let response = await signUp(firstname.value, lastname.value, email.value, password.value);
      if(response){
        success.value = 'Your account was successfully created.'
        navigateTo('/login');
      }
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