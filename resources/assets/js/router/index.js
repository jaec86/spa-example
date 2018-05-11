import VueRouter from 'vue-router';

let routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        component: require('./views/auth/login'),
        meta: { requiresGuest: true }
    },
    {
        path: '/account/register',
        component: require('./views/auth/register'),
        meta: { requiresGuest: true }
    },
    {
        path: '/account/activate/:token',
        component: require('./views/auth/activate'),
    },
    {
        path: '/password/forgot',
        component: require('./views/auth/forgot'),
        meta: { requiresGuest: true }
    },
    {
        path: '/password/reset/:token',
        component: require('./views/auth/reset'),
    },
    {
        path: '/profile',
        component: require('./views/profile'),
        meta: { requiresAuth: true }
    },
    {
        path: '*',
        component: require('./views/notFound')
    }
];

export default new VueRouter({
    mode: 'history',
    routes
});