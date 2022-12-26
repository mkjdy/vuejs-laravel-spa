<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        :nudge-width="200"
        :nudge-bottom="15"
        offset-y
        :max-width="500"
        left
        content-class="my-menu"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-icon
                v-bind="attrs"
                v-on="on"
            >
                mdi-bell
            </v-icon>
        </template>

        <v-card flat>
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

<style scoped>
    .my-menu {
        /* margin-top: 40px; */
        contain: initial;
        overflow: visible;
    }
    .my-menu::before {
        position: absolute;
        content: "";
        top: 0;
        right: 4px;
        transform: translateY(-100%);
        width: 10px;
        height: 13px;
        border-left: 10px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 8px solid #fff;
    }
</style>