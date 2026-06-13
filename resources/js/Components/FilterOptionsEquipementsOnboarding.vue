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
const emit = defineEmits(['updateFilteredEquipements']);
const updateFilteredEquipements = (equipements) => {
    emit('updateFilteredEquipements', equipements);
}

const filtersOpen = ref({
    sous_categorie: true,
    status: true
})
const toggleFilter = (filter) => filtersOpen.value[filter] = !filtersOpen.value[filter];

let uniqueSousCategories = [...new Set(props.equipementsMarketPlace.map(equipement => equipement.sous_categorie))];
const form = useForm({
    table: "products",
    colonnes: ['sous_categorie'],
    sous_categorie: uniqueSousCategories.map(sous_categorie => ({
        title: sous_categorie,
        selected: false
    })),
    'superadmin': true
});
const filtersBy = ref([
    {
        title: 'Equipement', value: "sous_categorie", icon_purple: "https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65d72910b0c603417c17958f_Vectors-Wrapper.svg", icon_black: "/images/equipement_black.svg",
        options: form.sous_categorie
    },
]);
const updateSelection = async (filterTitle, optionTitle) => {
    const formOption = form[filterTitle].find(o => o.title === optionTitle);
    formOption.selected = !formOption.selected;

    try {
        const response = await axios.post('/api/filterSearch', form);
        updateFilteredEquipements(response.data);
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
                    <div class="text14px purple text-capitalize">{{ option.title }}</div>
                </div>
            </div>
        </div>
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

.listofoptions {
    top: 50px;
}
</style>
