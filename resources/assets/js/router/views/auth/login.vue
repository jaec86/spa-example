<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <form class="flex flex-col justify-center p-8 w-full max-w-xs h-screen xs:h-auto bg-white text-center shadow" @submit.prevent="login">
            <div class="relative pb-6">
                <input type="email" placeholder="Email" class="input" v-model="email" @input="removeError('email')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('email')">{{ getError('email') }}</div></transition>
            </div>
            <div class="relative pb-6">
                <input type="password" placeholder="Password" class="input" v-model="password" @input="removeError('password')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('password')">{{ getError('password') }}</div></transition>
            </div>
            <div class="pb-6">
                <button type="submit" class="btn w-full">Login</button>
            </div>
            <div class="pb-6">
                <router-link to="/account/register" tag="button" class="btn-border w-full">Register</router-link>
            </div>
            <div>
                <router-link to="/password/forgot" class="link">Forgot your password?</router-link>
            </div>
        </form>
    </div>
</template>

<script>
    import { mapGetters, mapMutations, mapActions } from 'vuex';

    export default {
        data() {
            return {
                email: null,
                password: null
            }
        },
        computed: {
            ...mapGetters(['getError']),
        },
        methods: {
            ...mapMutations(['removeError', 'setData']),
            ...mapActions(['submitForm']),
            async login() {
                try {
                    let data = await this.submitForm({
                        url: '/api/login',
                        method: 'post',
                        data: { email: this.email, password: this.password }
                    });
                    this.setData({
                        access_token: data.access_token,
                        refresh_token: data.refresh_token,
                    });
                    this.$router.push('/profile');
                } catch (data) {}
            }
        }
    }
</script>