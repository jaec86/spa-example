<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <form class="flex flex-col justify-center p-8 w-full max-w-xs h-screen xs:h-auto bg-white text-center shadow" @submit.prevent="register">
            <div class="flex relative pb-6">
                <input type="text" placeholder="First Name" class="input rounded-r-none" v-model="first_name" @input="removeError('first_name')">
                <input type="text" placeholder="Last Name" class="input -ml-px rounded-l-none" v-model="last_name" @input="removeError('last_name')">
                <div class="absolute w-full" style="top: 2.75rem">
                    <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error w-1/2" v-if="getError('first_name')" :title="getError('first_name')">{{ getError('first_name') }}</div></transition>
                    <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error w-1/2" style="left: 50%" v-if="getError('last_name')" :title="getError('last_name')">{{ getError('last_name') }}</div></transition>
                </div>
            </div>
            <div class="relative pb-6">
                <input type="email" placeholder="Email" class="input" v-model="email" @input="removeError('email')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('email')">{{ getError('email') }}</div></transition>
            </div>
            <div class="relative pb-6">
                <input type="password" placeholder="Password" class="input" v-model="password" @input="removeError('password')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('password')">{{ getError('password') }}</div></transition>
            </div>
            <div class="relative pb-6">
                <input type="password" placeholder="Password Confirmation" class="input" v-model="password_confirmation" @input="removeError('password_confirmation')">
                <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('password_confirmation')">{{ getError('password_confirmation') }}</div></transition>
            </div>
            <div class="pb-6">
                <button type="submit" class="btn w-full">Register</button>
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
                first_name: null,
                last_name: null,
                email: null,
                password: null,
                password_confirmation: null,
            }
        },
        computed: {
            ...mapGetters(['getError']),
        },
        methods: {
            ...mapMutations(['removeError', 'setData']),
            ...mapActions(['submitForm']),
            async register() {
                try {
                    let data = await this.submitForm({
                        url: '/api/register',
                        method: 'post',
                        data: {
                            first_name: this.first_name,
                            last_name: this.last_name,
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.password_confirmation
                        }
                    });
                    this.$router.push('/login');
                } catch (data) {}
            }
        }
    }
</script>