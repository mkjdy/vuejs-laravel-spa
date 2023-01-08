<template>
    <router-view />
</template>

<script>
export default {
    mounted() {
        if (localStorage.getItem("app_theme") == null) {
            localStorage.setItem("app_theme", 0)
        } else {
            this.$vuetify.theme.dark = localStorage.getItem("app_theme") == "1" ? true : false
        }
    }
}
</script>