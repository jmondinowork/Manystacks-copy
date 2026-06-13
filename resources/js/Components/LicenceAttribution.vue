<script setup>
import CreateCollaborateur from "@/Components/CreateCollaborateur.vue";
import { userInitials } from '@/functions';

import { usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref, watch } from "vue";
import { useStore } from "vuex";
import EmptyPage from "./EmptyPage.vue";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
    },
    licencesAvailable: {
        type: Array,
    },
});
const emit = defineEmits(['closeAttribution']);

const close = () => {
    emit('closeAttribution');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const users = ref(props.users);
const searchQuery = ref('');
const searchUsers = () => {
    if (searchQuery.value.trim() === '') {
        users.value = props.users;
        return;
    }
    users.value = props.users.filter(a => a.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
}
watch(() => props.users, (newUsers, oldUsers) => {
    users.value = newUsers;
}, {
    deep: true
});

const showCreateCollaborateur = ref(false);
const openCreateCollaborateur = () => showCreateCollaborateur.value = true;
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;

const assign = async (userId) => {
    try {
        const response = await axios.post('/api/assignLicence', {
            licence_id: data.licencesAvailable[0].id,
            user_id: userId,
        });

        props.licences = response.data.licences;
        props.users = response.data.users;
        store.dispatch('updateAnnounce', `${response.data.user.name} a reçu la licence ${response.data.licences[0].name}`);
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'attribution");
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer">
                <div class="w_container _100 justifyspacebetween aligncenter">
                    <div class="text20px unbounded">Attribuer une licence</div>
                    <img @click="close"
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8bbf4430c3c231a740166_Vectors-Wrapper.svg"
                        loading="lazy" width="24" height="24" alt="" class="image28x28px cursor-pointer">
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical gap12px">
                        <div class="searchbar" v-if="data.licencesAvailable.length" ref="searchBarContainer w-full">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <input type="text" class="text14px grey900 show w-full p-0" ref="searchbar" id="searchbar"
                                v-model="searchQuery" @input="searchUsers" placeholder="Rechercher"
                                autocomplete="off">
                        </div>
                        <div class="separatorhorizontal" v-if="data.licencesAvailable.length"></div>
                        <div v-if="data.licencesAvailable.length" class="w_container vertical gap12px _264px">
                            <div v-for="user in users" :key="user.id" @click="assign(user.id)"
                                class="w_container aligncenter gap12px padding12px white round clickable">
                                <div v-if="user.profile_img" class="avatarcircle_img"
                                    :style="{ 'background-image': 'url(' + user.profile_img + ')' }">
                                </div>
                                <div v-else class="avatarcircle">
                                    <div class="text40px white">{{ userInitials(user.name) }}</div>
                                </div>
                                <div class="w_container justifyspacebetween _100 adresses">
                                    <div class="w_container aligncenter _100 gap20px adresses">
                                        <div class="w_container vertical gap4px">
                                            <div class="text16px medium">{{ user.name }}</div>
                                            <div class="w_container gap12px items-center">
                                                <div v-for="tag in user.tags.slice(0, 2)" :key="tag.id"
                                                    class="tagblock w-fit cursor-pointer"
                                                    :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                                    <div class="texttag">
                                                        {{ tag.name }}
                                                    </div>
                                                </div>
                                                <div v-if="user.poste && user.tags.length"
                                                    class="separatorvertical h-7"></div>
                                                <div v-if="user.poste" class="text14px">{{ user.poste }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <EmptyPage v-else :section="'licences'"></EmptyPage>
                    </div>
                    <div class="w_container vertical gap8px">
                        <div v-if="data.licencesAvailable.length" class="w_container horizontalthenvertical gap12px">
                            <div @click="openCreateCollaborateur()" class="bigbutton purple">
                                <div class="text14px white">Créer un nouveau collaborateur</div>
                            </div>
                        </div>
                        <div class="bigbutton" @click="close">
                            <div class="text14px">Fermer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <CreateCollaborateur :from="'attribution'" :type="'Personne'" :titre="'Ajouter un nouveau collaborateur'"
        :show="showCreateCollaborateur" @closeCreateCollaborateur="closeCreateCollaborateur">
    </CreateCollaborateur>
</template>

<style scoped>
.avatarcircle,
.avatarcircle_img {
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
}

.avatarcircle .text40px {
    font-size: 18px;
}
</style>
