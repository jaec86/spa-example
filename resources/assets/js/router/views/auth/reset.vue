<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <form class="flex flex-col justify-center p-8 w-full max-w-xs h-screen xs:h-auto bg-white text-center shadow" @submit.prevent="reset">
            <div class="relative pb-6">
                <input type="password" placeholder="Password" class="input" v-model="password" @input="removeError('password')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('password')">{{ getError('password') }}</div></transition>
            </div>
            <div class="relative pb-6">
                <input type="password" placeholder="Password Confirmation" class="input" v-model="password_confirmation" @input="removeError('password_confirmation')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('password_confirmation')">{{ getError('password_confirmation') }}</div></transition>
            </div>
            <div class="pb-6">
                <button type="submit" class="btn w-full">Save Password</button>
            </div>
            <div>
                <router-link to="/login" class="link">Go to Login Page</router-link>
            </div>
        </form>
    </div>
</template>

<script>
    import { mapGetters, mapMutations, mapActions } from 'vuex';

    export default {
        data() {
            return {
                password: null,
                password_confirmation: null,
            }
        },
        computed: {
            ...mapGetters(['getError']),
        },
        methods: {
            ...mapMutations(['removeError', 'setData']),
            ...mapActions(['showToast', 'submitForm']),
            async reset() {
                try {
                    let data = await this.submitForm({
                        url: '/api/password/reset/' + this.$route.params.token,
                        method: 'post',
                        showToastOnFailure: false,
                        data: {
                            password: this.password,
                            password_confirmation: this.password_confirmation
                        }
                    });
                    this.setData({
                        access_token: data.access_token,
                        refresh_token: data.refresh_token,
                    });
                    this.$router.replace('/profile');
                } catch (data) {
                    this.showToast({ type: 'error', message: data.message });
                }
            }
        }
    }
</script>