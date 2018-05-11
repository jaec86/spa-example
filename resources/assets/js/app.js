import './bootstrap';
import router from './router';
import store from './store';

router.beforeResolve((to, from, next) => {
    store.commit('resetErrors');
    if (to.meta.requiresAuth === true && localStorage.getItem('access_token') === null) {
        return next('/login');
    }
    if (to.meta.requiresGuest === true && localStorage.getItem('access_token') !== null) {
        return next('/profile');
    }
    next();
});

const app = new Vue({
    el: '#app',
    router,
    store,
});