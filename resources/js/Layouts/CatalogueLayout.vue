<script setup>
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ProductsPreview from '@/Components/ProductsPreview.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MenuBurgerContainer from '@/Components/MenuBurgerContainer.vue';

import { ref, computed, nextTick, onMounted, watch } from 'vue';
import { useStore } from 'vuex';
import { vOnClickOutside } from '@vueuse/components'
import { Link, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const store = useStore();
const props = usePage().props;

const searchbar = ref(null);
const isSearchBarVisible = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const page = window.location.pathname.split('/')[1];
const currentPage = window.location.pathname.split('/').pop();
const currentCategory = window.location.pathname.split('/')[2];
const currentSousCategory = window.location.pathname.split('/')[3];

const currentCount = ref(null);
watch(
    () => store.state.filteredProductCount,
    () => {
        if (store.state.filteredProductCountFrom == "props")
            currentCount.value = store.state.filteredProductCount;
    }
);

const panierLength = computed(() => store.state.panierLength);
const searchProducts = debounce(() => {
    if (searchQuery.value.trim() === '') {
        searchResults.value = [];
        store.dispatch('updateFilteredProductCount', { count: currentCount.value, from: "props" })
        return;
    }

    axios.post('/api/searchProducts', { searchInput: searchQuery.value })
        .then(response => {
            searchResults.value = response.data.searchResults;
            store.dispatch('updateFilteredProductCount', { count: new Set(response.data.searchResults.map(product => product.slug)).size, from: "searchbar" })
        })
        .catch(error => {
            console.error(error);
        });
}, 300);
const specialCategories = ['ordinateurs', 'telephones', 'tablettes', 'licences'];
const getCategoryLink = computed(() => {
    if (props.categories) {
        const basePath = '/catalogue';

        return props.categories.map(categorie => ({
            categorie, link: `${basePath}/${categorie}`
        }));
    }
    return [];
});
const getSousCategorieLink = computed(() => {
    if (props.sous_categories && !specialCategories.includes(props.sous_categories[0])) {
        const urlSegments = window.location.pathname.split('/');
        const basePath = urlSegments.length > 2 ? `/${page}/${urlSegments[2]}` : page;

        return props.sous_categories.map(categorie => ({
            categorie, link: `${basePath}/${categorie}`
        }));
    }
    return null;
});

const toggleSearchbar = async (event) => {
    if (!isSearchBarVisible.value) {
        isSearchBarVisible.value = true;
        await nextTick();
        searchbar.value.focus();
    }
    else {
        if (event.target.tagName === 'IMG')
            isSearchBarVisible.value = false;
    }
};
const hideSearchbar = () => { if (!searchbar.value.value) isSearchBarVisible.value = false };

const productCountMessage = () => {
    const countFiltered = store.state.filteredProductCount;
    return countFiltered <= 1 ? `${countFiltered} produit` : `${countFiltered} produits`;
}
const displaySeparator = computed(() => getCategoryLink.value && getCategoryLink.value.length > 0);


const showModal = ref(false);
const closeModal = () => showModal.value = false;
const openModal = () => showModal.value = true;
</script>


<template>
    <AppLayout>
        <header class="componentcontainer directionvertical gap16px">
            <div class="w_container justifyspacebetween">
                <div class="w_container gap12px _44pxheight h-auto" style="align-items: flex-start !important;">
                    <!-- Menu burger -->
                    <div class="pageselector hide" @click="openModal">
                        <img class="image20x20px" loading="lazy" width="20" height="20"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753156c3a7a4413c901c8_Vectors-Wrapper.svg" />
                    </div>
                    <!-- Home -->
                    <Link :href="route('catalogue')" class="pageselector"
                        :class="{ 'selected': currentPage == 'catalogue' }">
                    <img src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f7c77ff69649417a1115_Vectors-Wrapper.svg"
                        loading="lazy" width="20" height="20" alt="" class="image20x20px">
                    </Link>
                    <div class="separatorvertical"></div>
                    <!-- Bouton pour les stacks populaires (ajustez l'URL de l'image et le lien selon vos besoins) -->
                    <Link class="pageselector" :class="{ 'selected': currentPage == 'mes-stacks' }"
                        style="    width: max-content;" :href="route('mes-stacks')">
                    <img class="image20x20px" loading="lazy" width="20" height="20"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753db4436aab32b0c2c39_Vectors-Wrapper.svg" />
                    <div class="text14px">Mes stacks</div>
                    </Link>
                    <!-- <Link class="pageselector" :class="{ 'selected': currentPage == 'mon-catalogue' }"
                        :href="route('mon-catalogue')">
                    <img class="image20x20px" loading="lazy" width="20" height="20"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654943141812dfc0db7504c5_Vectors-Wrapper.svg" />
                    <div class="text14px icontitle">Mon catalogue</div>
                    </Link> -->

                    <!-- Ajouter ici d'autres sélecteurs de page selon vos besoins -->

                    <!-- Séparateur vertical -->
                    <div v-if="displaySeparator || isSearchBarVisible" class="separatorvertical"></div>

                    <div class="flex flex-col gap-2">
                        <div class="w_container gap12px _44pxheight">
                            <!-- Catégories (générées dynamiquement) -->
                            <Link :href="link.link" class="pageselector" v-for="link in getCategoryLink"
                                :key="link.categorie"
                                :class="{ '_hide': isSearchBarVisible, 'selected': currentCategory == link.categorie }">
                            <div class="text14px capitalize">{{ link.categorie }}</div>
                            </Link>
                        </div>
                        <div class="w_container gap12px _44pxheight" v-if="getSousCategorieLink">
                            <!-- Catégories (générées dynamiquement) -->
                            <Link :href="link.link" class="pageselector" v-for="link in getSousCategorieLink"
                                :key="link.categorie"
                                :class="{ '_hide': isSearchBarVisible, 'selected': currentSousCategory == link.categorie }">
                            <div class="text14px capitalize">{{ link.categorie }}</div>
                            </Link>
                        </div>
                    </div>
                </div>

                <div :class="{ 'width-full': isSearchBarVisible }"
                    class="w_container gap12px justifyright margin12pxleft">
                    <!-- Bouton de recherche -->
                    <div class="buttoncircle" ref="searchBarContainer" v-on-click-outside="hideSearchbar"
                        :class="{ 'width-full': isSearchBarVisible }" @click="toggleSearchbar($event)">
                        <img class="image20x20px" loading="lazy" width="20" height="20"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                        <input type="text" class="text14px grey900" :class="{ 'show': isSearchBarVisible }"
                            ref="searchbar" id="searchbar" v-model="searchQuery" @input="searchProducts"
                            placeholder="Rechercher" autocomplete="off">
                    </div>
                    <div class="separatorvertical"></div>
                    <!-- Bouton du panier -->
                    <Link :href="route('panier')" class="buttoncircle">
                    <img class="image20x20px" loading="lazy" width="20" height="20"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddbd62af1c2923621b_Vectors-Wrapper.svg" />
                    <div class="indicatornumber">{{ panierLength }}</div>
                    </Link>
                </div>
            </div>

            <!-- Affichage du nombre de produits -->
            <div class="w_container justifyspacebetween">
                <Breadcrumb></Breadcrumb>
                <div class="w_container">
                    <div class="text14px grey400">{{ productCountMessage() }}</div>
                </div>
            </div>
        </header>


        <!-- Résultats de recherche -->
        <div v-if="searchQuery.trim() !== ''">
            <ProductsPreview :title="'Résultat de la recherche'" :filteredProducts="searchResults"></ProductsPreview>
        </div>
        <!-- Slot pour le contenu supplémentaire -->
        <slot v-else></slot>

        <MenuBurgerContainer :show="showModal" @close="closeModal"></MenuBurgerContainer>
    </AppLayout>
</template>


<style scoped>
.separatorvertical {
    height: 40px;
}

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
    border: 2px solid var(--main);
}
</style>
