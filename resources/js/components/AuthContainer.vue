<template>
    <v-app>
        <v-navigation-drawer :color="$vuetify.theme.dark ? '' : '#FCFCFC'" :dark="$vuetify.theme.dark" app :temporary="!!isMobile" :permanent="!isMobile || (!!isMobile && !!show_nav)" left v-model="drawer" :mini-variant.sync="mini" v-if="show_nav">
            <template v-slot:prepend>
                <v-list-item two-line class="pl-2">
                    <v-list-item-avatar class="my-0">
                        <!--<img src="https://randomuser.me/api/portraits/women/81.jpg">-->
                        <img class="display-image" :src="profilePic">
                    </v-list-item-avatar>
                    <v-list-item-content>
                        <v-list-item-title> {{ authUser.first_name }} </v-list-item-title>
                        <v-list-item-subtitle>Logged In</v-list-item-subtitle>
                    </v-list-item-content>
                    <v-btn icon  @click.stop="toggleSideNav()" title="Minimize Sidebar">
                        <v-icon>mdi-chevron-left</v-icon>
                    </v-btn>
                </v-list-item>
            </template>
            <v-divider/>
            <v-list nav dense>
                <v-list-item-group v-model="selectedItem" color="primary">
                    <!-- <v-list-item v-for="(item, i) in items" :key="i" :to="{ name: item.name }">
                        <v-list-item-icon>
                            <v-icon>{{ item.icon }}</v-icon>
                        </v-list-item-icon>

                        <v-list-item-content>
                            <v-list-item-title>{{ item.title }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item> -->


                    <v-list-item v-if="$can('view-dashboard')" :to="{ name: 'Dashboard' }" title="Dashboard">
                        <v-list-item-icon>
                            <v-icon>mdi-chart-bar</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Dashboard</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item v-if="$can('generate-reports')" :to="{ name: 'Reports' }" title="Reports">
                        <v-list-item-icon>
                            <v-icon>mdi-folder-open-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Reports</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <!-- <v-list-item :to="{ name: 'Settings' }">
                        <v-list-item-icon>
                            <v-icon>mdi-cog-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Settings</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item> -->

                    <v-list-group
                        v-if="$can([
                            'view-role',
                            'view-user',
                        ])"
                        :value="['Roles', 'User Management'].includes($route.name)"
                        no-action
                        :title="`Access Control${['Roles', 'User Management'].includes($route.name) ? ` (${$route.name})` : ''}`"
                    >
                        <template v-slot:activator>
                            <v-list-item-icon>
                                <v-icon>mdi-account-cog-outline</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>Access Control</v-list-item-title>
                            </v-list-item-content>
                        </template>
                        <v-list-item v-if="$can('view-role')" :to="{ name: 'Roles' }" title="Roles">
                            <v-list-item-title>Roles</v-list-item-title>
                            <v-list-item-icon>
                                <v-icon>mdi-account-key</v-icon>
                            </v-list-item-icon>
                        </v-list-item>
                        <v-list-item v-if="$can('view-user')" :to="{ name: 'User Management' }" title="User Management">
                            <v-list-item-title>User Management</v-list-item-title>
                            <v-list-item-icon>
                                <v-icon>mdi-account-multiple-outline</v-icon>
                            </v-list-item-icon>
                        </v-list-item>
                    </v-list-group>

                    <v-list-item :to="{ name: 'User Profile' }" title="User Profile">
                        <v-list-item-icon>
                            <v-icon>mdi-account-circle-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>User Profile</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                    <v-list-item v-if="$can('view-setting')" :to="{ name: 'Settings' }" title="Settings">
                        <v-list-item-icon>
                            <v-icon>mdi-cog-outline</v-icon>
                        </v-list-item-icon>
                        <v-list-item-content>
                            <v-list-item-title>Settings</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                </v-list-item-group>
            </v-list>
            <template v-slot:append>
                <v-dialog v-model="logout_confirm_dialog" max-width="450px">
                    <template v-slot:activator="{ on, attrs }">
                        <!-- <div class="pa-2">
                            <v-btn block class="error" v-if="!mini" v-bind="attrs" v-on="on">
                                <v-icon left>mdi-logout</v-icon>
                                Logout
                            </v-btn>
                            <v-btn block class="error" v-else icon dark elevation="1" v-bind="attrs" v-on="on">
                                <v-icon small>mdi-logout</v-icon>
                            </v-btn>
                        </div> -->
                        <v-list nav dense title="Logout">
                            <!-- <v-list-item-group v-model="selectedItem" color="error"> -->
                                <v-list-item v-bind="attrs" v-on="on">
                                    <v-list-item-icon>
                                        <v-icon color="error">mdi-logout</v-icon>
                                    </v-list-item-icon>

                                    <v-list-item-content>
                                        <v-list-item-title>Logout</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            <!-- </v-list-item-group> -->
                        </v-list>
                    </template>
                    <template v-slot:default="dialog">
                        <v-card>
                            <v-card-title class="headline">
                                <v-icon color="error" class="mr-2" size="20px">mdi-logout</v-icon>
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

        <v-app-bar :color="$vuetify.theme.dark ? '' : 'primary'" elevation="1" app dark :style="(isMobile ? '' : 'height:64px;')">
            <v-app-bar-nav-icon @click.stop="navVisibility" title="Toggle Sidebar"/>
            <v-app-bar-nav-icon @click="fullscreenMode()" title="Toggle Full screen mode (F11)">
                <v-icon v-if="fullscreen_mode">mdi-fullscreen-exit</v-icon>
                <v-icon v-else>mdi-fullscreen</v-icon>
            </v-app-bar-nav-icon>
            <v-toolbar-title> {{ $route.name }} </v-toolbar-title>

            <v-progress-linear
                v-if="!!app_loader"
                :active="app_loader"
                indeterminate
                absolute
                bottom
                color="#F72e2E"
            ></v-progress-linear>

            <v-spacer></v-spacer>

            <!-- <v-icon @click.stop="">mdi-bell</v-icon> -->
            <Notification/>

        </v-app-bar>

        <v-main>
            <v-snackbar v-model="snackbar_visible" :color="snackbar.type">
                {{ snackbar.message }}
                <template v-slot:action="{ attrs }">
                    <v-btn text v-bind="attrs" @click="$store.commit('UNSET_SNACKBAR')"> Close </v-btn>
                </template>
            </v-snackbar>
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

    import { mapGetters } from 'vuex'
    import axios from '../config/axios'
    import Notification from './general/Notification'

    export default {
        components: {
            // Notification: () => import(/* webpackChunkName: "notification" */ "./general/Notification"),
            Notification
        },
        data () {
            return {
                dialog: false,
                drawer: null,
                mini: false,
                show_nav: true,
                // items: [
                //     { title: 'Dashboard', name: 'Dashboard', icon: 'mdi-chart-bar' },
                //     { title: 'Purchase Requests', name: 'Purchase Requests', icon: 'mdi-file-document-multiple-outline' },
                //     { title: 'Obligation Requests', name: 'Obligation Requests', icon: 'mdi-checkbox-multiple-marked-outline' },
                //     { title: 'Reports', name: 'Reports', icon: 'mdi-folder-open-outline' },
                //     { title: 'Settings', name: 'Settings', icon: 'mdi-cog-outline', sub_menu: [{ title: 'Settings', name: 'Settings', icon: 'mdi-cog-outline' }]},
                // ],
                selectedItem: 0,
                logout_confirm_dialog: false,
                fullscreen_mode: false,
                snackbar_visible: false,
            }
        },
        watch: {
            'snackbar.show'(val) {
                if (!val) {
                    this.$store.commit('UNSET_SNACKBAR')
                    this.snackbar_visible = false
                } else {
                    this.snackbar_visible = true
                }
            }
        },
        computed: {
            ...mapGetters(['auth', 'app_loader', 'snackbar']),
            profilePic() {
                return location.origin.concat('/') + (this.authUser?.avatar || 'images/no_image.jpg')
            },
            isMobile() {
                return this.$vuetify.breakpoint.smAndDown
                // return $vuetify.breakpoint.mobile
            },
            authUser() {
                return this.auth || {}
            },
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
                axios.post('logout').then(response => {
                    this.$store.commit("UNSET_AUTH")
                    localStorage.removeItem("meta");
                    this.$router.replace({ name: "Login" })
                }).catch(error=>{
                    this.$store.commit("UNSET_AUTH")
                    localStorage.removeItem("meta");
                    this.$router.replace({ name: "Login" })
                })
            },
            toggleSideNav() {
                if (!!this.isMobile) {
                    this.show_nav = !this.show_nav
                } else {
                    this.mini = !this.mini
                }
            },
            fullscreenMode() {
                if (document.fullscreenElement) {
                    document.exitFullscreen()
                } else {
                    document.documentElement.requestFullscreen();
                }
            }
        },
        mounted() {
            if (!!this.isMobile) {
                this.show_nav = false
            }

            window.addEventListener('resize', (evt) => {
                if (window.innerHeight == screen.height) {
                    this.fullscreen_mode = true
                } else {
                    this.fullscreen_mode = false
                }
            });
        }
    }
</script>

<style scoped>
    .display-image {
        border: 1px solid rgba(0,0,0,0.1);
    }
</style>