export default {
    state: {
        access_token: localStorage.getItem('access_token'),
        refresh_token: localStorage.getItem('refresh_token'),
    },
    getters: {
        authenticated(state) {
            return state.access_token !== null && state.refresh_token !== null;
        },
        accessToken(state) {
            return state.access_token;
        },
        refreshToken(state) {
            return state.refresh_token;
        }
    },
    mutations: {
        resetData(state) {
            state.access_token = null;
            state.refresh_token = null;
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
        },
        setData(state, { access_token, refresh_token }) {
            state.access_token = access_token;
            state.refresh_token = refresh_token;
            localStorage.setItem('access_token', state.access_token);
            localStorage.setItem('refresh_token', state.refresh_token);
        }
    }
}