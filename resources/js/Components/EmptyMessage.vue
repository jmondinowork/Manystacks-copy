<script setup>
const data = defineProps({
    value: {
        type: String
    }
});
</script>

<template>
    <div class="enrollment-message">
        <div class="w_container gap16px">
            <img data-v-ba5a145c="" src="/images/alert-circle.svg" loading="lazy" width="20" height="20" alt=""
                class="image20x20px">
            <p class="text14px" v-if="data.value == 'enrollment'">
                Pour accéder à ces informations, il est nécessaire <span class="font-bold">d'enroller</span> la machine.
            </p>
            <p class="text14px" v-else>
                {{ data.value }}
            </p>
        </div>
    </div>
</template>

<style scoped>
.enrollment-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9;
    background-color: #fff;
    border-radius: 8px;
    padding: 12px 24px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    width: 80%;
}

.enrollment-message.static {
    position: static;
    transform: none;
    margin-top: 16px;
    box-shadow: none;
    width: 100%;
}
</style>
