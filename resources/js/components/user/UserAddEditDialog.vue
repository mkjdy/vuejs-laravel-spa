<template>
    <v-row justify="center">
        <v-dialog
            v-model="dialog"
            persistent
            max-width="600px"
        >
            <ValidationObserver ref="observer" v-slot="{ invalid }">
            <v-card>
                <v-card-title>
                    <span class="text-h5"> {{ manage_dialog.title }} User</span>
                </v-card-title>
                <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <div align="center" class="py-0">
                                <v-avatar
                                    width="250"
                                    height="250"
                                    v-if="imageData.length > 0 && form.image"
                                >
                                    <img
                                        :src="imageData"
                                        alt="Display Image"
                                        class="profile"
                                    >
                                </v-avatar>
                                <a
                                    v-if="(isAddImage == false && (!form.image && imageData.length < 1)) || (isAddImage == false && !form.image) || !form.image"
                                    :href="!form.image?buildURL():imageData"
                                    target="_blank"
                                >
                                    <v-avatar
                                        width="250"
                                        height="250"
                                    >
                                        <img
                                            @error="defaultImage"
                                            :src="!form.image?buildURL():imageData"
                                            alt="Display Image"
                                            class="profile"
                                        >
                                    </v-avatar>
                                </a>
                                <v-file-input
                                    :label="`Display Image`"
                                    accept=".jpg,.png"
                                    ref="files"
                                    show-size
                                    clearable
                                    v-model="form.image"
                                    @change="previewImage"
                                    truncate-length="105"
                                />
                            </div>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="4"
                            md="4"
                        >
                            <ValidationProvider v-slot="{ errors }" rules="required">
                                <v-text-field
                                    v-model="form.first_name"
                                    label="First Name*"
                                    required
                                    :error-messages="errors"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="4"
                            md="4"
                        >
                            <v-text-field
                                v-model="form.middle_name"
                                label="Middle Name"
                            ></v-text-field>
                        </v-col>
                        <v-col
                            cols="12"
                            sm="4"
                            md="4"
                        >
                            <ValidationProvider v-slot="{ errors }" rules="required">
                                <v-text-field
                                    v-model="form.last_name"
                                    label="Last Name*"
                                    persistent-hint
                                    required
                                    :error-messages="errors"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col
                            v-if="isEdit"
                            cols="12"
                        >
                            <v-checkbox
                                color="success"
                                v-model="form.change_username_password"
                                label="Change username or password"
                            ></v-checkbox>
                        </v-col>
                        <v-col v-if="!isEdit || form.change_username_password" cols="12" sm="4">
                            <ValidationProvider v-slot="{ errors }" rules="required">
                                <v-text-field
                                    v-model="form.username"
                                    label="Username*"
                                    required
                                    :error-messages="errors"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col v-if="!isEdit || form.change_username_password" cols="12" sm="4">
                            <ValidationProvider name="Password" v-slot="{ errors }" :rules="`required|length:5|alpha_num${!!form.password_confirmation ? '|confirmed:confirmation' : ''}`">
                                <v-text-field
                                    v-model="form.password"
                                    label="Password*"
                                    type="password"
                                    required
                                    :error-messages="errors"
                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col v-if="!isEdit || form.change_username_password" cols="12" sm="4">
                            <ValidationProvider v-slot="{ errors }" rules="required|length:5|alpha_num" vid="confirmation">
                                <v-text-field
                                    v-model="form.password_confirmation"
                                    label="Confirm Password*"
                                    type="password"
                                    required
                                    :error-messages="errors"

                                ></v-text-field>
                            </ValidationProvider>
                        </v-col>
                        <v-col cols="12">
                            <v-select
                                v-model="form.role"
                                :items="roles"
                                item-text="name"
                                multiple
                                chips
                                deletable-chips
                                clearable
                                label="Role"
                            />
                        </v-col>
                    </v-row>
                </v-container>
                <small>*indicates required field</small>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="blue darken-1"
                        text
                        @click="dialog = false"
                    >
                        Close
                    </v-btn>
                    <v-btn
                        v-if="!submit_loading && !invalid"
                        color="blue darken-1"
                        text
                        @click="save()"
                    >
                        Save
                    </v-btn>
                    <v-btn
                        v-else
                        color="blue darken-1"
                        text
                        disabled
                        :loading="submit_loading"
                    >
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
            </ValidationObserver>
        </v-dialog>
    </v-row>
</template>

<script>
import { ValidationProvider, ValidationObserver } from "vee-validate";
import axios from '../../config/axios'
import { mapGetters } from 'vuex';
export default {
    props: {
        manage_dialog: {
            default: false
        }
    },
    components: {
        ValidationProvider,
        ValidationObserver
    },
    data() {
        return {
            dialog: false,
            roles: [],
            form: {
                first_name: null,
                middle_name: null,
                last_name: null,
                username: null,
                password: null,
                password_confirmation: null,
                file_name: null,
                avatar: null,
                image: null,
                role: null,

                change_username_password: true,
            },
            baseURL: location.origin.concat('/images/no_image.jpg'),
            imageData: "",
            backUp: null,
            submit_loading: false,
        }
    },
    computed: {
        ...mapGetters([]),
        isEdit() {
            return !!this.manage_dialog?.item?.id
        },
        isAddImage() {
            let value = JSON.parse(JSON.stringify(this.backUp))
            return (value)?false:true
        },
    },
    watch: {
        dialog(val) {
            if (!val) {
                this.$emit('close-dialog')
            }
        }
    },
    methods: {
        defaultImage(event){
            event.target.src = window.location.origin + "/images/no_image.jpg"
        },
        previewImage: function(event) {
            if (event) {
                var reader = new FileReader();
                reader.onload = (e) => { this.imageData = e.target.result; }
                reader.readAsDataURL(event);
            }
            this.form.file_name = `${Math.floor(Date.now() / 1000)}_${this.form.image?.name}`
            this.form.avatar = typeof this.form.image == 'string' || !this.form.image? 'images/no_image.jpg' : `storage/images/${this.form.file_name}`

            this.$emit('costum-validation', { validate: !this.backUp && !this.image?false:true, key: 'image', data: this.image })
        },
        buildURL() {
            let value = this.backUp ? JSON.parse(JSON.stringify(this.backUp)) : 'images/no_image.jpg'
            return value ? location.origin.concat('/') + value.replace('\\', '/').replace('public/', '') : null
        },
        save() {
            this.submit_loading = true
            var data = new FormData();
            for (var row in Object.entries(this.form)) {
                const [key, value] = Object.entries(this.form)[row]
                data.append(key, key == 'role' ? JSON.stringify(value) : (value == null ? '' : value ) );
            }

            if (this.isEdit) {
                axios.post('users/'+this.form.id+'?_method=PUT', data).then(response=>{
                    this.$emit('success-edit')
                    this.$store.commit('SET_SNACKBAR', response.data)
                    this.submit_loading = false
                }).catch(error=>{
                    this.$store.commit('SET_SNACKBAR', error.response.data)
                    this.submit_loading = false
                })
            } else {
                axios.post('users', data).then(response=>{
                    this.$emit('success-add')
                    this.$store.commit('SET_SNACKBAR', response.data)

                    this.$refs.observer.reset()
                    this.form.first_name = null
                    this.form.middle_name = null
                    this.form.last_name = null
                    this.form.username = null
                    this.form.password = null
                    this.form.password_confirmation = null
                    this.form.file_name = null
                    this.form.avatar = null
                    this.form.image = null
                    this.form.role = null

                    this.submit_loading = false
                }).catch(error=>{
                    this.$store.commit('SET_SNACKBAR', error.response.data)
                    this.submit_loading = false
                })
            }
        },
    },
    async created() {
        this.dialog = !!JSON.parse(JSON.stringify(this.manage_dialog))

        // this.backUp = JSON.parse(JSON.stringify(this.$attrs.value))

        if (this.isEdit) {
            const item = JSON.parse(JSON.stringify(this.manage_dialog.item))
            this.form.id = item.id
            this.form.first_name = item.first_name
            this.form.middle_name = item.middle_name
            this.form.last_name = item.last_name
            this.form.username = item.username
            // this.form.password = item.password
            // this.form.password_confirmation = item.password_confirmation
            // this.form.file_name = item.file_name
            this.form.avatar = item.avatar
            // this.form.image = item.avatar
            this.form.role = item.role

            this.form.change_username_password = false

            this.backUp = item.avatar
        }

        await axios.get('roles').then(response => {
            this.roles = response.data;
        });
    }
}
</script>

<style scoped>
    .profile {
        border: 2px solid rgba(0,0,0,0.1);
        padding: 4px;
    }
</style>