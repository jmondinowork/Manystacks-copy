<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
});

const filtersOpen = ref({
    sous_categorie: true,
    statut: true
})
const toggleFilter = (filter) => filtersOpen.value[filter] = !filtersOpen.value[filter];

const form = useForm({
    table: "commandes",
    colonnes: ['statut'],
    statut: [
        { title: "En attente de financement", selected: true },
        { title: "En attente de signature", selected: true },
        { title: "En validation du contrat", selected: true },
        { title: "Livraison en cours", selected: true },
        { title: "Terminée", selected: false },
        { title: "Erreur", selected: false },
    ]
});
const filtersBy = ref([
    {
        title: 'Statut', value: "statut", icon_purple: "/images/statut_purple.svg", icon_black: "https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d72913a33625943458fc24_Vectors-Wrapper.svg",
        options: form.statut
    }
]);
const updateSelection = async (filterTitle, optionTitle) => {
    const formOption = form[filterTitle].find(o => o.title === optionTitle);
    formOption.selected = !formOption.selected;

    try {
        const response = await axios.post('/api/filterSearch', form);
        props.mes_commandes = response.data;
    } catch (error) {
        console.error(error);
    }
}
</script>

<template>
    <div v-if="data.show" class="listofoptions filter">
        <div class="filterclosed title">
            <div class="text14px semibold">Filtrer par</div>
        </div>
        <div v-for="filter in filtersBy" :key="filter.title"
            :class="{ 'filteropen': filtersOpen[filter.value], 'filterclosed': !filtersOpen[filter.value] }">
            <div @click="toggleFilter(filter.value)"
                class="w_container aligncenter gap12px title justify-between w-full cursor-pointer">
                <div class="w_container aligncenter gap12px">
                    <img v-if="filtersOpen[filter.value]" :src="filter.icon_purple" loading="lazy" width="20" height="20"
                        alt="" class="image20x20px">
                    <img v-else :src="filter.icon_black" loading="lazy" width="20" height="20" alt="" class="image20x20">
                    <div class="text14px titletext text-capitalize">{{ filter.title }}</div>
                </div>
                <img v-if="filtersOpen[filter.value]"
                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d72911ee3c81aaf42dce99_Vectors-Wrapper.svg"
                    loading="lazy" width="20" height="20" alt="" class="image20x20px open">
                <img v-else
                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d7291323964a663754a759_Vectors-Wrapper.svg"
                    loading="lazy" width="20" height="20" alt="" class="image20x20 close">
            </div>

            <div class="separatorfilter">
                <div class="separatorhorizontal purple purple"></div>
            </div>
            <div class="w_container vertical nogap filtersoptions">
                <div v-for="option in filter.options" :key="option.title" class="filterselect">
                    <input type="checkbox" class="checkboxunselected" :checked="option.selected"
                        @change="updateSelection(filter.value, option.title)" />
                    <div class="text14px purple" :class="{'text-capitalize': filter.title != 'Statut'}">{{ option.title }}</div>
                </div>
            </div>
        </div>
        <!-- <div class="filterclosed title">
            <div class="text14px semibold">Trier par</div>
        </div>
        <div class="filterclosed selected">
            <div class="frame-223"><img
                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d72a5e18460edf7fd738f4_check_full.svg"
                    loading="lazy" width="20" height="20" alt="" class="image16x16px">
                <div class="text14px semibold purple">Equipement</div>
            </div>
        </div>
        <div class="filterclosed">
            <div class="frame-223"><img
                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d731043f9e85e717d17a79_check_empty_black.svg"
                    loading="lazy" width="20" height="20" alt="" class="image16x16px">
                <div class="text14px">Statut</div>
            </div>
        </div> -->
    </div>
</template>

<style scoped>
.filterclosed img.close {
    transform: rotate(180deg);
}

.filterclosed .filtersoptions {
    display: none;
}

.filterclosed .separatorfilter {
    display: none;
}

.filteropen .title {
    padding: 6px 12px;
}

.filteropen .titletext {
    font-weight: 600;
    color: var(--main);
}
</style>
