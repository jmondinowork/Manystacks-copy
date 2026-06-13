<script setup>
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const data = defineProps({
    table: {
        type: String,
        default: ''
    },
    ids: {
        type: Array,
        default: []
    },
    title: {
        type: String,
        default: ''
    },
    options: {
        type: Object,
        default: {
            padding: true,
            reload: true
        }
    }
});
const showDialog = ref(false);
const previousUrl = data.options.reload ? window.location.href.substring(0, window.location.href.lastIndexOf("/")) : window.location.pathname;

const deleteRecord = () => {
    if (data.ids.length > 0)
        showDialog.value = true;
}
const confirmDelete = () => {
    const form = useForm({
        table: data.table,
        ids: data.ids,
    });

    axios.delete(route('deleteRecord'), { data: form })
        .then((response) => {
            let sessionStatus;
            if (response.data.deleted > 1) {
                sessionStatus = 'itemsDeleted';
            } else if (response.data.deleted === 1) {
                sessionStatus = 'itemDeleted';
            } else {
                sessionStatus = 'itemNotDeleted';
            }
            sessionStorage.setItem(sessionStatus, true);
            if (!data.options.reload) window.location.reload(true);
            else window.location.href = previousUrl;
        })
        .catch((error) => {
        });
}
</script>

<template>
    <div @click="deleteRecord" class="w_container gap-2 cursor-pointer" :class="{ 'optionunit': data.options.padding }">
        <img src="/images/trash-icon.svg" loading="lazy" alt="" class="image20x20px">
        <div class="text14px nowrap">Supprimer {{ data.title }}</div>
    </div>

    <div class="darkmodalbackground" :class="{ 'show': showDialog }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Êtes-vous sûr de vouloir supprimer {{ data.title }} ?
                </div>
                <div class="w_container alignright cursor-pointer" @click="showDialog = false;">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer w_container vertical gap16px">
                <div v-if="data.table == 'user' && data.title !== 'cette salle'" class="text16px semibold">
                    Toutes les licences et équipements associés à ce collaborateur lui seront retirés.
                    L'adresse email pro de ce collaborateur sera supprimer.
                </div>
                <div class="text16px semibold">
                    Cette action est irréversible. Voulez-vous continuer ?
                </div>
                <div class="w_container justify-between w-full gap-4">
                    <div class="bigbutton" style="background-color: var(--red);" @click="confirmDelete">
                        <div class="text14px" style="color: #fff;">
                            Confirmer
                        </div>
                    </div>
                    <div class="bigbutton" @click="showDialog = false" style="background-color: var(--grey-100);">
                        <div class="text14px">
                            Annuler
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.darkmodalbackground {
    position: fixed;
    top: 0;
    left: 0;
}

.optionunit {
    gap: 12px !important;
}
</style>
