<script setup>
import { useStore } from 'vuex';
import { computed, watch, ref } from 'vue';

const store = useStore();
const announceError = computed(() => store.state.announceError);
const announceErrorId = computed(() => store.state.announceErrorId);
const isVisible = ref(false);
let timeoutId = null;

watch(announceErrorId, () => {
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
    <div :class="{ 'visible': isVisible }" class="w_container announce error">
        <img class="image16x16px" loading="lazy" width="auto" height="auto" alt=""
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65741c8ac4e2ecd7e67084ef_alert-triangle.svg" />
        <div class="text14px white">
            {{ announceError }}
        </div>
    </div>
</template>

<style scoped>
.announce.error {
    transition: right 0.8s ease;
    right: -100%;
    background-color: #ef5944;
}

.announce.error.visible {
    right: 4px;
    transition: right 0.8s ease;
}
</style>
