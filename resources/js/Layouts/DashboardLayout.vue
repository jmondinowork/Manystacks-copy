<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import SwitchEntreprise from '@/Components/SwitchEntreprise.vue';

import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { debounce } from 'lodash';
import { userInitials } from '@/functions';
import { vOnClickOutside } from '@vueuse/components';

const { props } = usePage();

const searchbar = ref(null);
const searchQuery = ref('');
const searchBarOpen = ref(false);
const OpenSearchBar = () => {
    if (searchQuery.value.trim() !== '') {
        searchBarOpen.value = true;
        searchbar.value.focus();
    }
}
const searchResults = ref([]);
const searchDashboard = debounce(() => {
    if (searchQuery.value.trim() === '') {
        searchResults.value = [];
        searchBarOpen.value = false;
        return;
    }

    axios.post('/api/searchDashboard', { searchInput: searchQuery.value })
        .then(response => {
            searchResults.value = response.data.searchResults;
            searchBarOpen.value = true;
        })
        .catch(error => {
            console.error(error);
        });
}, 300);

const highlightMatch = (name) => {
    if (!name) return '';
    try {
        if (!searchQuery.value) return name;

        const normalizedSearchQuery = removeAccents(searchQuery.value);
        const regex = new RegExp(`(${escapeRegExp(normalizedSearchQuery)})`, 'gi');
        const normalizedName = removeAccents(name);

        const matches = normalizedName.match(regex);
        if (!matches) return name;

        let highlightedName = name;
        matches.forEach(match => {
            const originalMatch = name.substr(normalizedName.indexOf(match), match.length);
            highlightedName = highlightedName.replace(originalMatch, `<span class="super-bold-text">${originalMatch}</span>`);
        });

        return highlightedName;
    } catch (error) {
        console.error(`Error processing name: ${name}`, error);
        return name;
    }
}

const escapeRegExp = (string) => {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

const removeAccents = (string) => {
    return string.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}
</script>

<template>
    <AppLayout>
        <header class="componentcontainer directionvertical gap16px">
            <div class="w_container justify-between">
                <div class="containerwelcome horizontal justify-between w-full">
                    <div class="containertitle">
                        <h1>Bonjour {{ props.userAuth.name.split(' ')[0] }}</h1>
                        <div>Bienvenue sur votre dashboard</div>
                    </div>
                    <div class="buttoncircle 2xl:w-3/6 xl:w-2/6 searchbar" v-on-click-outside="() => searchBarOpen = false"
                        @click="OpenSearchBar()">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670532bb03a6463f99654e79_search.png"
                            loading="lazy" width="20" height="20" alt="" class="image20x20px">
                        <input type="text" class="text14px grey900 show" ref="searchbar" id="searchbar"
                            v-model="searchQuery" @input="searchDashboard" placeholder="Rechercher" autocomplete="off">

                        <div v-if="searchBarOpen" class="search-results">
                            <Link v-for="(result, index) in searchResults" :href="result.link" :key="index"
                                class="result">
                            <div class="flex gap-2 items-center w-4/5">
                                <div class="image_container small">
                                    <img v-if="result.image" :src="result.image"
                                        :class="{ 'rounded-full': result.path == 'Mon équipe >', 'contain': result.path != 'Mon équipe >' }">
                                    <div v-else class="avatarcircle">
                                        <div class="text14px white">{{ userInitials(result.name) }}</div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 w-fit">
                                    <div class="text14px" v-html="highlightMatch(result.name)"></div>
                                    <div class="text12px gray" v-html="highlightMatch(result.description)"></div>
                                </div>
                            </div>
                            <div class="text12px purple">{{ result.path }}</div>
                            </Link>
                        </div>
                    </div>

                    <SwitchEntreprise />

                    <div class="buttoncircle" style="background-color: transparent">
                        <!-- <img width="20" height="20" alt=""
                            src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67091320148a04dac0347d31_bell.png"
                            class="image20x20px">
                        <div class="indicatornumber yellow">4</div> -->
                    </div>
                </div>
            </div>
            <div class="divider_gray_horizontal"></div>
            <div class="containernotifications horizontal">
                <Link :href="route('mon-equipe')" class="dashboard-card hover">
                <div class="horizontal">
                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67091490674dafa04e38305d_Frame%2035.png"
                        width="74" alt="">
                    <div class="vertical">
                        <div class="text32px bold-text">{{ props.count.collaborateurs }}</div>
                        <div class="text14px">collaborateurs</div>
                    </div>
                </div>
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                    width="59" alt="" class="image24x24px">
                </Link>
                <Link :href="route('mes-licences')" class="dashboard-card hover">
                <div class="horizontal">
                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67091490e3b5f95fb3eedfca_Frame%2035-1.png"
                        width="74" alt="">
                    <div class="vertical">
                        <div class="text32px bold-text">{{ props.count.licences }}</div>
                        <div class="text14px">licences</div>
                    </div>
                </div>
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                    width="59" alt="" class="image24x24px">
                </Link>
                <Link :href="route('mes-equipements')" class="dashboard-card hover">
                <div class="horizontal">
                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670914907b28ea61b5825d87_Frame%2035-2.png"
                        width="74" alt="">
                    <div class="vertical">
                        <div class="text32px bold-text">{{ props.count.equipements }}</div>
                        <div class="text14px">appareils</div>
                    </div>
                </div>
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                    width="59" alt="" class="image24x24px">
                </Link>
                <div class="dashboard-card cursor-default">
                    <div class="horizontal">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670914906f34a25480ade8d8_Frame%2035-4.png"
                            width="74" alt="">
                        <div class="vertical">
                            <div class="text32px bold-text">{{ props.count.prix_location_total.toFixed(2) }}€</div>
                            <div class="text14px">Mensualité</div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-card hover:border-white cursor-default">
                    <div class="horizontal">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670914906f34a25480ade8d8_Frame%2035-4.png"
                            width="74" alt="">
                        <div class="vertical">
                            <div class="text32px bold-text">{{ props.count.prix_achat_total.toFixed(2) }}€</div>
                            <div class="text14px">Achats ce mois-ci</div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <slot></slot>
    </AppLayout>
</template>

<style scoped>
.searchbar {
    border: 1px solid rgb(136, 150, 171);
    border-radius: 8px;
}

.search-results {
    position: absolute;
    top: 43px;
    width: 100%;
    left: 0;
    display: flex;
    padding: 12px;
    padding-right: 24px;
    padding-left: 24px;
    flex-flow: column;
    align-items: flex-start;
    gap: 12px;
    border-style: none solid solid;
    border-width: 1px;
    border-color: rgb(136, 150, 171);
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
    cursor: default;
    z-index: 100;
    max-height: 600px;
    overflow: auto;
}

.result {
    cursor: pointer;
    display: flex;
    width: 100%;
    padding: 12px;
    flex-flow: row;
    justify-content: space-between;
    align-items: center;
}

.result:hover {
    background-color: var(--lightpurple);
}

h1 {
    font-size: 38px;
    line-height: 44px;
    margin-top: 20px;
    font-weight: bold;
    margin-bottom: 10px;
}

.horizontal {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}

.horizontal.gap8px {
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}


.containernotifications.horizontal {
    justify-content: space-between;
    align-items: center;
}

.dashboard-card {
    display: flex;
    width: 350px;
    height: 100px;
    padding: 12px 16px;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
    border: 1px solid #fff;
    cursor: pointer;
}

.dashboard-card.hover:hover {
    border: 1px solid var(--grey-200);
}

.text32px {
    font-size: 32px;
}

.containerwelcome.horizontal {
    justify-content: space-between;
    align-items: center;
}

.indicatornumber {
    position: absolute;
    inset: 65% auto auto 65%;
    display: flex;
    width: 20px;
    height: 20px;
    min-height: 20px;
    min-width: 20px;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    background-color: var(--yellow);
    color: rgb(51, 51, 51);
    font-size: 12px;
    line-height: 16px;
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
</style>
