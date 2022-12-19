<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="200"
        offset-y
        :max-width="500"
        left
    >
        <template v-slot:activator="{ on, attrs }">
            <v-icon
                v-bind="attrs"
                v-on="on"
            >
                mdi-bell
            </v-icon>
        </template>

        <v-card outlined>
            <v-card-text class="pb-0">
                asfasfasf
                <br>
                safasfasf
                asfsaf
                zxvlkzxvlkhzcxlk klxhlk hxclkvhxl khxlkhvlkZHFLKZxhklhzxgkl klhkxchzkxglkxhzcgklh lkhkldhgklshdglksdhglksd khsdlkg
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<script>
export default {
    data() {
        return {
            menu: false,
        }
    }
}
</script>

<style>

</style>