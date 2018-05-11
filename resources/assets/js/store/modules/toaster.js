export default {
    state: {
        toasts: [],
    },
    mutations: {
        addToast(state, toast) {
            state.toasts.unshift(toast);
        },
        removeToast(state, toast_id) {
            state.toasts = state.toasts.filter((toast) => {
                return toast.id !== toast_id;
            });
        }
    },
    actions: {
        showToast({ commit }, toast) {
            let id = '_' + Math.random().toString(36).substr(2, 9);
            commit('addToast', { id, ...toast });
            setTimeout(() => {
                commit('removeToast', id);
            }, 4000);
        }
    }
}