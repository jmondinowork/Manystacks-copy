<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useStore } from 'vuex';
import { vOnClickOutside } from '@vueuse/components'
import { ref } from "vue";

const { props } = usePage();
const store = useStore();

const previewfile = ref(props.entreprise.profile_img);
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

const form = useForm({
    siret: "",
    raison_sociale: "",
    adresse: "",
    complement_adresse: "",
    code_postal: "",
    ville: "",
    profile_img: "",
    pays: "France"
})
if (props.entreprise) {
    Object.assign(form, props.entreprise);
}

const submit = () => {
    try {
        form.post(route('profile.editCompany'));
        store.dispatch('updateAnnounce', 'Vos informations ont été modifiées.');

    } catch (error) {
        store.dispatch('updateErrorAnnounce', 'Une erreur est survenue.');
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="text20px unbounded">Mon entreprise</div>
            <div class="separatorhorizontal"></div>
            <div class="w_container vertical gap12px">
                <div class="w_container vertical gap20px padding20px white round outlinegrey">
                    <div class="w_container vertical">
                        <div class="text14px medium">Image de l'entreprise</div>
                        <div class="w_container gap20px">
                            <div class="avatarcircle_img" :style="{ 'background-image': 'url(' + previewfile + ')' }">
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
                    <div class="w_container vertical">
                        <label for="siret" class="text14px medium">SIRET</label>
                        <div class="textinput grey text14px cursor-not-allowed">{{ form.siret }}</div>
                        <!-- <input id="siret" type="text" class="textinput grey text14px" v-model="form.siret"> -->
                    </div>
                    <div class="w_container vertical">
                        <label for="raison_sociale" class="text14px medium">Raison sociale</label>
                        <input id="raison_sociale" type="text" class="textinput grey text14px"
                            v-model="form.raison_sociale">
                    </div>
                    <div class="w_container vertical">
                        <label for="adresse" class="text14px medium">Adresse</label>
                        <input id="adresse" type="text" class="textinput grey text14px" v-model="form.adresse">
                    </div>
                    <div class="w_container vertical">
                        <label for="complement_adresse" class="text14px medium">Complément d'adresse</label>
                        <input id="complement_adresse" type="text" class="textinput grey text14px"
                            v-model="form.complement_adresse">
                    </div>
                    <div class="w_container gap20px justifyspacebetween">
                        <div class="w_container vertical">
                            <label for="code_postal" class="text14px medium">Code postal</label>
                            <input id="code_postal" type="text" class="textinput grey text14px"
                                v-model="form.code_postal">
                        </div>
                        <div class="w_container vertical">
                            <label for="ville" class="text14px medium">Ville</label>
                            <input id="ville" type="text" class="textinput grey text14px" v-model="form.ville">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">Pays</div>
                        <div class="textinput grey text14px justify-start cursor-not-allowed">
                            <img src="/images/fr-flags.png" loading="lazy" alt="" class="flags">
                            <div class="text14px medium nowrap pl-2">France</div>
                        </div>
                    </div>
                    <div class="w_container vertical gap8px items-end">
                        <button type="submit" class="bigbutton purple w-fit">
                            <div class="text14px white"> Enregistrer </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<style scoped>
.select-item:hover {
    background-color: #F7F8F9;
}

.select-item {
    padding: 12px;
}

.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}

.textinput.pays {
    cursor: pointer !important;
}
</style>
