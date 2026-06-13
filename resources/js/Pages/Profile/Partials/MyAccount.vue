<script setup>
import { userInitials } from '@/functions';

import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from 'vue';
import { useStore } from 'vuex';

const { props } = usePage();
const store = useStore();

const fileInput = ref(null);
const infoPersoForm = useForm({
    id: props.userAuth.id,
    fname: props.userAuth.name.split(' ')[0],
    lname: props.userAuth.name.split(' ')[1],
    tel: props.userAuth.tel,
    profile_img: props.userAuth.profile_img,
    poste: props.userAuth.poste,
    email: props.userAuth.email,
    type: 'Personne'
});
const passwordForm = useForm({
    password: null,
    password_confirmation: null
});
const passwordErrors = ref({
    'password': '',
    'password_confirmation': ''
});
const handleChange = (input) => validatePasswordField(passwordForm[input.target.id], input.target.id);
const validatePasswordField = (value, fieldName) => {
    if (!value) {
        passwordErrors.value[fieldName] = 'Ce champ est requis';
        return false;
    }

    if (fieldName == 'password' && passwordForm.password.length < 8) {
        passwordErrors.value[fieldName] = "Le mot de passe doit faire au moins 8 caractères"
        return false;
    }

    if (fieldName === 'password_confirmation') {
        if (passwordForm.password !== passwordForm.password_confirmation) {
            passwordErrors.value[fieldName] = 'Les mots de passe ne correspondent pas';
            return false;
        }
    }

    passwordErrors.value[fieldName] = '';
    return true;
};

const previewfile = ref(props.userAuth.profile_img);
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewfile.value = e.target.result;
        };
        reader.readAsDataURL(file);
        infoPersoForm.profile_img = file;
    }
};
const triggerFileInput = (fileID) => document.getElementById(fileID).click();

const infoPersoSubmit = async () => {
    let formData = new FormData();
    Object.keys(infoPersoForm).forEach(key => {
        if (infoPersoForm[key])
            formData.append(key, infoPersoForm[key]);
        else
            formData.append(key, '');
    });

    try {
        const response = await axios.post(route('profile.editAccount'), formData);
        store.dispatch('updateAnnounce', 'Vos informations personnelles ont été modifiées avec succès');
        props.userAuth = response.data;
    } catch (error) {
        store.dispatch('updateErrorAnnounce', 'Une erreur s\'est produite lors de la modification de vos informations personnelles');
    }
};
const passwordSubmit = () => {
    let hasError = 0;

    for (const fieldName in passwordErrors.value) {
        if (!validatePasswordField(passwordForm[fieldName], fieldName)) {
            hasError++;
        }
    }

    if (!hasError) {
        passwordForm.post(route('profile.editPassword'), {
            onFinish: () => {
                passwordForm['password'] = null
                passwordForm['password_confirmation'] = null;
                store.dispatch('updateAnnounce', 'Vos informations ont été modifiées.');
            }
        });
    }
}
</script>

<template>
    <div class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="text20px unbounded">Mon compte</div>
            <div class="separatorhorizontal"></div>
            <div class="w_container vertical gap12px">
                <div class="text16px medium">Mes informations personnelles</div>
                <form @submit.prevent="infoPersoSubmit"
                    class="w_container vertical gap20px padding20px white round outlinegrey">
                    <div class="w_container vertical">
                        <div class="text14px medium">Photo de profil</div>
                        <div class="w_container gap20px">
                            <div v-if="!previewfile" class="avatarcircle">
                                <div class="text40px white">{{ userInitials(props.userAuth.name) }}</div>
                            </div>
                            <div v-else class="avatarcircle_img"
                                :style="{ 'background-image': 'url(' + previewfile + ')' }">
                            </div>
                            <div @click="triggerFileInput('profile_img_input')"
                                class="w_container aligncenter gap8px clickable">
                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a2c627aaa0abee4d19e582_Vectors-Wrapper.svg"
                                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                <div class="text14px medium purple">Charger une image</div>
                            </div>

                            <input :ref="'fileInput'" class="d-none" :name="'profile_img_input'"
                                :id="'profile_img_input'" type="file" @change="handleFileChange">
                        </div>
                    </div>
                    <div class="w_container gap20px justifyspacebetween">
                        <div class="w_container vertical">
                            <label for="fname" class="text14px medium">Prénom</label>
                            <input id="fname" type="text" class="textinput grey text14px" v-model="infoPersoForm.fname">
                        </div>
                        <div class="w_container vertical">
                            <label for="lname" class="text14px medium">Nom</label>
                            <input id="lname" type="text" class="textinput grey text14px" v-model="infoPersoForm.lname">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <label for="poste" class="text14px medium">Poste</label>
                        <input id="poste" type="text" class="textinput grey text14px" v-model="infoPersoForm.poste">
                    </div>
                    <!-- <div  class="w_container gap20px justifyspacebetween">
                        <div class="w_container vertical">
                            <label for="email" class="text14px medium">Email</label>
                            <input id="email" type="text" class="textinput grey text14px" v-model="infoPersoForm.email">
                        </div>
                    </div> -->
                    <div class="w_container gap20px justifyspacebetween">
                        <div class="w_container vertical">
                            <div class="text14px medium">Email</div>
                            <div class="textinput grey cursor-not-allowed">
                                <div class="text14px">{{ props.userAuth.email }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <label for="tel" class="text14px medium">Numéro de téléphone</label>
                        <input id="tel" type="text" class="textinput grey text14px" v-model="infoPersoForm.tel">
                    </div>
                    <div class="w_container vertical gap8px items-end">
                        <button type="submit" class="bigbutton purple w-fit">
                            <div class="text14px white"> Enregistrer </div>
                        </button>
                    </div>
                </form>
            </div>

            <!-- <div class="text16px medium">Mon mot de passe</div>
            <form @submit.prevent="passwordSubmit"
                class="w_container vertical gap20px padding20px white round outlinegrey">
                <div class="w_container gap20px justifyspacebetween">
                    <div class="w_container vertical">
                        <label for="password" class="text14px medium">Nouveau mot de passe</label>
                        <input :class="{ 'input-error': passwordErrors.password }" @input="handleChange($event)"
                            id="password" type="password" class="textinput grey text14px"
                            v-model="passwordForm.password">
                        <div v-if="passwordErrors.password" class="error">{{ passwordErrors.password }}</div>
                    </div>
                </div>
                <div class="w_container gap20px justifyspacebetween">
                    <div class="w_container vertical">
                        <label for="password_confirmation" class="text14px medium">Confirmer votre mot de passe</label>
                        <input :class="{ 'input-error': passwordErrors.password_confirmation }"
                            @input="handleChange($event)" id="password_confirmation" type="password"
                            class="textinput grey text14px" v-model="passwordForm.password_confirmation">
                        <div v-if="passwordErrors.password_confirmation" class="error">
                            {{ passwordErrors.password_confirmation }}
                        </div>
                    </div>
                </div>
                <div class="w_container vertical gap8px items-end">
                    <button type="submit" class="bigbutton purple w-fit">
                        <div class="text14px white"> Enregistrer </div>
                    </button>
                </div>
            </form> -->
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
</style>
