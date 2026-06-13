<script setup>
import DeleteRecord from "@/Components/DeleteRecord.vue";

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    selectedItems: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(['closeSelectMultiple']);
const close = () => {
    emit('closeSelectMultiple');
}

const deleteTitle = () => {
    let currentPathSegment = window.location.pathname.split('/')[1].replace('-', '_');

    if (currentPathSegment == 'mon_equipe') {
        return 'ces collaborateurs';
    } else if (currentPathSegment == 'mes_salles') {
        return 'ces salles';
    }
}
</script>

<template>
    <div class="w_container justifyspacebetween bg-white rounded-lg w-full px-4" v-if="show">
        <div class="w_container aligncenter gap12px py-2">
            <div class="text14px semibold">
                {{ selectedItems.length }} sélectionné(s)
            </div>
            <div class="separatorvertical"></div>
            <DeleteRecord :table="'user'" :ids="selectedItems" :title="deleteTitle()"
                :options="{ padding: false, reload: false }">
            </DeleteRecord>
        </div>
        <div class="w_container aligncenter cursor-pointer" @click="close">
            <img class="image24x24px clickable" loading="lazy" width="30" height="30"
                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
        </div>
    </div>
</template>
