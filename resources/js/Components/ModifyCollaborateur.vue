<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, watch, ref } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentCollaborateur: {
        type: Object,
        default: () => ({})
    },
    titre: {
        type: String,
        default: "Modifier un collaborateur",
    },
    type: {
        type: String,
        default: "Personne",
    }
});
const emit = defineEmits(['closeModifyCollaborateur']);

const close = () => {
    emit('closeModifyCollaborateur');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const adressesPopup = ref(false);
const currentAddress = ref(props.adresses.find(address => address.default === 1));

const form = useForm({
    id: null,
    name: "",
    poste: "",
    email: "",
    tel: "",
    type: data.type,
    adresse_id: null,
});

const selectAddress = (adresse) => {
    currentAddress.value = adresse;
    adressesPopup.value = false;
    form.adresse_id = adresse.id;
}

watch(() => data.currentCollaborateur, (newCollaborateur, oldAdresse) => {
    if (newCollaborateur) {
        form.id = newCollaborateur.id
        form.name = newCollaborateur.name
        form.poste = newCollaborateur.poste
        form.email = newCollaborateur.email
        form.tel = newCollaborateur.tel
        form.adresse_id = newCollaborateur.adresse_id ? newCollaborateur.adresse_id : currentAddress.value.id
    }
}, {
    deep: true
})

const submit = async () => {
    form.processing = true;
    try {
        const response = await axios.post('/api/editCollaborateur', form);
        if (data.type == 'Personne') {
            props.collaborateurs = response.data;
        } else {
            props.salles = response.data;
        }
        form.reset();
        store.dispatch('updateAnnounce', "Le collaborateur a été modifié avec succès");
        form.processing = false;
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la modification du collaborateur");
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    {{ data.titre }}
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container gap16px">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                <template v-if="data.type == 'Personne'">
                                    Nom - Prénom <span class="red">*</span>
                                </template>
                                <template v-else>
                                    Nom <span class="red">*</span>
                                </template>
                            </div>
                            <div class="textinput">
                                <input id="createName" class="text14px w-full" type="text" v-model="form.name"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div v-if="data.type == 'Personne'" class="w_container vertical">
                            <div class="text14px medium">
                                Poste
                            </div>
                            <div class="textinput">
                                <input id="createPoste" class="text14px w-full" type="text" v-model="form.poste"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div v-if="data.type == 'Personne'" class="w_container vertical">
                        <div class="text14px medium">
                            Email <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="createEmail" class="text14px w-full" type="text" v-model="form.email"
                                autocomplete="off">
                        </div>
                    </div>
                    <div v-if="data.type == 'Personne'" class="w_container vertical">
                        <div class="text14px medium">
                            Téléphone
                        </div>
                        <div class="textinput">
                            <input id="createTel" class="text14px w-full" type="text" v-model="form.tel"
                                autocomplete="off">
                        </div>
                    </div>
                    <div v-if="data.type == 'Salle' && currentAddress" class="w_container vertical">
                        <div class="text14px medium">
                            Adresse
                        </div>
                        <div @click="adressesPopup = !adressesPopup"
                            class="w_container justifyspacebetween _100 height40px aligncenter padding12px cursor-pointer bg-white">
                            <div class="w_container aligncenter">
                                <div class="text14px medium nowrap p-2">
                                    {{ currentAddress.titre }}
                                </div>
                            </div>
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                        </div>
                        <div v-if="adressesPopup" class="selectadresschoice">
                            <div class="w_container vertical overflowauto">
                                <div v-for="adresse in props.adresses" :key="adresse" @click="selectAddress(adresse)"
                                    class="w_container vertical gap4px padding12px grey clickable">
                                    <div class="text14px medium">{{ adresse.titre }}</div>
                                    <div class="text14px grey400">{{ adresse.adresse }}</div>
                                    <div class="text14px grey400">{{ adresse.code_postal + ' ' +
                                        adresse.ville }}</div>
                                    <div class="text14px grey400">{{ adresse.pays }}</div>
                                </div>
                            </div>
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
