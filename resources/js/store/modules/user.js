import axios from "axios";

const state = {
    auth: null,
    authenticated: false
};

const getters = {
    auth: state => state.auth,
};

const actions = {
};

const mutations = {
    SET_AUTH: (state, auth) => {
        try {
            var token = auth.token
            state.auth = {...auth.user, token}
            state.authenticated = true
        } catch (error) { state.auth = null; state.authenticated = false; }
    },
    UNSET_AUTH: (state) => {
        state.auth = null
        state.authenticated = false
    },
};

export default {
    state,
    getters,
    actions,
    mutations
};
