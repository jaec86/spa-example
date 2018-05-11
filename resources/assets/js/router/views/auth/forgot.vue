<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <form class="flex flex-col justify-center p-8 w-full max-w-xs h-screen xs:h-auto bg-white text-center shadow" @submit.prevent="sendResetLink">
            <div class="relative pb-6">
                <input type="email" placeholder="Email" class="input" v-model="email" @input="removeError('email')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('email')">{{ getError('email') }}</div></transition>
            </div>
            <div class="pb-6">
                <button type="submit" class="btn w-full">Send Reset Link</button>
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
                email: null,
            }
        },
        computed: {
            ...mapGetters(['getError']),
        },
        methods: {
            ...mapMutations(['removeError', 'setData']),
            ...mapActions(['submitForm']),
            async sendResetLink() {
                try {
                    let data = await this.submitForm({
                        url: '/api/password/forgot',
                        method: 'post',
                        data: { email: this.email }
                    });
                    this.$router.push('/login');
                } catch (data) {}
            }
        }
    }
</script>