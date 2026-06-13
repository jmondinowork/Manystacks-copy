<script setup>
import CreateAdresse from "@/Components/CreateAdresse.vue";
import ModifyAdresse from "@/Components/ModifyAdresse.vue";

import { useForm, usePage } from "@inertiajs/vue3";
import { useStore } from 'vuex';
import { ref } from "vue";

const { props } = usePage();
const store = useStore();


const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => showCreate.value = true;

const currentAdresse = ref({});
const showModify = ref(false);
const closeModify = () => showModify.value = false;
const openModify = (adresse) => {
    currentAdresse.value = adresse;
    showModify.value = true;
}

const deleteAdresseConfirm = ref(null);

const setDefault = async (adresse) => {
    if (!adresse.default) {
        try {
            const response = await axios.post(route('profile.setAdresseDefault'), {
                adresse_id: adresse.id,
            });
            props.adresses = response.data
            store.dispatch('updateAnnounce', "L'adresse par défaut a bien été modifiée");
        } catch (error) {
            store.dispatch('updateErrorAnnounce', 'Une erreur s\'est produite lors de la modification de l\'adresse par défaut');
        }
    }
}
const deleteAdresse = (adresse) => {
    let form = useForm({
        adresse_id: adresse.id
    });

    form.post(route('profile.deleteAdresse'), {
        onFinish: () => {
            const index = props.adresses.findIndex(a => a.id === adresse.id);
            if (index !== -1)
                props.adresses.splice(index, 1);
        }
    });
}
</script>

<template>
    <div class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                <div class="text20px unbounded">Mes adresses</div>
            </div>
            <div class="separatorhorizontal"></div>
            <div class="adressescontainer">
                <div class="w_container vertical gap12px adresses">
                    <div v-for="adresse in props.adresses" :key="adresse.id"
                        class="w_container aligncenter gap20px white round padding20px">
                        <div @click="setDefault(adresse)" class="adressesicon" :class="{ 'purple': adresse.default }">
                            <img v-if="adresse.default"
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a3afb54b373885b68f4bcd_Vectors-Wrapper.svg"
                                loading="lazy" width="24" height="24" alt="" class="image24x24px">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a3b482c9b72634cfd56fe5_Vectors-Wrapper.svg"
                                loading="lazy" width="24" height="24" alt="" class="image24x24px">
                            <div v-if="adresse.default" class="w_container popuphover adresses">
                                <div class="text13px grey600 _100">Adresse par défaut</div>
                            </div>
                            <div v-else class="w_container popuphover adresses">
                                <div class="text13px grey600 _100">Définir par défaut</div>
                            </div>
                        </div>
                        <div class="w_container justifyspacebetween _100 adresses">
                            <div class="w_container aligncenter _100 gap20px">
                                <div class="w_container vertical gap4px">
                                    <div class="text16px medium">{{ adresse.titre }}</div>

                                </div>
                                <div class="w_container vertical gap4px">
                                    <div class="text14px">{{ adresse.adresse }}</div>
                                    <div class="text14px">{{ adresse.code_postal + ' ' + adresse.ville }}</div>
                                </div>
                            </div>
                            <div class="w_container aligncenter gap8px">
                                <div v-if="deleteAdresseConfirm !== adresse.id" @click="openModify(adresse)"
                                    class="lightbutton">
                                    <div class="frame-164">
                                        <div class="text14px medium purple">Modifier</div>
                                    </div>
                                </div>

                                <div v-if="deleteAdresseConfirm !== adresse.id" @click="deleteAdresseConfirm = adresse.id"
                                    class="lightbutton">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579db02389fa9aac727b450_trash_purple.svg"
                                        loading="lazy" alt="" class="image16x16px">
                                </div>
                                <template v-else>
                                    <div class="w_container gap12px">
                                        <div class="bigbutton" style="background-color: var(--red);"
                                            @click="deleteAdresse(adresse)">
                                            <div class="text14px" style="color: #fff;">
                                                Confirmer
                                            </div>
                                        </div>
                                        <div class="bigbutton" @click="deleteAdresseConfirm = null"
                                            style="background-color: var(--grey-100);">
                                            <div class="text14px">
                                                Annuler
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bigbutton purple" @click="openCreate">
                    <div class="text14px white">Ajouter une adresse</div>
                </div>
            </div>
        </div>
    </div>

    <CreateAdresse :show="showCreate" @closeCreate="closeCreate"></CreateAdresse>
    <ModifyAdresse :currentAdresse="currentAdresse" :show="showModify" @closeModify="closeModify">
    </ModifyAdresse>
</template>

<style scoped>
.w_container.justifyspacebetween._100.adresses {
    flex-direction: row !important;
}

.w_container.popuphover.adresses {
    bottom: 80%;
    display: none;
    width: max-content;
}

.adressesicon:hover .popuphover.adresses {
    display: flex;
}

.darkmodalbackground {
    position: fixed;
}
</style>
