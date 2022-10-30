import Vue from 'vue'
import VueRouter from 'vue-router'
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
            component: () => import(/* webpackChunkName: "welcome_container" */ './components/AppContainer'),
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
                            component: () => import('./components/Login'),
                            name: 'Login',
                        },
                        {
                            path: 'guest',
                            component: () => import('./components/Guest'),
                            name: 'Guest',
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
                            component: () => import(/* webpackChunkName: "dashboard" */ './components/general/Dashboard'),
                            name: 'Dashboard'
                        },
                        {
                            path: 'dashboard2',
                            component: () => import(/* webpackChunkName: "sample_page" */ './components/general/SamplePage'),
                            name: 'Dashboard2'
                        }
                    ]
                }
            ]
        }
    ]
})

function sessionAlive() {
    if(localStorage?.meta) {
        let user = JSON.parse(window.localStorage?.meta)?.user
        return user?.authenticated && user?.auth;
    }
    return false;
};

routes.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.isAuth)) {
        return !sessionAlive() ? next({ name: 'Login' }) : next()
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
