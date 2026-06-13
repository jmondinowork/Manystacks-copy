<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import SmallProductsPreview from '@/Components/SmallProductsPreview.vue';
import { filterMappingProduct } from '@/config.js';
import { genderMap } from '@/config.js';

import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { useStore } from 'vuex';

const props = usePage().props;
const store = useStore();

const filters = ref({});
const filterMapping = filterMappingProduct[props.current_categorie];

const selectedFilters = ref({});
selectedFilters.value = Object.keys(filterMapping).reduce((acc, key) => {
    if (key === 'price')
        acc[key] = { min: 1, max: 200 };
    else
        acc[key] = {};
    return acc;
}, {});
const columns = Object.values(filterMapping);
columns.forEach(column => {
    if (column === 'poids') {
        filters.value['poids'] = [
            'Moins de 1 kg',
            'Entre 1,1 kg et 2 kg',
            'Entre 2,1 kg et 2,5 kg',
            'Plus de 2,5 kg'
        ];
    } else if (column !== 'prix_location') {
        filters.value[column] = [...new Set(props.products.map(product => product[column]))];
    }
});

const filterOptions = computed(() => {
    if (!filters.value) return {};

    let options = {};

    for (const [label, key] of Object.entries(filterMapping)) {
        if (filters.value[key] && filters.value[key][0] !== '' && filters.value[key][0] !== null) {
            options[label] = [...filters.value[key]];
        }
    }
    return options;
});

const filteredProducts = computed(() => {
    const allFiltersEmpty = Object.keys(selectedFilters.value).every(category => {
        return !selectedFilters.value[category] || Object.keys(selectedFilters.value[category]).length === 0;
    });
    if (allFiltersEmpty) {
        return props.products;
    }

    return props.products.filter(product => {
        return Object.keys(selectedFilters.value).every(category => {
            if (selectedFilters.value[category] && Object.keys(selectedFilters.value[category]).length > 0) {
                const attributeValue = product[filterMapping[category]];
                return checkFilter(selectedFilters.value[category], attributeValue, filterMapping[category]);
            }
            return true;
        });
    });
});

function checkFilter(filters, attributeValue, attributeName) {
    if (attributeName === 'poids') {
        if (filters['Moins de 1 kg'] && parseFloat(attributeValue) < 1) return true;
        if (filters['Entre 1,1 kg et 2 kg'] && parseFloat(attributeValue) >= 1.1 && parseFloat(attributeValue) <= 2) return true;
        if (filters['Entre 2,1 kg et 2,5 kg'] && parseFloat(attributeValue) >= 2.1 && parseFloat(attributeValue) <= 2.5) return true;
        if (filters['Plus de 2,5 kg'] && parseFloat(attributeValue) > 2.5) return true;
        return false;
    }

    if (attributeName === 'prix_location') {
        return attributeValue >= selectedFilters.value.price.min && attributeValue <= selectedFilters.value.price.max;
    }

    return filters[attributeValue] === true;
}

watch(selectedFilters, (newValue) => {
    Object.keys(newValue).forEach(category => {
        Object.keys(newValue[category]).forEach(filter => {
            if (!newValue[category][filter]) {
                delete newValue[category][filter];
            }
        });
    });
}, { deep: true });
watch(filteredProducts, (newFilteredProducts) => {
    const distinctProductCount = new Set(newFilteredProducts.map(product => product.slug)).size;
    store.dispatch('updateFilteredProductCount', { count: distinctProductCount, from: "props" });
});
onMounted(() => {
    store.dispatch('updateFilteredProductCount', { count: props.products.length, from: "props" })
})

const productPreviewTitle = `Retrouvez ${genderMap[props.current_categorie] === 'f' ? 'toutes' : 'tous'} nos ${props.current_categorie}`;
</script>


<template>

    <Head>
        <title>{{ props.current_categorie }}</title>
        <meta name="description" :content="`Retrouvez tous nos ${props.current_categorie}`">
    </Head>

    <CatalogueLayout>
        <div class="productfilterpage">
            <div class="componentcontainer height100" style="min-height: unset;">
                <div class="w_container vertical gap20px thenhorizontal">
                    <div class="w_container vertical gap12px">
                        <p class="text20px unbounded">Prix</p>
                        <div class="w_container gap12px">
                            <div class="w_container vertical">
                                <label for="minPrice" class="text14px">Min</label>
                                <input type="number" id="minPrice" v-model="selectedFilters.price.min"
                                    class="textinput text14px" />
                            </div>
                            <div class="w_container vertical">
                                <label for="maxPrice" class="text14px">Max</label>
                                <input type="number" id="maxPrice" v-model="selectedFilters.price.max"
                                    class="textinput text14px" />
                            </div>
                        </div>
                    </div>

                    <div v-for="(items, title) in filterOptions" :key="title" class="w_container vertical gap12px">
                        <p class="text20px unbounded">{{ title }}</p>
                        <div v-for="(value, index) in items" :key="index"
                            class="w_container vertical gap12px cursor-pointer fdp"
                            @click="selectedFilters[title][value] = !selectedFilters[title][value]">
                            <div class="w_container gap8px _100 clickable">
                                <input type="checkbox" :id="value" v-model="selectedFilters[title][value]"
                                    class="checkboxunselected" @click.stop />
                                <div class="text14px mb-1">{{ value }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <SmallProductsPreview :contact="true" :title="productPreviewTitle" :filteredProducts="filteredProducts">
            </SmallProductsPreview>
        </div>
    </CatalogueLayout>
</template>
