<script setup>
import CreateTags from "./CreateTags.vue";

import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { computed } from "vue";
import { vOnClickOutside } from '@vueuse/components'
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    titre: {
        type: String,
        default: "Ajouter un nouveau collaborateur",
    },
    type: {
        type: String,
        default: "Personne",
    },
    from: {
        type: String,
        default: "profile",
    }
});
const emit = defineEmits(['closeCreateCollaborateur', 'updateCollaborateurCreated']);

const close = () => {
    form.reset();
    emit('closeCreateCollaborateur');
}
const updateCollaborateurCreated = () => {
    emit('updateCollaborateurCreated');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const adressesPopup = ref(false);
const currentAddress = ref(props.adresses ? props.adresses.find(address => address.default === 1) : null);

const form = useForm({
    id: null,
    equipement_id: props.equipement ? props.equipement.id : null,
    name: "",
    fname: "",
    lname: "",
    poste: "",
    email: "",
    tel: "",
    type: data.type,
    from: data.from,
    adresse_id: currentAddress.value ? currentAddress.value.id : null,
});

const selectAddress = (adresse) => {
    currentAddress.value = adresse;
    adressesPopup.value = false;
    form.adresse_id = adresse.id;
}

const showTag = ref(false);
const closeTag = () => showTag.value = false;
const openTag = () => showTag.value = true;


const dropdownOpen = ref(false);
const openDropdown = () => dropdownOpen.value = true;
const hideDropDown = () => dropdownOpen.value = false;
const userTags = ref([]);
const allTags = ref([...(props.tags ?? [])]);
const selectTag = (id, event) => {
    event.stopPropagation();

    userTags.value.push(allTags.value.find(tag => tag.id === id));
    allTags.value = allTags.value.filter(tag => tag.id !== id);
}
const undoTag = (id) => {
    allTags.value.push(userTags.value.find(tag => tag.id === id));
    userTags.value = userTags.value.filter(tag => tag.id !== id);
}

const searchTerm = ref('');
const filteredTags = computed(() => allTags.value.filter(
    tag => tag.name.toLowerCase().includes(
        searchTerm.value.toLowerCase()
    ))
);

const updateTags = (tags) => {
    userTags.value.push(tags);
    props.tags.push(tags);
}

const submit = async () => {
    form.processing = true;
    try {
        form.type = data.type;
        form.tags = userTags.value.map(tag => tag.id);
        const response = await axios.post('/api/editCollaborateur', form);

        if (data.type == 'Personne') {
            props.collaborateurs = response.data;
        } else {
            props.salles = response.data;
        }
        if (data.from == 'attribution') {
            props.equipement = response.data.equipement;
            props.historiques = response.data.historiques;
        }
        else if (data.from == 'index') {
            props.mes_attributions = response.data.mes_attributions;
        }


        form.reset();
        allTags.value = props.tags;
        userTags.value = [];
        updateCollaborateurCreated();
        if (data.type == 'Personne') {
            store.dispatch('updateAnnounce', "Le collaborateur a été créé avec succès");
        } else {
            store.dispatch('updateAnnounce', "La salle a été créée avec succès");
        }
        form.processing = false;
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite.");
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
                    <div v-if="data.type == 'Salle'" class="w_container vertical">
                        <div class="text14px medium">
                            Nom <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="createName" class="text14px w-full" type="text" v-model="form.name"
                                autocomplete="off">
                        </div>
                    </div>
                    <div v-else class="w_container gap20px justifyspacebetween">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Prénom <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="createFirstname" class="text14px w-full" type="text" v-model="form.fname"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Nom <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="createLastname" class="text14px w-full" type="text" v-model="form.lname"
                                    autocomplete="off">
                            </div>
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
                    <div class="w_container vertical">
                        <label for="tags" class="text14px medium">Tags</label>
                        <div class="w_container gap-2">
                            <div v-for="tag in userTags" :key="tag.id" class="tagblock w-fit cursor-pointer"
                                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                <div class="texttag">
                                    {{ tag.name }}
                                </div>
                                <span @click="undoTag(tag.id)">&#x2715;</span>
                            </div>
                        </div>
                        <div class="w_container justify-center items-center">
                            <div @click="openDropdown" class="searchbar grey w-full" ref="searchBarContainer">
                                <img class="image20x20px" loading="lazy" width="20" height="20"
                                    src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                                <input type="text" class="text14px grey600 light w-full p-0" ref="searchbar"
                                    id="searchbar" placeholder="Rechercher" v-model="searchTerm" autocomplete="off">

                                <div @click="openTag" class="bigbutton purple w-fit text-nowrap h-fit p-1 rounded">
                                    <div class="text14px white"> Créer un tag </div>
                                </div>

                                <div v-on-click-outside="hideDropDown" v-if="dropdownOpen" class="select-items p-4">
                                    <div class="w_container gap-2 flex-wrap">
                                        <div v-for="tag in filteredTags" :key="tag.id"
                                            @click="selectTag(tag.id, $event)" class="tagblock w-fit cursor-pointer"
                                            :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                            <div class="texttag">
                                                {{ tag.name }}
                                            </div>
                                        </div>
                                    </div>
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

    <CreateTags :show="showTag" @closeTag="closeTag" @updateTags="updateTags"></CreateTags>
</template>

<style scoped>
.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    cursor: default;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}
</style>
