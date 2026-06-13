<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AddEntrepriseToLink from '@/Components/AddEntrepriseToLink.vue';

import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const { props } = usePage();

const showLinkEntreprise = ref(false);
const openLinkEntreprise = () => showLinkEntreprise.value = true;
const closeLinkEntreprise = () => showLinkEntreprise.value = false;
</script>

<template>

    <Head>
        <title>Entreprise</title>
    </Head>

    <AppLayout :layoutKey="'superadmin'">
        <div class="maincontainer">
            <div class="componentcontainer directionvertical gap16px" style="height: fit-content;">
                <Link href="/usersAdmin" class="w_container items-center gap-2 cursor-pointer">
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67334d6d958dace9fee303c8_chevron-right.svg"
                    loading="lazy" alt="" class="image12x12px">
                <div class="text14px bold-text">Retour aux entreprises</div>
                </Link>
            </div>

            <div class="dashboardcontainer">
                <div class="dashboard-vertical">
                    <div class="componentcontainer h-full">
                        <div class="text20px unbounded">Entreprise</div>
                    </div>
                </div>
                <div class="dashboard-horizontal">
                    <div class="componentcontainer h-1/2">
                        <div class="flex justify-between">
                            <div class="text20px unbounded">Entreprises liées</div>
                            <div class="lightbutton" @click="openLinkEntreprise">
                            <div class="frame-164">
                                <div class="text14px medium purple">Ajouter</div>
                            </div>
                        </div>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-3 position-relative overflow-auto">
                            <Link :href="'/superadmin/entreprise/' + entreprise.id"
                                v-for="entreprise in props.entreprise.siblings" :key="entreprise.id"
                                class="flex p-3 justify-between items-center cursor-pointer w-full bg-white rounded-lg hover:bg-purple-50">
                            <div class="flex flex-col gap-1">
                                <div class="black text16px">{{ entreprise.raison_sociale }}</div>
                                <div class="text12px gray">SIRET : {{ entreprise.siret }}</div>
                            </div>
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67335656e78806f61b78dd9c_log-in.png"
                                loading="lazy" alt="" class="image24x24px" />
                            </Link>
                        </div>
                    </div>
                    <div class="componentcontainer h-1/2">
                        <div class="text20px unbounded">Commandes liées</div>
                        <div class="mt-6 flex flex-wrap gap-3 position-relative overflow-auto">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <AddEntrepriseToLink :show="showLinkEntreprise" @close="closeLinkEntreprise" />

</template>

<style scoped>
.maincontainer {
    display: flex;
    overflow: auto;
    width: auto;
    flex-flow: column;
    gap: 8px;
}

.actionrapide {
    width: 48%;
    height: 75px;
}

.buttoncircle {
    height: auto;
}

.dashboardcontainer {
    display: flex;
    width: 100%;
    height: 100%;
    justify-content: stretch;
    place-items: stretch start;
    align-self: center;
    gap: 8px;
    height: calc(100vh - 191px);
}

.dashboard-vertical {
    display: flex;
    width: 70%;
    height: 100%;
    flex-flow: column;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
}

.dashboard-horizontal {
    display: flex;
    width: 30%;
    height: 100%;
    flex-flow: column;
    align-items: flex-start;
    gap: 8px;
}

.componentcontainer {
    width: 100%;
    flex-direction: column
}

.dashboardmenu {
    margin-top: 16px;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.horizontal {
    display: flex;
    width: auto;
    height: auto;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    border-style: none;
    border-width: 1px;
    border-color: black;
}

.tab.selected {
    color: var(--main);
    border-bottom: 2px solid var(--main);
    line-height: 12px;
}

.card_container {
    display: flex;
    margin-top: 16px;
    flex-flow: column;
    gap: 12px;
    height: 100%;
    overflow: auto;
}

.card {
    cursor: pointer;
    width: 100%;
    margin-top: 0px;
    padding: 12px 24px;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    background-color: var(--white);
}

.tagcontainer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}
</style>
