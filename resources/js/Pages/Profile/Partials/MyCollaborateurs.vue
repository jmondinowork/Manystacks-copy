<script setup>
import CreateCollaborateur from "@/Components/CreateCollaborateur.vue";
import ModifyCollaborateur from "@/Components/ModifyCollaborateur.vue";
import { userInitials } from '@/functions';

import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const { props } = usePage();

const showCreateCollaborateur = ref(false);
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;
const openCreateCollaborateur = () => showCreateCollaborateur.value = true;

const currentCollaborateur = ref({});
const showModifyCollaborateur = ref(false);
const closeModifyCollaborateur = () => showModifyCollaborateur.value = false;
const openModifyCollaborateur = (collaborateur) => {
    currentCollaborateur.value = collaborateur;
    showModifyCollaborateur.value = true;
}

const deleteCollaborateurConfirm = ref(null);
const deleteCollaborateur = (collaborateur) => {
    let form = useForm({
        collaborateur_id: collaborateur.id
    });

    form.post(route('profile.deleteCollaborateur'), {
        onFinish: () => {
            const index = props.collaborateurs.findIndex(a => a.id === collaborateur.id);
            if (index !== -1)
                props.collaborateurs.splice(index, 1);
        }
    });
}
</script>

<template>
    <div class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                <div class="text20px unbounded">Mes collaborateurs</div>
                <div class="bigbutton purple w-fit" @click="openCreateCollaborateur">
                    <div class="text14px white">Ajouter un collaborateur</div>
                </div>
            </div>
            <div class="separatorhorizontal"></div>
            <div class="adressescontainer">
                <div class="w_container vertical gap12px adresses">
                    <div v-for="collaborateur in props.collaborateurs" :key="collaborateur.id"
                        class="w_container aligncenter gap20px white round padding20px">
                        <div class="w_container gap20px">
                            <div v-if="!collaborateur.profile_img" class="avatarcircle">
                                <div class="text40px white">{{ userInitials(collaborateur.name) }}</div>
                            </div>
                            <div v-else class="avatarcircle_img"
                                :style="{ 'background-image': 'url(' + collaborateur.profile_img + ')' }">
                            </div>
                        </div>
                        <div class="w_container justifyspacebetween _100 adresses">
                            <div class="w_container aligncenter _100 gap20px">
                                <div class="w_container vertical gap4px">
                                    <div class="text16px medium">{{ collaborateur.name }}</div>
                                    <div class="text14px">{{ collaborateur.poste }}</div>
                                </div>
                                <div class="w_container vertical gap4px">
                                    <div class="text14px">{{ collaborateur.email }}</div>
                                    <div class="text14px">{{ collaborateur.tel }}</div>
                                </div>
                            </div>
                            <div class="w_container aligncenter gap8px">
                                <div v-if="deleteCollaborateurConfirm !== collaborateur.id"
                                    @click="openModifyCollaborateur(collaborateur)" class="lightbutton">
                                    <div class="frame-164">
                                        <div class="text14px medium purple">Modifier</div>
                                    </div>
                                </div>

                                <div v-if="deleteCollaborateurConfirm !== collaborateur.id"
                                    @click="deleteCollaborateurConfirm = collaborateur.id" class="lightbutton">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579db02389fa9aac727b450_trash_purple.svg"
                                        loading="lazy" alt="" class="image16x16px">
                                </div>
                                <template v-else>
                                    <div class="w_container gap12px">
                                        <div class="bigbutton" style="background-color: var(--red);"
                                            @click="deleteCollaborateur(collaborateur)">
                                            <div class="text14px" style="color: #fff;">
                                                Confirmer
                                            </div>
                                        </div>
                                        <div class="bigbutton" @click="deleteCollaborateurConfirm = null"
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
            </div>
        </div>
    </div>

    <CreateCollaborateur :show="showCreateCollaborateur" @closeCreateCollaborateur="closeCreateCollaborateur">
    </CreateCollaborateur>
    <ModifyCollaborateur :show="showModifyCollaborateur" :currentCollaborateur="currentCollaborateur"
        @closeModifyCollaborateur="closeModifyCollaborateur"></ModifyCollaborateur>
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

.avatarcircle {
    width: 60px;
    height: 60px;
}

.avatarcircle .text40px {
    font-size: 24px;
}
</style>
