<script setup>
import CreateCollaborateur from "@/Components/CreateCollaborateur.vue";
import { userInitials } from '@/functions';

import { usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref, watch } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
    }
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

const attributions = ref(props.attributions);
const searchQuery = ref('');
const searchProducts = () => {
    if (searchQuery.value.trim() === '') {
        attributions.value = props.attributions;
        return;
    }
    attributions.value = props.attributions.filter(a => a.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
}
watch(() => props.attributions, (newAttributions, oldAttributions) => {
    attributions.value = newAttributions;
}, {
    deep: true
});

const attribute = async (attributionId) => {
    try {
        const response = await axios.post('/api/editEquipement', {
            id: props.equipement.id,
            user_attributed_id: attributionId,
            user_attribution: attributionId
        });

        props.attributions = response.data.attributions_available;
        props.equipement = response.data.equipement;
        props.historiques = response.data.historiques;

        if (attributionId) {
            const user = response.data.attribution;
            store.dispatch('updateAnnounce', `L'équipement a bien été attribué à ${user.name}`);
        } else {
            store.dispatch('updateAnnounce', "L'équipement a bien été retiré");
        }
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'attribution de l'équipement");
    }
}

const titreCreateCollaborateur = ref(null);
const typeCreateCollaborateur = ref(null);
const showCreateCollaborateur = ref(false);
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;
const openCreateCollaborateur = (typeCurrent) => {
    showCreateCollaborateur.value = true;
    typeCreateCollaborateur.value = typeCurrent;
    titreCreateCollaborateur.value = typeCurrent === 'Personne' ? 'Ajouter un nouveau collaborateur' : 'Ajouter une nouvelle salle';
    close();
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer">
                <div class="w_container _100 justifyspacebetween aligncenter">
                    <div class="text20px unbounded">Attribuer l'équipement</div>
                    <img @click="close"
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8bbf4430c3c231a740166_Vectors-Wrapper.svg"
                        loading="lazy" width="24" height="24" alt="" class="image28x28px cursor-pointer">
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap12px">
                    <div class="text14px">Attribution</div>
                    <div v-if="props.equipement.user_attributed_id"
                        class="w_container _100 justifyspacebetween white round padding12px thenvertical">
                        <div class="w_container gap16px">
                            <div v-if="props.equipement.user_attributed.profile_img" class="avatarcircle_img"
                                :style="{ 'background-image': 'url(' + props.equipement.user_attributed.profile_img + ')' }">
                            </div>
                            <div v-else class="avatarcircle">
                                <div class="text40px white">{{ userInitials(props.equipement.user_attributed.name) }}</div>
                            </div>
                            <div class="w_container vertical nogap">
                                <div class="text16px unbounded medium">{{ props.equipement.user_attributed.name }}</div>
                                <div class="w_container gap12px items-center">
                                    <div v-for="tag in props.equipement.user_attributed.tags.slice(0, 2)" :key="tag.id"
                                        class="tagblock w-fit cursor-pointer"
                                        :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                        <div class="texttag">
                                            {{ tag.name }}
                                        </div>
                                    </div>
                                    <div v-if="props.equipement.user_attributed.poste && props.equipement.user_attributed.tags.length"
                                        class="separatorvertical h-7"></div>
                                    <div v-if="props.equipement.user_attributed.poste" class="text14px">
                                        {{ props.equipement.user_attributed.poste }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div @click="attribute(null)" class="lightbutton">
                            <div class="text14px medium purple nowrap">Retirer</div>
                        </div>
                    </div>
                    <div v-else class="w_container _100 justifyspacebetween white round padding12px thenvertical">
                        <div class="w_container gap16px">
                            <div class="circle40px"><img
                                    src="/images/tag-icon.svg"
                                    loading="lazy" width="20" height="20" alt="" class="image20x20px"></div>
                            <div class="w_container vertical gap4px">
                                <div class="text16px">Cet équipement n’est pas attribué</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical gap12px">
                        <div class="searchbar" ref="searchBarContainer w-full">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <input type="text" class="text14px grey900 show w-full p-0" ref="searchbar" id="searchbar"
                                v-model="searchQuery" @input="searchProducts" placeholder="Rechercher" autocomplete="off">
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div class="w_container vertical gap12px _264px">
                            <div v-for="attribution in attributions" :key="attribution.id"
                                @click="attribute(attribution.id)"
                                class="w_container aligncenter gap12px padding12px white round clickable">
                                <div v-if="attribution.profile_img" class="avatarcircle_img"
                                    :style="{ 'background-image': 'url(' + attribution.profile_img + ')' }">
                                </div>
                                <div v-else class="avatarcircle">
                                    <div class="text40px white">{{ userInitials(attribution.name) }}</div>
                                </div>
                                <div class="w_container justifyspacebetween _100 adresses">
                                    <div class="w_container aligncenter _100 gap20px adresses">
                                        <div class="w_container vertical gap4px">
                                            <div class="text16px medium">{{ attribution.name }}</div>
                                            <div class="w_container gap12px items-center">
                                                <div v-for="tag in attribution.tags.slice(0, 2)" :key="tag.id"
                                                    class="tagblock w-fit cursor-pointer"
                                                    :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                                    <div class="texttag">
                                                        {{ tag.name }}
                                                    </div>
                                                </div>
                                                <div v-if="attribution.poste && attribution.tags.length"
                                                    class="separatorvertical h-7"></div>
                                                <div v-if="attribution.poste" class="text14px">{{ attribution.poste }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical gap8px">
                        <div class="w_container horizontalthenvertical gap12px">
                            <div @click="openCreateCollaborateur('Personne')" class="bigbutton purple">
                                <div class="text14px white">Créer un nouveau collaborateur</div>
                            </div>
                            <div @click="openCreateCollaborateur('Salle')" class="bigbutton purple">
                                <div class="text14px white">Créer une nouvelle salle</div>
                            </div>
                        </div>
                        <div class="bigbutton" @click="close">
                            <div class="text14px">Annuler</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <CreateCollaborateur :from="'attribution'" :type="typeCreateCollaborateur" :titre="titreCreateCollaborateur"
        :show="showCreateCollaborateur" @closeCreateCollaborateur="closeCreateCollaborateur">
    </CreateCollaborateur>
</template>

<style scoped>
.stackimage {
    max-width: 300px;
    padding-bottom: 0;
}

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
