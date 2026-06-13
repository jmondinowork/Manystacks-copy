<script setup>
import PasserCommandeLayout from '@/Layouts/PasserCommandeLayout.vue';
import AttributeLivraisonCommande from '@/Components/AttributeLivraisonCommande.vue';
import RecapitulatifCommandes from '@/Components/RecapitulatifCommandes.vue';
import { userInitials } from '@/functions';

import { usePage, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const { props } = usePage();

const multiplePanier = computed(() => {
    const panierEtendu = [];
    props.panier.panier_products.forEach(item => {
        for (let i = 0; i < item.quantity; i++) {
            const itemCopie = { ...item, quantity: 1 };

            panierEtendu.push(itemCopie);
        }
    });
    return panierEtendu;
})


const currentUser = ref(null);
const currentAddress = ref(null);
const currentIndex = ref(null);
const showAttribute = ref(false);

const closeAttribute = () => showAttribute.value = false;
const openAttribute = (user, address, index = null) => {
    showAttribute.value = true;
    currentAddress.value = address;
    currentUser.value = user;
    currentIndex.value = index;
}

const onAttributeSelected = (newUser, newAddress) => {
    if (adresseType.value === 'single') {
        if (newUser) {
            form.single.user.id = newUser.id;
            form.single.user.name = newUser.name;
            form.single.user.profile_img = newUser.profile_img;
        }
        if (newAddress) {
            form.single.address.id = newAddress.id;
            form.single.address.titre = newAddress.titre;
        }
    } else if (adresseType.value === 'multiple') {
        if (newUser) {
            form.multiple[currentIndex.value].user.id = newUser.id;
            form.multiple[currentIndex.value].user.name = newUser.name;
            form.multiple[currentIndex.value].user.profile_img = newUser.profile_img;
        }
        if (newAddress) {
            form.multiple[currentIndex.value].address.id = newAddress.id;
            form.multiple[currentIndex.value].address.titre = newAddress.titre;
        }
    }
}

const adresseType = ref('single');
const form = useForm({
    single: {
        user: {
            name: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').name,
            profile_img: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').profile_img,
            id: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').id
        },
        address: {
            titre: props.adresses.find(address => address.default === 1).titre,
            id: props.adresses.find(address => address.default === 1).id
        },
        products: multiplePanier.value.map(item => ({
            id: item.product.id,
            quantity: item.quantity,
            type_contrat: item.type_contrat
        }))
    },
    multiple: multiplePanier.value.map(item => ({
        id: item.product.id,
        quantity: item.quantity,
        type_contrat: item.type_contrat,
        address: {
            titre: props.adresses.find(address => address.default === 1).titre,
            id: props.adresses.find(address => address.default === 1).id
        },
        user: {
            name: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').name,
            profile_img: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').profile_img,
            id: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').id
        },
    })),
    adresseType: adresseType.value
});

watch(adresseType, (newType) => form.adresseType = newType);

const submit = () => {
    form.processing = true;
    if (adresseType.value == 'single' && form.single.address.id !== 0 ||
        (adresseType.value == 'multiple' && form.multiple.every(item => item.address.id !== 0))) {
        form.post(route('store_livraison'));
    }
}
</script>

<template>
    <PasserCommandeLayout>
        <form @submit.prevent="submit">
            <div class="orderingcontainer h-full">
                <div class="componentcontainer height100 alignstretch minmax100">
                    <div class="w_container vertical gap24px overflowhidden">
                        <div class="w_container vertical gap16px">
                            <Link :href="route('entreprise')" class="w_container aligncenter gap8px clickable w-fit">
                            <img class="image16x16px"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65787c766a0ec3c317110ab2_Vectors-Wrapper.svg"
                                alt="Retour" />
                            <div class="text14px semibold">Retour</div>
                            </Link>
                            <div class="text20px unbounded">Confirmez l’adresse de livraison</div>
                            <div class="text14px medium">Sélectionnez une adresse unique, ou une adresse par produit.
                            </div>
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                            <div class="text14px semibold">Envoyer à</div>
                            <div class="w_container white round toggles">
                                <div class="toggle" :class="{ 'selected': adresseType === 'single' }"
                                    @click="adresseType = 'single'">
                                    <div class="text14px">Une seule adresse</div>
                                </div>
                                <div class="toggle" :class="{ 'selected': adresseType === 'multiple' }"
                                    @click="adresseType = 'multiple'">
                                    <div class="text14px">Plusieurs adresses</div>
                                </div>
                            </div>
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div v-if="adresseType == 'multiple'" class="w_container vertical gap12px overflowauto h-full">
                            <template v-for="(panier, index) in multiplePanier" :key="panier.id">
                                <div v-if="panier.product.categorie !== 'licences'"
                                    class="w_container vertical gap16px white round padding12px">
                                    <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                        <div class="w_container aligncenter gap16px w-full max-w-80">
                                            <div class="w_container _80x80 grey">
                                                <div class="productimagecontainer"
                                                    :style="{ 'background-image': 'url(' + panier.product.image_principale + ')' }">
                                                </div>
                                            </div>
                                            <div class="w_container vertical gap2px">
                                                <div class="text14px medium">{{ panier.product.name }}</div>
                                            </div>
                                        </div>
                                        <div v-if="panier.product.delais_livraison" class="text12px purple medium tag">
                                            {{
                                                panier.product.delais_livraison }} jours
                                            ouvrés
                                        </div>
                                    </div>
                                    <div class="separatorhorizontal"></div>
                                    <div class="w_container vertical gap12px">
                                        <div class="w_container aligncenter justifyspacebetween">
                                            <div class="text14px medium">Envoyer à <span class="red">*</span></div>
                                        </div>
                                        <div @click="openAttribute(form.multiple[index].user, form.multiple[index].address, index)"
                                            class="w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey cursor-pointer">
                                            <div class="w_container aligncenter">
                                                <img v-if="form.multiple[index].user.profile_img"
                                                    class="avatarcontainer" :src="form.multiple[index].user.profile_img"
                                                    alt="">
                                                <div v-else class="avatarcontainer">
                                                    <div class="text14px medium white">{{
                                                        userInitials(form.multiple[index].user.name) }}</div>
                                                </div>
                                                <div class="text14px medium nowrap p-2">
                                                    {{ form.multiple[index].user.name + ' - ' +
                                                        form.multiple[index].address.titre }}
                                                </div>
                                            </div>
                                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                                loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div v-else class="w_container vertical gap12px overflowauto h-full">
                            <div class="w_container vertical gap16px white round padding12px">
                                <div class="w_container vertical gap12px">
                                    <div class="w_container aligncenter justifyspacebetween">
                                        <div class="text14px medium">Adresse d’envoi <span class="red">*</span></div>
                                    </div>
                                    <div @click="openAttribute(form.single.user, form.single.address)"
                                        class="w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey cursor-pointer">
                                        <div class="w_container aligncenter">
                                            <img v-if="form.single.user.profile_img" class="avatarcontainer"
                                                :src="form.single.user.profile_img" alt="">
                                            <div v-else class="avatarcontainer">
                                                <div class="text14px medium white">{{
                                                    userInitials(form.single.user.name) }}</div>
                                            </div>
                                            <div class="text14px medium nowrap p-2">
                                                {{ form.single.user.name + ' - ' +
                                                    form.single.address.titre }}
                                            </div>
                                        </div>
                                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                            loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                                    </div>
                                </div>

                            </div>
                            <div class="w_container vertical gap16px white round padding12px">
                                <template v-for="(panier, index) in props.panier.panier_products" :key="panier.id">
                                    <div v-if="panier.product.categorie !== 'licences'">
                                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                            <div class="w_container aligncenter gap16px w-full max-w-80">
                                                <div class="w_container _80x80 grey">
                                                    <div class="productimagecontainer"
                                                        :style="{ 'background-image': 'url(' + panier.product.image_principale + ')' }">
                                                    </div>
                                                </div>
                                                <div class="w_container vertical gap2px">
                                                    <div class="text14px medium">{{ panier.product.name }}</div>
                                                </div>
                                            </div>
                                            <div v-if="panier.product.delais_livraison"
                                                class="text12px purple medium tag">{{ panier.product.delais_livraison
                                                }} jours
                                                ouvrés
                                            </div>
                                            <div class="text20px unbounded">x {{ panier.quantity }}</div>
                                        </div>
                                        <div v-if="index < props.panier.length - 1" class="separatorhorizontal pt-4">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <RecapitulatifCommandes :commandes="props.panier.panier_products">
                    <button type="submit" :disabled="form.processing"
                        :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                        <div class="text14px white">Continuer</div>
                    </button>
                </RecapitulatifCommandes>
            </div>
        </form>

        <AttributeLivraisonCommande :currentUser="currentUser" :currentAddress="currentAddress"
            @onAttributeSelected="onAttributeSelected" @closeAttribute="closeAttribute" :show="showAttribute" />
    </PasserCommandeLayout>
</template>


<style scoped>
.componentcontainer {
    overflow: auto;
}

form {
    height: calc(100vh - 126px);
}

.toggle.selected div {
    color: var(--main);
}

.avatarcontainer {
    background-position: center;
    background-size: cover;
    width: 30px !important;
    height: 30px !important;
    min-width: 30px !important;
    min-height: 30px !important;
    cursor: pointer;
    border-radius: 100000px;
    justify-content: center;
    align-items: center;
    display: flex;
}
</style>
