<template>
    <transition-group enter-class="-mt-8 opacity-0" leave-to-class="mt-8 opacity-0" class="fixed pin z-50 pointer-events-none">
        <div class="pt-4 text-center transition" v-for="toast in toaster.toasts" :key="toast.id">
            <div class="relative inline-block p-2 rounded border-t-2 text-sm bg-white shadow" :class="{ 'border-green': toast.type === 'success', 'border-red': toast.type === 'error' }">
                <i class="material-icons absolute text-xl text-red" style="top: 0.35rem; left: 0.35rem" v-if="toast.type === 'error'">error_outline</i>
                <i class="material-icons absolute text-xl text-green" style="top: 0.35rem; left: 0.35rem" v-if="toast.type === 'success'">check_circle_outline</i>
                <div style="padding-left: 1.35rem">{{ toast.message }}</div>
            </div>
        </div>
    </transition-group>
</template>

<script>
    import { mapState } from 'vuex';

    export default {
        computed: {
            ...mapState(['toaster']),
        }
    }
</script>