<script setup>
import { computed } from 'vue';

const data = defineProps({
    commandes: Array
});

const productPriceAchat = (item) => {
    if (item.type_contrat === 'achat') {
        return item.prix ? item.prix : item.product.prix_achat;
    }
};
const totalLocationMonth = computed(() => {
    return data.commandes
        .filter(item => {
            if (item.categorie === 'licences') {
                return item.type_licence === 'Mensuel';
            } else {
                return item.type_contrat === 'location';
            }
        })
        .reduce((acc, item) => acc + (item.prix * item.quantity), 0)
        .toFixed(2);
});
const totalLocationYear = computed(() => {
    return data.commandes
        .filter(item => {
            if (item.categorie === 'licences') {
                return item.type_licence === 'Annuel';
            } else {
                return item.type_contrat === 'location';
            }
        })
        .reduce((acc, item) => acc + (item.prix * item.quantity), 0)
        .toFixed(2);
});
const totalAchat = computed(() => {
    return data.commandes.filter(item => item.type_contrat === 'achat').reduce((acc, item) => acc + (item.prix ? item.prix : item.product.prix_achat) * item.quantity, 0).toFixed(2);
});
const commandesLength = computed(() => {
    const length = data.commandes.reduce((total, item) => total + parseInt(item.quantity, 10), 0);
    return length > 1 ? `${length} articles` : `${length} article`;
});
</script>

<template>
    <div class="componentcontainer height100 alignstretch max100">
        <div class="recap_container">
            <div class="text20px unbounded">Récapitulatif</div>
            <div class="recapsmallcontainer">

                <div v-for="commande in data.commandes" :key="commande.id" class="w_container vertical gap12px">
                    <div class="w_container vertical gap16px white round padding12px">
                        <div class="w_container aligncenter gap16px">
                            <div class="w_container _80x80 grey">
                                <div class="productimagecontainer"
                                    :style="{ 'background-image': 'url(' + (commande.product?.image_principale || commande.image_principale) + ')' }">
                                </div>
                            </div>
                            <div class="w_container vertical gap2px">
                                <div class="text16px medium">{{ commande.product?.name || commande.name }}</div>
                                <div class="text14px">{{ commande.product?.proprietes || commande.proprietes }}</div>
                            </div>
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div class="w_container justifyspacebetween">
                            <div class="text20px unbounded">x {{ commande.quantity }}</div>
                            <div class="w_container alignend" v-if="commande.type_contrat === 'location' || commande.categorie === 'licences'">
                                <div class="text20px unbounded">{{ commande.prix }} €</div>
                                <div class="text14px unbounded">/mois</div>
                            </div>
                            <div class="w_container alignend" v-else>
                                <div class="text20px unbounded">{{ productPriceAchat(commande) }} €</div>
                                <div class="text14px unbounded">/unité</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w_container vertical gap12px">
                <div class="text14px">{{ commandesLength }}</div>
                <div class="w_container aligncenter justifyspacebetween" v-if="totalLocationMonth > 0">
                    <div class="text14px semibold">Sous-total :</div>
                    <div class="w_container alignend">
                        <div class="text20px unbounded">{{ totalLocationMonth }} €</div>
                        <div class="text14px unbounded">/mois</div>
                    </div>
                </div>
                <div class="w_container aligncenter justifyspacebetween" v-if="totalLocationYear > 0">
                    <div class="text14px semibold">Sous-total :</div>
                    <div class="w_container alignend">
                        <div class="text20px unbounded">{{ totalLocationYear }} €</div>
                        <div class="text14px unbounded">/an</div>
                    </div>
                </div>
                <div class="w_container aligncenter justifyspacebetween" v-if="totalAchat > 0">
                    <div class="text14px semibold">Sous-total :</div>
                    <div class="w_container alignend">
                        <div class="text20px unbounded">{{ totalAchat }} €</div>
                        <div class="text14px unbounded">En achat</div>
                    </div>
                </div>
                <slot></slot>
            </div>
        </div>
    </div>
</template>
