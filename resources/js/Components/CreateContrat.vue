<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeCreateContrat']);

const close = () => {
    form.reset();
    emit('closeCreateContrat');
};
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    numero_contrat: '',
    contrat_signe: '',
    date_debut_contrat: '',
    date_fin_contrat: '',
});

const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file)
        form.contrat_signe = file;
};

const submit = () => {
    if (!form.numero_contrat || !form.date_debut_contrat || !form.date_fin_contrat) {
        store.dispatch('updateErrorAnnounce', "Veuillez remplir tous les champs obligatoires");
        return;
    }
    let formData = new FormData();

    formData.append('numero_contrat', form.numero_contrat);
    formData.append('contrat_signe', form.contrat_signe);
    formData.append('date_debut_contrat', form.date_debut_contrat);
    formData.append('date_fin_contrat', form.date_fin_contrat);

    form.processing = true;

    axios.post(route('createContrat'), formData)
        .then((response) => {
            store.dispatch('updateAnnounce', "Le contrat a bien été ajouté");
            props.mes_contrats = response.data;
            close();
        })
        .catch((error) => {
            store.dispatch('updateErrorAnnounce', "Une erreur est survenue lors de l'ajout du contrat");
        })
        .finally(() => form.processing = false);
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Importer un contrat
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Contrat PDF
                        </div>
                        <div class="text12px gray">
                            {{ form.contrat_signe ? form.contrat_signe.name : 'Aucun fichier sélectionné' }}
                        </div>
                        <div class="lightbutton" @click="() => triggerFileInput('contrat_signe')">
                            <div class="frame-164">
                                <div class="text14px medium purple" v-if="!form.contrat_signe">Ajouter</div>
                                <div class="text14px medium purple" v-else>Modifier</div>
                            </div>
                        </div>
                        <input class="d-none" type="file" name="contrat_signe" id="contrat_signe"
                            @input="handleFileChange($event)">
                    </div>

                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Numéro du contrat <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="numeroContrat" class="text14px w-full" type="text" v-model="form.numero_contrat"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Date de début du contrat <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="dateDebutContrat" class="text14px w-full" type="date"
                                v-model="form.date_debut_contrat" autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Date de fin du contrat <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="dateFinContrat" class="text14px w-full" type="date"
                                v-model="form.date_fin_contrat" autocomplete="off">
                        </div>
                    </div>

                    <div class="w_container vertical gap8px mt-10">
                        <button type="submit" :disabled="form.processing"
                            :class="['button gap-5', form.processing ? 'gray' : '']">
                            <div class="text14px white">
                                Confirmer
                            </div>
                            <span v-if="form.processing" class="loader small"></span>
                        </button>
                        <div class="bigbutton" @click="close">
                            <div class="text14px">
                                Annuler
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
