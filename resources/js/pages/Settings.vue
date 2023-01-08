<template>
    <div>
        <v-tabs v-model="tab" show-arrows>
            <v-tab><v-icon left>mdi-cogs</v-icon> General</v-tab>
            <v-tab><v-icon left>mdi-account-details</v-icon>Profile</v-tab>
            <v-tab><v-icon left>mdi-account-cog-outline</v-icon>Account</v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item>
                <v-card
                    :loading="change_appearance_loading"
                    :disabled="change_appearance_loading"
                    flat
                >
                    <v-subheader>Appearance</v-subheader>

                    <v-card-text class="py-0">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>Theme</v-list-item-title>
                                    <v-list>
                                        <v-list-item>
                                            <v-switch
                                                v-model="dark_mode"
                                                inset
                                                label="Dark Mode"
                                                hide-details
                                                @change="changeTheme()"
                                            ></v-switch>
                                        </v-list-item>
                                    </v-list>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>

                <!-- <v-card
                    :loading="loading1"
                    :disabled="loading1"
                    flat
                >
                    <v-subheader>Others</v-subheader>

                    <v-card-text class="py-0">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>Title</v-list-item-title>
                                    <v-list>
                                        <v-list-item>
                                            Content
                                        </v-list-item>
                                    </v-list>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn
                            color="primary"
                            text
                            @click="reserve"
                        >
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-card> -->
            </v-tab-item>
            <v-tab-item>
                <v-card flat>
                    <v-card-text>
                        <UserProfile />
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item>
                <v-card
                    :loading="update_account_loading"
                    :disabled="update_account_loading"
                    flat
                >
                    <v-subheader>Login Security</v-subheader>

                    <v-card-text class="py-0">
                        <v-list>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>Change Username</v-list-item-title>
                                    <v-list>
                                        <v-list-item>
                                            <ValidationProvider
                                                v-slot="{ errors, valid }"
                                                rules="required"
                                            >
                                                <v-text-field
                                                    dense
                                                    filled
                                                    label="Username"
                                                    v-model="form.username"
                                                    :error-messages="errors"
                                                    style="width:350px"
                                                ></v-text-field>
                                            </ValidationProvider>
                                        </v-list-item>
                                    </v-list>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-list-item-title>Change Password</v-list-item-title>
                                    <v-list>
                                        <v-list-item>
                                            <v-row>
                                                <v-col cols="12">
                                                    <ValidationProvider
                                                        v-slot="{ errors, valid }"
                                                        rules="required"
                                                    >
                                                        <v-text-field
                                                            dense
                                                            filled
                                                            label="Current Password"
                                                            v-model="form.old_password"
                                                            :error-messages="errors"
                                                            :append-icon="show_current_password ? 'mdi-eye' : 'mdi-eye-off'"
                                                            :type="show_current_password ? 'text' : 'password'"
                                                            @click:append="show_current_password = !show_current_password"
                                                            style="width:350px"
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </ValidationProvider>
                                                </v-col>
                                                <v-col cols="12">
                                                    <ValidationProvider
                                                        v-slot="{ errors, valid }"
                                                        rules="required|length:5|alpha_num|confirmed:confirmation"
                                                    >
                                                        <v-text-field
                                                            dense
                                                            filled
                                                            label="New Password"
                                                            v-model="form.new_password"
                                                            :error-messages="errors"
                                                            :append-icon="show_new_password ? 'mdi-eye' : 'mdi-eye-off'"
                                                            :type="show_new_password ? 'text' : 'password'"
                                                            @click:append="show_new_password = !show_new_password"
                                                            style="width:350px"
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </ValidationProvider>
                                                </v-col>
                                                <v-col cols="12">
                                                    <ValidationProvider
                                                        v-slot="{ errors, valid }"
                                                        rules="required|length:5|alpha_num"
                                                        vid="confirmation"
                                                    >
                                                        <v-text-field
                                                            dense
                                                            filled
                                                            label="Confirm New Password"
                                                            v-model="form.confirm_new_password"
                                                            :error-messages="errors"
                                                            :append-icon="show_confirm_new_password ? 'mdi-eye' : 'mdi-eye-off'"
                                                            :type="show_confirm_new_password ? 'text' : 'password'"
                                                            @click:append="show_confirm_new_password = !show_confirm_new_password"
                                                            style="width:350px"
                                                            hide-details="auto"
                                                        ></v-text-field>
                                                    </ValidationProvider>
                                                </v-col>
                                            </v-row>
                                        </v-list-item>
                                    </v-list>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn
                            color="primary"
                            text
                            @click="updateAccount()"
                        >
                            Save
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-tab-item>
        </v-tabs-items>

        <!-- <h1>SETTINGS</h1>
        Theme :
        <v-btn v-if="$vuetify.theme.dark" @click="$vuetify.theme.dark = !$vuetify.theme.dark">Light</v-btn>
        <v-btn v-else @click="$vuetify.theme.dark = !$vuetify.theme.dark">dark</v-btn> -->
    </div>
</template>

<script>
import UserProfile from './UserProfile'
import { ValidationObserver, ValidationProvider } from 'vee-validate'
export default {
    components: {
        UserProfile,
        ValidationObserver,
        ValidationProvider,
    },
    data () {
        return {
            tab: "1",
            dark_mode: false,
            change_appearance_loading: false,
            update_account_loading: false,
            loading1: false,
            form: {
                avatar: null,
                first_name: null,
                middle_name: null,
                last_name: null,
                username: null,
                email: null,
                contact_number: null,
                old_password: null,
                new_password: null,
                confirm_new_password: null
            },
            show_current_password: false,
            show_new_password: false,
            show_confirm_new_password: false,
        }
    },
    methods: {
        changeTheme() {
            this.change_appearance_loading = true
            setTimeout(() => {
                this.change_appearance_loading = false
                localStorage.setItem("app_theme", !!this.dark_mode ? 1 : 0)
                this.$vuetify.theme.dark = this.dark_mode
            }, 500)
        },
        updateAccount() {
            this.update_account_loading = true
            setTimeout(() => {
                this.update_account_loading = false
            }, 500)
        },
        reserve () {
            this.loading1 = true

            setTimeout(() => (this.loading1 = false), 2000)
        },
    },
    mounted() {
        this.dark_mode = localStorage.getItem("app_theme") != 0 ? true : false
        this.$store.commit('SET_APP_LOADER', false)
    }
}
</script>

<style>

</style>