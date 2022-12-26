<template>
    <v-app>
        <v-main>
            <v-container fluid fill-height>
                <v-layout class="align-center justify-center">
                    <v-card width="600px" flat color="transparent">
                        <v-layout class="align-center justify-center">
                            <v-img
                                :max-width="max_width"
                                :max-height="max_height"
                                src="images/app_logo.png"
                                lazy-src="images/app_logo.png"
                            >
                                <template v-slot:placeholder>
                                    <v-row
                                        class="fill-height ma-0"
                                        align="center"
                                        justify="center"
                                    >
                                        <v-progress-circular
                                            indeterminate
                                            color="#F72e2E"
                                        />
                                    </v-row>
                                </template>
                            </v-img>
                        </v-layout>
                        <v-card class="mt-2">
                            <v-toolbar color="primary" dark flat>
                                <v-toolbar-title>Login</v-toolbar-title>
                            </v-toolbar>
                            <v-card-text>
                                <v-alert
                                    v-if="!!login_error"
                                    outlined
                                    type="error"
                                    text
                                >
                                    <v-row align="center">
                                        <v-col class="grow">
                                            {{ login_error }}
                                        </v-col>
                                        <v-col class="shrink">
                                            <v-btn icon small @click.stop="login_error=null"><v-icon color="error">mdi-close</v-icon></v-btn>
                                        </v-col>
                                    </v-row>
                                </v-alert>
                                <validation-observer ref="observer" v-slot="{ invalid }">
                                    <form @submit.prevent="submit">
                                        <validation-provider v-slot="{ errors }" name="username" rules="required">
                                            <v-text-field
                                                class="mt-4"
                                                v-model="username"
                                                :error-messages="errors"
                                                label="Username"
                                                required
                                                clear-icon="mdi-close-circle"
                                                prepend-inner-icon="mdi-account-circle"
                                                outlined
                                                @input="login_error=null"
                                                ref="username"
                                            />
                                        </validation-provider>
                                        <validation-provider v-slot="{ errors }" name="password" rules="required">
                                            <v-text-field
                                                class="mb-4"
                                                v-model="password"
                                                :error-messages="errors"
                                                label="Password"
                                                required
                                                clear-icon="mdi-close-circle"
                                                prepend-inner-icon="mdi-form-textbox-password"
                                                outlined
                                                :append-icon="show_pass ? 'mdi-eye' : 'mdi-eye-off'"
                                                :type="show_pass ? 'text' : 'password'"
                                                @click:append="show_pass = !show_pass"
                                                @input="login_error=null"
                                            />
                                        </validation-provider>
                                        <v-card-actions>
                                            <v-row align="center" justify="center">
                                                <v-btn
                                                    color="primary"
                                                    type="submit"
                                                    :disabled="invalid || login_btn_loading || !username || !password || !!login_error"
                                                    :loading="login_btn_loading"
                                                    justify="end"
                                                    @click="submitRedirect"
                                                    large
                                                >
                                                    login
                                                </v-btn>
                                            </v-row>
                                        </v-card-actions>
                                    </form>
                                </validation-observer>
                            </v-card-text>
                        </v-card>
                    </v-card>
                </v-layout>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>
    import {  ValidationObserver, ValidationProvider } from 'vee-validate'

    export default {
        components: {
            ValidationProvider,
            ValidationObserver,
        },
        data: () => ({
            password: '',
            username: '',
            show_pass: false,
            login_error: null,
            login_btn_loading: false,
        }),

        computed: {
            max_width() {
                switch (this.$vuetify.breakpoint.name) {
                    case "xs":
                        return 300;
                    case "sm":
                        return 400;
                    case "md":
                        return 400;
                    case "lg":
                        return 415;
                    case "xl":
                        return 580;
                }
            },
            max_height() {
                switch (this.$vuetify.breakpoint.name) {
                    case "xs":
                        return 350;
                    case "sm":
                        return 420;
                    case "md":
                        return 450;
                    case "lg":
                        return 455;
                    case "xl":
                        return 620;
                }
            }
        },

        methods: {
            submit () {
                this.$refs.observer.validate()
            },
            submitRedirect() {
                this.login_btn_loading = true
                axios.get("/sanctum/csrf-cookie").then(res => {
                    axios.post('/api/login', {username: this.username, password: this.password}).then(async response => {
                        this.$store.commit('SET_AUTH', response.data)
                        this.$router.push({ name: "Dashboard" })
                    }).catch(error=>{
                        if ([401, 409].includes(error.response.status)) {
                            this.login_error = error?.response?.data?.message
                        }
                    }).finally(()=>{
                        setTimeout(() => {
                            this.login_btn_loading = false
                        }, 100);
                    })
                }).catch(()=>{ this.login_btn_loading = false });
            },
        },
        mounted() {
            this.$refs.username.focus()
        }
    }
</script>

<style>

</style>