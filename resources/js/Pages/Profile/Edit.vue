<script setup>
import { ref, onMounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

import AppLayout from "@/Layouts/AppLayout.vue";
import MyAccount from "./Partials/MyAccount.vue";
import MyAdresses from "./Partials/MyAdresses.vue";
import MyCompany from "./Partials/MyCompany.vue";
import MyIntegrations from './Partials/MyIntegrations.vue';
import MDM from './Partials/MDM.vue';
// import MyCollaborateurs from "./Partials/MyCollaborateurs.vue";
// import MySalles from "./Partials/MySalles.vue";

import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const currentPage = ref('');

onMounted(() => {
    const path = window.location.pathname;
    if (path.includes('mon-compte')) {
        currentPage.value = 'mon-compte';
    } else if (path.includes('mes-adresses')) {
        currentPage.value = 'mes-adresses';
    } else if (path.includes('mon-entreprise')) {
        currentPage.value = 'mon-entreprise';
    } else if (path.includes('mes-integrations')) {
        currentPage.value = 'mes-integrations';
    } else if (path.includes('mdm')) {
        currentPage.value = 'mdm';
    }
    // else if (path.includes('mes-collaborateurs')) {
    //     currentPage.value = 'mes-collaborateurs';
    // } else if (path.includes('mes-salles')) {
    //     currentPage.value = 'mes-salles';
    // }
});

const isAccountPage = computed(() => currentPage.value === 'mon-compte');
const isCompanyPage = computed(() => currentPage.value === 'mon-entreprise');
const isAddressesPage = computed(() => currentPage.value === 'mes-adresses');
const isIntegrationsPage = computed(() => currentPage.value === 'mes-integrations');
const isMDMPage = computed(() => currentPage.value === 'mdm');
// const iscollaborateurPage = computed(() => currentPage.value === 'mes-collaborateurs');
// const isSallesPage = computed(() => currentPage.value === 'mes-salles');
</script>

<template>
    <AppLayout :layoutKey="props.userAuth.role">
        <div class="commandecontainer">
            <div class="w_container vertical">
                <div class="componentcontainer height100">
                    <div class="flex flex-col justify-between h-full w-full">
                        <div class="w_container vertical height100 gap12px">
                            <div class="text20px unbounded">Réglages</div>
                            <div class="separatorhorizontal"></div>
                            <div class="w_container vertical">
                                <Link :href="'/profile/mon-compte'" class="selectpage"
                                    :class="{ 'active': isAccountPage }">
                                <div class="text14px medium" :class="{ 'purple': isAccountPage }">Mon compte</div>
                                </Link>
                                <template v-if="props.userAuth.role === 'admin'">
                                    <Link :href="'/profile/mon-entreprise'" class="selectpage"
                                        :class="{ 'active': isCompanyPage }">
                                    <div class="text14px medium" :class="{ 'purple': isCompanyPage }">Mon entreprise
                                    </div>
                                    </Link>
                                    <Link :href="'/profile/mes-adresses'" class="selectpage"
                                        :class="{ 'active': isAddressesPage }">
                                    <div class="text14px medium" :class="{ 'purple': isAddressesPage }">Mes adresses
                                    </div>
                                    </Link>
                                    <Link :href="'/profile/mes-integrations'" class="selectpage"
                                        :class="{ 'active': isIntegrationsPage }">
                                    <div class="text14px medium" :class="{ 'purple': isIntegrationsPage }">Mes
                                        intégrations
                                    </div>
                                    </Link>
                                    <Link :href="'/profile/mdm'" class="selectpage" :class="{ 'active': isMDMPage }">
                                    <div class="text14px medium" :class="{ 'purple': isMDMPage }">MDM</div>
                                    </Link>
                                </template>
                                <!-- <Link :href="'/profile/mes-collaborateurs'" class="selectpage"
                                :class="{ 'active': iscollaborateurPage }">
                            <div class="text14px medium" :class="{ 'purple': iscollaborateurPage }">Mes collaborateurs</div>
                            </Link>
                            <Link :href="'/profile/mes-salles'" class="selectpage"
                                :class="{ 'active': isSallesPage }">
                            <div class="text14px medium" :class="{ 'purple': isSallesPage }">Mes salles</div>
                            </Link> -->
                            </div>
                        </div>

                        <div>
                            <a class="underline text-blue-500"
                                href="https://www.manystacks.co/legal/politique-de-confidentialite">Politique de
                                confidentialité</a>
                        </div>
                    </div>
                </div>
                <Link :href="route('logout')" method="post" as="button" class="bigbutton purple">
                <div class="w_container aligncenter gap8px"><img
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a28224fe36fd976fb72ee8_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    <div class="text14px white">Se déconnecter</div>
                </div>
                </Link>
            </div>

            <MyAccount v-if="isAccountPage" />
            <MyCompany v-else-if="isCompanyPage" />
            <MyAdresses v-else-if="isAddressesPage" />
            <MyIntegrations v-else-if="isIntegrationsPage" />
            <MDM v-else-if="isMDMPage" />
            <!-- <MyCollaborateurs v-else-if="iscollaborateurPage" />
            <MySalles v-else-if="isSallesPage" /> -->
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
    grid-template-columns: 1fr minmax(70%, 30%);
    grid-auto-columns: 1fr;
    justify-content: center;
    align-items: stretch;
    display: grid;
    overflow: hidden;
}

@media (max-width: 991px) {
    .commandecontainer {
        grid-template-columns: 1fr;
        /* Une seule colonne pour une disposition horizontale */
        grid-template-rows: auto auto;
        /* Lignes ajustées automatiquement */
    }
}
</style>
