<script setup>
import { useStore } from 'vuex';
import { computed, watch, ref } from 'vue';

const store = useStore();
const announce = computed(() => store.state.announce);
const announceId = computed(() => store.state.announceId);
const isVisible = ref(false);
let timeoutId = null;

watch(announceId, () => {
    isVisible.value = true;

    clearTimeout(timeoutId);
    isVisible.value = false;

    setTimeout(() => {
        isVisible.value = true;
        timeoutId = setTimeout(() => {
            isVisible.value = false;
        }, 4000);
    }, 200);
})
</script>

<template>
    <div class="w_container announce" :class="{ 'visible': isVisible }">
        <img class="image16x16px" loading="lazy" width="auto" height="auto" alt=""
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/656ef0da8b52229fa946650c_check-circle.svg" />
        <div class="text14px white">
            {{ announce }}
        </div>
    </div>
</template>

<style scoped>
.announce {
    transition: right 0.8s ease;
    right: -100%;
}

.announce.visible {
    right: 4px;
    transition: right 0.8s ease;
}
</style>
