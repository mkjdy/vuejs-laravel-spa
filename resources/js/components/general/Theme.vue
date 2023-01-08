<template>
    <v-menu
        bottom
        left
        offset-y
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                dark
                icon
                v-bind="attrs"
                v-on="on"
                plain
            >
                <v-icon>mdi-theme-light-dark</v-icon>
            </v-btn>
        </template>

        <v-list>
            <v-list-item @click="changeTheme(0)">
                <v-icon left>mdi-white-balance-sunny</v-icon>
                <v-list-item-title>Light</v-list-item-title>
            </v-list-item>
            <v-list-item @click="changeTheme(1)">
                <v-icon left>mdi-moon-full</v-icon>
                <v-list-item-title>Dark</v-list-item-title>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
export default {
    methods: {
        changeTheme(dark) {
            localStorage.setItem("app_theme", dark)
            this.$vuetify.theme.dark = !!dark
        },
    }
}
</script>

<style>

</style>