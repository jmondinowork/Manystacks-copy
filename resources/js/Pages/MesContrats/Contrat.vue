<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateSupport from '@/Components/CreateSupport.vue';
import UserAttribution from '@/Components/UserAttribution.vue';
import { downloadImage, formattedDate, slugify, userInitials } from '@/functions.js';

import { usePage, useForm, Link } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { computed, ref } from 'vue';
import moment from 'moment';

const { props } = usePage();
const store = useStore();

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;

const differenceMois = () => {
    let debut = moment();
    let fin = moment(props.contrat.date_fin_contrat);

    let diff = Math.ceil(fin.diff(debut, 'months', true));

    return diff <= 0 ? 0 : diff;
}

const calculerProgression = () => {
    let debut = moment(props.contrat.date_debut_contrat);
    let fin = moment(props.contrat.date_fin_contrat);
    let today = moment();

    if (today.isAfter(fin)) {
        return 100;
    }

    let total = fin.diff(debut, 'months');
    let passe = today.diff(debut, 'months');

    return (passe / total) * 100;
}

const totalLocation = computed(() => {
    return props.contrat.commande_products
        .filter(produit => produit.type_contrat === 'location')
        .reduce((acc, produit) => acc + parseFloat(produit.prix || 0), 0)
        .toFixed(2);
});
const totalAchat = computed(() => {
    return props.contrat.commande_products
        .filter(produit => produit.type_contrat === 'achat')
        .reduce((acc, produit) => acc + parseFloat(produit.prix || 0), 0)
        .toFixed(2);
});

const showDocument = ref(false);
const filePreview = ref(null);
const isLoading = ref(false);
const showPreviewContrat = () => {
    filePreview.value = props.contrat.contrat_signe;
    showDocument.value = true
}

const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        props.contrat.contrat_signe = file;

        try {
            let formData = new FormData();
            formData.append('contrat_signe', file);
            formData.append('id', props.contrat.id);

            axios.post(route('modifyContrat'), formData)
                .then((response) => {
                    props.contrat = response.data;
                    store.dispatch('updateAnnounce', "Le contrat a bien été importé");
                });
        } catch (error) {
            store.dispatch('updateErrorAnnounce', "Une erreur est survenue lors de l'importation du contrat");
        }
    }
};
const showAttribution = ref(false);
const closeAttribution = () => showAttribution.value = false;
const openAttribution = () => showAttribution.value = true;

const updateAttribution = (productId, action) => {
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('contrat_id', props.contrat.id);
    formData.append('action', action);

    axios.post(route('editEquipementToContrat'), formData)
        .then((response) => {
            props.contrat = response.data.contrat;
            props.equipement_available = response.data.equipement_available;
            if (action == 'retirer')
                store.dispatch('updateAnnounce', "L'équipement a bien été retiré du contrat");
            else
                store.dispatch('updateAnnounce', "L'équipement a bien été ajouté au contrat");

        })
        .catch((error) => {
            store.dispatch('updateErrorAnnounce', "Une erreur est survenue lors de la modification de l'équipement");
        });
}

const formEquipement = useForm({});
const ficheProduit = ref({});
const toggleFicheProduit = (productId) => {
    ficheProduit.value[productId] = !ficheProduit.value[productId];
}
const typeContrat = (product, type) => {
    if (formEquipement[product.id]) {
        return formEquipement[product.id].type_contrat == type;
    }
    return product.type_contrat == type;
}
const setTypeContrat = (productId, type) => {
    if (!formEquipement[productId]) {
        formEquipement[productId] = {};
    }
    formEquipement[productId].type_contrat = type;
};

const setPrice = (productId, event) => {
    if (!formEquipement[productId]) {
        formEquipement[productId] = {};
    }
    formEquipement[productId].prix = event.target.value;
};
const submitEquipement = (productId) => {
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('contrat_id', props.contrat.id);
    if (formEquipement[productId].type_contrat)
        formData.append('type_contrat', formEquipement[productId].type_contrat);
    if (formEquipement[productId].prix)
        formData.append('prix', formEquipement[productId].prix);

    axios.post(route('editEquipementToContrat'), formData)
        .then((response) => {
            props.contrat = response.data.contrat;
            store.dispatch('updateAnnounce', "L'équipement a bien été modifié");
        })
        .catch((error) => {
            store.dispatch('updateErrorAnnounce', "Une erreur est survenue lors de la modification de l'équipement");
        });
}
</script>

<template>
    <AppLayout>
        <div class="maincontainer">
            <div class="componentcontainer">
                <div class="w_container _100 vertical">
                    <Link :href="route('mes-contrats')" class="w_container aligncenter gap8px clickable w-fit">
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6582adf6f93be72dd31f4776_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    <div class="text14px semibold">Retour aux appareils</div>
                    </Link>
                    <div class="text20px unbounded">Contrat n°{{ props.contrat.reference_commande }}</div>
                </div>
                <div class="lightbutton" @click="openCreateSupport">
                    <img src="/images/signaler_icon.svg" class="image20x20px cursor-pointer" alt="">
                </div>
            </div>
            <div class="frame-160">
                <div class="w_container vertical gap20px">
                    <div class="w_container justifyspacebetween">
                        <div class="w_container vertical nogap">
                            <div class="text14px semibold">Montant du contrat</div>
                            <Link :href="'/mes-commandes/' + props.contrat.reference_commande"
                                class="w_container aligncenter gap4px clickable" v-if="props.contrat.financeur">
                            <div class="text14px medium purple">Accéder à la commande</div>
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                loading="lazy" width="16" height="16" alt="" class="image16x16px">
                            </Link>
                        </div>
                        <div class="flex flex-col gap-2 items-end">
                            <div class="w_container alignend" v-if="totalLocation > 0">
                                <div class="text20px unbounded nowrap">{{ totalLocation }} €</div>
                                <div class="text14px unbounded">/mois</div>
                            </div>
                            <div class="w_container alignend" v-if="totalAchat > 0">
                                <div class="text20px unbounded nowrap">{{ totalAchat }} €</div>
                                <div class="text14px unbounded">d'achat</div>
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="progressionbar white">
                            <div class="greenprogressionbar" :style="{ width: calculerProgression() + '%' }">
                            </div>
                        </div>
                        <div class="w_container justifyspacebetween">
                            <div class="text14px">
                                Du <strong class="bold-text">{{ formattedDate(props.contrat.date_debut_contrat)
                                    }}</strong>
                                au <strong class="bold-text-2">{{ formattedDate(props.contrat.date_fin_contrat)
                                    }}</strong>
                            </div>
                            <div v-if="differenceMois()" class="text14px">{{ differenceMois() }} mois restants</div>
                            <div v-else class="text14px">Contrat terminé</div>
                        </div>
                    </div>
                    <div class="separatorhorizontal"></div>
                    <div class="w_container aligncenter justifyspacebetween" v-if="props.contrat.financeur">
                        <div class="text14px">Financé par</div>
                        <div class="text14px">{{ props.contrat.financeur }}</div>
                    </div>
                    <div class="w_container _100 justifyspacebetween white round padding12px thenvertical aligncenter">
                        <div class="w_container gap16px">
                            <div class="adressesicon purple" @click="showPreviewContrat">
                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b36be372594364348d0404_Vectors-Wrapper.svg"
                                    loading="lazy" width="24" height="24" alt="" class="image24x24px">
                            </div>
                            <div class="text14px medium">Contrat de financement</div>
                        </div>
                        <div @click.prevent="downloadImage(props.contrat.contrat_signe, 'contrat-de-financement')"
                            v-if="props.contrat.contrat_signe" class="lightbutton">
                            <div class="text14px medium purple nowrap">Télécharger</div>
                        </div>

                        <div class="lightbutton" v-else @click="() => triggerFileInput('contrat_signe')">
                            <div class="text14px medium purple nowrap">Ajouter</div>

                            <input class="d-none" type="file" name="contrat_signe" id="contrat_signe"
                                @input="handleFileChange($event)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="attribution">
                <div class="componentcontainer height100">
                    <div class="attributionblock">
                        <div class="w_container vertical gap12px overflowhidden">
                            <div class="flex justify-between">
                                <div class="text14px">{{ props.contrat.commande_products.length }} équipements associés
                                    au
                                    contrat</div>
                                <div class="lightbutton" v-if="!props.contrat.financeur" @click="openAttribution">
                                    <div class="text14px medium purple nowrap">Ajouter de l'équipement</div>
                                </div>
                            </div>
                            <div class="w_container vertical overflowauto" style="gap: 16px">
                                <div class="flex flex-col" v-for="product in props.contrat.commande_products"
                                    :key="product.id">
                                    <div @click="toggleFicheProduit(product.id)"
                                        class="w_container _100 white round padding12px thenvertical aligncenter clickable d_container-row"
                                        :class="{ 'cols-3': props.contrat.financeur, 'cols-4': !props.contrat.financeur }">
                                        <div class="w_container aligncenter gap16px">
                                            <div class="w_container _80x80 grey">
                                                <div class="productimagecontainer"
                                                    :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                                </div>
                                            </div>
                                            <div class="w_container vertical gap2px">
                                                <div class="text14px medium">{{ product.name }}</div>
                                                <div class="text12px gray">{{ product.numero_unique }}</div>
                                            </div>
                                        </div>
                                        <div class="w-container justify-end">
                                            <div v-if="product.user_attributed_id"
                                                class="w_container aligncenter gap8px justify-end">
                                                <img v-if="product.user_attributed.type == 'Salle'"
                                                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2ca6b777543b95c54bcc_Vectors-Wrapper.svg"
                                                    loading="lazy" width="20" height="20" alt="" class="image20x20px">
                                                <div v-else-if="product.user_attributed.profile_img"
                                                    class="avatarcircle_img"
                                                    :style="{ 'background-image': 'url(' + product.user_attributed.profile_img + ')' }">
                                                </div>
                                                <div v-else class="avatarcircle">
                                                    <div class="text40px white">
                                                        {{ userInitials(product.user_attributed.name) }}
                                                    </div>
                                                </div>
                                                <div class="w_container vertical nogap">
                                                    <div class="text14px">{{ product.user_attributed.name }}</div>
                                                </div>
                                            </div>
                                            <div v-else class="w_container aligncenter gap8px justify-end">
                                                <img src="/images/tag-icon.svg" loading="lazy" width="20" height="20"
                                                    alt="" class="image20x20px">
                                                <div class="text14px">Non attribué</div>
                                            </div>
                                        </div>
                                        <div class="w_container justify-end">
                                            <div class="tagblock w-fit" :class="slugify(product.status)">
                                                <div class="texttag"><span class="text-span-9">•&nbsp;</span> {{
                                                    product.status
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w_container justify-end">
                                            <div class="lightbutton w-fit" v-if="!props.contrat.financeur"
                                                @click="(event) => { event.stopPropagation(); updateAttribution(product.id, 'retirer') }">
                                                <div class="text14px medium purple nowrap">Retirer</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="componentcontainer bg-white" v-if="ficheProduit[product.id]">
                                        <div class="w_container vertical gap24px">
                                            <div class="w_container vertical">
                                                <div class="text14px medium">Type du contrat</div>
                                                <div
                                                    class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                                    <div class="w_container white round toggles"
                                                        v-if="!props.contrat.financeur">
                                                        <div class="toggle"
                                                            :class="{ 'selected': typeContrat(product, 'location') }"
                                                            @click="setTypeContrat(product.id, 'location')">
                                                            <div class="text14px">Location</div>
                                                        </div>
                                                        <div class="toggle"
                                                            :class="{ 'selected': typeContrat(product, 'achat') }"
                                                            @click="setTypeContrat(product.id, 'achat')">
                                                            <div class="text14px">Achat</div>
                                                        </div>
                                                    </div>
                                                    <div class="w_container white round toggles" v-else>
                                                        <div class="toggle"
                                                            :class="{ 'selected': product.type_contrat == 'location' }">
                                                            <div class="text14px">Location</div>
                                                        </div>
                                                        <div class="toggle"
                                                            :class="{ 'selected': product.type_contrat == 'achat' }">
                                                            <div class="text14px">Achat</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="w_container vertical">
                                                <div class="w_container vertical gap16px">
                                                    <div v-if="typeContrat(product, 'achat')" class="text14px medium">
                                                        Prix de l'équipement
                                                    </div>
                                                    <div v-else class="text14px medium">Mensualité de
                                                        l'équipement</div>
                                                    <div class="textinput gray w-fit">
                                                        <input v-if="!props.contrat.financeur" :value="product.prix"
                                                            class="text14px w-fit" type="text" autocomplete="off"
                                                            @change="setPrice(product.id, $event)">
                                                        <div v-else class="text14px w-fit">
                                                            {{ product.prix }} €
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex flex-col justify-between">
                                            <Link :href="'/mes-equipements/' + product.id" class="flex gap-2">
                                            <div class="text14px medium purple" style="width: 180px;">Accédez à
                                                l'équipement
                                            </div>
                                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                                loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                            </Link>
                                            <div v-if="!props.contrat.financeur" class="w_container justify-end">
                                                <div class="bigbutton purple w-auto"
                                                    @click="submitEquipement(product.id)">
                                                    <div class="text14px white"> Enregistrer </div>
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
        </div>
    </AppLayout>

    <div class="darkmodalbackground" :class="{ 'show': showDocument }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Aperçu
                </div>
                <div class="w_container alignright cursor-pointer" @click="showDocument = false">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div v-if="!isLoading" class="componentcontainer justify-center max-h-full">
                <div style="height: 600px; width: 400px;">
                    <embed :src="filePreview" type="application/pdf" style="width:100%;height:100%;">
                </div>
            </div>
        </div>
    </div>

    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
        :object="'J\'ai un problème avec mon contrat'" :commande="props.contrat"></CreateSupport>
    <UserAttribution :show="showAttribution" @closeAttribution="closeAttribution" :title="'équipements'"
        :equipement_available="props.equipement_available" @updateAttribution="updateAttribution"></UserAttribution>
</template>

<style scoped>
.avatarcircle,
.avatarcircle_img,
.circle40px {
    width: 30px;
    height: 30px;
    min-width: 30px;
    min-height: 30px;
}

.avatarcircle .text40px {
    font-size: 14px;
}
</style>
