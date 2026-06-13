<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import EmptyMessage from '@/Components/EmptyMessage.vue';

import { usePage, Link } from '@inertiajs/vue3';
import { formattedDate, slugify } from '@/functions';

const { props } = usePage();

const statutClass = (statut) => 'statut-' + statut.replace(/\s+/g, '-').toLowerCase();
</script>

<template>
    <DashboardLayout>
        <div style="height: 80vh;">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-2">
                <div class="dashboardcontainer vertical" style="height: calc(100vh - 304px);">
                    <div class="containertitle-icon horizontal justify-between">
                        <div class="text20px unbounded">Mes Licences</div>
                    </div>
                    <div class="container-pe h-1/2 overflow-auto">
                        <div class="containerml-pe flex justify-between">
                            <div class="text14px pb-3">Mensualité :</div>
                            <div class="text14px bold-text">{{ props.count.prix_licences_month.toFixed(2) }}€/mois</div>
                        </div>
                        <div class="vertical" v-if="props.licencesMonth.length">
                            <Link :href="'mes-licences/' + licence.slug" class="component-card"
                                v-for="licence in props.licencesMonth">
                            <div class="image_container small">
                                <img :src="licence.image_principale" loading="lazy" alt="">
                            </div>
                            <div class="component-card-content flex-col" style="align-items: baseline;">
                                <div class="flex gap-2">
                                    <div class="text14px bold-text">{{ licence.name }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="text14px bold-text gray">{{ licence.prix_u }}€</div>
                                    <div class="divider_gray"></div>
                                    <div class="text14px">Quantité : {{ licence.total }}</div>
                                </div>
                            </div>
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                alt="" class="image24x24px">
                            </Link>
                        </div>
                        <EmptyMessage v-else :value="'Aucune licence sur votre tenant.'" class="static" />
                    </div>

                    <div class="container-pe h-1/2 overflow-auto">
                        <div class="containerml-pe flex justify-between">
                            <div class="text14px pb-3">Annuité :</div>
                            <div class="text14px bold-text">{{ props.count.prix_licences_year.toFixed(2) }}€/an</div>
                        </div>
                        <div class="vertical" v-if="props.licencesYear.length">
                            <Link :href="'mes-licences/' + licence.slug" class="component-card"
                                v-for="licence in props.licencesYear">
                            <div class="image_container small">
                                <img :src="licence.image_principale" loading="lazy" alt="">
                            </div>
                            <div class="component-card-content flex-col" style="align-items: baseline;">
                                <div class="flex gap-2">
                                    <div class="text14px bold-text">{{ licence.name }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="text14px bold-text gray">{{ licence.prix_u }}€</div>
                                    <div class="divider_gray"></div>
                                    <div class="text14px">Quantité : {{ licence.total }}</div>
                                </div>
                                <div class="text14px">Prochaine échéance :
                                    <span class="bold-text" v-if="licence.echeance > 0">{{ licence.echeance }} mois</span>
                                    <span class="bold-text" v-else>Ce mois-ci</span>
                                </div>
                            </div>
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                alt="" class="image24x24px">
                            </Link>
                        </div>
                        <EmptyMessage v-else :value="'Aucune licence sur votre tenant.'" class="static" />
                    </div>
                </div>

                <div class="vertical" style="height: calc(100vh - 304px);">
                    <div class="dashboardcontainer">
                        <div class="containertitle-icon horizontal justify-between">
                            <div class="text20px unbounded">Équipements</div>
                        </div>
                        <div class="flex flex-col gap-2" style="height: 90%;">
                            <div class="container-pe h-1/2 overflow-auto">
                                <div class="containerml-pe justify-between flex">
                                    <div class="text14px pb-3">Mensualité :</div>
                                    <div class="text14px bold-text">{{ props.count.prix_equipements_location.toFixed(2)
                                        }}€/mois
                                    </div>
                                </div>
                                <div class="vertical" v-if="props.equipements_location.length">
                                    <Link :href="'mes-equipements/' + equipement.id" class="component-card"
                                        v-for="equipement in props.equipements_location">
                                    <div class="image_container small">
                                        <img :src="equipement.image_principale" loading="lazy" alt="" class="contain">
                                    </div>
                                    <div class="component-card-content flex-col" style="align-items: baseline;">
                                        <div class="flex gap-2">
                                            <div class="text14px">{{ equipement.name }}</div>
                                        </div>
                                        <div class="flex  gap-2">
                                            <div class="text14px bold-text gray">{{ equipement.prix_u }}€</div>
                                            <div class="divider_gray"></div>
                                            <div class="text14px">Quantité : {{ equipement.total }}</div>
                                        </div>
                                    </div>
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                        alt="" class="image24x24px">
                                    </Link>
                                </div>

                                <EmptyMessage v-else :value="'Aucun équipement en location sur votre compte.'"
                                    class="static" />
                            </div>

                            <div class="container-pe h-1/2 overflow-auto">
                                <div class="containerml-pe justify-between flex">
                                    <div class="text14px pb-3">Derniers achats :</div>
                                    <div class="text14px bold-text">{{ props.count.prix_equipements_achat.toFixed(2)
                                        }}€
                                    </div>
                                </div>
                                <div class="vertical" v-if="props.equipements_achat.length">
                                    <Link :href="'mes-equipements/' + equipement.id" class="component-card"
                                        v-for="equipement in props.equipements_achat">
                                    <div class="image_container small">
                                        <img :src="equipement.image_principale" loading="lazy" alt="">
                                    </div>
                                    <div class="component-card-content flex-col" style="align-items: baseline;">
                                        <div class="flex gap-2">
                                            <div class="text14px">{{ equipement.name }}</div>
                                        </div>
                                        <div class="flex  gap-2">
                                            <div class="text14px bold-text gray">{{ equipement.prix_u }}€</div>
                                            <div class="divider_gray"></div>
                                            <div class="text14px">Quantité : {{ equipement.total }}</div>
                                        </div>
                                    </div>
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                        alt="" class="image24x24px">
                                    </Link>
                                </div>

                                <EmptyMessage v-else :value="'Aucun équipement acheté ce mois-ci.'"
                                    class="static" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="vertical" style="height: calc(100vh - 304px);">
                    <div class="dashboardcontainer">
                        <div class="containertitle-icon horizontal">
                            <div class="text20px unbounded">Onboardings &amp; Offboardings</div>
                            <div class="prochainement">Prochainement</div>
                        </div>
                        <div class="horizontal">
                            <div class="containeronboarding">
                                <div class="horizontal">
                                    <div class="flex  gap-2">
                                        <div class="text14px">Onboardings :</div>
                                    </div>
                                    <div class="flex  gap-2">
                                        <div class="text14px bold-text">3</div>
                                    </div>
                                </div>
                            </div>
                            <div class="containeronboarding">
                                <div class="horizontal">
                                    <div class="flex  gap-2">
                                        <div class="text14px">Offboardings :</div>
                                    </div>
                                    <div class="flex  gap-2">
                                        <div class="text14px bold-text">1</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="dashboardcontainer" style="overflow: scroll;">
                        <div class="containertitle-icon horizontal">
                            <div class="text20px unbounded">Dernières commandes</div>
                        </div>
                        <div class="container-pe">
                            <div class="vertical" v-if="props.commandes.length">
                                <Link :href="'mes-commandes/' + commande.reference_commande" class="component-card"
                                    v-for="commande in props.commandes">
                                <div class="image_container small">
                                    <img :src="commande.commande_products[0].image_principale" class="contain" loading="lazy" alt="">
                                </div>
                                <div class="component-card-content">
                                    <div class="flex gap-2 flex-col">
                                        <div class="text14px">#<span class="bold-text">{{
                                            commande.reference_commande }}</span>
                                        </div>
                                        <div class="text14px-2 gray">{{ commande.commande_products.length }}
                                            produit(s)</div>
                                    </div>
                                    <div class="flex  gap-2">
                                        <div class="tagblock" :class="statutClass(commande.statut)">
                                            <div class="dot"></div>
                                            <div>{{ commande.statut.split(' ').pop() }}</div>
                                        </div>
                                    </div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    class="image24x24px">
                                </Link>
                            </div>

                            <EmptyMessage v-else :value="'Aucune commande passée dernièrement.'" class="static" />
                        </div>
                    </div>
                    <!-- <div class="dashboardcontainer" style="overflow: scroll;">
                        <div class="containertitle-icon horizontal">
                            <div class="text20px unbounded">Dernières demmandes</div>
                            <div class="prochainement">Prochainement</div>
                        </div>
                        <div class="container-pe">
                            <div class="vertical">
                                <div class="component-card horizontal">
                                    <img width="60" alt=""
                                        src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67092669f6431f0a8cd2dfd0_image%207.png">
                                    <div class="component-card-content">
                                        <div class="flex gap-2 flex-col">
                                            <div class="text14px">Apple iPhone 15 Pro
                                            </div>
                                            <div class="text14px-2 gray">Demandé le 14/09/2024</div>
                                        </div>
                                        <div class="flex  gap-2">
                                            <div class="horizontal gap8px">
                                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67092d6cbc85ffebbc322881_Rectangle%2017.png"
                                                    width="43" alt="" class="avatarcontainer_small">
                                                <div class="">Carla Dupont</div>
                                            </div>
                                        </div>
                                    </div>
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                        alt="" class="image28x28px">
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="dashboardcontainer" style="overflow: scroll;">
                        <div class="containertitle-icon horizontal">
                            <div class="text20px unbounded">Assistance et sécurité</div>
                        </div>
                        <div class="container-pe">
                            <div class="vertical" v-if="props.supports.length">
                                <Link :href="'supports/' + support.id" v-for="support in props.supports"
                                    class="component-card horizontal" style="justify-content: space-between;height: auto;padding: 14px;">
                                <div class="vertical">
                                    <div class="text14px">Ticket n°{{ support.numero_support }}</div>
                                    <div class="text12px gray">Crée le {{ formattedDate(support.created_at) }}</div>
                                    <div class="text12px gray">{{ support.user.name }}</div>
                                </div>
                                <div class="tagblock" :class="slugify(support.status)">
                                    <div class="dot"></div>
                                    <div>{{ support.status }}</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    alt="" class="image28x28px">
                                </Link>
                            </div>

                            <EmptyMessage v-else :value="'Aucun ticket sur votre compte.'" class="static" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </DashboardLayout>
</template>

<style scoped>
.componentcontainer {
    display: flex;
    overflow: visible;
    padding: 20px;
    flex-direction: row;
    align-items: stretch;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--grey-100);
    border-right-color: var(--grey-100);
    border-bottom-color: var(--grey-100);
    border-left-color: var(--grey-100);
    border-radius: 12px;
    background-color: var(--grey-50);
}

.component-card {
    display: flex;
    width: auto;
    margin-bottom: 12px;
    padding: 14px 20px;
    justify-content: flex-start;
    align-items: center;
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
    gap: 20px;
    border: 1px solid #fff;
    cursor: pointer;
}

.component-card:hover {
    border: 1px solid var(--grey-200);
}

.component-card-2 {
    display: flex;
    width: auto;
    height: 100px;
    margin-bottom: 12px;
    padding: 0px 20px;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
}

.component-card-content {
    display: flex;
    width: 100%;
    height: auto;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.componentcontainer.directionvertical {
    flex-direction: column;
}

.componentcontainer.directionvertical.gap16px {
    display: flex;
    gap: 16px;
}

.horizontal {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}

.horizontal.gap8px {
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
}

.dashboardcontainer {
    border-radius: 12px;
    background-color: rgb(247, 248, 249);
    height: 100%;
    padding: 0px 20px 12px;
    border: 1px solid #fff;
    position: relative;
}

.containertitle-icon {
    padding: 20px 0 24px;
}

.containertitle-icon.horizontal {
    justify-content: space-between;
    align-items: center;
}

.vertical {
    display: flex;
    flex-flow: column;
    gap: 8px;
}

.containeronboarding {
    display: flex;
    margin-bottom: 12px;
    padding: 20px 20px;
    justify-content: flex-start;
    align-items: center;
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
}
</style>
