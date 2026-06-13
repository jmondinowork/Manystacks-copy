<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import Annoucer from '@/Components/Annoucer.vue';
import ErrorAnnouncer from '@/Components/ErrorAnnouncer.vue';

import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const formClient = useForm({
    fname: '',
    lname: '',
    siret: '',
    email: '',
    tel: '',
    processing: false,
});
const handleSiretChange = (event) => {
    formClient.siret = event.target.value.replace(/\D/g, '');
}
const formEquipement = useForm({
    file: null,
    processing: false,
});
const newClientId = ref(null);

const step = ref(0);
const createClient = () => {
    formClient.processing = true;

    axios.post(route('register'), formClient)
        .then(response => {
            formClient.processing = false;
            step.value = 1;
            newClientId.value = response.data.id;

            store.dispatch('updateAnnounce', "Le client a été créé avec succès");
        })
        .catch(error => {
            store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la création du client, un email a été envoyé au client pour qu'il puisse terminer son inscription");
            console.error(error);
        });
};

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
        formEquipement.file = event.dataTransfer.files[0];
    } else
        fileError.value = true;
};
const handleFiles = (event) => {
    if (event.target.files.length > 0 && event.target.files[0].type === 'text/csv') {
        fileError.value = false;
        formEquipement.file = event.target.files[0];
    } else
        fileError.value = true;
};
const fileInputClick = () => {
    document.querySelector('input[type="file"]').click();
};

const submitEquipement = () => {
    formEquipement.processing = true;
    if (!formEquipement.file || !formEquipement.file.name.endsWith('.csv')) {
        fileError.value = true;
        fileErrorMsg.value = "Veuillez sélectionner un fichier .csv valide.";
        return;
    }

    let formData = new FormData();
    formData.append('file', formEquipement.file);
    formData.append('user_id', newClientId.value);

    axios.post('/api/equipements/importer-csv', formData)
        .then(() => {
            store.dispatch('updateAnnounce', "Les équipements ont été importés avec succès");

            formClient.reset();
            formEquipement.reset();
            step.value = 0;
        })
        .catch(error => {
            fileError.value = true;
            fileErrorMsg.value = error.response.data.message;
            store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'importation des équipements");
        });
};
const skipStep = () => {
    formClient.reset();
    formEquipement.reset();
    step.value = 0;
};
</script>

<template>
    <div class="componentcontainer connexion">
        <div class="w_container vertical aligncenter gap12px">
            <ApplicationLogo class="image52x52px" />
            <div class="text24px unbounded">Créer un compte client</div>
        </div>

        <div class=" max-w-md w-full">
            <form v-if="step == 0" class="w_container vertical gap20px padding20px white round outlinegrey">
                <div class="w_container vertical">
                    <label for="siret" class="text14px medium">SIRET<span class="red">*</span></label>
                    <input id="siret" type="text" class="textinput grey text14px" @input="handleSiretChange($event)"
                        v-model="formClient.siret" required autofocus autocomplete="siret">
                </div>
                <div class="w_container gap16px">
                    <div class="w_container vertical">
                        <label for="lname" class="text14px medium">Nom<span class="red">*</span></label>
                        <input id="lname" type="text" class="textinput grey text14px" v-model="formClient.lname"
                            required autofocus autocomplete="lname">
                    </div>
                    <div class="w_container vertical">
                        <label for="fname" class="text14px medium">Prénom<span class="red">*</span></label>
                        <input id="fname" type="text" class="textinput grey text14px" v-model="formClient.fname"
                            required autofocus autocomplete="fname">
                    </div>
                </div>
                <div class="w_container vertical">
                    <label for="email" class="text14px medium">Email<span class="red">*</span></label>
                    <input id="email" type="email" class="textinput grey text14px" v-model="formClient.email" required
                        autocomplete="username">
                </div>
                <div class="w_container vertical">
                    <label for="tel" class="text14px medium">Numéro de téléphone</label>
                    <input id="tel" type="text" class="textinput grey text14px" v-model="formClient.tel" required
                        autofocus autocomplete="tel">
                </div>
                <div @click="createClient()" :class="['bigbutton', formClient.processing ? 'gray' : 'purple']">
                    <div class="text14px white">Continuer</div>
                </div>
            </form>

            <form @submit.prevent="submitEquipement" v-if="step == 1">
                <div class="componentcontainer bg-white">
                    <div class="w_container vertical gap20px">
                        <div class="flex justify-center items-center w-full h-64 bg-gray-200 rounded-lg cursor-pointer dragndrop"
                            :class="{ 'dragActive': dragActive || (formEquipement.file && !fileError), 'error': fileError }"
                            @dragover.prevent="dragOver" @dragleave.prevent="dragLeave" @drop.prevent="handleDrop"
                            @click="fileInputClick">
                            <input type="file" ref="fileInput" class="hidden" @change="handleFiles" multiple />
                            <div v-if="!dragActive && !formEquipement.file && !fileError">
                                <div class="text16px medium text-center">Glissez et déposez le fichier des équipement du
                                    client ici</div>
                                <div class="text14px mt-2 text-center">Ou cliquez pour sélectionner un fichier</div>
                            </div>
                            <div v-if="dragActive" class="text16px purple medium">Relâchez votre fichier ici</div>
                            <template v-else>
                                <div v-if="formEquipement.file && !fileError" class="text16px purple medium">
                                    {{ formEquipement.file.name }}
                                </div>
                                <div v-if="fileError" class="text16px red medium text-center">
                                    {{ fileErrorMsg ? fileErrorMsg : 'Le fichier doit être au format CSV' }}
                                </div>
                            </template>
                        </div>

                        <div class="w_container vertical gap8px">
                            <button type="submit" :disabled="formEquipement.processing"
                                :class="['bigbutton', formEquipement.processing ? 'gray' : 'purple']">
                                <div class="text14px white">
                                    Importer
                                </div>
                            </button>
                            <div class="bigbutton" style="background-color: var(--grey-100);" @click="skipStep()">
                                <div class="text14px">
                                    Passer cette étape
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <Annoucer></Annoucer>
    <ErrorAnnouncer></ErrorAnnouncer>
</template>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
