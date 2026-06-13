<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateEquipement from '@/Components/CreateEquipement.vue';
import CreateCollaborateur from "@/Components/CreateCollaborateur.vue";
import CreateContrat from '@/Components/CreateContrat.vue';
import OnboardCollaborateur from '@/Components/OnboardCollaborateur.vue';
import FilterOptionsEquipements from '@/Components/FilterOptionsEquipements.vue';
import FilterOptionsCommandes from '@/Components/FilterOptionsCommandes.vue';
// import FilterOptionsContrats from '@/Components/FilterOptionsContrats.vue';
import FilterOptionsLicences from '@/Components/FilterOptionsLicences.vue';
import FilterOptionsEquipe from '@/Components/FilterOptionsEquipe.vue';
import FilterOptionsSalles from '@/Components/FilterOptionsSalles.vue';
import FilterOptionsSupports from '@/Components/FilterOptionsSupports.vue';
import CreateEquipementCSV from '@/Components/CreateEquipementCSV.vue';
import SelectMultipleEquipements from '@/Components/SelectMultipleEquipements.vue';
import SelectMultipleAttributions from '@/Components/SelectMultipleAttributions.vue';


import { usePage, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { debounce } from 'lodash';
import { vOnClickOutside } from '@vueuse/components';

const superadmin = window.location.pathname.includes('Admin');

const showCreateEquipement = ref(false);
const closeCreateEquipement = () => showCreateEquipement.value = false;
const openCreateEquipement = () => showCreateEquipement.value = true;

const showCreateEquipementCSV = ref(false);
const closeCreateEquipementCSV = () => showCreateEquipementCSV.value = false;
const openCreateEquipementCSV = () => showCreateEquipementCSV.value = true;

const showFilterOptions = ref(false);
const closeFilterOptions = () => showFilterOptions.value = false;
const openFilterOptions = () => showFilterOptions.value = true;
const toggleFilterOptions = () => showFilterOptions.value = !showFilterOptions.value;

const showCreateContrat = ref(false);
const closeCreateContrat = () => showCreateContrat.value = false;
const openCreateContrat = () => showCreateContrat.value = true;


const emit = defineEmits(['resetSelected']);
const showSelectMultiple = ref(false);
const closeSelectMultiple = () => {
    showSelectMultiple.value = false;
    emit('resetSelected');
};
const openSelectMultiple = () => showSelectMultiple.value = true;


const { props } = usePage();
const store = useStore();
const currentPathSegment = window.location.pathname.split('/')[1].replace('-', '_');

const title = computed(() => {
    let tmp = currentPathSegment
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');

    if (tmp.split(' ').length > 1) {
        tmp = tmp.split(' ')[1]
    }
    if (tmp === 'Supports')
        tmp = 'Support';
    return tmp;
});
const countMessage = computed(() => {
    switch (currentPathSegment) {
        case 'mes_equipements':
            return props.mes_equipements.length > 1 ? `${props.mes_equipements.length} équipements` : `${props.mes_equipements.length} équipement`;
        case 'mes_commandes':
            return props.mes_commandes.length > 1 ? `${props.mes_commandes.length} commandes` : `${props.mes_commandes.length} commande`;
        case 'mes_contrats':
            return props.mes_contrats.length > 1 ? `${props.mes_contrats.length} contrats` : `${props.mes_contrats.length} contrat`;
        case 'mon_equipe':
            return props.mes_attributions.length > 1 ? `${props.mes_attributions.length} collaborateurs` : `${props.mes_attributions.length} collaborateur`;
        case 'mes_salles':
            return props.mes_attributions.length > 1 ? `${props.mes_attributions.length} salles` : `${props.mes_attributions.length} salle`;
        case 'supports':
        case 'supportsAdmin':
            return props.supports.length > 1 ? `${props.supports.length} tickets` : `${props.supports.length} ticket`;
        case 'usersAdmin':
            return props.usersAdmin.length > 1 ? `${props.usersAdmin.length} utilisateurs` : `${props.usersAdmin.length} utilisateur`;
        case 'mes_licences':
            return props.mes_licences.length > 1 ? `${props.mes_licences.length} licences` : `${props.mes_licences.length} licence`;
    }
});

// const prixMessage = computed(() => {
//     switch (currentPathSegment) {
//         case 'mes_licences':
//             let totalPrix = 0;
//             props.mes_licences.forEach(licence => {
//                 totalPrix += licence.prix_u * licence.total;
//             });
//             totalPrix = totalPrix.toFixed(2);
//             return `Prix total ${totalPrix} €`;

//     }
// })
const currentFilterOption = computed(() => {
    switch (currentPathSegment) {
        case 'mes_equipements':
            return FilterOptionsEquipements;
        case 'mes_commandes':
            return FilterOptionsCommandes;
        // case 'mes_contrats':
        //     return FilterOptionsContrats;
        case 'mon_equipe':
            return FilterOptionsEquipe;
        case 'mes_salles':
            return FilterOptionsSalles;
        case 'supports':
        case 'supportsAdmin':
            return FilterOptionsSupports;
        case 'usersAdmin':
            return null;
        case 'mes_licences':
            return FilterOptionsLicences;
    }
});

const currentSelectOption = computed(() => {
    switch (currentPathSegment) {
        case 'mes_equipements':
            return SelectMultipleEquipements;
        case 'mes_salles':
            // case 'mon_equipe':
            return SelectMultipleAttributions;
    }
});

const searchbar = ref(null);
const isSearchBarVisible = ref(false);
const toggleSearchbar = async (event) => {
    if (!isSearchBarVisible.value) {
        isSearchBarVisible.value = true;
        await nextTick();
        searchbar.value.focus();
    }
    else {
        if (event.target.tagName === 'IMG') {
            isSearchBarVisible.value = false;
        }
    }
};
const hideSearchbar = () => { if (!searchbar.value.value) isSearchBarVisible.value = false };


const searchQuery = ref('');
const saveData = { 'mes_licences': props.mes_licences, 'usersAdmin': props.usersAdmin, 'supportsAdmin': props.supports, 'supports': props.supports, 'mes_equipements': props.mes_equipements, 'mes_commandes': props.mes_commandes, 'mes_salles': props.mes_attributions, 'mon_equipe': props.mes_attributions, 'mes_contrats': props.mes_contrats };
const apiPaths = { 'mes_licences': '/api/searchLicences', 'usersAdmin': '/api/searchUsers', 'supportsAdmin': '/api/seachSupports', 'supports': '/api/seachSupports', 'mes_equipements': '/api/searchEquipements', 'mes_commandes': '/api/searchCommandes', 'mes_salles': '/api/searchAttributions', 'mon_equipe': '/api/searchAttributions', 'mes_contrats': '/api/searchContrats' };
const searchProducts = debounce(async () => {
    let path = currentPathSegment;
    let type;
    if (currentPathSegment === 'mes_salles' || currentPathSegment === 'mon_equipe') {
        path = 'mes_attributions';
        type = currentPathSegment === 'mes_salles' ? 'Salle' : 'Personne';
    }
    if (currentPathSegment === 'supportsAdmin') {
        path = 'supports';
    }

    if (searchQuery.value.trim() === '') {
        props[path] = saveData[currentPathSegment];
        return;
    }

    try {
        const response = await axios.post(apiPaths[currentPathSegment], { searchInput: searchQuery.value, type: type, superadmin: superadmin });
        props[path] = response.data.searchResults;
    } catch (error) {
        console.error(`Error during search: ${error}`);
    }
}, 300);

const gotCta = currentPathSegment === 'mes_equipements' || currentPathSegment === 'mon_equipe' || currentPathSegment === 'mes_salles';

const titreCreateCollaborateur = ref(null);
const typeCreateCollaborateur = ref(null);
const showCreateCollaborateur = ref(false);
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;
const openCreateCollaborateur = (typeCurrent) => {
    showCreateCollaborateur.value = true;
    typeCreateCollaborateur.value = typeCurrent;
    titreCreateCollaborateur.value = typeCurrent === 'Personne' ? 'Ajouter un nouveau collaborateur' : 'Ajouter une nouvelle salle';
}
const showOnboardCollaborateur = ref(false);
const openOnboardCollaborateur = () => {
    showOnboardCollaborateur.value = true;
}

const layoutKey = ref('');
onMounted(() => {
    if (superadmin) {
        layoutKey.value = 'superadmin';
    }
    if (sessionStorage.getItem('reloaded') === 'true') {
        store.dispatch('updateAnnounce', "Les équipements ont été importés avec succès");
        sessionStorage.removeItem('reloaded');
    }
});

const data = defineProps({
    selectedItems: {
        type: Object,
        default: []
    }
});
</script>

<template>
    <AppLayout :layoutKey="layoutKey">
        <header class="componentcontainer directionvertical gap16px">
            <div class="w_container justifyspacebetween">
                <div class="w_container vertical nogap max-w-fit" v-if="!showSelectMultiple">
                    <div class="text20px unbounded text-nowrap">{{ title }}</div>
                    <div class="w_container">
                        <div class="text14px grey400">{{ countMessage }}</div>
                    </div>
                    <!-- <div class="w_container">
                        <div class="text14px grey400">{{ prixMessage }}</div>
                    </div> -->
                </div>
                <div v-if="isSearchBarVisible" class="separatorvertical pl-3"></div>
                <div :class="{ 'width-full': isSearchBarVisible || showSelectMultiple }"
                    class="w_container gap12px justifyright margin12pxleft">
                    <template v-if="!showSelectMultiple && props.userAuth.role != 'collaborateur'">
                        <div @click="openCreateEquipement"
                            v-if="currentPathSegment == 'mes_equipements' && !isSearchBarVisible" class="lightbutton">
                            <div class="text14px medium purple nowrap">Ajouter un équipement</div>
                        </div>
                        <div @click="openCreateEquipementCSV"
                            v-if="currentPathSegment == 'mes_equipements' && !isSearchBarVisible" class="lightbutton">
                            <img src="/images/csv-icon.png" loading="lazy" width="20" height="20" alt=""
                                class="image20x20">
                        </div>
                        <div @click="openCreateCollaborateur('Personne')"
                            v-if="currentPathSegment == 'mon_equipe' && !isSearchBarVisible && !props.userAuth.oauth.includes('microsoft')"
                            class="lightbutton">
                            <div class="text14px medium purple nowrap">Ajouter un collaborateur</div>
                        </div>
                        <div @click="openOnboardCollaborateur"
                            v-if="currentPathSegment == 'mon_equipe' && !isSearchBarVisible && props.userAuth.oauth.includes('microsoft')"
                            class="lightbutton">
                            <div class="text14px medium purple nowrap">Onboarder un collaborateur</div>
                        </div>
                        <div @click="openCreateCollaborateur('Salle')"
                            v-if="currentPathSegment == 'mes_salles' && !isSearchBarVisible" class="lightbutton">
                            <div class="text14px medium purple nowrap">Ajouter une salle</div>
                        </div>
                        <Link href="/register" v-if="currentPathSegment == 'usersAdmin' && !isSearchBarVisible"
                            class="lightbutton">
                        <div class="text14px medium purple nowrap">Ajouter un nouveau client</div>
                        </Link>
                        <div @click="openCreateContrat()"
                            v-if="currentPathSegment == 'mes_contrats' && !isSearchBarVisible" class="lightbutton">
                            <div class="text14px medium purple nowrap">Importer un contrat</div>
                        </div>
                        <div v-if="gotCta && !isSearchBarVisible" class="separatorvertical">
                        </div>
                        <div class="buttoncircle" v-on-click-outside="hideSearchbar"
                            :class="{ 'width-full': isSearchBarVisible }" @click="toggleSearchbar($event)">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <input type="text" class="text14px grey900" :class="{ 'show': isSearchBarVisible }"
                                ref="searchbar" id="searchbar" v-model="searchQuery" @input="searchProducts"
                                placeholder="Rechercher" autocomplete="off">
                        </div>
                        <template v-if="currentSelectOption">
                            <div class="buttoncircle" @click="openSelectMultiple">
                                <img src="/images/selectOption.svg" loading="lazy" width="20" height="20" alt=""
                                    class="image20x20">
                            </div>
                        </template>
                    </template>
                    <component :is="currentSelectOption" :show="showSelectMultiple" :selectedItems="data.selectedItems"
                        @closeSelectMultiple="closeSelectMultiple">
                    </component>
                    <div v-if="currentFilterOption" v-on-click-outside="closeFilterOptions">
                        <div class="buttoncircle" @click="toggleFilterOptions">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2ab9102a4896b638bedf_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                        </div>
                        <component :is="currentFilterOption" :show="showFilterOptions"
                            @closeFilterOptions="closeFilterOptions">
                        </component>
                    </div>
                </div>
            </div>
        </header>

        <slot :showSelectMultiple="showSelectMultiple"></slot>
    </AppLayout>

    <CreateEquipementCSV :show="showCreateEquipementCSV" @closeCreate="closeCreateEquipementCSV"></CreateEquipementCSV>
    <CreateEquipement :show="showCreateEquipement" @closeCreate="closeCreateEquipement"></CreateEquipement>
    <CreateCollaborateur :from="'index'" :type="typeCreateCollaborateur" :titre="titreCreateCollaborateur"
        :show="showCreateCollaborateur" @closeCreateCollaborateur="closeCreateCollaborateur">
    </CreateCollaborateur>
    <OnboardCollaborateur v-if="currentPathSegment == 'mon_equipe' && props.userAuth.oauth.includes('microsoft')" :show="showOnboardCollaborateur" @closeOnboardCollaborateur="showOnboardCollaborateur = false"></OnboardCollaborateur>
    <CreateContrat :show="showCreateContrat" @closeCreateContrat="closeCreateContrat"></CreateContrat>
</template>

<style scoped>
#searchbar {
    border: none;
    padding: 0;
    display: none;
}

#searchbar.show {
    width: 100%;
    display: block;
}

#searchbar:focus {
    outline: none;
    box-shadow: none;
}

#searchbar::placeholder {
    color: #bbc3cf;
    font-weight: 300;
}

._hide {
    display: none;
}

.width-full {
    width: 100% !important;
}
</style>
