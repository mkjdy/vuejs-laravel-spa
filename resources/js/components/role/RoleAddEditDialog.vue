<template>
    <div>
        <v-row justify="center">
            <v-dialog
                v-model="dialog"
                persistent
                transition="scale-transition"
                max-width="600px"
                scrollable
            >
                <v-card>
                    <!-- <v-toolbar
                        dark
                        color="primary"
                        flat
                    >
                        <v-toolbar-title>{{ manage_dialog.title }} Role</v-toolbar-title>
                        <v-spacer />
                        <v-btn icon dark @click="dialog=false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar> -->

                    <v-card-title>
                        <span class="text-h5"> {{ manage_dialog.title }} User</span>
                    </v-card-title>


                    <v-card-text class="pa-0">
                        <v-container>
                            <v-list two-line>
                                <v-list-item>
                                    <v-text-field
                                        v-model="role"
                                        color="primary"
                                        label="Role Name*"
                                        type="text"
                                        @input="checkIfHaveSelected"
                                        :rules="[rules.required, rules.unique]"
                                        clearable
                                        ref="role"
                                    ></v-text-field>
                                </v-list-item>
                                <v-list-item>
                                    <v-textarea
                                        v-model="description"
                                        auto-grow
                                        rows="1"
                                        color="primary"
                                        label="Role Description"
                                        clearable
                                        hide-details
                                    ></v-textarea>
                                </v-list-item>
                            </v-list>

                            <v-list two-line subheader>
                                <v-subheader>
                                    Permissions*
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on }">
                                            <v-icon v-on="on" class="ml-3" color="primary" type="button" v-if="panel.length" @click="none">mdi-eye-off</v-icon>
                                            <v-icon v-on="on" class="ml-3" color="primary" type="button" v-else @click="all">mdi-eye</v-icon>
                                        </template>
                                        <span>{{ panel.length?'Hide All':'Show All' }}</span>
                                    </v-tooltip>
                                </v-subheader>
                                <v-list-item class="mb-2"  style="display:block">
                                    <div>
                                        <v-expansion-panels
                                            v-model="panel"
                                            multiple
                                            focusable
                                        >
                                            <v-expansion-panel
                                                v-for="(item, categoryIdx) in permissions"
                                                :key="categoryIdx"
                                            >
                                                <v-expansion-panel-header style="padding:10px 24px !important;">
                                                    <v-switch
                                                        v-model="item.selected"
                                                        inset
                                                        :label="`${item.category} (${ item.count })`"
                                                        dense
                                                        hide-details
                                                        class="pa-0 mt-1"
                                                        @click.stop="hasChangeSelect(categoryIdx)"
                                                        small
                                                    >
                                                    <template v-slot:label>
                                                        <span
                                                            class="pt-2"
                                                            :style="`${item.count.split('/')[0] != 0 ? 'color:#0675BB' : ''}`"
                                                        >
                                                            {{ `${item.category} (${ item.count })` }}
                                                        </span>
                                                    </template>
                                                    </v-switch>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                </v-expansion-panel-header>
                                                <v-expansion-panel-content>
                                                    <v-sheet>
                                                        <v-switch
                                                            v-for="permit in item.metadata" :key="permit.name"
                                                            v-model="permit.selected"
                                                            inset
                                                            :label="permit.name"
                                                            dense
                                                            hide-details
                                                            class="mt-2"
                                                            @change="hasChange(categoryIdx)"
                                                        />
                                                    </v-sheet>
                                                </v-expansion-panel-content>
                                            </v-expansion-panel>
                                        </v-expansion-panels>
                                    </div>
                                </v-list-item>
                            </v-list>
                        </v-container>
                        <small class="ml-4">*indicates required field</small>
                    </v-card-text>
                    <!-- <v-divider class="my-0" /> -->

                    <v-card-actions>
                        <v-btn
                            color="error"
                            text
                            @click="resetSelected"
                        >
                            {{ manage_dialog.item.id ? 'Reset' : 'Clear' }}
                        </v-btn>
                        <v-spacer></v-spacer>

                        <v-btn
                            color="blue darken-1"
                            text
                            @click="dialog = false"
                        >
                            Close
                        </v-btn>
                        <v-btn
                            color="blue darken-1"
                            text
                            v-if="!(
                                !hasSelected ||
                                role == '' ||
                                role == undefined ||
                                role == null ||
                                checkIfNoChanges()
                            ) &&
                            (
                                manage_dialog.item.id?true:!roles_and_permissions.map(r=>(r.name).toLowerCase()).includes(role.toLowerCase()) &&
                                (!parseInt(role) && (typeof role === 'string' && role.trim() != ''))
                            )"
                            @click="save"
                        >
                            Save
                        </v-btn>
                        <v-btn color="blue darken-1" text v-else disabled>Save</v-btn>
                    </v-card-actions>

                </v-card>
            </v-dialog>
        </v-row>
    </div>
</template>

<script>
import axios from '../../config/axios'
export default {
    props: ['manage_dialog'],
    data() {
        return {
            permissions: this.manage_dialog.item.data,
            dialog: true,
            hasSelected: false,
            panel: [],
            // items: 10,
            selectedRolesAndPermissions: [],
            role: '',
            description: null,
            backUpExistingSelected: [],
            rules: {
                required: value => !!value && (!parseInt(value) && (typeof value === 'string' && value.trim() != "")) || "This field is required",
                unique: v => this.manage_dialog.item.id?true:!this.roles_and_permissions?.map(r=>(r.name)?.toLowerCase())?.includes(v?.toLowerCase()) || `The role "${v}" already exist`
            },
            btnLoading: false,
            roles_and_permissions: []
        }
    },
    watch: {
        dialog(val) {
            if (!val) {
                this.$emit('close-dialog')
            }
        }
    },
    methods: {
        all () {
            this.panel = [...Array(this.permissions.length).keys()].map((k, i) => i)
        },
        none () {
            this.panel = []
        },
        hasChange(categoryIdx) {
            let metadata = this.permissions[categoryIdx].metadata
            this.permissions[categoryIdx].count = `${metadata.filter(permit=>permit.selected==true).length}/${metadata.length}`
            this.permissions[categoryIdx].selected = metadata.filter(permit=>permit.selected==true).length == metadata.length
            this.checkIfHaveSelected()
        },
        hasChangeSelect(categoryIdx) {
            let metadata = this.permissions[categoryIdx].metadata
            this.permissions[categoryIdx].metadata.forEach(element => {
                element.selected = this.permissions[categoryIdx].selected
            });
            this.permissions[categoryIdx].count = `${metadata.filter(permit=>permit.selected==true).length}/${metadata.length}`
            this.checkIfHaveSelected()
        },
        checkIfHaveSelected(){
            this.hasSelected = this.permissions.reduce((accumulator, currentValue, index, arraydata) => {
                currentValue.metadata.forEach(per=>{
                    if (per.selected) {
                        accumulator = true
                    }
                })
                return accumulator
            }, false)
        },
        checkIfNoChanges() {
            return JSON.stringify(this.permissions) == JSON.stringify(this.backUpExistingSelected.data) &&  this.role == this.backUpExistingSelected.role && this.description == this.backUpExistingSelected.description
        },
        async save() {
            this.btnLoading = true
            this.selectedRolesAndPermissions = this.permissions.reduce((accumulator, currentValue, index, arraydata) => {
                currentValue.metadata.forEach(per=>{
                    if (per.selected) {
                        accumulator.push(per.name)
                    }
                })
                return accumulator
             }, [])

            let form = {id: this.manage_dialog.item.id, type: this.manage_dialog.item.id?'edit':'add', role: this.role, description: this.description, data: this.selectedRolesAndPermissions}
            if (!!this.manage_dialog?.item?.id) {
                await axios.put(`roles/${this.manage_dialog.item.id}`, form).then(response => {
                    this.$store.commit('SET_SNACKBAR', response.data)
                    this.$emit('reload')

                    this.btnLoading = false
                    this.dialog = false
                }).catch((error)=>{
                    this.$store.commit('SET_SNACKBAR', error.response.data)
                    this.btnLoading = false
                });
            } else {
                await axios.post(`roles`, form).then(response => {
                    this.$store.commit('SET_SNACKBAR', response.data)
                    this.$emit('reload')

                    this.resetSelected()
                    this.btnLoading = false
                }).catch((error)=>{
                    this.$store.commit('SET_SNACKBAR', error.response.data)
                    this.btnLoading = false
                });
            }
        },
        resetSelected() {
            this.permissions = JSON.parse(JSON.stringify(this.backUpExistingSelected.data))
            this.role = JSON.parse(JSON.stringify(this.backUpExistingSelected.role))
            this.description = JSON.parse(JSON.stringify(this.backUpExistingSelected.description))
            this.checkIfHaveSelected()

            if (!this.manage_dialog?.item?.id) {
                this.$refs.role.reset()
            }
            this.$nextTick(()=>{
                this.$refs.role.$el.getElementsByTagName('input')[0].focus();
            })
        },
    },
    async created() {
        await axios.get(`roles`).then(response => {
            this.roles_and_permissions = response.data
        }).catch((error)=>{
            this.$store.commit('SET_SNACKBAR', {
                type: 'error',
                message: 'Failed'
            })
        })
    },
    mounted() {
        this.$nextTick(()=>{
            this.$refs.role.$el.getElementsByTagName('input')[0].focus();
        })
        this.backUpExistingSelected = {
            data: JSON.parse(JSON.stringify(this.manage_dialog.item.data)),
            role: JSON.parse(JSON.stringify(this.manage_dialog.item.role)),
            description: JSON.parse(JSON.stringify(this.manage_dialog.item.description || null))
        }
        if (this.manage_dialog.item.id) {
            this.role = this.manage_dialog.item.role
            this.description = this.manage_dialog.item.description
            this.checkIfHaveSelected()
        }
    }
}
</script>

<style scoped>
    .v-expansion-panel--active>.v-expansion-panel-header {
        min-height: auto !important;
    }
</style>
