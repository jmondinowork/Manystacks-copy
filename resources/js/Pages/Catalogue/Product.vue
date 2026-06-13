<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import SmallProductsPreview from '@/Components/SmallProductsPreview.vue';
import AddToStack from '@/Components/AddToStack.vue';
import AvantagesLocation from '@/Components/AvantagesLocation.vue';
import AvantagesAchat from '@/Components/AvantagesAchat.vue';
import AppsInclusesLicence from '@/Components/AppsInclusesLicence.vue';
import { caracteristiquesTechniquesProduct } from '@/config.js'
import { mainCaracteristiquesTechniquesProduct } from '@/config.js'

import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';
import { Head, usePage } from '@inertiajs/vue3';


const store = useStore();
const props = usePage().props;

const isLoading = ref(false);

const addToPanier = async () => {
    try {
        const response = await axios.post('/api/addToPanier', {
            product_id: currentProduct.value.id,
            quantity: selectedQuantity.value,
            type_contrat: typeContrat.value
        });
        store.dispatch('updatePanierLength', response.data.panier_products.reduce((total, item) => total + parseInt(item.quantity, 10), 0));
        if (currentProduct.value.categorie === 'licences')
            store.dispatch('updateAnnounce', "Licence ajoutée au panier avec succès");
        else
            store.dispatch('updateAnnounce', "Equipement ajouté au panier avec succès");

    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Erreur lors de l'ajout au panier");
    } finally {
        isLoading.value = false;
    }
};

const index = ref(props.products.findIndex(product => product.id === props.current_product.id));

const currentProduct = computed(() => props.products[index.value]);
const selectedImage = ref(currentProduct.value.images[0]);
const selectedQuantity = ref(1);
const price_location = computed(() => { return (currentProduct.value.prix_location * selectedQuantity.value).toFixed(2); });
const price_achat = computed(() => { return (currentProduct.value.prix_achat * selectedQuantity.value).toFixed(2); });
const commonFeatures = ref({});
const uniqueFeatures = ref({});

const selectQuantity = (event) => {
    selectedQuantity.value = event.target.value;
};
const selectImage = (image) => {
    selectedImage.value = image;
};
const productFeatures = mainCaracteristiquesTechniquesProduct[currentProduct.value.sous_categorie];
const productColors = computed(() => {
    const colors = props.products
        .filter(product => product.couleur !== null)
        .map(product => product.couleur);

    if (colors.length === 0) return null;

    return [...new Set(colors)];
});
const filteredFeatures = computed(() => { return productFeatures.filter(feature => currentProduct.value[feature.property]); })
const calculateFeatures = () => {
    const common = {};
    const unique = {};

    productFeatures.forEach(({ property }) => {
        const values = props.products
            .filter(product => product.couleur === currentProduct.value.couleur)
            .map(product => product[property]);
        const allValues = new Set(values);

        if (allValues.size === 1) {
            common[property] = values[0];
        } else {
            unique[property] = Array.from(allValues);
        }
    });

    commonFeatures.value = common;
    uniqueFeatures.value = unique;
};
calculateFeatures();

const updateCurrentProduct = (property, value) => {
    if (property === 'couleur') {
        var matchingIndex = props.products.findIndex(product => product[property] == value);
    } else {
        var matchingIndex = props.products.findIndex(product => product[property] == value && product.couleur == currentProduct.value.couleur);
    }
    if (matchingIndex !== -1) {
        index.value = matchingIndex;
    } else {
        index.value = 0;
    }

    calculateFeatures();
};

watch(index, () => {
    selectedImage.value = currentProduct.value.images[0];
});

const screenSize = ref(window.innerWidth);
const resizeScreen = () => screenSize.value = window.innerWidth;
onMounted(() => {
    window.addEventListener('resize', resizeScreen)
    store.dispatch('updateFilteredProductCount', { count: 1, from: "props" })
});
onUnmounted(() => window.removeEventListener('resize', resizeScreen));

const showAdd = ref(false);
const closeAdd = () => showAdd.value = false;
const openAdd = () => showAdd.value = true;

const formattedText = (property) => {
    if (!currentProduct.value[property]) return '';
    return currentProduct.value[property].replace(/\n/g, '<br>');
}

const typeContrat = ref('location');
</script>

<template>

    <Head>
        <title>{{ currentProduct.name }}</title>
        <meta name="description" :content="`Retrouvez pleins de produits similaire à ${currentProduct.name}`">
    </Head>

    <CatalogueLayout>
        <div class="productpagegrid">
            <div class="w_container vertical gap8px">
                <!-- Fiche technique -->
                <div class="componentcontainer">
                    <div class="w_container horizontalthenvertical _100">
                        <div class="w_container verticalthenhorizontal gap12px justifycenter">
                            <div v-for="image in currentProduct.images" :key="image.id" class="productimagesideselector"
                                :class="{ 'selected': image.id === selectedImage.id }" @click="selectImage(image)">
                                <img class="image100" loading="lazy" :src="image.image_url" />
                            </div>
                        </div>
                        <div class="w_container aligncenter justifycenter _100">
                            <div class="productimagecontainer"
                                :style="{ 'backgroundImage': 'url(' + selectedImage.image_url + ')' }">
                            </div>
                        </div>
                        <div class="w_container vertical gap24px _100 white">
                            <div class="text24px unbounded medium">
                                {{ currentProduct.name }}
                            </div>
                            <div class="w_container vertical gap8px">
                                <div v-for="feature in filteredFeatures" :key="feature.property"
                                    class="w_container aligncenter">
                                    <div class="text14px grey400 _100">{{ feature.title }}</div>
                                    <div v-if="feature.property in commonFeatures"
                                        class="w_container justifyspacebetween _100 aligncenter padding12px">
                                        <div class="text14px medium nowrap" v-html="formattedText(feature.property)">
                                        </div>
                                    </div>
                                    <select v-else-if="feature.property in uniqueFeatures"
                                        class="text14px medium nowrap tech w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey"
                                        @change="updateCurrentProduct(feature.property, $event.target.value)">
                                        <option v-for="value in uniqueFeatures[feature.property]" :key="value"
                                            :value="value" :selected="value == currentProduct[feature.property]">
                                            {{ value }}
                                        </option>
                                    </select>
                                </div>
                                <div v-if="productColors" class="w_container aligncenter">
                                    <div class="text14px grey400 _100">Couleur</div>
                                    <select
                                        class="text14px medium nowrap tech w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey"
                                        @change="updateCurrentProduct('couleur', $event.target.value)">
                                        <option v-for="value in productColors" :key="value" :value="value"
                                            :selected="value == currentProduct.couleur">
                                            {{ value }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="componentcontainer h-full">
                    <div class="w_container vertical gap8px">
                        <div class="text14px semibold">
                            Description
                        </div>
                        <div class="text14px" v-html="formattedText('description')">
                        </div>
                    </div>
                </div>
            </div>


            <div class="w_container vertical gap8px">
                <!-- Prix -->
                <div class="componentcontainer directionvertical gap16px">
                    <div class="w_container vertical">
                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical"
                            v-if="currentProduct.categorie !== 'licences'">
                            <div class="toggle" :class="{ 'selected': typeContrat == 'location' }"
                                @click="typeContrat = 'location'">
                                <div class="text14px">En location</div>
                            </div>
                            <div class="toggle" :class="{ 'selected': typeContrat == 'achat' }"
                                @click="typeContrat = 'achat'">
                                <div class="text14px">En achat</div>
                            </div>
                        </div>
                    </div>
                    <div class="w_container aligncenter justifyspacebetween">
                        <div class="w_container alignend gap4px" v-if="typeContrat == 'location'">
                            <div class="text24px unbounded medium">
                                {{ price_location }}€
                            </div>
                            <template v-if="currentProduct.categorie === 'licences'">
                                <div class="text14px unbounded" v-if="currentProduct.type_licence === 'Mensuel'">
                                    /mois
                                </div>
                                <div class="text14px unbounded" v-else>
                                    /an
                                </div>
                            </template>

                            <div class="text14px unbounded" v-else>
                                /mois
                            </div>
                        </div>
                        <div class="w_container alignend gap4px" v-else>
                            <div class="text24px unbounded medium">
                                {{ price_achat }}€
                            </div>
                            <div class="text14px unbounded">
                                /unité
                            </div>
                        </div>
                    </div>

                    <div v-if="currentProduct.delais_livraison" class="w_container alignend gap4px">
                        <div class="text14px">
                            Livré entre
                        </div>
                        <div class="text14px semibold">
                            {{ currentProduct.delais_livraison }} jours
                        </div>
                    </div>

                    <div class="w_container aligncenter gap8px">
                        <div class="text14px">
                            Quantité :
                        </div>
                        <select class="inputquantity text14px" @change="selectQuantity">
                            <option v-for="number in 100" :key="number" :value="number">{{ number }}</option>
                        </select>
                    </div>

                    <div class="w_container vertical gap8px">
                        <div v-if="currentProduct.fournisseur === 'microsoft' && !props.auth.user.entreprise.licence_microsoft"
                            class="text14px">
                            Pour commander des licences {{ currentProduct.fournisseur }}, veuillez nous contacter.
                        </div>
                        <div v-else class="bigbutton purple" @click="addToPanier">
                            <div class="text14px white">
                                Ajouter au panier
                            </div>
                        </div>
                        <div class="bigbutton" @click="openAdd">
                            <div class="text14px purple">
                                Ajouter à une stack
                            </div>
                        </div>
                    </div>
                </div>

                <SmallProductsPreview v-if="screenSize < 991" :title="'Ça pourrait bien vous intéresser'"
                    :filtered-products="props.otherProducts">
                </SmallProductsPreview>

                <!-- CO2 -->
                <div v-if="currentProduct.co2" class="componentcontainer directionvertical gap12px">
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
                            C’est <span class="text-span-3">la réduction de l’empreinte carbone</span> en passant
                            par Manystacks
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
                <template v-if="currentProduct.categorie === 'licences'">
                    <AppsInclusesLicence :currentProduct="currentProduct" />
                </template>
                <template v-else>
                    <AvantagesLocation v-if="typeContrat == 'location'" />
                    <AvantagesAchat v-else />
                </template>
            </div>
        </div>

        <div class="componentcontainer" v-if="currentProduct.categorie !== 'licences'">
            <div class="w_container vertical gap16px">
                <div class="text14px semibold">
                    Caractéristiques techniques
                </div>
                <div class="grid gap-x-20 gap-y-2 grid-cols-[minmax(0,max-content)_1fr] caracteristics">
                    <template v-for="carac in caracteristiquesTechniquesProduct[currentProduct.sous_categorie]"
                        :key="carac">
                        <template v-if="currentProduct[carac.property]">
                            <div class="text14px grey400">{{ carac.title }}</div>
                            <div class="text14px" v-html="formattedText(carac.property)"></div>
                            <div class="separatorhorizontal col-span-2"></div>
                        </template>
                    </template>
                </div>

            </div>
        </div>

        <SmallProductsPreview v-if="screenSize > 991" :title="'Ça pourrait bien vous intéresser'"
            :filtered-products="props.otherProducts">
        </SmallProductsPreview>

        <AddToStack :currentProduct="currentProduct" :show="showAdd" @closeAdd="closeAdd">
        </AddToStack>
    </CatalogueLayout>
</template>

<style scoped>
.productpagegrid {
    grid-row-gap: 0;
}

.caracteristics .separatorhorizontal:last-child {
    display: none;
}

.tech {
    cursor: pointer;
}

.inputquantity {
    padding: 4px 12px;
}

.addcatalogue:hover+.popuphover.catalogue {
    display: block;
}

.select-item:hover {
    background-color: #F7F8F9;
}

.select-item {
    padding: 12px;
}

.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}

select.tech {
    border: none;
    width: 100%;
    background-color: transparent;
    text-align: left;
}

select {
    padding: 0;
}

.option-flag {
    background-repeat: no-repeat;
    background-position: left center;
    padding-left: 28px;
    background-size: 20px 14px;
}

.fr-flag {
    background-image: url('/images/fr-flags.png');
}

.en-flag {
    background-image: url('/images/en-flags.png');
}
</style>
