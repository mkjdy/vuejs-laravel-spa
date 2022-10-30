<template>
    <div>
        <v-row>
            <v-col>
                GUEST AREA
                <v-btn @click="loginPage">
                    Go To Login Section
                </v-btn>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    methods: {
        loginPage() {
            this.$router.push({ name: "Login" })
        }
    },
    mounted() {
        //console.log(localStorage.getItem('sample_key'))
    }

}
</script>

<style>

</style>