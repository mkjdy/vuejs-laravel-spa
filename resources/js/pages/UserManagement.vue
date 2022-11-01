<template>
    <div>
        <v-data-table
            :headers="headers"
            :items="users.data"
            :options.sync="options"
            :server-items-length="totalDocument"
            :footer-props="{
                'items-per-page-options': [5, 10, 15, 30, 50],
                'show-first-last-page': true
            }"
            @update:items-per-page="reloadData()"
            @update:page="reloadData()"
            @update:sort-desc="reloadData()"
            :page.sync="page"
            :items-per-page="10"
            class="elevation-1"
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
                    <v-text-field v-model="search" label="Search" placeholder="Name" prepend-inner-icon="mdi-magnify" hide-details @keyup.enter="searchFunction()">
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
                            <v-list-item @click.stop="manage_dialog={title: 'Add'}">
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

            <template v-slot:item.actions="{ item }">
                <v-icon small class="mr-2" @click="manage_dialog={title: 'Edit', item: item}"> mdi-pencil </v-icon>

                <v-dialog width="450px">
                    <template v-slot:activator="{ on, attrs }">
                        <v-icon small v-bind="attrs" v-on="on"> mdi-delete </v-icon>
                    </template>
                    <v-card>
                        <v-card-title class="text-h5">
                            <v-icon left color="error">mdi-alert-remove-outline</v-icon>
                            Remove
                        </v-card-title>

                        <v-card-text>
                            Are you sure you want to remove this record.
                        </v-card-text>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn text> YES </v-btn>
                            <v-btn text> NO </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
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

        <UserAddEditDialog v-if="!!manage_dialog" :manage_dialog="manage_dialog" @close-dialog="manage_dialog=false" />

    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import axios from '../config/axios'
import UserAddEditDialog from '../components/user/UserAddEditDialog'
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
            },
            { text: "Username", value: "username" },
            { text: "Actions", value: "actions", sortable: false, align: "center", width: 10 },
        ],
        search: null,
        options: {},
        users: {data:[]},
        totalDocument: 0,
        page: 1,
        manage_dialog: false,
    }),

    computed: {
        ...mapGetters(['visit_page', 'app_loader'])
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
            await axios.get(`user/get` +
                `?page=${opt_page}` +
                `&rows=${this.options.itemsPerPage}` +
                `${!!this.options?.sortBy?.length?`&order_by=${this.options?.sortBy[0]?.match('updated_at')?'updated_at':this.options?.sortBy[0]},${this.options?.sortDesc[0]?'desc':'asc'}`:''}` +
                `${!!this.search?'&search='+this.search:''}`
            ).then((res)=>{

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
    },
};
</script>

<style>
</style>