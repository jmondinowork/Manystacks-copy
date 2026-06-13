<script setup>
import { computed } from 'vue';

import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { downloadImage } from '@/functions.js';

import Financement from '@/Pages/Admin/Commandes/statuts/Financement.vue';
import ConfirmationAchat from '@/Pages/Admin/Commandes/statuts/ConfirmationAchat.vue';
import Signature from '@/Pages/Admin/Commandes/statuts/Signature.vue';
import Validation from '@/Pages/Admin/Commandes/statuts/Validation.vue';
import Livraison from '@/Pages/Admin/Commandes/statuts/Livraison.vue';
import Contrat from '@/Pages/Admin/Commandes/statuts/Contrat.vue';
import Termine from '@/Pages/Admin/Commandes/statuts/Termine.vue';

import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();

const statusToComponent = {
    "En attente de financement": Financement,
    "En confirmation d'achat": ConfirmationAchat,
    "En attente de signature": Signature,
    "En validation du contrat": Validation,
    "Livraison en cours": Livraison,
    "Contrat à transmettre": Contrat,
    "Terminée": Termine
};
const selectComponent = () => {
    return statusToComponent[props.commande.statut];
}

const locationProduct = computed(() => props.commande.products.filter(product => product.type_contrat === 'location'))
const achatProduct = computed(() => props.commande.products.filter(product => product.type_contrat === 'achat'))

const entrepriseInfos = [
    { title: 'Raison Sociale', property: 'raison_sociale' },
    { title: 'SIRET', property: 'siret' },
    { title: 'Auto entreprise', property: 'auto_entreprise' },
    { title: 'Adresse', property: 'adresse' },
    { title: "Complément d'adresse", property: 'complement_adresse' },
    { title: 'Code Postal', property: 'code_postal' },
    { title: 'Ville', property: 'ville' },
    { title: 'Pays', property: 'pays' }
];
const signataireInfos = [
    { title: 'Prénom', property: 'prenom' },
    { title: 'Nom', property: 'nom' },
    { title: 'Téléphone', property: 'telephone' },
    { title: 'Email', property: 'mail' },
    { title: 'Date de Naissance', property: 'date_naissance' },
    { title: 'Ville de Naissance', property: 'ville_naissance' },
    { title: 'Représentant Légal', property: 'representant_legal' },
    { title: 'Pièce d\'Identité Recto', property: 'piece_identite_recto', type: 'file' },
    { title: 'Pièce d\'Identité Verso', property: 'piece_identite_verso', type: 'file' },
    { title: 'Pouvoir', property: 'pouvoir', type: 'file' },
    { title: 'IBAN', property: 'iban', type: 'file' }
];

const form = useForm({
    id: props.commande.id
});

const submit = () => {
    form.processing = true;
    form.post(route('commande.termine'), {
        onFinish: () => window.location.reload(true),
    });
};
</script>


<template>
    <AppLayout :layoutKey="'superadmin'">
        <header class="componentcontainer directionvertical gap16px">
            <div class="w_container justifyspacebetween">
                <Breadcrumb></Breadcrumb>
            </div>
        </header>

        <div class="commandecontainer h-full">
            <div class="componentcontainer height100 alignstretch minmax100">
                <div class="w_container vertical gap16px">
                    <div class="text14px semibold">Informations commande (location)</div>
                    <div class="w_container vertical gap8px white round px-4 py-2">
                        <template v-for="(product, index) in locationProduct" :key="product">
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Nom</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Ref fournisseur</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.ref_fournisseur }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Quantité</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.quantity }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Adresse livraison</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.adresse_livraison.adresse + ', '
                                            + product.adresse_livraison.code_postal + ' '
                                            + product.adresse_livraison.ville + ', ' + product.adresse_livraison.pays
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Destinataire</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.user_livraison.name }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="index < props.commande.products.length - 1" class="separatorhorizontal"></div>
                        </template>
                    </div>

                    <div class="text14px semibold">Informations commande (achat)</div>
                    <div class="w_container vertical gap8px white round px-4 py-2">
                        <template v-for="(product, index) in achatProduct" :key="product">
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Nom</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.name }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Ref fournisseur</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.ref_fournisseur }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Quantité</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.quantity }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Adresse livraison</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.adresse_livraison.adresse + ', '
                                            + product.adresse_livraison.code_postal + ' '
                                            + product.adresse_livraison.ville + ', ' + product.adresse_livraison.pays
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div class="w_container aligncenter">
                                <div class="text14px grey400 _100">Destinataire</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{ product.user_livraison.name }}
                                    </div>
                                </div>
                            </div>
                            <div v-if="index < props.commande.products.length - 1" class="separatorhorizontal"></div>
                        </template>
                    </div>


                    <div class="text14px semibold">Informations entreprise</div>
                    <div class="w_container vertical gap8px white round px-4 py-2">

                        <template v-for="info in entrepriseInfos" :key="info">
                            <div v-if="props.commande.entreprise[info.property]" class="w_container aligncenter">
                                <div class="text14px grey400 _100">{{ info.title }}</div>
                                <div class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{
                                            typeof props.commande.entreprise[info.property] === 'number' ?
                                                (props.commande.entreprise[info.property] ? 'Oui' : 'Non') :
                                                props.commande.entreprise[info.property]
                                        }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="text14px semibold">Informations signataire</div>
                    <div class="w_container vertical gap8px white round px-4 py-2">

                        <template v-for="info in signataireInfos" :key="info">
                            <div v-if="props.commande.signataire[info.property] || info.property == 'representant_legal'"
                                class="w_container aligncenter">
                                <div class="text14px grey400 _100">{{ info.title }}</div>
                                <a :href="props.commande.signataire[info.property].url"
                                    @click.prevent="downloadImage(props.commande.signataire[info.property].url, props.commande.signataire[info.property].name)"
                                    v-if="info.type == 'file'"
                                    class="w_container _100 height40px aligncenter padding12px">
                                    <div class="text14px medium purple">Télécharger</div>
                                    <img class="image16x16px" loading="lazy" width="16" height="16"
                                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg">
                                </a>
                                <div v-else class="w_container justifyspacebetween _100 aligncenter p-2">
                                    <div class="text14px medium">
                                        {{
                                            typeof props.commande.signataire[info.property] === 'boolean' ?
                                                (props.commande.signataire[info.property] ? 'Oui' : 'Non') :
                                                props.commande.signataire[info.property]
                                        }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="componentcontainer height100 alignstretch minmax100">
                <div class="w_container vertical gap16px">
                    <div class="flex justify-between items-center">
                        <div class="text20px unbounded">
                            {{ props.commande.statut }}
                        </div>
                        <div v-if="props.commande.statut == 'Livraison en cours'">
                            <form @submit.prevent="submit">
                                <button type="submit" :disabled="form.processing"
                                    :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                                    <div class="text14px white">
                                        Commande livrée
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="separatorhorizontal"></div>
                    <component :is="selectComponent()"></component>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.commandecontainer {
    width: 100%;
    height: 100%;
    max-height: 100%;
    min-height: 100%;
    grid-column-gap: 8px;
    grid-row-gap: 8px;
    grid-template-rows: auto;
    grid-template-columns: 1fr minmax(60%, 30%);
    grid-auto-columns: 1fr;
    justify-content: center;
    align-items: stretch;
    display: grid;
    overflow: hidden;
}

.w_container.justifyspacebetween._100.height40px {
    height: 30px;
    min-height: 30px;
}

.commandecontainer .componentcontainer {
    overflow: auto;
    max-height: calc(100% - 74px) !important;
    min-height: unset !important;
}
</style>
