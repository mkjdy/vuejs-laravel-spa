import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from "vuex-persistedstate";
//import SecureLS from "secure-ls";
// class CustomSecureLS extends SecureLS {
//     init() {
//         this.utils.metaKey = `meta`;
//         super.init();
//     }
// }
// var ls = new CustomSecureLS({
//     encodingType: 'aes',
//     isCompression: false,
//     encryptionSecret: `@210012~-%${Math.random().toString(36).slice(2)}%`,
// });

import user from "./modules/user";


Vue.use(Vuex)

const store = {
    modules: {
        strict: process.env.NODE_ENV !== "production",
        user,
    },
    plugins: [
        createPersistedState({
            //key: "secudata",
            // storage: {
            //     getItem: (key) => ls.get(key),
            //     setItem: (key, value) => ls.set(key, value),
            //     removeItem: (key) => ls.remove(key),
            // },
            key: 'meta',
            reducer (meta) {
                if (!meta.user.auth || !meta.user.authenticated) {
                    return null
                }
                return meta
            },
            storage: localStorage
        }),
    ]
}

export default new Vuex.Store(store)