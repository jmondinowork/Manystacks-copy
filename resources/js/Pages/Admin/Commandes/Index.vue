<script setup>
import CommandesAdminLayout from '@/Layouts/CommandesAdminLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import { formattedDateHour } from '@/functions.js';

import { Head, usePage, Link } from '@inertiajs/vue3';

const { props } = usePage();

const statutClass = (statut) => 'statut-' + statut.replace(/\s+/g, '-').toLowerCase()
</script>

<template>

    <Head>
        <title>Commandes</title>
        <meta name="description" content="">
    </Head>

    <CommandesAdminLayout>
        <div v-if="props.commandes.length" class="componentcontainer directionvertical h-full">
            <div class="w_container vertical gap16px">
                <div class="text20px unbounded">
                    Toutes les commandes
                </div>
                <div class="separatorhorizontal"></div>
                <div class="stacksgrid">
                    <Link :href="'/commandesAdmin/' + commande.reference_commande"
                        class="stackunitcontainer cursor-pointer" v-for="commande in props.commandes"
                        :key="commande.id">
                    <div class="w_container vertical gap24px">
                        <div class="w_container gap12px">
                            <div v-for="product in commande.commande_products.slice(0, 4)" :key="product.id"
                                class="productimagesideselector">
                                <img class="image100" loading="lazy" :src="product.image_principale" />
                            </div>
                        </div>
                        <div class="stacktitlecontainer">
                            <div class="w_container vertical gap16px">
                                <div class="w_container gap16px">
                                    <div class="w_container vertical gap4px">
                                        <div class="text20px unbounded">{{ commande.reference_commande }}</div>
                                    </div>
                                </div>
                                <div class="text16px">
                                    {{ commande.entreprise.raison_sociale }}
                                </div>
                                <div class="tagblock" :class="statutClass(commande.statut)">
                                    <div class="texttag">{{ commande.statut }}</div>
                                </div>
                                <div class="w_container aligncenter gap8px"><img
                                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2f331dca694dc88c681f_Vectors-Wrapper.svg"
                                        loading="lazy" width="20" height="20" alt="" class="image20x20px">
                                    <div class="text14px">{{ commande.commande_products.length }} appareils</div>
                                </div>
                                <div class="w_container vertical">
                                    <div class="text14px">{{ formattedDateHour(commande.created_at) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'admincommande'"></EmptyPage>
    </CommandesAdminLayout>
</template>

<style scoped>
.productimagesideselector {
    opacity: 1;
}
</style>
