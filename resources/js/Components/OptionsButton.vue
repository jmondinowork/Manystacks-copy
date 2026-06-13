<script setup>
import { vOnClickOutside } from '@vueuse/components'
import { ref } from 'vue'

const optionsPopup = ref(false);
const closeOptions = () => optionsPopup.value = false;
const toggleOptions = () => optionsPopup.value = !optionsPopup.value;
</script>

<template>
    <div v-on-click-outside="closeOptions" class="w_container h-fit">
        <div @click="toggleOptions" class="lightbutton">
            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae47201da8e4cf6a6e033c_Vectors-Wrapper.svg"
                loading="lazy" width="20" height="20" alt="" class="image20x20px">
            <div class="text14px medium purple">Options</div>
        </div>
        <div v-if="optionsPopup" class="listofoptions">
            <slot></slot>
        </div>
    </div>
</template>
