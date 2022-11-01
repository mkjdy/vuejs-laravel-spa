import { buildDateTime } from '../../helpers'

const state = {
    auth: null,
    authenticated: false,
    app_loader: false,
    visit_page: {},
    snackbar: {
        show: false,
        type: "",
        message: "",
    },
};

const getters = {
    auth: state => state.auth,
    app_loader: state => state.app_loader,
    visit_page: state => state.visit_page,
    snackbar: state => state.snackbar,
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
    SET_APP_LOADER: (state, loading) => {
        state.app_loader = loading
    },
    SET_VISIT_PAGE: (state, page) => {
        state.visit_page[page.name] = page.param
        state.visit_page[page.name].last_update_data = buildDateTime(new Date())
    },
    SET_SNACKBAR: (state, param = null) => {
        state.snackbar.show = false
        setTimeout(() => {
            state.snackbar.show = true
            state.snackbar.type = param?.type || "error"
            state.snackbar.message = param?.message || "Failed"
        }, 50);
    },
    UNSET_SNACKBAR: (state) => {
        state.snackbar.show = false
    },
};

export default {
    state,
    getters,
    actions,
    mutations
};
