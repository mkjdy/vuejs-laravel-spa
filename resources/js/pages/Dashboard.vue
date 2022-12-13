<template>
    <div>
        <h1>DASHBOARD</h1>
        <!-- <router-link to="/">Home</router-link>
        <router-link to="/">Login</router-link>
        <router-link to="/user/gg">Page Not Exist</router-link>
        <router-link to="/dashboard">Dashboard 1</router-link>
        <router-link to="/dashboard2">Dashboard 2</router-link> -->
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