<template>
    <div>
        <h1>SETTINGS</h1>
        Theme :
        <v-btn v-if="$vuetify.theme.dark" @click="$vuetify.theme.dark = !$vuetify.theme.dark">Light</v-btn>
        <v-btn v-else @click="$vuetify.theme.dark = !$vuetify.theme.dark">dark</v-btn>
    </div>
</template>

<script>
export default {
    mounted() {
        this.$store.commit('SET_APP_LOADER', false)
    }
}
</script>

<style>

</style>