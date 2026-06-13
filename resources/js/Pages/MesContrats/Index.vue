<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import Visualisation from '@/Components/Visualisation.vue';
import { formattedDateHour } from '@/functions.js';
import Cookies from 'js-cookie';

import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import moment from 'moment';

const { props } = usePage();

const differenceMois = (date_fin) => {
    let debut = moment();
    let fin = moment(date_fin);

    let diff = Math.ceil(fin.diff(debut, 'months', true));

    return diff <= 0 ? 0 : diff;
};

const calculerProgression = (date_debut, date_fin) => {
    let debut = moment(date_debut);
    let fin = moment(date_fin);
    let today = moment();

    if (today.isAfter(fin)) {
        return 100;
    }

    let total = fin.diff(debut, 'months');
    let passe = today.diff(debut, 'months');

    return (passe / total) * 100;
}

const totalLocation = (contrat) => {
    if (!contrat.commande_products) return 0;

    return contrat.commande_products
        .filter(produit => produit.type_contrat === 'location')
        .reduce((acc, produit) => acc + parseFloat(produit.prix || 0), 0)
        .toFixed(2);
};
const totalAchat = (contrat) => {
    if (!contrat.commande_products) return 0;

    return contrat.commande_products
        .filter(produit => produit.type_contrat === 'achat')
        .reduce((acc, produit) => acc + parseFloat(produit.prix || 0), 0)
        .toFixed(2);
};


const stackProductImage = (products) => {
    let result = products.slice(0, 4);
    while (result.length < 4) {
        result.push(null);
    }
    return result;
}

const display = ref(Cookies.get('display') || 'grid');
const changeDisplay = (type) => {
    display.value = type;
    Cookies.set('display', type);
};
</script>

<template>

    <Head>
        <title>Contrats</title>
        <meta name="description" content="Retrouvez ici toutes vos Contrats">
    </Head>

    <SimpleLayout>
        <div v-if="props.mes_contrats.length" class="componentcontainer">
            <div class="w_container vertical alignleft _100 gap24px">
                <Visualisation :display="display" @changeDisplay="changeDisplay"></Visualisation>

                <div class="stacksgrid _3" v-if="display == 'grid'">
                    <Link :href="'mes-contrats/' + contrat.reference_commande" v-for="contrat in props.mes_contrats"
                        :key="contrat.id">
                    <div class="stacklicencecontainer">
                        <div class="imagex4">
                            <div class="image_container large"
                                v-for="product in stackProductImage(contrat.commande_products)" :key="product">
                                <img v-if="product && product.image_principale" :src="product.image_principale"
                                    loading="lazy" alt="" class="imageequipement-grid">
                                <div v-else class="imageequipement-grid"></div>
                            </div>
                        </div>
                        <div class="w_container vertical gap24px">
                            <div class="w_container vertical gap8px">
                                <div class="w_container vertical alignleft">
                                    <div class="w_container_3">
                                        <div class="text20px unbounded">Contrat n°{{ contrat.reference_commande }}</div>
                                    </div>
                                    <div class="text14px">Date de début : {{
                                        formattedDateHour(contrat.date_debut_contrat) }}</div>
                                    <div class="text14px">Date de fin : {{
                                        formattedDateHour(contrat.date_fin_contrat) }}</div>
                                    <div class="progressionbar">
                                        <div class="greenprogressionbar"
                                            :style="{ width: calculerProgression(contrat.date_debut_contrat, contrat.date_fin_contrat) + '%' }">
                                        </div>
                                    </div>
                                    <div v-if="differenceMois(contrat.date_fin_contrat)" class="text14px gray">Période
                                        restante : {{
                                            differenceMois(contrat.date_fin_contrat) }} mois</div>
                                    <div v-else class="text14px gray">Contrat terminé</div>
                                </div>
                                <div class="flex gap-2 items-baseline" v-if="totalLocation(contrat) > 0">
                                    <div class="text20px unbounded">{{ totalLocation(contrat) }} €</div>
                                    <div class="text14px gray">/mois</div>
                                </div>
                                <div class="flex gap-2 items-baseline" v-if="totalAchat(contrat) > 0">
                                    <div class="text20px unbounded">{{ totalAchat(contrat) }} €</div>
                                    <div class="text14px gray">d'achat</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </Link>
                </div>

                <div class="stacksrow" v-else>
                    <Link :href="'mes-contrats/' + contrat.reference_commande" v-for="contrat in props.mes_contrats"
                        class="stacklicencerow">
                    <div class="image_container small">
                        <img :src="contrat.commande_products[0].image_principale" loading="lazy" alt="">
                    </div>
                    <div class="w_container vertical gap-6">
                        <div class="w_container vertical">
                            <div class="d_container-row cols-4">
                                <div class="description_licence_container flex-col items-start">
                                    <div class="text20px unbounded">Contrat n°{{ contrat.reference_commande }}</div>
                                    <div class="progressionbar">
                                        <div class="greenprogressionbar"
                                            :style="{ width: calculerProgression(contrat.date_debut_contrat, contrat.date_fin_contrat) + '%' }">
                                        </div>
                                    </div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="text14px black">{{ formattedDateHour(contrat.date_debut_contrat)
                                        }}</div>
                                </div>
                                <div class="description_licence_container justify-end"
                                    v-if="differenceMois(contrat.date_fin_contrat)">
                                    <div class="text14px black">Période restante :</div>
                                    <div class="text14px black bold-text"> {{
                                        differenceMois(contrat.date_fin_contrat) }} mois</div>
                                </div>
                                <div class="description_licence_container justify-end" v-else>
                                    <div class="text14px black bold-text">Contrat terminé</div>
                                </div>
                                <div class="description_licence_container items-end flex-col">
                                    <div class="flex gap-2 items-baseline" v-if="totalLocation(contrat) > 0">
                                        <div class="text14px bold-text">{{ totalLocation(contrat) }} €</div>
                                        <div class="text12px gray">/mois</div>
                                    </div>
                                    <div class="flex gap-2 items-baseline" v-if="totalAchat(contrat) > 0">
                                        <div class="text14px bold-text">{{ totalAchat(contrat) }} €</div>
                                        <div class="text12px gray">d'achat</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'mes_contrats'"></EmptyPage>
    </SimpleLayout>
</template>
