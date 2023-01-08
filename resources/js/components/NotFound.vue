<template>
    <v-app>
        <v-main>
            <img :src="baseUrl" class="logo-404" />

            <p class="title">Oh no!!</p>
            <p class="subtitle">
                You’re either misspelling the URL <br /> or requesting a page that's no longer here.
            </p>
            <p class="subtitle">
                <v-btn v-if="!!$store.getters.auth" color="error" link outlined class="mr-2" @click="confirm_dialog=true" :loading="app_loader">
                    <v-icon left dark> mdi-delete-empty-outline </v-icon> Clear Cache
                </v-btn>

                <v-btn color="primary" link outlined class="mr-2" @click="goBack" :loading="app_loader">
                    <v-icon left dark> mdi-keyboard-backspace </v-icon> Back
                </v-btn>
            </p>
        </v-main>

        <v-dialog width="450px" v-model="confirm_dialog">
            <v-card>
                <v-card-title class="text-h5">
                    <v-icon left color="error">mdi-delete-empty-outline</v-icon>
                    Confirm
                </v-card-title>

                <v-card-text>
                    Are you sure you want to clear cache ? <br>
                    It will redirect you to login page. <br>
                    Your selected filters will reset automatically.
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="goClearCache()"> YES </v-btn>
                    <v-btn text @click="confirm_dialog=false"> NO </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </v-app>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    data() {
        return {
            baseUrl: location.origin.concat('/images/404.svg'),
            confirm_dialog: false,
        }
    },
    watch: {
        confirm_dialog(val) {
            if (!val) {
                this.confirm_dialog = false
            }
        }
    },
    computed: {
        ...mapGetters(['app_loader'])
    },
    methods: {
        goBack() {
            this.$store.commit("SET_APP_LOADER", true)
            this.$router.go(-1)
        },
        goClearCache() {
            // localStorage.clear()
            localStorage.removeItem("meta");
            this.$router.replace({ name: "Login" })
        },
    },
    created() {
        this.$vuetify.theme.dark = localStorage.getItem("app_theme") != '0' ? true : false
    },
    mounted() {
        this.$store.commit("SET_APP_LOADER", false)
    }
}

</script>

<style scoped>
    .logo-404{
        position: absolute;
        margin-left: auto;
        margin-right: auto;
        left: 0;
        right: 0;
        top: 16vmin;
        width: 30vmin;
    }

    .title{
        font-weight: 600;
        text-align: center;
        font-size: 5vmin;
        margin-top: 31vmin;
    }

    .subtitle {
        font-weight: 400;
        text-align: center;
        font-size: 3.5vmin;
        margin-top: -1vmin;
        margin-bottom: 9vmin;
    }
</style>
