<script setup>
const data = defineProps({
    display: {
        type: String,
        default: 'list',
    }
});
const emit = defineEmits(['changeDisplay']);

const changeDisplay = (display) => {
    emit('changeDisplay', display);
}
</script>

<template>
    <div class="w_container gap-2 items-center">
        <div class="text14px">Visualisation</div>
        <img :src="data.display == 'grid' ? '/images/grid-icon-selected.svg' : '/images/grid-icon.svg'"
            :class="{ purple: data.display == 'grid' }" class="cursor-pointer bigbutton" alt=""
            @click="changeDisplay('grid')">
        <img :src="data.display == 'list' ? '/images/list-icon-selected.svg' : '/images/list-icon.svg'"
            :class="{ purple: data.display == 'list' }" class="cursor-pointer bigbutton" alt=""
            @click="changeDisplay('list')">
    </div>
</template>

<style scoped>
.bigbutton {
    background-color: var(--grey-50);
}
.bigbutton.purple {
    background-color: var(--main) !important;
}
</style>
