<script setup>
import CreateStack from "./CreateStack.vue";
import CreateCollaborateur from "@/Components/CreateCollaborateur.vue";
import CreateSupport from "@/Components/CreateSupport.vue";

import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();

const data = defineProps({
    section: String
});

const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => showCreate.value = true;

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;

const titreCreateCollaborateur = ref(null);
const typeCreateCollaborateur = ref(null);
const showCreateCollaborateur = ref(false);
const closeCreateCollaborateur = () => showCreateCollaborateur.value = false;
const openCreateCollaborateur = (typeCurrent) => {
    showCreateCollaborateur.value = true;
    typeCreateCollaborateur.value = typeCurrent;
    titreCreateCollaborateur.value = typeCurrent === 'Personne' ? 'Ajouter un nouveau collaborateur' : 'Ajouter une nouvelle salle';
}

const emit = defineEmits(['updateCurrentTicket']);
const updateCurrentTicket = (ticket) => {
    emit('updateCurrentTicket', ticket);
}
const updateCurrentTicketCreated = (ticket) => updateCurrentTicket(ticket);

const superadmin = window.location.pathname.includes('Admin');
</script>

<template>
    <div class="componentcontainer height100">
        <div class="w_container vertical aligncenter">
            <!-- Panier vide -->
            <div v-if="section == 'panier'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Votre panier est actuellement vide.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Votre panier vous permet de rassembler les produits que vous souhaitez avant de finaliser votre
                        commande. Pour commencer à ajouter des produits, explorez notre catalogue en cliquant sur le
                        bouton ci-dessous.
                    </div>
                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </Link>
                </div>
            </div>

            <!-- mon catalogue vide -->
            <div v-else-if="section == 'mon-catalogue'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Votre catalogue Manystacks est vide.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Votre catalogue personnel vous permet de rassembler les articles qui vous intéressent
                    </div>
                    <div class="text14px aligncenter flex">
                        Cliquez simplement sur l'icône
                        <img class="px-1" src="/images/favoris_icon_off.svg" alt="">
                        lorsque vous sélectionnez un produit
                    </div>
                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </Link>
                </div>
            </div>

            <!-- mes stacks vide -->
            <div v-else-if="section == 'mes-stacks'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Votre collection de stacks est vide.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Les "stacks" sont conçues pour simplifier vos commandes groupées en vous permettant de créer des
                        ensembles personnalisés adaptés aux besoins de votre équipe. Commencez dès maintenant à créer
                        votre première stack pour optimiser vos commandes futures.
                    </div>
                </div>
                <div class="bigbutton purple" @click="openCreate">
                    <div class="text14px white">Créer une stack</div>
                </div>
            </div>

            <!-- stack vide -->
            <div v-else-if="section == 'stack'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucun produit dans votre stack.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Les stacks vous permettent d'organiser et de commander facilement des ensembles de produits pour
                        votre équipe. Pour ajouter des produits à une stack existante, utilisez l'option
                        <span class="text14px medium">"Ajouter à une stack"</span>
                        lorsque vous sélectionnez un produit depuis le catalogue.
                    </div>
                </div>
                <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                <div class="text14px medium purple">Accédez au catalogue</div>
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                </Link>
            </div>

            <!-- mes commandes vide -->
            <div v-else-if="section == 'mes_commandes'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucune commande en cours</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici toutes vos commandes en cours. Pour commencer à passer une commande,
                        vous pouvez explorer notre catalogue en cliquant sur le bouton ci-dessous.
                    </div>
                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </Link>
                </div>
            </div>

            <!-- mes contrats vide -->
            <div v-else-if="section == 'mes_contrats'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucun contrat.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici tous vos contrats en cours, une fois vos commandes finalisées. Pour
                        commencer à passer une commande, vous pouvez explorer notre catalogue en cliquant sur le bouton
                        ci-dessous.
                    </div>
                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </Link>
                </div>
            </div>

            <!-- mes equipements vide -->
            <div v-else-if="section == 'mes_equipements'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucun équipement.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici tous vos équipements commandés afin de les attribuer à vos salles et
                        à votre équipe. Pour commencer à passer une commande, vous pouvez explorer notre catalogue en
                        cliquant sur le bouton ci-dessous.
                    </div>
                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </Link>
                </div>
            </div>

            <!-- mes salles vide -->
            <div v-else-if="section == 'mes_salles'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucune salle.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici toutes vos salles afin de leur attribuer de l'équipement.
                    </div>
                    <div @click="openCreateCollaborateur('Salle')" v-if="props.userAuth.role != 'collaborateur'"
                        class="w_container aligncenter gap4px cursor-pointer">
                        <div class="text14px medium purple">Ajouter des salles</div>
                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                            loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </div>
                </div>
            </div>

            <!-- mon équipe vide -->
            <div v-else-if="section == 'mon_equipe'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucun collaborateur.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici toute votre équipe afin de leur attribuer de l'équipement.
                    </div>
                    <div @click="openCreateCollaborateur('Personne')" v-if="props.userAuth.role != 'collaborateur'"
                        class="w_container aligncenter gap4px cursor-pointer">
                        <div class="text14px medium purple">Ajouter des collaborateurs</div>
                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                            loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    </div>
                </div>
            </div>

            <!-- support vide -->
            <div v-else-if="section == 'support'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucun ticket.</div>
                <template v-if="!superadmin">
                    <div class="frame-34">
                        <div class="text14px aligncenter">
                            Vous pouvez retrouver ici tous vos tickets de support, que ce soit pour poser une question
                            ou
                            signaler un problème. Pour créer un nouveau ticket, veuillez cliquer sur le bouton
                            ci-dessous.
                        </div>
                        <div @click="openCreateSupport" class="w_container aligncenter gap4px cursor-pointer">
                            <div class="text14px medium purple">Créer un ticket</div>
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                loading="lazy" width="16" height="16" alt="" class="image16x16px">
                        </div>
                    </div>
                </template>
            </div>

            <!-- commande admin vide -->
            <div v-else-if="section == 'admincommande'" class="w_container vertical aligncenter gap12px empty">
                <div class="frame-34">
                    <div class="text20px unbounded">
                        Il n'y a aucune commande en cours pour l'instant !
                    </div>
                </div>
            </div>

            <!-- users admin vide -->
            <div v-else-if="section == 'usersadmin'" class="w_container vertical aligncenter gap12px empty">
                <div class="frame-34">
                    <div class="text20px unbounded">
                        Il n'y a aucun utilisateur pour l'instant !
                    </div>
                </div>
            </div>

            <!-- mes licences vide -->
            <div v-else-if="section == 'mes_licences'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez aucune licence.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pourrez retrouver ici toutes vos licences en cours. Pour commencer à passer une commande,
                        vous
                        pouvez explorer notre catalogue en cliquant sur le bouton ci-dessous.
                    </div>
                    <Link :href="'/catalogue/licences/licences'" class="w_container aligncenter gap4px">
                    <div class="text14px medium purple">Accédez au catalogue</div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16">
                    </Link>
                </div>
            </div>

            <!-- licences attribution vide -->
            <div v-else-if="section == 'licences'" class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Vous n'avez plus de sièges disponibles sur cette licence.</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Vous pouvez augmenter le nombre de sièges disponibles en commandant une nouvelle licence
                        directement depuis le bouton "Ajouter des licences" en haut à droite de la page.
                    </div>
                </div>
            </div>

            <!-- composant à venir -->
            <div v-else class="w_container vertical aligncenter gap12px empty">
                <div class="text20px unbounded">Composant à venir</div>
                <div class="frame-34">
                    <div class="text14px aligncenter">
                        Ce composant est en cours de développement, il sera bientôt disponible.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <CreateStack :show="showCreate" @closeCreate="closeCreate"></CreateStack>
    <CreateCollaborateur :from="'index'" :type="typeCreateCollaborateur" :titre="titreCreateCollaborateur"
        :show="showCreateCollaborateur" @closeCreateCollaborateur="closeCreateCollaborateur">
    </CreateCollaborateur>
    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
        @updateCurrentTicketCreated="updateCurrentTicketCreated">
    </CreateSupport>
</template>

<style scoped>
.componentcontainer.height100 {
    align-items: center !important;
}
</style>
