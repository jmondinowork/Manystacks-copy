<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import CreateStack from '@/Components/CreateStack.vue';
import AddToStack from '@/Components/AddToStack.vue';
import AvantagesLocation from '@/Components/AvantagesLocation.vue';
import AvantagesAchat from '@/Components/AvantagesAchat.vue';
import AppsInclusesLicence from '@/Components/AppsInclusesLicence.vue';
import OptionsButton from '@/Components/OptionsButton.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import { mainCaracteristiquesTechniquesProduct } from '@/config.js';

import { usePage, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';

const { props } = usePage();
const store = useStore();
const currentStack = ref(props.stack);
const calculateTotalPrice = computed(() =>
    currentStack.value.products.reduce((total, product) => total + parseFloat(product.prix_location), 0).toFixed(2)
);

const currentProduct = ref(currentStack.value.products[0]);
const productFeatures = ref([]);
const selectproduct = (product) => {
    currentProduct.value = product;
    selectedImage.value = currentProduct.value ? currentProduct.value.images[0] : null;
    productFeatures.value = currentProduct.value ? mainCaracteristiquesTechniquesProduct[currentProduct.value.sous_categorie] : [];
}
const selectedImage = ref(currentProduct.value ? currentProduct.value.images[0] : null);
const selectImage = (image) => selectedImage.value = image;

const filteredFeatures = computed(() => {
    if (!currentProduct.value) return [];
    return productFeatures.value.filter(feature => currentProduct.value[feature.property])
});

const isLoading = ref(false);
const addToPanier = async () => {
    try {
        isLoading.value = true;

        const response = await axios.post('/api/addToPanier', {
            product_id: currentProduct.value.id,
            quantity: 1,
        });

        store.dispatch('updatePanierLength', response.data.panier_products.reduce((total, item) => total + parseInt(item.quantity, 10), 0));
        store.dispatch('updateAnnounce', 'Equipement ajouté au panier avec succès');
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Erreur lors de l'ajout au panier");
    } finally {
        isLoading.value = false;
    }
};
const addStackToPanier = async () => {
    try {
        isLoading.value = true;

        const response = await axios.post('/api/addStackToPanier', {
            stack_id: currentStack.value.id
        });

        store.dispatch('updatePanierLength', response.data.panier_products.reduce((total, item) => total + parseInt(item.quantity, 10), 0));
        store.dispatch('updateAnnounce', 'Stack ajoutée au panier avec succès');
    }
    catch (error) {
        store.dispatch('updateErrorAnnounce', "Erreur lors de l'ajout au panier de la stack");
    } finally {
        isLoading.value = false;
    }
}
const removeProductFromStack = async (product_id) => {
    try {
        const response = await axios.post('/api/removeProductFromStack', {
            stack_id: currentStack.value.id,
            product_id: product_id
        });

        currentStack.value = response.data;

        store.dispatch('updateFilteredProductCount', { count: currentStack.value.products.length, from: "props" });
        store.dispatch('updateAnnounce', 'Equipement retiré de la stack avec succès');

        if (currentProduct.value.id == product_id)
            selectproduct(currentStack.value.products[0])
    }
    catch (error) {
        store.dispatch('updateErrorAnnounce', 'Erreur lors de la suppression de l\'équipement de la stack');
    }
}

const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => {
    if (!currentStack.value.public)
        showCreate.value = true
};

const showAdd = ref(false);
const closeAdd = () => showAdd.value = false;
const openAdd = () => showAdd.value = true;

onMounted(() => {
    selectproduct(currentProduct.value);
    store.dispatch('updateFilteredProductCount', { count: currentStack.value.products.length, from: "props" });
})

const formattedText = (property) => {
    if (!currentProduct.value[property]) return '';
    return currentProduct.value[property].replace(/\n/g, '<br>');
}
</script>

<template>
    <CatalogueLayout>
        <div class="stackpagecontainer">
            <!-- Left side -->
            <div class="w_container vertical">
                <div class="componentcontainer">
                    <div class="w_container nogap colorside w-full" :style="{ 'border-color': currentStack.color }">
                        <div class="w_container vertical nogap">
                            <div class="text16px medium">Stack</div>
                            <div class="text20px unbounded">{{ currentStack.stack_name }}</div>
                        </div>
                        <OptionsButton v-if="!currentStack.public" @click="openCreate" class="mr-2"></OptionsButton>
                    </div>
                </div>

                <div v-if="currentProduct" class="bigbutton purple" @click="addStackToPanier">
                    <div class="text14px white">Ajouter toute la stack au panier</div>
                </div>
                <div class="componentcontainer height100">
                    <div class="w_container vertical">
                        <div class="w_container vertical gap4px">
                            <div class="w_container alignend gap4px">
                                <div class="text20px unbounded">{{ calculateTotalPrice }} €</div>
                                <div class="text14px unbounded">/mois</div>
                            </div>
                            <div class="text14px">{{ currentStack.products.length }} équipements</div>
                        </div>
                        <div class="separatorhorizontal"></div>

                        <div class="w_container vertical">
                            <div class="w_container gap8px flex-col">
                                <div v-for="product in currentStack.products" :key="product.id"
                                    class="w_container gap8px stackproduct">
                                    <div @click="selectproduct(product)"
                                        class="w_container gap16px padding8px white hover cursor-pointer w-full"
                                        :class="{ 'selected': product.id == currentProduct.id }">
                                        <div class="w_container _80x80 grey">
                                            <div class="productimagecontainer"
                                                :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                            </div>
                                        </div>
                                        <div class="w_container vertical gap2px overflowhidden">
                                            <div class="text14px medium">{{ product.name }}</div>
                                            <div class="text14px nowrap w-2">{{ product.proprietes }}</div>
                                        </div>
                                    </div>
                                    <div v-if="!currentStack.public" class="deletestack cursor-pointer"
                                        @click="removeProductFromStack(product.id)">
                                        <img class="image20x20px" loading="lazy" width="28.28564453125"
                                            height="28.28564453125"
                                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65718b9aea22de73aa4e8f78_Vectors-Wrapper.svg" />
                                    </div>
                                </div>
                                <EmptyPage v-if="!currentStack.public && !currentStack.products.length"
                                    :section="'stack'"></EmptyPage>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- main side -->
            <div class="w_container vertical">
                <div class="componentcontainer min-h-full">
                    <div class="w_container vertical _100">
                        <div class="w_container gap12px justifycenter">
                            <template v-if="currentProduct">
                                <div class="productimagesideselector" v-for="image in currentProduct.images"
                                    :key="image.id" :class="{ 'selected': image.id === selectedImage.id }"
                                    @click="selectImage(image)">
                                    <img class="image100" loading="lazy" width="44" height="44"
                                        :src="image.image_url" />
                                </div>
                            </template>
                            <template v-else>
                                <div class="productimagesideselector opacity-100" v-for="n in 5" :key="n"
                                    style="background-color: #fff; width: 60px; height: 60px;">
                                </div>
                            </template>
                        </div>
                        <div class="w_container aligncenter justifycenter _100" style="width: 500px;margin: 0 auto">
                            <div v-if="selectedImage" class="productimagecontainer"
                                :style="{ 'backgroundImage': 'url(' + selectedImage.image_url + ')' }"></div>
                            <div v-else class="productimagecontainer bg-white mt-8 mb-8"
                                :style="{ 'backgroundImage': 'url()' }"></div>
                        </div>
                        <div class="w_container vertical gap24px _100 white min-h-48">
                            <div class="w_container vertical">
                                <div class="w_container aligncenter verticalonphone stack"
                                    v-for="feature in filteredFeatures" :key="feature.property">
                                    <div class="text14px grey400 _100">{{ feature.title }}</div>
                                    <div class="w_container justify-start _100 height40px aligncenter p-2">
                                        <div class="text14px medium nowrap" v-html="formattedText(feature.property)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- right side  -->
            <div class="w_container vertical gap8px">
                <!-- Prix -->
                <div class="componentcontainer directionvertical gap16px">
                    <div class="w_container aligncenter justifyspacebetween">
                        <div class="w_container alignend gap4px">
                            <div class="text24px unbounded medium">
                                {{ currentProduct ? currentProduct.prix_location : 0 }}€
                            </div>
                            <div class="text14px unbounded">
                                /mois
                            </div>
                        </div>
                    </div>

                    <div v-if="currentProduct && currentProduct.delais_livraison" class="w_container alignend gap4px">
                        <div class="text14px">
                            Livré entre
                        </div>
                        <div class="text14px semibold">
                            {{ currentProduct.delais_livraison }} jours
                        </div>
                    </div>

                    <!-- <div class="w_container aligncenter gap8px">
                        <div class="text14px">
                            Quantité :
                        </div>
                        <select class="inputquantity text14px" @change="selectQuantity">
                            <option v-for="number in 100" :key="number" :value="number">{{ number }}</option>
                        </select>
                    </div> -->

                    <div v-if="currentProduct" class="w_container vertical gap8px">
                        <div class="bigbutton purple" @click="addToPanier">
                            <div class="text14px white">
                                Ajouter cet équipement au panier
                            </div>
                        </div>
                        <div v-if="currentStack.public" class="bigbutton" @click="openAdd">
                            <div class="text14px purple">
                                Ajouter à une stack
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CO2 -->
                <div v-if="currentProduct && currentProduct.co2" class="componentcontainer directionvertical gap12px">
                    <div class="w_container">
                        <div class="w_container alignend gap4px co2">
                            <div class="text16px unbounded medium green">
                                - 180Kg
                            </div>
                            <div class="text14px unbounded green">
                                CO²e
                            </div>
                        </div>
                    </div>

                    <div class="w_container vertical gap8px">
                        <div class="text14px">
                            C’est <span class="text-span-3">la réduction de l’empreinte carbone</span> en passant par
                            Manystacks
                        </div>
                        <!-- <div class="w_container aligncenter gap4px clickable">
                            <div class="text14px medium purple">
                                Comprendre ce chiffre
                            </div>
                            <img class="image16x16px" loading="lazy" width="16" height="16"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg" />
                        </div> -->
                    </div>
                </div>

                <!-- Avantages -->
                <template v-if="currentProduct && currentProduct.categorie === 'licences'">
                    <AppsInclusesLicence :currentProduct="currentProduct" />
                </template>
                <template v-else>
                    <AvantagesLocation />
                    <AvantagesAchat/>
                </template>
            </div>
        </div>

        <CreateStack :title="'Modifiez votre stack'" :show="showCreate" @closeCreate="closeCreate" :action="'Modifier'"
            :stack_name="currentStack.stack_name" :stack_color="currentStack.color" :stack_id="currentStack.id">
        </CreateStack>

        <AddToStack v-if="currentStack.public" :currentProduct="currentProduct" :show="showAdd" @closeAdd="closeAdd">
        </AddToStack>
    </CatalogueLayout>
</template>

<style scoped>
.deletestack {
    display: none;
}

.stackproduct:hover .deletestack {
    display: flex;
}

.deletestack {
    height: 96px;
}

.colorside {
    border-right: 4px solid var(--orange);
}
</style>
