<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import Visualisation from '@/Components/Visualisation.vue';
import { formattedDateHour } from '@/functions.js';
import Cookies from 'js-cookie';

import { Head, usePage, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';


const { props } = usePage();

const statutClass = (statut) => 'statut-' + statut.replace(/\s+/g, '-').toLowerCase();

onMounted(() => {
    props.mes_commandes.forEach(commande => {
        if (commande.statut === 'Contrat à transmettre') {
            commande.statut = 'Terminée';
        }
    });
});

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
        <title>Commandes</title>
        <meta name="description" content="Retrouvez ici toutes vos Commandes">
    </Head>

    <SimpleLayout>
        <div v-if="props.mes_commandes.length" class="componentcontainer">
            <div class="w_container vertical alignleft _100 gap24px">
                <Visualisation :display="display" @changeDisplay="changeDisplay"></Visualisation>

                <div class="stacksgrid" v-if="display == 'grid'">
                    <Link :href="'mes-commandes/' + commande.reference_commande" v-for="commande in props.mes_commandes"
                        :key="commande.id">
                    <div class="stacklicencecontainer">
                        <div class="imagex4">
                            <div class="image_container large"
                                v-for="product in stackProductImage(commande.commande_products)" :key="product">
                                <img v-if="product && product.image_principale" :src="product.image_principale"
                                    loading="lazy" alt="" class="imageequipement-grid">
                                <div v-else class="imageequipement-grid"></div>
                            </div>
                        </div>
                        <div class="w_container vertical gap24px">
                            <div class="w_container vertical gap8px">
                                <div class="w_container vertical alignleft">
                                    <div class="w_container_4">
                                        <div class="text20px unbounded">Commande n°{{ commande.reference_commande }}
                                        </div>
                                    </div>
                                    <div class="tagblock" :class="statutClass(commande.statut)">
                                        <div class="dot"></div>
                                        <div>{{ commande.statut }}</div>
                                    </div>
                                    <div class="divider_gray_horizontal"></div>
                                    <div class="datecommande">
                                        <div class="text14px">Date de commande : {{
                                            formattedDateHour(commande.created_at) }}</div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2bca03dab88c100affc70_monitor.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px gray">{{ commande.commande_products.length }} équipements</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </Link>
                </div>

                <div class="stacksrow" v-else>
                    <Link :href="'mes-commandes/' + commande.reference_commande" v-for="commande in props.mes_commandes"
                        class="stacklicencerow">
                    <div class="image_container small">
                        <img :src="commande.commande_products[0].image_principale" loading="lazy" alt="">
                    </div>
                    <div class="w_container vertical gap-6">
                        <div class="w_container vertical">
                            <div class="d_container-row cols-4">
                                <div class="description_licence_container">
                                    <div class="text20px unbounded">Commande n°{{ commande.reference_commande }}
                                    </div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="text14px black">{{ formattedDateHour(commande.created_at)
                                        }}</div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="text14px black bold-text">{{ commande.commande_products.length }}
                                    </div>
                                    <div class="text14px black">équipement(s)</div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="tagblock" :class="statutClass(commande.statut)">
                                        <div class="dot"></div>
                                        <div>{{ commande.statut }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'mes_commandes'"></EmptyPage>
    </SimpleLayout>
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

.stacktitlecontainer {
    border-right: none;
}
</style>
