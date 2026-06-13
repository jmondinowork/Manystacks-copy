<script setup>
import CatalogueLayout from '@/Layouts/CatalogueLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';

import { computed, onMounted, ref, watch } from 'vue';
import { usePage, Head, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex';

const { props } = usePage();
const store = useStore();
const isLoading = ref(false);

const totalLocationMonth = computed(() => {
    const filteredProducts = props.panier.panier_products.filter(item => {
        if (item.product.categorie === 'licences') {
            return item.product.type_licence === 'Mensuel';
        } else {
            return item.type_contrat === 'location';
        }
    });

    const sum = filteredProducts.reduce((acc, item) => acc + (item.product.prix_location * item.quantity), 0);
    return sum.toFixed(2);
});
const totalLocationYear = computed(() => {
    const filteredProducts = props.panier.panier_products.filter(item => {
        if (item.product.categorie === 'licences') {
            return item.product.type_licence === 'Annuel';
        }
    });

    const sum = filteredProducts.reduce((acc, item) => acc + (item.product.prix_location * item.quantity), 0);
    return sum.toFixed(2);
});
const totalAchat = computed(() => {
    const filteredProducts = props.panier.panier_products.filter(item => item.type_contrat == 'achat');
    const sum = filteredProducts.reduce((acc, item) => acc + item.product.prix_achat * item.quantity, 0);
    return sum.toFixed(2);
});

const panierLength = computed(() => {
    const length = props.panier.panier_products.reduce((total, product) => total + product.quantity, 0);
    return length > 1 ? `${length} articles` : `${length} article`;
});

async function incrementQuantity(item) {
    try {
        item.quantity++;

        await axios.post('/api/updateQuantity', { panierId: item.panier_id, productId: item.product_id, quantity: item.quantity });
        store.dispatch('updatePanierLength', parseInt(store.state.panierLength) + 1);
    } catch (error) {
        console.error('Erreur lors de la mise à jour de la quantité', error);
    }
}

async function decrementQuantity(item) {
    if (item.quantity > 1) {
        try {
            item.quantity--;

            await axios.post('/api/updateQuantity', { panierId: item.panier_id, productId: item.product_id, quantity: item.quantity });
            store.dispatch('updatePanierLength', parseInt(store.state.panierLength) - 1);
        } catch (error) {
            console.error('Erreur lors de la diminution de la quantité', error);
        }
    }
    else removeItem(item);
}

async function removeItem(item) {
    try {
        await axios.post('/api/removeItem', { productId: item.product_id, panierId: item.panier_id });

        // UNE ERREUR SE PRODUIT ICI
        props.panier.panier_products = props.panier.panier_products.filter(product => product.id !== item.id);
        store.dispatch('updatePanierLength', parseInt(store.state.panierLength) - 1);
        store.dispatch('updateFilteredProductCount', { count: parseInt(store.state.panierLength) - 1, from: "props" });
    } catch (error) {
        console.error("Erreur lors de la suppression de l'article", error);
    }
}

async function validateInput(event, item) {
    let value = event.target.value;

    if (!value || isNaN(value) || parseInt(value) < 1) {
        value = 1
        event.target.value = 1;
    }

    item.quantity = value;
    await axios.post('/api/updateQuantity', { panierId: item.id, productId: item.product_id, quantity: value });
    store.dispatch('updatePanierLength', props.panier.panier_products.reduce((total, item) => total + parseInt(item.quantity, 10), 0));
}
const getImageSrc = (quantity) => {
    return quantity == 1 || !quantity ?
        "/images/trash-icon.svg" :
        "/images/minus-circle.svg";
}

const addToPanier = async (product) => {
    try {
        isLoading.value = true;

        const response = await axios.post('/api/addToPanier', {
            product_id: product.id,
            quantity: "1",
            type_contrat: 'location'
        });
        props.panier = response.data;

        store.dispatch('updatePanierLength', parseInt(store.state.panierLength) + 1);
        store.dispatch('updateFilteredProductCount', { count: props.panier.panier_products.length, from: "props" });
        store.dispatch('updateAnnounce', "Equipement ajouté au panier avec succès");

    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Erreur lors de l'ajout au panier");
    } finally {
        isLoading.value = false;
    }
};

const sendPanier = () => {
    try {
        axios.post('/api/sendPanier');

        props.panier = null;
        store.dispatch('updatePanierLength', 0);
        store.dispatch('updateFilteredProductCount', { count: 0, from: "props" });
        store.dispatch('updateAnnounce', "Demande envoyée avec succès");

    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Erreur lors de l'envoi de la demande");
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    if (props.panier)
        store.dispatch('updateFilteredProductCount', { count: props.panier.panier_products.length, from: 'props' });

    if (props.flash && props.flash.error) {
        store.dispatch('updateErrorAnnounce', props.flash.error);
    }
})
const calculateWidth = (val) => `${val.toString().length}ch`;
</script>

<template>

    <Head>
        <title>Panier</title>
        <meta name="description" content="Bienvenue dans votre panier">
    </Head>

    <CatalogueLayout>
        <div v-if="props.panier" class="basketpagecontainer">
            <!-- Produits -->
            <div class="componentcontainer height100">
                <div class="w_container vertical gap12px">
                    <div class="text20px unbounded">
                        Votre panier
                    </div>
                    <div v-for="(item, index) in props.panier.panier_products" :key="index"
                        class="w_container vertical gap12px">
                        <div class="w_container vertical gap16px white round padding12px">
                            <div class="w_container aligncenter gap16px">
                                <div class="w_container _80x80 grey">
                                    <div class="productimagecontainer"
                                        :style="{ 'background-image': 'url(' + item.product.image_principale + ')' }">
                                    </div>
                                </div>
                                <div class="w_container vertical gap2px">
                                    <div class="text16px medium">
                                        {{ item.product.name }}
                                    </div>
                                    <div class="text14px">
                                        {{ item.product.proprietes }}
                                    </div>
                                </div>
                                <div class="text14px gray w-40 flex justify-end mb-16"
                                    v-if="item.type_contrat == 'location'">En location</div>
                                <div class="text14px gray w-40 flex justify-end mb-16" v-else>En achat</div>
                            </div>
                            <div class="separatorhorizontal"></div>
                            <div class="w_container aligncenter justifyspacebetween">
                                <div class="button_ajouter-licences">
                                    <img @click="decrementQuantity(item)" :src="getImageSrc(item.quantity)"
                                        loading="lazy" alt="" class="image20x20px cursor-pointer">
                                    <input type="number" min="1" v-model="item.quantity"
                                        @input="validateInput($event, item)" class="text16px h-5 p-0"
                                        :style="{ width: calculateWidth(item.quantity) }" />
                                    <img @click="incrementQuantity(item)"
                                        src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f176f982e38482d9311975_plus-circle.png"
                                        loading="lazy" alt="" class="image20x20px cursor-pointer">
                                </div>
                                <template v-if="item.product.categorie === 'licences'">
                                    <div class="text20px unbounded" v-if="item.product.type_licence == 'Mensuel'">
                                        {{ item.product.prix_location }} € <span class="text14px unbounded">/mois</span>
                                    </div>
                                    <div class="text20px unbounded" v-else>
                                        {{ item.product.prix_location }} € <span class="text14px unbounded">/an</span>
                                    </div>
                                </template>

                                <template v-else>
                                    <div class="text20px unbounded" v-if="item.type_contrat == 'location'">
                                        {{ item.product.prix_location }} € <span class="text14px unbounded">/mois</span>
                                    </div>
                                    <div class="text20px unbounded" v-else>
                                        {{ item.product.prix_achat }} € <span class="text14px unbounded">/unité</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w_container vertical">
                <!-- Prix -->
                <div class="componentcontainer">
                    <div class="w_container vertical gap20px">
                        <div class="w_container vertical gap12px">
                            <div class="w_container alignend gap4px" v-if="totalLocationMonth > 0">
                                <div class="text24px unbounded medium">
                                    {{ totalLocationMonth }} €
                                </div>
                                <div class="text14px unbounded">
                                    /mois
                                </div>
                            </div>
                            <div class="w_container alignend gap4px" v-if="totalLocationYear > 0">
                                <div class="text24px unbounded medium">
                                    {{ totalLocationYear }} €
                                </div>
                                <div class="text14px unbounded">
                                    /an
                                </div>
                            </div>
                            <div class="w_container alignend gap4px" v-if="totalAchat > 0">
                                <div class="text24px unbounded medium">
                                    {{ totalAchat }} €
                                </div>
                                <div class="text14px unbounded">
                                    En achat
                                </div>
                            </div>
                            <div class="text14px">
                                Sous-total ({{ panierLength }})
                            </div>
                        </div>
                        <Link v-if="props.userAuth.role != 'collaborateur'" :href="route('entreprise')"
                            class="bigbutton purple">
                        <div class="text14px white">
                            Passer commande
                        </div>
                        </Link>
                        <div v-else @click="sendPanier" class="bigbutton purple">
                            <div class="text14px white">
                                Envoyer la demande
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="componentcontainer height100">
                    <div class="w_container vertical gap24px">
                        <div class="text20px unbounded">
                            Vous pourriez aussi aimer
                        </div>
                        <div class="w_container vertical gap12px">
                            <div v-for="product in props.recommendation" :key="product.id"
                                class="w_container gap16px padding8px white _100">
                                <div class="w_container _80x80 grey">
                                    <div class="productimagecontainer"
                                        :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                    </div>
                                </div>
                                <div class="w_container vertical gap2px overflowhidden alignstart">
                                    <div class="text14px medium">
                                        {{ product.name }}
                                    </div>
                                    <div class="text14px nowrap">
                                        {{ product.proprietes }}
                                    </div>
                                    <div @click="addToPanier(product)" class="smallpurplebutton cursor-pointer">
                                        <div class="frame-137">
                                            <div class="text-14">
                                                Ajouter au panier
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'panier'"></EmptyPage>
    </CatalogueLayout>
</template>

<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>
