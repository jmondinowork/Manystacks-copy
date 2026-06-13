<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import CreateStack from '@/Components/CreateStack.vue';
import EmptyPage from '@/Components/EmptyPage.vue';

import { usePage, Head, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { computed, ref, onMounted } from 'vue';

const store = useStore();
const props = usePage().props;

const stacksToShow = computed(() => store.state.searchResultsStacks || props.mes_stacks);

const calculateTotalPrice = (stack) => stack.products.reduce((total, product) => total + parseFloat(product.prix_location), 0).toFixed(2)

const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => showCreate.value = true;

onMounted(() => {
    store.dispatch('updateFilteredProductCount', { count: props.mes_stacks.length, from: "props" });
})

const stackProductImage = (products) => {
    let result = products.slice(0, 4);
    while (result.length < 4) {
        result.push(null);
    }
    return result;
}
</script>


<template>
    <Head>
        <title>Stacks</title>
        <meta name="description" content="Retrouvez ici toutes vos stacks">
    </Head>

    <CatalogueLayout>
        <div v-if="props.mes_stacks.length" class="componentcontainer">
            <div class="w_container vertical alignleft _100 gap24px">
                <div class="w_container vertical gap24px overflowhidden">
                    <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                        <div class="text20px unbounded">
                            Votre collection de stacks
                        </div>
                    </div>
                </div>

                <div class="stacksgrid">
                    <Link :href="`/mes-stacks/${stack.slug}`" class="stackunitcontainer cursor-pointer"
                        v-for="stack in stacksToShow" :key="stack.id">
                    <div class="w_container vertical gap24px">
                        <div class="w_container gap12px">
                            <div class="stackimagegrid">
                                <div v-for="product in stackProductImage(stack.products)" :key="product"
                                    class="stackimage">
                                    <div class="productimagecontainer absolute"
                                        :style="{ 'background-image': product ? 'url(' + product.image_principale + ')' : 'none' }">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stacktitlecontainer" :style="{ 'border-color': stack.color }">
                            <div class="w_container vertical gap16px">
                                <div class="w_container gap16px">
                                    <div class="w_container vertical gap4px">
                                        <div class="text20px unbounded">{{ stack.stack_name }}</div>
                                    </div>
                                </div>
                                <div class="w_container aligncenter gap8px"><img
                                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2f331dca694dc88c681f_Vectors-Wrapper.svg"
                                        loading="lazy" width="20" height="20" alt="" class="image20x20px">
                                    <div class="text14px">{{ stack.products.length }} appareils</div>
                                </div>
                                <div class="w_container alignend gap4px">
                                    <div class="text16px unbounded">
                                        <strong>{{ calculateTotalPrice(stack) }} €</strong>
                                    </div>
                                    <div class="text14px unbounded">
                                        /mois
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </Link>

                    <div class="stackunitcontainer cursor-pointer" @click="openCreate" style="min-height: 400px;">
                        <div class="w_container vertical aligncenter">
                            <img class="image24x24px" loading="lazy" width="24" height="24"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65719b02675146e2a3e327e4_Vectors-Wrapper.svg" />
                            <div class="text20px unbounded">
                                Créer une stack
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'mes-stacks'"></EmptyPage>

        <CreateStack :show="showCreate" @closeCreate="closeCreate"></CreateStack>
    </CatalogueLayout>
</template>

<style scoped>
.productimagesideselector {
    padding: 0;
}

.stacktitlecontainer {
    border-right: 4px solid;
}

.productimagesideselector {
    opacity: 1;
}

.productimagesideselector:hover {
    border: none;
}
</style>
