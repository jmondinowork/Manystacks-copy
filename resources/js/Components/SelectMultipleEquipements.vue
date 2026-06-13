<script setup>
import OptionsProduct from './OptionsProduct.vue';
import DeleteRecord from "@/Components/DeleteRecord.vue";

import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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

const actionOptions = ref(null);

const showOptions = ref(false);
const closeOptions = () => showOptions.value = false;
const openOptions = (action) => {
    if (data.selectedItems.length > 0) {
        actionOptions.value = action;
        showOptions.value = true;
    }
}
const updateStatus = () => {
    const form = useForm({
        status: actionOptions.value,
        equipements: data.selectedItems,
    });

    form.post(route('editMultipleEquipements'), {
        onFinish: () => {
            sessionStorage.setItem('editMultipleEquipements', 'true');
            window.location.reload(true)
        },
    });
}
</script>

<template>
    <div class="w_container justifyspacebetween bg-white rounded-lg w-full px-4" v-if="show">
        <div class="w_container aligncenter gap12px py-2">
            <div class="text14px semibold">
                {{ selectedItems.length }} sélectionné(s)
            </div>
            <div class="separatorvertical"></div>
            <div class="w_container aligncenter gap8px cursor-pointer" @click="openOptions('En service')">
                <img src="/images/en-service-icon.svg" alt="" class="image20x20px">
                <div class="text14px">
                    Mettre en service
                </div>
            </div>
            <div class="separatorvertical"></div>
            <div class="w_container aligncenter gap8px cursor-pointer" @click="openOptions('En réserve')">
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e62670173b4bfc36b_Vectors-Wrapper.svg"
                    alt="" class="image20x20px">
                <div class="text14px">
                    Mettre en réserve
                </div>
            </div>
            <div class="separatorvertical"></div>
            <div class="w_container aligncenter gap8px cursor-pointer" @click="openOptions('En maintenance')">
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e1a00956a6c4065ec_Vectors-Wrapper.svg"
                    alt="" class="image20x20px">
                <div class="text14px">
                    Mettre en maintenance
                </div>
            </div>
            <div class="separatorvertical"></div>
            <div class="w_container aligncenter gap8px cursor-pointer" @click="openOptions('Hors service')">
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55febe7bfcd9035b643_Vectors-Wrapper.svg"
                    alt="" class="image20x20px">
                <div class="text14px">
                    Mettre hors service
                </div>
            </div>
            <div class="separatorvertical"></div>
            <DeleteRecord :table="'commandeProduct'" :ids="selectedItems" :title="'ces équipements'"
                :options="{ padding: false, reload: false }">
            </DeleteRecord>
        </div>
        <div class="w_container aligncenter cursor-pointer" @click="close">
            <img class="image24x24px clickable" loading="lazy" width="30" height="30"
                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
        </div>
    </div>

    <OptionsProduct :show="showOptions" :action="actionOptions" :displayOnly="true" @closeOptions="closeOptions"
        @updateStatus="updateStatus" />
</template>
