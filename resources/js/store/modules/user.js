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
    selector_filter: {
        office_filter: null,
        budget_filter: null,
    }
};

const getters = {
    auth: state => state.auth,
    app_loader: state => state.app_loader,
    visit_page: state => state.visit_page,
    snackbar: state => state.snackbar,
    selector_filter: state => state.selector_filter,
};

const actions = {
};

const mutations = {
    SET_AUTH: (state, auth) => {
        try {
            var auth_user = JSON.parse(JSON.stringify(auth.user))
            auth_user.permissionsArray = []
            auth_user.rolesArray = auth_user.roles.reduce((roles, role) => {
                role.permissions.forEach(permission => {
                    if (!auth_user.permissionsArray?.includes(permission?.name?.split('-')[1])) {
                        auth_user.permissionsArray?.push(permission?.name?.split('-')[1])
                    }
                    auth_user.permissionsArray.push(permission.name)
                })
                roles.push(role.name)
                return roles
            }, [])

            var token = (auth.token).split('|')[1]
            state.auth = {...auth_user, token}
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
    SET_TABLE_GROUP_OPEN: (state, page) => {
        state.visit_page[page.name].table_group_open = page.group_name
    },
    SET_SELECTOR_FILTER: (state, filters) => {
        state.selector_filter = filters
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
