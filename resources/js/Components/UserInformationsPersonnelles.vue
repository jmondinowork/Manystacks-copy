<script setup>
import AddTags from './AddTags.vue';
import { userInitials, objectToFormData } from '@/functions';

import { useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';

const { props } = usePage();
const store = useStore();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['close']);

const close = () => {
    emit('close');
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    id: props.attribution.id,
    fname: props.attribution.name.split(' ')[0],
    lname: props.attribution.name.split(' ')[1],
    name: props.attribution.name,
    tel: props.attribution.tel,
    profile_img: props.attribution.profile_img,
    poste: props.attribution.poste,
    email: props.attribution.email,
    type: props.attribution.type,
    date_arrivee: props.attribution.date_arrivee,
    date_sortie: props.attribution.date_sortie,
    tags: props.attribution.tags.map(tag => tag.id),
    email_perso: props.attribution.email_perso,
    processing: false
});

const previewfile = ref(props.attribution.profile_img);
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewfile.value = e.target.result;
        };
        reader.readAsDataURL(file);
        form.profile_img = file;
    }
};
const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const updateTagsForm = (tags) => {
    form.tags = tags.map(tag => tag.id);
}

const infoPersoSubmit = async () => {
    form.processing = true;

    var formData = objectToFormData(form);

    // let formData = new FormData();
    // Object.keys(form).forEach(key => {
    //     if (form[key])
    //         formData.append(key, form[key]);
    //     else
    //         formData.append(key, '');
    // });

    try {
        await axios.post(route('profile.editAccount'), formData);
        localStorage.setItem('edited', 'Les informations personnelles ont été modifiées avec succès');
        window.location.reload();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', 'Une erreur s\'est produite lors de la modification de vos informations personnelles');
        form.processing = false;
    }
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer" style="max-width: 1000px;">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Informations personnelles
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="w_container vertical gap12px overflowauto">
                <form @submit.prevent="infoPersoSubmit" class="w_container vertical gap12px bg-white p-10 rounded-lg">
                    <div class="horizontal">
                        <div class="containerphotoprofil">
                            <div class="text14px medium">Photo de profil</div>
                            <div class="flex flex-col w-full h-full gap-2 items-center">
                                <div class="image_container big">
                                    <img v-if="previewfile" :src="previewfile" loading="lazy" class="rounded-full"
                                        alt="">
                                    <div v-else class="avatarcircle big">
                                        <div class="text-6xl text-white">{{ userInitials(props.attribution.name) }}
                                        </div>
                                    </div>
                                </div>
                                <div @click="triggerFileInput('profile_img_input')"
                                    class="w_container aligncenter gap8px clickable">
                                    <!-- <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a2c627aaa0abee4d19e582_Vectors-Wrapper.svg"
                                        loading="lazy" width="16" height="16" alt="" class="image16x16px"> -->
                                    <div class="text12px gray underline">Modifier la photo</div>
                                </div>

                                <input :ref="'fileInput'" class="d-none" :name="'profile_img_input'"
                                    :id="'profile_img_input'" type="file" @change="handleFileChange">
                            </div>
                        </div>
                        <div class="flex flex-col gap-4" style="width: 75%;"
                            v-if="props.attribution.type === 'Personne'">
                            <div class="flex flex-row gap-4">
                                <div class="w_container vertical">
                                    <div class="w_container vertical">
                                        <label for="fname" class="text14px medium">Prénom</label>
                                        <input id="fname" type="text" class="textinput grey text14px"
                                            v-model="form.fname">
                                    </div>
                                    <div class="w_container vertical">
                                        <label for="lname" class="text14px medium">Nom</label>
                                        <input id="lname" type="text" class="textinput grey text14px"
                                            v-model="form.lname">
                                    </div>
                                    <div class="w_container vertical">
                                        <label for="poste" class="text14px medium">Poste</label>
                                        <input id="poste" type="text" class="textinput grey text14px"
                                            v-model="form.poste">
                                    </div>
                                    <div class="w_container vertical">
                                        <label for="tel" class="text14px medium">Numéro de téléphone</label>
                                        <input id="tel" type="text" class="textinput grey text14px" v-model="form.tel">
                                    </div>
                                </div>
                                <div class="w_container vertical">
                                    <div class="w_container vertical">
                                        <label for="fname" class="text14px medium">Date d'arrivée</label>
                                        <input id="fname" type="date" class="textinput grey text14px w-full"
                                            v-model="form.date_arrivee">
                                    </div>
                                    <div class="w_container vertical">
                                        <label for="lname" class="text14px medium">Date de sortie</label>
                                        <input id="lname" type="date" class="textinput grey text14px w-full"
                                            v-model="form.date_sortie">
                                    </div>
                                    <div class="w_container gap20px justifyspacebetween">
                                        <div class="w_container vertical">
                                            <div class="text14px medium">Email Personnel</div>
                                            <input id="email_perso" type="text" class="textinput grey text14px"
                                                v-model="form.email_perso">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="w_container gap20px justifyspacebetween">
                                <div class="w_container vertical">
                                    <AddTags @updateTagsForm="updateTagsForm" :userTags="props.attribution.tags"
                                        class="dark" />
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4" style="width: 75%;" v-else>
                            <div class="flex flex-row gap-4">
                                <div class="w_container vertical">
                                    <div class="w_container vertical">
                                        <label for="name" class="text14px medium">Nom de la salle</label>
                                        <input id="name" type="text" class="textinput grey text14px"
                                            v-model="form.name">
                                    </div>
                                </div>
                            </div>
                            <div class="w_container gap20px justifyspacebetween">
                                <div class="w_container vertical">
                                    <AddTags @updateTagsForm="updateTagsForm" :userTags="props.attribution.tags"
                                        class="dark" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="w_container vertical gap8px items-end mt-4">
                        <button type="submit" :disabled="form.processing"
                            :class="['button gap-5', form.processing ? 'gray' : '']">
                            <div class="text14px white">
                                Enregistrer
                            </div>
                            <span v-if="form.processing" class="loader small"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.avatarcircle_img {
    background-position: center;
    background-size: cover;
    width: 100px;
    height: 100px;
    grid-column-gap: 10px;
    grid-row-gap: 10px;
    border-radius: 10000px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    display: flex;
}

.horizontal {
    display: flex;
    width: auto;
    height: auto;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    border-style: none;
    border-width: 1px;
    border-color: black;
}

.containerphotoprofil {
    display: flex;
    width: 20%;
    height: auto;
    margin-right: 0px;
    margin-left: 0px;
    flex-flow: column;
    justify-content: space-between;
    align-items: center;
    align-self: center;
    gap: 16px;
}
</style>
