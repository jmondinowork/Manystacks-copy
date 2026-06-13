<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecapitulatifMesCommandes from '@/Components/RecapitulatifMesCommandes.vue';

import CreateSupport from "@/Components/CreateSupport.vue";
import { formattedDateHour } from '@/functions.js';

import { Link, usePage, useForm } from '@inertiajs/vue3';
import { ref } from "vue";

const { props } = usePage();

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;


const statuts = {
    "En attente de financement": 1,
    "En attente de signature": 2,
    "En validation du contrat": 3,
    "Livraison en cours": 4,
    "Contrat à transmettre": 5,
    "Terminée": 5
};

const displayStatutPart = (index) => {
    if (statuts[props.commande.statut] >= index)
        return true;
    return false;
}
const displayBaseMessage = (index) => {
    if (index > statuts[props.commande.statut])
        return true;
    return false;
}

const form = useForm({
    id: props.commande.id
});
const signContract = () => {
    form.post(route('mes-commandes.signature'), {
        onFinish: () => window.location.reload(true),
    });
}

const tab = ref(props.products_location.length ? 'equipement-location' : props.products_achat.length ? 'equipement-achat' : 'licences');
const changeTab = (currentTab) => {
    tab.value = currentTab;
}
</script>

<template>
    <AppLayout>
        <div class="orderingcontainer h-full">
            <div class="commandes" style="overflow-y: scroll;">
                <div class="componentcontainer flex-col">
                    <div class="d-flex">
                        <div class="w_container _100 vertical nogap">
                            <Link :href="route('mes-commandes')" class="w_container aligncenter gap8px clickable w-fit">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6582adf6f93be72dd31f4776_Vectors-Wrapper.svg"
                                loading="lazy" width="16" height="16" alt="" class="image16x16px">
                            <div class="text14px semibold">Retour aux commandes</div>
                            </Link>
                            <div class="w_container justifyspacebetween">
                                <div class="w_container gap12px _44pxheight">
                                    <div class="text20px unbounded">Commande n°{{ props.commande.reference_commande }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="lightbutton" @click="openCreateSupport">
                            <img src="/images/signaler_icon.svg" class="image20x20px cursor-pointer" alt="">
                        </div>
                    </div>

                    <div class="w_container _100 gap20px borderbottom mt-4">
                        <div v-if="props.products_location.length" class="tabs"
                            :class="{ 'selected': tab === 'equipement-location' }"
                            @click="changeTab('equipement-location')">
                            <img v-if="tab == 'equipement-location'" src="/images/activite-selected.svg" alt="">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb2656a7a5789a10c56b51_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <div class="text14px grey400">Équipements en location</div>
                        </div>
                        <div v-if="props.products_achat.length" class="tabs"
                            :class="{ 'selected': tab === 'equipement-achat' }" @click="changeTab('equipement-achat')">
                            <img v-if="tab == 'equipement-achat'" src="/images/activite-selected.svg" alt="">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb2656a7a5789a10c56b51_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <div class="text14px grey400">Équipements en achat</div>
                        </div>
                        <div v-if="props.licences.length" class="tabs" :class="{ 'selected': tab === 'licences' }"
                            @click="changeTab('licences')">
                            <img v-if="tab == 'licences'" src="/images/financement-selected.svg" alt="">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb265669da30c0c51a6313_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <div class="text14px grey400">Licences</div>
                        </div>
                    </div>
                </div>

                <div class="commandecontainer" v-if="tab == 'equipement-location'">
                    <!-- /*
                    |--------------------------------------------------------------------------
                    | Financement part
                    |--------------------------------------------------------------------------
                    */ -->
                    <div class="componentcontainer commandes">
                        <div class="w_container vertical gap16px">
                            <div class="w_container vertical gap4px">
                                <div class="w_container aligncenter _100 justifyspacebetween">
                                    <div class="text16px medium">Demande de financement</div>
                                    <div class="text14px grey400">{{ formattedDateHour(props.commande.date_financement)
                                        }}
                                    </div>
                                </div>
                            </div>
                            <template v-if="!props.commande.financeur">
                                <div class="text14px">
                                    Votre demande de financement est actuellement en cours d’étude auprès de nos
                                    partenaires
                                    financiers.
                                </div>
                            </template>
                            <template v-else>
                                <div class="separatorhorizontal"></div>
                                <div class="w_container aligncenter justifyspacebetween">
                                    <div class="text14px">Félicitations ! Nous avons reçu un accord de financement avec
                                        :
                                        <span class="text16px medium">{{ props.commande.financeur }}</span>
                                    </div>
                                </div>
                            </template>


                        </div>
                        <div class="circle green absolute">
                            <div class="text14px white">1</div>
                        </div>
                        <div class="stepline" :class="{ 'grey': !displayStatutPart(2) }"></div>
                    </div>
                    <!-- /*
                    |--------------------------------------------------------------------------
                    | Demande de signature part
                    |--------------------------------------------------------------------------
                    */ -->
                    <div class="componentcontainer commandes">
                        <div class="w_container vertical gap16px">
                            <div class="w_container vertical gap4px">
                                <div class="w_container aligncenter _100 justifyspacebetween">
                                    <div class="text16px medium">Signature du contrat</div>
                                    <div class="text14px grey400">{{ formattedDateHour(props.commande.date_signature) }}
                                    </div>
                                </div>
                            </div>
                            <template v-if="displayBaseMessage(2)">
                                <div class="text14px">
                                    Une signature électronique du contrat de financement vous sera demandée.
                                </div>
                            </template>
                            <template v-if="displayStatutPart(2) && props.commande.statut == 'En attente de signature'">
                                <div v-if="props.commande.sign_again" class="text14px medium">
                                    Il semble qu'une erreur soit survenue. Nous vous invitons à signer de nouveau votre
                                    contrat.
                                </div>
                                <div class="separatorhorizontal"></div>
                                <a @click="signContract" :href="props.commande.lien_contrat" class="bigbutton purple"
                                    target="_blank">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6582adf84deeb3b0197ba589_Vectors-Wrapper.svg"
                                        loading="lazy" width="12" height="12" alt="" class="image12x12px">
                                    <div class="text14px white">Signer le contrat</div>
                                </a>
                            </template>
                            <template v-if="displayStatutPart(3)">
                                <div class="text14px">Félicitations ! Votre contrat a été signé.</div>
                            </template>
                        </div>
                        <div class="circle green absolute" :class="{ 'grey': !displayStatutPart(2) }">
                            <div class="text14px white">2</div>
                        </div>
                        <div class="stepline" :class="{ 'grey': !displayStatutPart(3) }"></div>
                    </div>
                    <!-- /*
                    |--------------------------------------------------------------------------
                    | Validation de signature part
                    |--------------------------------------------------------------------------
                    */ -->
                    <div class="componentcontainer commandes">
                        <div class="circle green absolute" :class="{ 'grey': !displayStatutPart(3) }">
                            <div class="text14px white">3</div>
                        </div>
                        <div class="stepline" :class="{ 'grey': !displayStatutPart(4) }"></div>
                        <div class="w_container vertical gap4px">
                            <div class="w_container aligncenter _100 justifyspacebetween">
                                <div class="text16px medium">Validation du contrat</div>
                                <div class="text14px grey400">{{ formattedDateHour(props.commande.date_validation) }}
                                </div>
                            </div>
                            <template v-if="displayBaseMessage(3)">
                                <div class="text14px">Nous procéderons à la vérification de votre signature avant de
                                    finaliser votre commande.</div>
                            </template>
                            <template
                                v-if="displayStatutPart(3) && props.commande.statut == 'En validation du contrat'">
                                <div class="text14px">Votre contrat de financement est en cours de validation.</div>
                            </template>
                            <template v-if="displayStatutPart(4)">
                                <div class="text14px">Félicitations ! Votre contrat a été signé et validé.</div>
                            </template>
                        </div>
                    </div>
                    <!-- /*
                    |--------------------------------------------------------------------------
                    | Livraison part
                    |--------------------------------------------------------------------------
                    */ -->
                    <div class="componentcontainer commandes">
                        <div class="circle green absolute" :class="{ 'grey': !displayStatutPart(4) }">
                            <div class="text14px white">4</div>
                        </div>
                        <div class="w_container vertical gap16px">
                            <div class="w_container vertical gap4px">
                                <div class="w_container aligncenter _100 justifyspacebetween">
                                    <div class="text16px medium">Livraison</div>
                                    <div class="text14px grey400">{{ formattedDateHour(props.commande.date_termine) }}
                                    </div>
                                </div>
                            </div>
                            <template v-if="displayBaseMessage(4)">
                                Vous pourrez suivre la progression de la livraison de votre commande en temps réel.
                            </template>
                            <template v-if="displayStatutPart(4) && props.commande.statut == 'Livraison en cours'">
                                <div class="separatorhorizontal"></div>

                                <div class="w_container vertical gap12px white round padding20px">
                                    <template v-for="(product, index) in props.products_location" :key="product.id">
                                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                            <div class="w_container aligncenter gap16px">
                                                <div class="w_container _80x80 grey">
                                                    <div class="productimagecontainer"
                                                        :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                                    </div>
                                                </div>
                                                <div class="w_container vertical gap2px">
                                                    <div class="text16px medium">{{ product.name }}</div>
                                                    <div class="text14px">{{ product.proprietes }}</div>
                                                </div>
                                            </div>

                                            <a v-if="product.lien_livraison" :href="product.lien_livraison"
                                                target="_blank" class="w_container aligncenter gap4px clickable">
                                                <div class="text14px medium purple nowrap">Suivre la livraison</div>
                                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                            </a>
                                        </div>
                                        <div class="text14px">
                                            {{ product.adresse_livraison.adresse + ', '
                                                + product.adresse_livraison.code_postal + ' '
                                                + product.adresse_livraison.ville + ', '
                                                + product.adresse_livraison.pays }}
                                        </div>
                                        <div class="text14px">
                                            {{ product.user_livraison.name }}
                                        </div>
                                        <div v-if="index < props.products_location.length - 1"
                                            class="separatorhorizontal">
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template v-else-if="displayStatutPart(5)">
                                <div class="text14px">Félicitations ! Votre commande est arrivée à destination.</div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="commandecontainer" v-if="tab == 'equipement-achat'">
                    <div class="componentcontainer commandes">
                        <div class="circle green absolute" :class="{ 'grey': !displayStatutPart(1) }">
                            <div class="text14px white">1</div>
                        </div>
                        <div class="w_container vertical gap16px">
                            <div class="w_container vertical gap4px">
                                <div class="w_container aligncenter _100 justifyspacebetween">
                                    <div class="text16px medium">Livraison</div>
                                    <div class="text14px grey400">{{ formattedDateHour(props.commande.date_termine) }}
                                    </div>
                                </div>
                            </div>
                            <template v-if="displayStatutPart(1) && !displayStatutPart(5)">
                                <div class="separatorhorizontal"></div>

                                <div class="w_container vertical gap12px white round padding20px">
                                    <template v-for="(product, index) in props.products_achat" :key="product.id">
                                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                            <div class="w_container aligncenter gap16px">
                                                <div class="w_container _80x80 grey">
                                                    <div class="productimagecontainer"
                                                        :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                                    </div>
                                                </div>
                                                <div class="w_container vertical gap2px">
                                                    <div class="text16px medium">{{ product.name }}</div>
                                                    <div class="text14px">{{ product.proprietes }}</div>
                                                </div>
                                            </div>

                                            <a v-if="product.lien_livraison" :href="product.lien_livraison"
                                                target="_blank" class="w_container aligncenter gap4px clickable">
                                                <div class="text14px medium purple nowrap">Suivre la livraison</div>
                                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                            </a>
                                        </div>
                                        <div class="text14px">
                                            {{ product.adresse_livraison.adresse + ', '
                                                + product.adresse_livraison.code_postal + ' '
                                                + product.adresse_livraison.ville + ', '
                                                + product.adresse_livraison.pays }}
                                        </div>
                                        <div class="text14px">
                                            {{ product.user_livraison.name }}
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template v-else-if="displayStatutPart(5)">
                                <div class="text14px">Félicitations ! Votre commande est arrivée à destination.</div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="commandecontainer" v-else-if="tab == 'licences'">
                    <div class="componentcontainer commandes" v-for="(licence, index) in props.licences"
                        :key="licence.id">
                        <div class="w_container vertical gap16px">
                            <div class="text14px">{{ licence.name }}</div>
                            <div class="separatorhorizontal"></div>
                            <div class="text14px" v-if="licence.commande_status == 'ON_HOLD'">
                                Votre licence est en cours d'acheminement vers votre tenant.
                            </div>
                            <div class="text14px" v-else-if="licence.commande_status == 'ERROR'">
                                Une erreur est survenue lors de la validation de la licence, vous ne serez pas
                                débité.
                            </div>
                            <div class="text14px" v-else-if="licence.commande_status == 'COMPLETED'">
                                Votre licence a été livrée sur votre tenant.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <RecapitulatifMesCommandes
                :commandes="tab == 'equipement-location' ? props.recapProducts_location : tab == 'equipement-achat' ? props.recapProducts_achat : props.recapLicences">
            </RecapitulatifMesCommandes>
            <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
                :object="'J\'ai un problème avec ma commande'" :commande="props.commande"></CreateSupport>
        </div>
    </AppLayout>
</template>

<style scoped>
.tabs.selected .text14px {
    color: var(--main);
}

.tabs {
    cursor: pointer;
}
</style>
