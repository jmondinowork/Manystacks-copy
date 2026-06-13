<script setup>
import { useForm } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { onMounted, onUnmounted, ref } from "vue";

const store = useStore();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeCreate']);

const close = () => {
    form.reset();
    emit('closeCreate');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => {
    document.addEventListener('keydown', closeOnEscape);
    if (sessionStorage.getItem('editMultipleEquipements') === 'true') {
        store.dispatch('updateAnnounce', "Le status des équipements a bien été modifié");
        sessionStorage.removeItem('editMultipleEquipements');
    }
});
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    file: null,
});
const dragActive = ref(false);
const fileError = ref(false);
const fileErrorMsg = ref('');

const dragOver = () => {
    dragActive.value = true;
    fileError.value = false;
};
const dragLeave = () => {
    dragActive.value = false;
};
const handleDrop = (event) => {
    event.preventDefault();
    dragActive.value = false;
    if (event.dataTransfer.files.length > 0 && event.dataTransfer.files[0].type === 'text/csv') {
        fileError.value = false;
        form.file = event.dataTransfer.files[0];
    } else
        fileError.value = true;
};
const handleFiles = (event) => {
    if (event.target.files.length > 0 && event.target.files[0].type === 'text/csv') {
        fileError.value = false;
        form.file = event.target.files[0];
    } else
        fileError.value = true;
};
const fileInputClick = () => {
    document.querySelector('input[type="file"]').click();
};

const submit = () => {
    form.processing = true;
    if (!form.file || !form.file.name.endsWith('.csv')) {
        fileError.value = true;
        fileErrorMsg.value = "Veuillez sélectionner un fichier .csv valide.";
        return;
    }

    let formData = new FormData();
    formData.append('file', form.file);
    axios.post('/api/equipements/importer-csv', formData)
        .then(() => {
            sessionStorage.setItem('reloaded', 'true');
            window.location.reload();
        })
        .catch(error => {
            fileError.value = true;
            fileErrorMsg.value = error.response.data.message;
            store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'importation des équipements");
        });
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Importer vos équipements CSV
                </div>
                <div class="w_container alignright cursor-pointer" @click="close(false)">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <form @submit.prevent="submit" class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="text14px medium">
                        Pour simplifier l'importation de vos équipements, <a class=" underline text14px purple medium"
                            href="/csv-exemple.csv" download>téléchargez notre modèle de fichier CSV.</a>
                        <br>
                        Attention à bien respecter le nom des colonnes et assurez-vous de renseigner au
                        minimum les 2 premières colonnes : Nom et Catégorie.
                    </div>
                    <div class="flex justify-center items-center w-full h-64 bg-gray-200 rounded-lg cursor-pointer dragndrop"
                        :class="{ 'dragActive': dragActive || (form.file && !fileError), 'error': fileError }"
                        @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="handleDrop"
                        @click="fileInputClick">
                        <input type="file" ref="fileInput" class="hidden" @change="handleFiles" multiple />
                        <div v-if="!dragActive && !form.file && !fileError">
                            <div class="text16px medium">Glissez et déposez votre fichier ici</div>
                            <div class="text14px mt-2">Ou cliquez pour sélectionner un fichier</div>
                        </div>
                        <div v-if="dragActive" class="text16px purple medium">Relâchez votre fichier ici</div>
                        <template v-else>
                            <div v-if="form.file && !fileError" class="text16px purple medium">
                                {{ form.file.name }}
                            </div>
                            <div v-if="fileError" class="text16px red medium text-center">
                                {{ fileErrorMsg ? fileErrorMsg : 'Le fichier doit être au format CSV' }}
                            </div>
                        </template>
                    </div>

                    <div class="w_container vertical gap8px">
                        <button type="submit" :disabled="form.processing"
                            :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                            <div class="text14px white">
                                Importer
                            </div>
                        </button>
                        <div class="bigbutton" @click="close(true)">
                            <div class="text14px">
                                Annuler
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.dragndrop {
    transition: background-color 0.3s;
    border: 2px dashed var(--grey-500);
}

.dragndrop.dragActive {
    background-color: var(--main-light) !important;
    border-color: var(--main) !important;
}

.dragndrop.error {
    background-color: var(--red-light);
    border-color: var(--red);
}
</style>
