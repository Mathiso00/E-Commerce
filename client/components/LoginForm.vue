<template>
  <div class=" d-flex justify-center mx-auto">
    <form @submit.prevent="handleSubmit"
          :class="{ 'w-75': !smAndUp, 'md:w-50': mdAndUp }"
          class="my-5 pt-5 pb-5 w-50 bg-white bord">
      <h1 class="text-center">Login here </h1>
      <v-text-field
          v-model="email.value.value"
          :error-messages="email.errorMessage.value"
          label="E-mail"
      ></v-text-field>
      <v-text-field
          v-model="password.value.value"
          :error-messages="password.errorMessage.value"
          label="Password"
      ></v-text-field>
      <div class=" d-flex justify-center py-4">
        <v-btn
            class="me-4"
            type="submit"
        >
          submit
        </v-btn>
      </div>

    </form>
  </div>
</template>

<script setup lang="ts">
import {useField, useForm} from "vee-validate";
import {useDisplay} from "vuetify";

const { smAndUp, mdAndUp } = useDisplay();
const email = useField('email');
const password = useField('password')

const handleSubmit = useForm({
  validationSchema: {
    email (value: string) {
      if (/^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(value)) return true

      return 'Must be a valid e-mail.'
    },

    password (value: string) {
      if (value?.length >= 4) return true

      return 'Password needs to be at least 4 characters.'
    },
  }
})

/*const submit = handleSubmit((values: any) => {
  alert(JSON.stringify(values, null, 2))
})*/
</script>

<style>
.bord {
  box-shadow: -10px -10px #cdbe7c;
}
</style>