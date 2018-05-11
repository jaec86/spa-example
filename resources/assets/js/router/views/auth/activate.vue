<template>
    <div class="flex flex-col justify-center items-center fixed pin bg-white">
        <div class="inline-block w-8 h-8 rounded-full border-4 border-purple-lighter fs-rotate" :style="{ 'border-top-color': '#794acf'}"></div>
        <div class="pt-6 uppercase tracking-wide fs-beat">Activando Cuenta</div>
    </div>
</template>

<script>
    import { mapMutations, mapActions } from 'vuex';

    export default {
        methods: {
            ...mapMutations(['resetData', 'setData']),
            ...mapActions(['showToast', 'submitForm']),
            sleep() {
                return new Promise(resolve => setTimeout(resolve, 3000));
            },
            async activate(showMessage = false) {
                let toast = { type: null, message: null, route: null };
                try {
                    let data = await this.submitForm({
                        url: '/api/activate/' + this.$route.params.token,
                        method: 'post',
                        showToastOnSuccess: showMessage,
                        showToastOnFailure: showMessage,
                    });
                    toast.type = 'success';
                    toast.message = data.message;
                    toast.route = '/profile';
                    this.setData({
                        access_token: data.access_token,
                        refresh_token: data.refresh_token,
                    });
                } catch (data) {
                    toast.type = 'error';
                    toast.message = data.message;
                    toast.route = '/login';
                }
                return toast;
            }
        },
        async mounted() {
            this.resetData();
            let [toast, sleep] = await Promise.all([
                this.activate(),
                this.sleep()
            ]);
            this.showToast(toast);
            this.$router.replace(toast.route);
        }
    }
</script>