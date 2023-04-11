<template>
  <div>
    <form @submit.prevent="submit">
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

      <v-btn
          class="me-4"
          type="submit"
      >
        submit
      </v-btn>
    </form>
  </div>
</template>

<script setup lang="ts">
import {useField, useForm} from "vee-validate";

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

const submit = handleSubmit((values: any) => {
  alert(JSON.stringify(values, null, 2))
})
</script>