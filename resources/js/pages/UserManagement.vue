<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="users.data"
            :options.sync="options"
            :server-items-length="totalDocument"
            :footer-props="{
                'items-per-page-options': [5, 10, 25, 50, 100],
                'show-first-last-page': true
            }"
            @update:items-per-page="reloadData()"
            @update:page="reloadData()"
            @update:sort-desc="reloadData()"
            :page.sync="page"
            :items-per-page="10"
            class="elevation-1"
            :fixed-header="users.data.length > 10"
            :height="users.data.length > 10 ? '75vh' : ''"
        >
            <template v-slot:[`footer.page-text`]="items" v-if="options.list.length">
                Page:&nbsp;
                <select
                    v-if="options.list.length > 1"
                    v-model="options.page"
                    :title="`Jump to page 1-${Math.max(...options.list)}`"
                    class="mr-4 px-2 pagination-select"
                    :style="`border:2px solid #F7F7F7;border-radius:5px;text-align:center;`"
                >
                    <option v-for="(page, i) in options.list" :value="page" :label="page" :key="i" style="color:black" />
                </select>
                <span v-else class="mr-4"> {{ options.list[0] }} </span>
                {{ (items.pageStart).toLocaleString() }} - {{ (items.pageStop).toLocaleString() }} of {{ (items.itemsLength).toLocaleString() }}
            </template>

            <template v-slot:top>
                <v-toolbar flat>
                    <v-text-field v-model="search" label="Search" placeholder="User" prepend-inner-icon="mdi-magnify" hide-details @keyup.enter="searchFunction()">
                        <template v-slot:append-outer v-if="!!search">
                            <v-btn depressed small @click="searchFunction()">SEARCH</v-btn>
                        </template>
                    </v-text-field>
                    <v-spacer></v-spacer>
                    <v-menu
                        bottom
                        left
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                icon
                                v-bind="attrs"
                                v-on="on"
                            >
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>

                        <v-list dense>
                            <v-list-item v-if="can.add" @click.stop="manage_dialog={title: 'Add'}">
                                <v-list-item-icon>
                                    <v-icon>mdi-plus</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Add New</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                            <v-list-item @click.stop="reloadData()">
                                <v-list-item-icon>
                                    <v-icon>mdi-reload {{ app_loader ? 'mdi-spin' : ''}}</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Reload</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-toolbar>
            </template>

            <template v-slot:[`item.role`]="{ item }">
                <v-menu open-on-hover top offset-y :close-on-content-click="false" v-for="(role, i) in item.role" :key="role" nudge-right="10">
                    <template v-slot:activator="{ on, attrs }">
                        <v-chip v-bind="attrs" v-on="on" class="ma-1" label small color="primary" dark>
                            {{ role }}
                        </v-chip>
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-content style="width:350px">
                                <v-list-item-title v-text="role" style="font-weight:bold" />
                                <span> {{ item.roles[i].description }} </span>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>

            <template v-slot:[`item.permission`]="{ item }">
                <v-expansion-panels flat dense title="User Access/Permissions" v-if="item.permission.length">
                    <v-expansion-panel style="background-color:rgba(0,0,0,0) !important">
                        <v-expansion-panel-header class="py-0 px-2">
                            <template v-slot:default="{ open }">
                                <v-row no-gutters>
                                    <v-col cols="12" class="text--secondary">
                                        <v-fade-transition leave-absolute>
                                            <span v-if="open" key="0"> Permissions </span>
                                            <div v-else key="1">
                                                <v-chip class="ma-1" label small v-for="(access, i) in getCategAccess(item.permission)" :key="i" :color="getCategColor(access)" dark>{{ access }}</v-chip>
                                            </div>
                                        </v-fade-transition>
                                    </v-col>
                                </v-row>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-chip class="ma-1" label small v-for="permission in item.permission" :key="permission" :color="getColor(permission)" dark>
                                {{ permission }}
                            </v-chip>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </template>

            <template v-slot:[`item.name`]="{ item }">
                <tr>
                    <th>
                        <a
                            :href="baseURL + (item.avatar || 'images/no_image.jpg')"
                            target="_blank"
                        >
                            <v-avatar size="32px">
                                <img
                                    style="border: 1px solid rgba(0,0,0,0.1)"
                                    alt="Display Image"
                                    :src="baseURL + (item.avatar || 'images/no_image.jpg')"
                                >
                            </v-avatar>
                        </a>
                    </th>
                    <td class="pl-2">
                        {{ item.name }}
                    </td>
                </tr>
            </template>

            <template v-slot:item.actions="{ item }">
                <v-icon v-if="can.edit" small class="mr-2" @click="manage_dialog={title: 'Edit', item: item}"> mdi-pencil </v-icon>
                <v-icon v-if="can.remove" small @click="confirm_dialog_item=item" title="Remove" :disabled="item.id == (auth || {}).id"> {{  (item.id == (auth || {}).id) ? 'mdi-cancel' : 'mdi-delete' }} </v-icon>
            </template>

            <template v-slot:no-data>
                {{ app_loader ? 'Loading... Please wait' : 'No data available' }}
            </template>

        </v-data-table>

        <v-card  style="border-top-left-radius: 0; border-top-right-radius: 0;">
            <div style="position: absolute; top: -39px; font-size:.8rem" class="ml-4 hidden-xs-only">
                <v-icon small>{{ app_loader ? 'mdi-reload mdi-spin' : 'mdi-clock-time-eight-outline' }}</v-icon> Last Updated: <strong>{{ visit_page[this.$route.path].last_update_data }}</strong>
            </div>
        </v-card>

        <v-card class="mt-1 d-xs-flex d-sm-none d-md-none d-lg-none d-xl-none" flat>
            <v-icon small>mdi-clock-time-eight-outline</v-icon> Last Updated: <strong>{{ visit_page[this.$route.path].last_update_data }}</strong>
        </v-card>

        <v-dialog width="450px" :value="!!confirm_dialog_item" persistent>
            <v-card>
                <v-card-title class="text-h5">
                    <v-icon left color="error">mdi-alert-remove-outline</v-icon>
                    Remove
                </v-card-title>

                <v-card-text>
                    Are you sure you want to remove this user <br> <strong>@{{ confirm_dialog_item.username }}</strong> {{ confirm_dialog_item.name }}
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="confirmDelete()"> YES </v-btn>
                    <v-btn text @click="confirm_dialog_item=false"> NO </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <UserAddEditDialog v-if="!!manage_dialog" :manage_dialog="manage_dialog" @close-dialog="manage_dialog=false" @success-add="reloadData()" @success-edit="reloadData();manage_dialog=false" />

    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import axios from '../config/axios'
import UserAddEditDialog from '../components/user/UserAddEditDialog'
import { permissionsCategoryColor } from '../helpers'
export default {
    components: {
        UserAddEditDialog
    },
    data: () => ({
        headers: [
            {
                text: "Name",
                align: "start",
                value: "name",
                width: "250px",
            },
            { text: "Username", value: "username", width: "250px", },
            { text: "Role", value: "role" },
            // { text: 'Access', value: "permission" },
            { text: "Actions", value: "actions", sortable: false, align: "end", width: 10 },
        ],
        search: null,
        options: {},
        users: {data:[]},
        totalDocument: 0,
        page: 1,
        manage_dialog: false,
        confirm_dialog_item: false,
        baseURL: location.origin.concat('/'),
    }),

    computed: {
        ...mapGetters(['visit_page', 'app_loader', 'auth']),
        can() {
            let add = this.$can('add-user')
            let edit = this.$can('edit-user')
            let remove = this.$can('delete-user')

            if (!edit && !remove) {
                this.headers.splice(this.headers.findIndex(h=>h.value == 'actions'), 1)
            }

            return { add, edit, remove }
        }
    },

    created() {
        this.options.itemsPerPage = this.visit_page[this.$route.path]?.per_page ?? 10
        this.options.page = (this.visit_page[this.$route.path]?.current_page ?? 1)
        this.options.list = []
        this.getDataFromApi()
    },

    methods: {
        reloadData() {
            this.getDataFromApi();
        },
        searchFunction() {
            this.options.page == 1 ? this.getDataFromApi() : this.options.page = 1
        },
        async getDataFromApi() {
            this.$store.commit("SET_APP_LOADER", true)
            this.$store.commit('SET_VISIT_PAGE', {
                name: this.$route.path,
                param: {
                    current_page: this.options.page ?? 1,
                    per_page: this.options.itemsPerPage ?? 10,
                }
            })
            var opt_page = Object.values(this.options).length ? this.options.page : 1
            await axios.get(`users` +
                `?page=${opt_page}` +
                `&rows=${this.options.itemsPerPage}` +
                `${!!this.options?.sortBy?.length?`&order_by=${this.options?.sortBy[0]?.match('updated_at')?'updated_at':this.options?.sortBy[0]},${this.options?.sortDesc[0]?'desc':'asc'}`:''}` +
                `${!!this.search?'&search='+this.search:''}`
            ).then((res)=>{

                res.data.data.forEach((user, index) => {
                    user.role = []
                    user.permission = []
                    user.roles.forEach(role=>{
                        user.role.push(role.name)
                        role.permissions.forEach(permission=>{
                            if (!user.permission.includes(permission.name)) {
                                user.permission.push(permission.name)
                            }

                        })
                    })
                    try {
                        user.permission = user.permission.sort((a,b) => (a.split("-")[1] > b.split("-")[1]) ? 1 : ((b.split("-")[1] > a.split("-")[1]) ? -1 : 0))
                    } catch (error) {}
                });

                this.users = res.data
                this.totalDocument = res.data.total
                this.options.list = Array(res.data?.last_page).fill(0).reduce((value, row, i)=>{ value.push(i+1); return value; }, [])
                this.$store.commit("SET_APP_LOADER", false)
            }).catch(function (thrown) {
                if (thrown?.response?.status != 401) {
                    this.$store.commit("SET_APP_LOADER", false)
                }
            })
        },
        getCategAccess(item_permission) {
            return item_permission.reduce((list, row)=>{
                let p = row?.split('-')[1]
                if (!list.includes(p)) {
                    list.push(p)
                }
                return list
            }, [])
        },
        getColor(permission) {
            let [action, category] = permission.split('-')
            return permissionsCategoryColor(category.toLowerCase().trim())
        },
        getCategColor(category) {
            return permissionsCategoryColor(category.toLowerCase().trim())
        },
        confirmDelete() {
            axios.delete('users/'+this.confirm_dialog_item?.id).then(response=>{
                this.reloadData()
                this.$store.commit('SET_SNACKBAR', response.data)
                this.confirm_dialog_item = false
            })
        },
    },
};
</script>

<style>
</style>