<template>
    <v-app>
        <v-app-bar
            app
            color="primary"
            dark
            flat
        >
            <v-toolbar-title>Application Title</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu offset-y>

                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        plain
                        icon
                        v-bind="attrs"
                        v-on="on"
                        class="d-flex d-sm-none"
                    >
                    <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item
                        @click.prevent="aboutPage"
                    >
                        <v-icon left>mdi-check</v-icon>
                        <v-list-item-title>
                            About
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        @click.prevent="changelogsPage"
                    >
                        <v-list-item-title>
                            <v-icon left>mdi-format-list-text</v-icon>
                            Changelogs
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item
                        @click.prevent="loginPage"
                    >
                        <v-list-item-title>
                            <v-icon left>mdi-login-variant</v-icon>
                            Login
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <v-btn
                plain
                dark
                @click.prevent="aboutPage"
                class="d-none d-sm-flex"
            >
                <v-icon left>mdi-information-outline</v-icon>
                About
            </v-btn>

            <v-btn
                plain
                dark
                @click.prevent="changelogsPage"
                class="d-none d-sm-flex"
            >
                <v-icon left>mdi-format-list-text</v-icon>
                Changelogs
            </v-btn>

            <v-btn
                plain
                dark
                @click.prevent="loginPage"
                class="d-none d-sm-flex"
            >
                <v-icon left>mdi-login-variant</v-icon>
                Login
            </v-btn>

            <Theme />

        </v-app-bar>

        <v-main>
            <router-view />
        </v-main>

    </v-app>
</template>

<script>
import Theme from "./general/Theme";

export default {
    components: {
        Theme,
    },
    methods: {
        loginPage() {
            if(this.$route.name != 'Login') {
                this.$router.push({name: 'Login'});
            }
        },
        changelogsPage() {
            if(this.$route.name != 'Changelog') {
                this.$router.push({name: 'Changelog'});
            }
        },
        aboutPage() {
            if(this.$route.name != 'About') {
                this.$router.push({name: 'About'});
            }
        }
    },
}
</script>