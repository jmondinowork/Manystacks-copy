<script setup>
import BesoinReference from './BesoinReference.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = usePage().props;
const data = defineProps({
    filteredProducts: Array,
    title: String,
    contact: Boolean
});
const productsToShow = computed(() => {
    const products = data.filteredProducts !== undefined ? data.filteredProducts : props.products;
    // const uniqueProductsMap = new Map();

    // products.forEach(product => {
    //     if (!uniqueProductsMap.has(product.slug)) {
    //         uniqueProductsMap.set(product.slug, product);
    //     } else {
    //         const existingProduct = uniqueProductsMap.get(product.slug);
    //         if (product.prix < existingProduct.prix) {
    //             uniqueProductsMap.set(product.slug, product);
    //         }
    //     }
    // });

    // return Array.from(uniqueProductsMap.values());
    return products;
});
</script>

<template>
    <div class="componentcontainer directionvertical">
        <div class="w_container vertical gap16px">
            <div class="text20px unbounded">
                {{ data.title }}
            </div>
            <div class="gridproducts">
                <Link v-for="product in productsToShow" :key="product.id"
                    :href="`/catalogue/${product.categorie}/${product.sous_categorie}/${product.slug}?id=${product.id}`"
                    class="w_container vertical gap16px white round padding20px aligncenter clickable">
                <div class="w_container vertical alignleft _100">
                    <div class="text14px medium purple tag" :class="{ 'show': product.top_produit }">
                        Top produits
                    </div>
                </div>
                <div class="productimagecontainer"
                    :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                </div>
                <div class="w_container vertical alignleft _100 gap4px">
                    <div class="text16px medium">
                        {{ product.name }}
                    </div>
                    <div class="text16px nowrap">
                        {{ product.proprietes }}
                    </div>
                </div>
                <div class="w_container vertical alignleft _100 gap4px">
                    <div class="text14px grey400">
                        à partir de
                    </div>
                    <div class="text16px nowrap">
                        <span class="text-span-2">{{ product.prix_location }} €</span>
                        <span v-if="product.categorie !== 'licences' || product.type_licence === 'Mensuel'">
                            /mois
                        </span>
                        <span v-else>
                            /an
                        </span>
                    </div>
                </div>
                </Link>

                <BesoinReference v-if="data.contact" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.tag.show {
    opacity: 1;
}

.tag {
    opacity: 0;
}
</style>
