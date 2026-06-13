<script setup>
import CreateAdresse from '@/Components/CreateAdresse.vue';
import CreateCollaborateur from '@/Components/CreateCollaborateur.vue';
import { userInitials } from '@/functions';

import { usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, watch } from "vue";

const { props } = usePage();

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentUser: {
        type: Object,
        default: () => [],
    },
    currentAddress: {
        type: Object,
        default: () => [],
    }
});

const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => showCreate.value = true;

const showCreateCollaborateur = ref(false);
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;
const openCreateCollaborateur = () => showCreateCollaborateur.value = true;

const emit = defineEmits(['closeAttribute', 'onAttributeSelected']);

const close = () => {
    emit('closeAttribute');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const adressesPopup = ref(false);
const usersPopup = ref(false);
const addressSelected = ref(data.currentAddress);
const userSelected = ref(data.currentUser);
const currentAddress = ref(data.currentAddress);
const currentUser = ref(data.currentUser);

watch(() => data.currentAddress, (newValue, oldValue) => {
    currentAddress.value = newValue;
});
watch(() => data.currentUser, (newValue, oldValue) => {
    currentUser.value = newValue;
});

const selectAddress = (adresse) => {
    addressSelected.value = adresse;
    currentAddress.value = adresse;
    adressesPopup.value = false;
}
const selectUser = (user) => {
    userSelected.value = user;
    currentUser.value = user;
    usersPopup.value = false;
}
const updateAdresseCreated = () => {
    addressSelected.value = props.adresses[props.adresses.length - 1];
    currentAddress.value = props.adresses[props.adresses.length - 1];
    adressesPopup.value = false;
}
const updateCollaborateurCreated = () => {
    userSelected.value = props.collaborateurs[props.collaborateurs.length - 1];
    currentUser.value = props.collaborateurs[props.collaborateurs.length - 1];
    usersPopup.value = false;
}

const onAttributeSelected = () => {
    emit('onAttributeSelected', userSelected.value, addressSelected.value);
    close();
}
</script>

<template>
    <div v-if="data.show" class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer" style="overflow: visible;">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Attribuer la livraison
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical gap12px">
                        <div class="w_container aligncenter justifyspacebetween">
                            <div class="text14px medium">Destinataire</div>
                        </div>
                        <div @click="usersPopup = !usersPopup"
                            class="w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey cursor-pointer bg-white">
                            <div class="w_container aligncenter gap-2">
                                <img v-if="currentUser.profile_img" class="avatarcontainer"
                                    :src="currentUser.profile_img" alt="">
                                <div v-else class="avatarcontainer">
                                    <div class="text16px medium white">{{
                                        userInitials(currentUser.name) }}</div>
                                </div>
                                <div class="text14px medium nowrap p-2">
                                    {{ currentUser.name }}
                                </div>
                            </div>
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                        </div>
                        <div v-if="usersPopup" class="selectadresschoice">
                            <div @click="openCreateCollaborateur" class="bigbutton purple">
                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/657ed73aa941ecab9566078e_plus.svg"
                                    loading="lazy" alt="" class="image20x20px">
                                <div class="text14px white">Ajouter un collaborateur</div>
                            </div>
                            <div class="w_container vertical overflowauto">
                                <div v-for="user in props.collaborateurs" :key="user" @click="selectUser(user)"
                                    class="w_container vertical gap4px padding12px grey clickable">
                                    <div class="w_container aligncenter gap-2">
                                        <img v-if="user.profile_img" class="avatarcontainer" :src="user.profile_img"
                                            alt="">
                                        <div v-else class="avatarcontainer">
                                            <div class="text16px medium white">{{
                                                userInitials(user.name) }}</div>
                                        </div>
                                        <div class="text14px medium">{{ user.name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="w_container vertical gap12px">
                        <div class="w_container aligncenter justifyspacebetween">
                            <div class="text14px medium">Adresse d’envoi</div>
                        </div>
                        <div @click="adressesPopup = !adressesPopup"
                            class="w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey cursor-pointer bg-white">
                            <div class="w_container aligncenter">
                                <div class="text14px medium nowrap p-2">
                                    {{ currentAddress.titre }}
                                </div>
                            </div>
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                        </div>
                        <div v-if="adressesPopup" class="selectadresschoice">
                            <div @click="openCreate" class="bigbutton purple">
                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/657ed73aa941ecab9566078e_plus.svg"
                                    loading="lazy" alt="" class="image20x20px">
                                <div class="text14px white">Ajouter une adresse</div>
                            </div>
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
                        <button type="submit" @click="onAttributeSelected" class="bigbutton purple">
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
        </div>
    </div>

    <CreateAdresse :show="showCreate" @closeCreate="closeCreate" @updateAdresseCreated="updateAdresseCreated" />
    <CreateCollaborateur :from="'livraison'" :show="showCreateCollaborateur"
        @closeCreateCollaborateur="closeCreateCollaborateur" @updateCollaborateurCreated="updateCollaborateurCreated" />
</template>

<style scoped>
.avatarcontainer {
    background-position: center;
    background-size: cover;
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    min-height: 30px !important;
    cursor: pointer;
    border-radius: 100000px;
    justify-content: center;
    align-items: center;
    display: flex;
}
</style>
