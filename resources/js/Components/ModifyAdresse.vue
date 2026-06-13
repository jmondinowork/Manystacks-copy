<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, watch } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentAdresse: {
        type: Object,
        default: () => ({})
    }
});
const emit = defineEmits(['closeModify']);

const close = () => {
    emit('closeModify');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    id: null,
    titre: '',
    adresse: '',
    complement_adresse: "",
    code_postal: '',
    ville: '',
    pays: ''
});

watch(() => data.currentAdresse, (newAdresse, oldAdresse) => {
    if (newAdresse) {
        form.id = newAdresse.id
        form.titre = newAdresse.titre
        form.adresse = newAdresse.adresse
        form.complement_adresse = newAdresse.complement_adresse
        form.code_postal = newAdresse.code_postal
        form.ville = newAdresse.ville
        form.pays = newAdresse.pays
    }
}, {
    deep: true
})

const submit = async () => {
    form.processing = true;
    try {
        const response = await axios.post('/api/store_adresse', form);
        props.adresses = response.data;
        form.reset();
        store.dispatch('updateAnnounce', "L'adresse a été modifiée avec succès");
        form.processing = false;
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la modification de l'adresse");
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Modifier cette adresse
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
                            Titre <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="modifTitre" class="text14px w-full" type="text" v-model="form.titre"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Adresse <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="modifAdresse" class="text14px w-full" type="text" v-model="form.adresse"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Complément d’adresse
                        </div>
                        <div class="textinput">
                            <input id="createComplementAdresse" class="text14px w-full" type="text"
                                v-model="form.complement_adresse" autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container gap16px">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Code postal <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="modifCodePostal" class="text14px w-full" type="text"
                                    v-model="form.code_postal" autocomplete="off">
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Ville <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="modifVille" class="text14px w-full" type="text" v-model="form.ville"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Pays <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="modifPays" class="text14px w-full" type="text" v-model="form.pays"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical gap8px">
                        <button type="submit" :disabled="form.processing"
                            :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                            <div class="text14px white">
                                Enregistrer
                            </div>
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
