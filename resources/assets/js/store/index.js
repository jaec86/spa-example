import Vuex from 'vuex';
import auth from './modules/auth';
import form from './modules/form';
import toaster from './modules/toaster';

export default new Vuex.Store({
    strict: true,
    modules: {
        auth,
        form,
        toaster,
    }
});