import router from '../../router'

export default {
    state: {
        errors: {}
    },
    mutations: {
        removeError(state, error) {
            Vue.delete(state.errors, error);
        },
        resetErrors(state) {
            state.errors = {};
        },
        setErrors(state, errors) {
            for (let error in errors) {
                Vue.set(state.errors, error, errors[error][0]);
            }
        }
    },
    getters: {
        getError: (state) => (name) => {
            return state.errors[name];
        }
    },
    actions: {
        async submitForm({ dispatch, commit, rootGetters }, { url, method, data, showToastOnSuccess, showToastOnFailure }) {
            try {
                if (rootGetters.authenticated !== null) {
                    axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootGetters.accessToken;
                }
                let response = await axios[method](url, data);
                commit('resetErrors');
                if (showToastOnSuccess !== false) {
                    dispatch('showToast', { type: 'success', message: response.data.message });
                }
                return response.data;
            } catch ({ response }) {
                if (rootGetters.authenticated && response.status === 401 && response.data.message === 'The access code expired.') {
                    try {
                        response = await dispatch('refreshToken', {
                            method: method,
                            url: url,
                            data: data,
                            showToastOnSuccess: showToastOnSuccess,
                            showToastOnFailure: showToastOnFailure
                        });
                        return response;
                    } catch (response) {
                        throw response;
                    }
                }
                if (showToastOnFailure !== false) {
                    dispatch('showToast', { type: 'error', message: response.data.message });
                }
                if (response.data.errors) {
                    commit('setErrors', response.data.errors);
                }
                throw response.data;
            }
        },
        async refreshToken({ dispatch, commit, rootGetters }, data) {
            try {
                axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootGetters.refreshToken;
                let response = await axios.post('/api/jwt/refresh');
                commit('setData', response.data);
                response = await dispatch('submitForm', data);
                return response;
            } catch ({ response }) {
                delete axios.defaults.headers.common.authorization;
                commit('resetData');
                dispatch('showToast', { type: 'error', message: 'Your session has expired. Please log in again.' });
                router.push({ path: '/login', query: { redirect: encodeURI(router.history.current.path) }});
                throw response.data;
            }
        }
    }
}