<template>
    <v-app>
        <v-navigation-drawer app :temporary="!!isMobile" :permanent="!isMobile || (!!isMobile && !!show_nav)" left v-model="drawer" :mini-variant.sync="mini" v-if="show_nav">
            <template v-slot:prepend>
                <v-list-item two-line class="pl-2">
                    <v-list-item-avatar class="my-0">
                        <!--<img src="https://randomuser.me/api/portraits/women/81.jpg">-->
                        <img :src="profilePic">
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title>Jane Smith</v-list-item-title>
                        <v-list-item-subtitle>Logged In</v-list-item-subtitle>
                    </v-list-item-content>
                    <v-btn icon  @click.stop="toggleSideNav()">
                        <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>
                </v-list-item>
            </template>
            <v-divider/>
            <v-list dense>
                <v-list-item-group v-model="selectedItem" color="primary">
                    <v-list-item v-for="item in items" :key="item.title">
                        <v-list-item-icon>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>{{ item.title }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
            <template v-slot:append>
                <v-dialog v-model="logout_confirm_dialog" max-width="450px">
                    <template v-slot:activator="{ on, attrs }">
                        <!-- <v-btn block  outlined v-bind="attrs" v-on="on">
                            <v-icon left>
                                mdi-logout-variant
                            </v-icon>
                            Logout
                        </v-btn> -->

                        <div class="pa-2">
                            <v-btn block class="error" v-if="!mini" v-bind="attrs" v-on="on">
                                <v-icon left>mdi-logout</v-icon>
                                Logout
                            </v-btn>
                            <v-btn block class="error" v-else icon dark elevation="1" v-bind="attrs" v-on="on">
                                <v-icon small>mdi-logout</v-icon>
                            </v-btn>
                        </div>
                    </template>
                    <template v-slot:default="dialog">
                        <v-card>
                            <v-card-title class="headline">
                                <v-icon class="mr-2" size="30px">mdi-logout</v-icon>
                                Logout
                            </v-card-title>
                            <v-card-text>
                                Are you sure you want to logout of the application?
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn text @click.prevent="logout">
                                    Yes
                                </v-btn>
                                <v-btn text @click.prevent="dialog.value = false">
                                    No
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-dialog>
                <!-- <div class="pa-2">
                    <v-btn block class="error" v-if="!mini" @click="logout">
                        <v-icon left>mdi-logout</v-icon>
                        Logout
                    </v-btn>
                    <v-btn block class="error" v-else icon dark elevation="1" @click="logout">
                        <v-icon small>mdi-logout</v-icon>
                    </v-btn>
                </div> -->
            </template>
        </v-navigation-drawer>

        <v-app-bar color="primary" dark app :style="(isMobile ? '' : 'height:64px;')">
            <v-app-bar-nav-icon @click.stop="navVisibility"/>
            <v-toolbar-title> {{ $route.name }} </v-toolbar-title>
        </v-app-bar>

        <v-main>
            <v-container fluid>
                <router-view />
            </v-container>
        </v-main>

        <!-- <v-footer app  class="indigo lighten-5" dense>
            <h6>All Right Reserve @c-2021</h6>
        </v-footer> -->
    </v-app>
</template>

<script>
    export default {
        data () {
            return {
                dialog: false,
                drawer: null,
                mini: false,
                show_nav: true,
                items: [
                    { title: 'Home', icon: 'mdi-home-city' },
                    { title: 'My Account', icon: 'mdi-account' },
                    { title: 'Users', icon: 'mdi-account-group-outline' },
                ],
                selectedItem: 0,
                logout_confirm_dialog: false,
            }
        },
        computed: {
            profilePic() {
                return location.origin + '/image/profile.jpg'
            },
            isMobile() {
                return this.$vuetify.breakpoint.smAndDown
                // return $vuetify.breakpoint.mobile
            }
        },
        methods: {
            navVisibility() {
                if (!!this.isMobile) {
                    this.show_nav = true
                } else {
                    if (this.mini && this.show_nav) {
                        this.mini = false
                    } else {
                        this.show_nav = !this.show_nav
                    }
                }
            },
            logout() {
                axios.post('/api/logout', { headers: { Authorization: 'Bearer' + this.$store?.state?.users?.auth?.token, 'Content-Type': 'application/json' } }).then(response => {
                    this.$store.commit("UNSET_AUTH")
                    localStorage.removeItem("meta");
                    this.$router.replace({ name: "Login" })
                }).catch(error=>{
                    console.log('error in logout:', error)
                })
            },
            toggleSideNav() {
                if (!!this.isMobile) {
                    this.show_nav = !this.show_nav
                } else {
                    this.mini = !this.mini
                }
            }
        },
        mounted() {
            if (!!this.isMobile) {
                this.show_nav = false
            }
            console.log('store:::', this.$store.state)
            /*setTimeout(() => {
                console.log('ds')
                this.$router.push({ name: 'Login'});
            }, 10000);*/
        }
    }
</script>