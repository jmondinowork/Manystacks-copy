<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

import { ref, nextTick } from 'vue';
import { vOnClickOutside } from '@vueuse/components'
import { usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import axios from 'axios';


const { props } = usePage();
const searchbar = ref(null);
const isSearchBarVisible = ref(false);
const searchQuery = ref('');

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

const filters = ref([
    { label: "financement", statut: "En attente de financement", selected: 1 },
    { label: "signature", statut: "En attente de signature", selected: 1 },
    { label: "validation", statut: "En validation du contrat", selected: 1 },
    { label: "livraison", statut: "Livraison en cours", selected: 1 },
    { label: "contrat", statut: "Contrat à transmettre", selected: 1 },
    { label: "confirmation", statut: "En confirmation d'achat", selected: 1 },
    { label: "terminée", statut: "Terminée", selected: 0 }
]);
const toggleSelected = (filter) => {
    filter.selected = !filter.selected;

    const selectedStatuses = filters.value.filter(filter => filter.selected).map(filter => filter.statut);
    axios.post('/api/filterCommande', { statuts: selectedStatuses })
        .then(response => {
            props.commandes = response.data;
        })
        .catch(error => {
            console.error("There was an error fetching the filtered commandes:", error);
        });
}
const searchProducts = debounce(() => {
    const selectedStatuses = filters.value.filter(filter => filter.selected).map(filter => filter.statut);

    axios.post('/api/searchCommande', { searchInput: searchQuery.value, statuts: selectedStatuses })
        .then(response => {
            props.commandes = response.data;
        })
        .catch(error => {
            console.error(error);
        });
}, 300);
</script>


<template>
    <AppLayout :layoutKey="'superadmin'">
        <header class="componentcontainer directionvertical gap16px">
            <div class="w_container justifyspacebetween">
                <div class="w_container gap12px _44pxheight">
                    <div class="pageselector p-2 w-fit" v-for="filter in filters" :key="filter.id"
                        :class="{ 'selected': filter.selected }" @click="toggleSelected(filter)">
                        <div class="text14px capitalize text-nowrap">{{ filter.label }}</div>
                    </div>
                    <div class="separatorvertical"></div>
                </div>



                <div :class="{ 'width-full': isSearchBarVisible }" class="w_container gap12px justifyright margin12pxleft">
                    <!-- Bouton de recherche -->
                    <div class="buttoncircle" ref="searchBarContainer" v-on-click-outside="hideSearchbar"
                        :class="{ 'width-full': isSearchBarVisible }" @click="toggleSearchbar($event)">
                        <img class="image20x20px" loading="lazy" width="20" height="20"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                        <input type="text" class="text14px grey900" :class="{ 'show': isSearchBarVisible }" ref="searchbar"
                            id="searchbar" v-model="searchQuery" @input="searchProducts" placeholder="Rechercher">
                    </div>
                </div>
            </div>

            <div class="w_container justifyspacebetween">
                <Breadcrumb></Breadcrumb>
            </div>
        </header>

        <slot></slot>
    </AppLayout>
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

.selected {
    background-color: var(--main);
}

.selected .text14px {
    color: #fff;
}
</style>
