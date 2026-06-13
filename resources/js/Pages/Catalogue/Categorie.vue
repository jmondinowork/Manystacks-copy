<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import ProductsPreview from '@/Components/ProductsPreview.vue';
import { genderMap } from '@/config.js';

import { Head, usePage } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { ref, onMounted } from 'vue';

const store = useStore();
const props = usePage().props;

onMounted(() => {
    store.dispatch('updateFilteredProductCount', { count: new Set(props.products.map(product => product.slug)).size, from: "props" })
})

const productPreviewTitle = `Retrouvez ${genderMap[props.current_categorie] === 'f' ? 'toutes' : 'tous'} nos ${props.current_categorie}`;
</script>

<template>

    <Head>
        <title>{{ props.current_categorie }}</title>
        <meta name="description" content="Retrouvez toutes nos catégories">
    </Head>

    <CatalogueLayout>
        <ProductsPreview
            :title="productPreviewTitle" :contact="true">
        </ProductsPreview>
    </CatalogueLayout>
</template>
