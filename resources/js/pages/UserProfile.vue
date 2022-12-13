<template>
    <v-row>
        <v-col cols="12" sm="12" md="3">
            <v-card height="50vh">
                <v-card-title>Personal Info</v-card-title>
                <v-card-text>
                    Info
                </v-card-text>
            </v-card>
        </v-col>
        <v-col cols="12" sm="12" md="9">
            <v-card height="80vh">
                <v-card-title>General Info</v-card-title>
                <v-card-text>
                    Info
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
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