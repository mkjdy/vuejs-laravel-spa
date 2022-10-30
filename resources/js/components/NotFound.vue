<template>
    <v-app>
        <v-main>
            <v-container fluid fill-height>
                <v-layout class="align-center justify-center">

                    <v-btn color="primary" link outlined class="mr-2" @click="goBack">
                        <v-icon left dark> mdi-keyboard-backspace </v-icon> Back
                    </v-btn>
                    <h3><span style="color:red">404 ERROR</span> - Page Not Found</h3>

                </v-layout>
            </v-container>
        </v-main>
    </v-app>
</template>

<script>

export default {
    methods: {
        goBack() {
            this.$router.go(-1)
        }
    }
}
</script>
