// https://nuxt.com/docs/api/configuration/nuxt-config
import { defineNuxtConfig } from "nuxt/config";

export default defineNuxtConfig({
    ssr: false,
    runtimeConfig: {
        public: {
            public: {
                apiBaseUrl: '/',
            }
        }
    },
    css: [
        'vuetify/lib/styles/main.sass',
        '@mdi/font/css/materialdesignicons.min.css'
    ],
    build: {
        transpile: ['vuetify'],
    },
    modules: [
        '@nuxt-alt/auth',
        '@pinia/nuxt',
    ],
    plugins: [
        { src: '~/plugins/vuetify' }
    ],
    auth: {
        globalMiddleware: true,
        strategies: {
            local: {
                token: {
                    property: 'token',
                    global: true,
                },
                endpoints: {
                    login: {
                        url: process.env.NUXT_PUBLIC_API_BASE_URL + '/login',
                        method: 'post',
                    }
                },
                user: {
                    property: false
                }
            }
        }
    },
    vuetify: {
        defaultAssets: {
            icons: 'mdi',
        },
    },

})
