<template>
    <div class="flex flex-col justify-center items-center h-screen">
        <div class="flex flex-col justify-center p-8 w-full max-w-xs h-screen xs:h-auto bg-white text-center shadow">
            <div class="pb-8">
                <div class="pb-3">{{ user.first_name }} {{ user.last_name }}</div>
                <div>{{ user.email }}</div>
            </div>
            <div class="pb-6">
                <button type="button" class="btn w-full" @click="openModal('showProfile')">Change Profile</button>
            </div>
            <div class="pb-6">
                <button type="button" class="btn-border w-full" @click="openModal('showPassword')">Change Password</button>
            </div>
            <div>
                <button type="link" class="link" @click="logout">Logout</button>
            </div>
        </div>
        <div class="flex flex-col justify-center items-center fixed pin transition z-20" :class="{ 'invisible opacity-0': !showProfile }">
            <div class="absolute pin bg-black opacity-75 cursor-pointer" @click="showProfile = false"></div>
            <form class="relative p-8 w-full max-w-xs rounded bg-white transition shadow" :style="{ 'transform': showProfile ? 'translateY(0)' : 'translateY(50vh)' }" @submit.prevent="changeProfile">
                <div class="flex relative pb-6">
                    <input type="text" placeholder="First Name" class="input rounded-r-none" v-model="userCopy.first_name" @input="removeError('first_name')">
                    <input type="text" placeholder="Last Name" class="input -ml-px rounded-l-none" v-model="userCopy.last_name" @input="removeError('last_name')">
                    <div class="absolute w-full" style="top: 2.75rem">
                        <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error w-1/2" v-if="getError('first_name')" :title="getError('first_name')">{{ getError('first_name') }}</div></transition>
                        <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error w-1/2" style="left: 50%" v-if="getError('last_name')" :title="getError('last_name')">{{ getError('last_name') }}</div></transition>
                    </div>
                </div>
                <div class="relative pb-6">
                    <input type="email" placeholder="Email" class="input" v-model="userCopy.email" @input="removeError('email')">
                    <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('email')">{{ getError('email') }}</div></transition>
                </div>
                <div class="flex">
                    <button type="submit" class="btn w-full rounded-r-none">Save</button>
                    <button type="button" class="btn-border w-full rounded-l-none" @click="showProfile = false">Cancel</button>
                </div>
            </form>
        </div>
        <div class="flex flex-col justify-center items-center fixed pin transition z-20" :class="{ 'invisible opacity-0': !showPassword }">
            <div class="absolute pin bg-black opacity-75 cursor-pointer" @click="showPassword = false"></div>
            <form class="relative p-8 w-full max-w-xs rounded bg-white transition shadow" :style="{ 'transform': showPassword ? 'translateY(0)' : 'translateY(50vh)' }" @submit.prevent="changePassword">
                <div class="relative pb-6">
                    <input type="password" placeholder="Old Password" class="input" v-model="old_password" @input="removeError('old_password')">
                    <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('old_password')">{{ getError('old_password') }}</div></transition>
                </div>
                <div class="relative pb-6">
                    <input type="password" placeholder="New Password" class="input" v-model="new_password" @input="removeError('new_password')">
                    <transition enter-class="opacity-0" leave-to-class="opacity-0"><div class="input-error" v-if="getError('new_password')">{{ getError('new_password') }}</div></transition>
                </div>
                <div class="relative pb-6">
                    <input type="password" placeholder="Password Confirmation" class="input" v-model="new_password_confirmation">
                </div>
                <div class="flex">
                    <button type="submit" class="btn w-full rounded-r-none">Save</button>
                    <button type="button" class="btn-border w-full rounded-l-none" @click="showPassword = false">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import { mapGetters, mapMutations, mapActions } from 'vuex';

    export default {
        data() {
            return {
                user: {},
                userCopy: {},
                old_password: null,
                new_password: null,
                new_password_confirmation: null,
                showProfile: false,
                showPassword: false
            }
        },
        computed: {
            ...mapGetters(['accessToken', 'refreshToken', 'getError']),
        },
        methods: {
            ...mapMutations(['resetData', 'removeError', 'resetErrors']),
            ...mapActions(['showToast', 'submitForm']),
            async changePassword() {
                try {
                    let data = await this.submitForm({
                        url: '/api/profile/password',
                        method: 'put',
                        data: {
                            old_password: this.old_password,
                            new_password: this.new_password,
                            new_password_confirmation: this.new_password_confirmation,
                        }
                    });
                    this.showPassword = false;
                    this.setData({
                        access_token: data.access_token,
                        refresh_token: data.refresh_token,
                    });
                } catch (data) {}
            },
            // async changeProfile() {
            //     try {
            //         let data = await this.submitForm({
            //             url: '/profile',
            //             methos: 'put',
            //             data: {
            //                 first_name: this.userCopy.first_name,
            //                 last_name: this.userCopy.last_name,
            //                 email: this.userCopy.email,
            //             }
            //         });
            //         this.user = data.user;
            //         this.showProfile = false;
            //     } catch (data) {}
            // },
            async changeProfile() {
                try {
                    let data = await this.submitForm({
                        url: '/api/profile',
                        method: 'put',
                        data: {
                            first_name: this.userCopy.first_name,
                            last_name: this.userCopy.last_name,
                            email: this.userCopy.email,
                        }
                    });
                    this.user = data.user;
                    this.showProfile = false;
                } catch (data) {}
            },
            async getProfile() {
                try {
                    let data = await this.submitForm({
                        url: '/api/profile',
                        method: 'get',
                        showToastOnSuccess: false,
                    });
                    this.user = data.user;
                } catch (data) {}
            },
            async invalidate(token) {
                try {
                    let data = await this.submitForm({
                        url: '/api/jwt/invalidate',
                        method: 'post',
                        showToastOnSuccess: false,
                        showToastOnFailure: false,
                        data: { token: this[token] }
                    });
                } catch (data) {}
            },
            async logout() {
                let [access, refresh] = await Promise.all([
                    this.invalidate('accessToken'),
                    this.invalidate('refreshToken'),
                ]);
                this.resetData();
                this.$router.replace('/login');
                this.showToast({ type: 'success', message: 'Se you soon!'});
            },
            openModal(modal) {
                this[modal] = true;
                this.resetErrors();
                this.userCopy = JSON.parse(JSON.stringify(this.user));
            }
        },
        mounted() {
            this.getProfile();
        }
    }
</script>