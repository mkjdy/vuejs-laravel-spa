import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'
Vue.use(VueRouter)

const routes = new VueRouter({
    base: '/',
    mode: 'history',
    routes: [
        {
            path: '*',
            component: () => import(/* webpackChunkName: "not_found" */ './components/NotFound'),
            name: 'NotFound'
        },
        {
            path: '/',
            component: () => import(/* webpackChunkName: "app_container" */ './components/AppContainer'),
            //redirect: { name: 'Login' },
            children: [
                {
                    path: '',
                    component: () => import(/* webpackChunkName: "quest_container" */ './components/GuestContainer'),
                    /*beforeEnter: (to, from, next) => {
                        axios.get('/api/authenticated').then(() => {
                            next()
                        }).catch(() => {
                            return next({ name: 'Login'})
                        });
                    },*/
                    meta: { isGuest: true },
                    children: [
                        {
                            path: '',
                            component: () => import(/* webpackChunkName: "login" */ './pages/Login'),
                            name: 'Login',
                        },
                        {
                            path: 'guest',
                            component: () => import(/* webpackChunkName: "guest" */ './components/Guest'),
                            name: 'Guest',
                        },
                        {
                            path: 'changelog',
                            component: () => import(/* webpackChunkName: "changelog" */ './pages/ChangeLog'),
                            name: 'Changelog',

                        },
                        {
                            path: 'about',
                            component: () => import(/* webpackChunkName: "about" */ './pages/About'),
                            name: 'About',
                        },
                    ]
                },
                {
                    path: '',
                    component: () => import(/* webpackChunkName: "auth_container" */ './components/AuthContainer'),
                    /*beforeEnter: (to, from, next) => {
                        axios.get('/api/authenticated').then(() => {
                            next()
                        }).catch(() => {
                            return next({ name: 'Login'})
                        });
                    },*/
                    meta: { isAuth: true },
                    children: [
                        {
                            path: 'dashboard',
                            component: () => import(/* webpackChunkName: "dashboard" */ './pages/Dashboard'),
                            name: 'Dashboard',
                            meta: { access: 'view-dashboard' },
                        },
                        {
                            path: 'reports',
                            component: () => import(/* webpackChunkName: "reports" */ './pages/Reports'),
                            name: 'Reports',
                            meta: { access: 'generate-reports' },
                        },
                        {
                            path: 'roles',
                            component: () => import(/* webpackChunkName: "roles" */ './pages/Roles'),
                            name: 'Roles',
                            meta: { access: 'view-role' },
                        },
                        {
                            path: 'user_management',
                            component: () => import(/* webpackChunkName: "user_management" */ './pages/UserManagement'),
                            name: 'User Management',
                            meta: { access: 'view-user' },
                        },
                        {
                            path: 'user_profile',
                            component: () => import(/* webpackChunkName: "user_profile" */ './pages/UserProfile'),
                            name: 'User Profile',
                        },
                    ]
                }
            ]
        }
    ]
})

function redirectToAvailableRoute() {
    return new Promise((resolve, reject)=>{
        try {
            routes.options.routes.find(rp=>rp.path=="/").children.find(cm=>cm.meta.isAuth).children.forEach(rn=>{
                if (!rn?.meta || Vue.prototype.$can(rn?.meta?.access)) {
                    resolve(rn.name);
                }
            })
        } catch (error) {
            resolve('NotFound')
        }
    })
}

function sessionAlive() {
    if(localStorage?.meta) {
        let user = JSON.parse(window.localStorage?.meta)?.user
        return user?.authenticated && user?.auth;
    }
    return false;
};

routes.beforeEach((to, from, next) => {
    let new_title = document.title.split('-')

    if (new_title.length > 1) {
        new_title.splice(0, 1, to.name + ' ')
    } else {
        new_title = [to.name, document.title]
    }

    document.title = new_title.join(' - ')

    if (to.matched.some(record => record.meta.isAuth)) {
        if (!sessionAlive()) {
            return next({ name: 'Login' })
        } else {
            if (to?.meta?.access && !Vue.prototype.$can(to?.meta?.access)) {
                redirectToAvailableRoute().then(route_name=>next({ name:route_name }))
            } else {
                store.commit('SET_APP_LOADER', true)
                return next()
            }
        }
    } else if (to.matched.some(record => record.meta.isGuest)) {
        return sessionAlive() ? next({ name: 'Dashboard' }) : next()
    } else {
        return next()
    }
});

/*routes.beforeEach((to, from, next) => {
    console.log("==", to)
    next()
})*/

/*routes.beforeEach((to, from, next) => {
    console.log("==", to)
    next()
})*/

export default routes
