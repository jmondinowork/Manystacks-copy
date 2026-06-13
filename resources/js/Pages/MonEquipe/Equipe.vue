<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserAttribution from '@/Components/UserAttribution.vue';
import CreateEmailPro from '@/Components/CreateEmailPro.vue';
import EmptyMessage from '@/Components/EmptyMessage.vue';
import Offboarding from '@/Components/Offboarding.vue';
import CreateSupport from "@/Components/CreateSupport.vue";
import UserInformationsPersonnelles from '@/Components/UserInformationsPersonnelles.vue';
import RetirerAttribution from '@/Components/RetirerAttribution.vue';

import { usePage, Link } from '@inertiajs/vue3';
import { useStore } from "vuex";
import { userInitials, slugify, pastDate, navigateTo, formattedDate } from '@/functions';
import { ref, computed, onMounted } from 'vue';

const { props } = usePage();
const store = useStore();

const currentProduct = ref({});
const showRetirerAttribution = ref(false);
const closeRetirerAttribution = () => showRetirerAttribution.value = false;
const retierAttribution = (product) => {
    currentProduct.value = product;
    showRetirerAttribution.value = true;
}

const equipements = computed(() =>
    props.attribution.commande_products.filter(equipement => equipement.sous_categorie !== 'licences')
);
const equipementsTab = ref('tous');
const equipementsTabs = [
    { value: 'tous', label: 'Tous les équipements :' },
    // { value: 'en_service', label: 'En service' },
    // { value: 'en_reparation', label: 'En réparation' },
];
const changeEquipementsTab = (tab) => {
    equipementsTab.value = tab;
}

const licences = computed(() =>
    props.attribution.commande_products.filter(licence => licence.sous_categorie === 'licences' && licence.status === 'active')
);
const licencesTab = ref('tous');
const licencesTabs = [
    { value: 'tous', label: 'Toutes les licences :' },
    // { value: 'saas', label: 'Saas' },
    // { value: 'sur_cloud', label: 'Sur le cloud' },
];
const changeLicencesTab = (tab) => {
    licencesTab.value = tab;
}

const title = ref('équipements');
const equipement_available = ref([]);
const showAttribution = ref(false);
const closeAttribution = () => showAttribution.value = false;
const openAttribution = (type) => {
    if (type == 'equipements') {
        title.value = "équipements";
        equipement_available.value = props.equipement_available;
    }
    else {
        title.value = "licences";
        equipement_available.value = props.licence_available;
    }

    showAttribution.value = true;
}

onMounted(() => {
    if (localStorage.getItem('attribution')) {
        store.dispatch('updateAnnounce', localStorage.getItem('attribution'));
        localStorage.removeItem('attribution');
    }
    if (localStorage.getItem('offboarding')) {
        store.dispatch('updateAnnounce', localStorage.getItem('offboarding'));
        localStorage.removeItem('offboarding');
    }
    if (localStorage.getItem('edited')) {
        store.dispatch('updateAnnounce', localStorage.getItem('edited'));
        localStorage.removeItem('edited');
    }
});
const updateAttribution = (product_id, action) => {
    let user_id = props.attribution.id;

    axios.post('/api/updateAttribution', { user_id, product_id, action })
        .then((response) => {
            localStorage.setItem('attribution', response.data.message);
            window.location.reload();
        })
        .catch((e) => {
            store.dispatch('updateErrorAnnounce', e.response.data.message);
        });
}

const showOnboarding = ref(false);
const showOffboarding = ref(false);
const openOnboarding = () => showOnboarding.value = true;
const openOffboarding = () => showOffboarding.value = true;
const closeOnboarding = () => showOnboarding.value = false;
const closeOffboarding = () => showOffboarding.value = false;

const showUserInformationsPersonnelles = ref(false);
const openUserInformationsPersonnelles = () => showUserInformationsPersonnelles.value = true;
const closeUserInformationsPersonnelles = () => showUserInformationsPersonnelles.value = false;

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;
</script>

<template>
    <AppLayout>
        <div class="maincontainer">
            <div class="componentcontainer directionvertical gap16px" style="height: fit-content;">
                <Link href="/mon-equipe" class="w_container items-center gap-2 cursor-pointer">
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67334d6d958dace9fee303c8_chevron-right.svg"
                    loading="lazy" alt="" class="image12x12px">
                <div class="text14px bold-text">Retour à mon équipe</div>
                </Link>
                <div class="w_container gap-2">
                    <div class="flex px-6 py-3 justify-between items-center rounded-lg bg-white w-4/5">
                        <div class="flex justify-between items-center gap-4">
                            <div class="image_container small">
                                <img v-if="props.attribution.profile_img" :src="props.attribution.profile_img"
                                    class="rounded-full" loading="lazy" alt="">
                                <div v-else class="avatarcircle">
                                    <div class="text40px white">{{ userInitials(props.attribution.name) }}</div>
                                </div>
                            </div>

                            <div>
                                <div class="text14px unbounded">{{ props.attribution.name }}</div>
                                <div class="text12px gray">{{ props.attribution.email }}</div>
                            </div>

                            <div class="small-black-divider"></div>

                            <div class="w_container gap12px items-center">
                                <div v-for="tag in props.attribution.tags.slice(0, 2)" :key="tag.id"
                                    class="tagblock w-fit cursor-pointer"
                                    :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                    <div class="texttag">
                                        {{ tag.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="relative group inline-block">
                            <img v-if="props.attribution.tenant_name === 'microsoft'" src="/images/Microsoft_logo.svg"
                                alt="Logo Microsoft" class="w-5 h-5" loading="lazy">
                            <img v-else-if="props.attribution.tenant_name === 'google'" src="/images/google-logo.png"
                                alt="Logo Google" class="w-5 h-5" loading="lazy">
                            <img v-else-if="props.attribution.tenant_name === 'waiting'"
                                src="/images/tenant_waiting.png" alt="En attente de synchronisation" class="w-7 h-7"
                                loading="lazy">
                            <img v-else src="/images/unsynchronise-icon.svg" alt="Tenant non synchronisé"
                                class="w-5 h-5" loading="lazy">

                            <div
                                class="tooltip absolute z-50 shadow-lg bg-white rounded-lg opacity-0 top-full mt-2 right-0 w-96 pointer-events-none">
                                <template v-if="props.attribution.tenant_name === 'microsoft'">
                                    <div class="p-2 rounded-lg" style="background-color: var(--green-light);">
                                        <div style="color: var(--green);">
                                            Cet utilisateur est synchronisé avec votre Microsoft 365.
                                        </div>
                                    </div>
                                </template>
                                <template v-else-if="props.attribution.tenant_name === 'google'">
                                    <div class="p-2 rounded-lg" style="background-color: var(--green-light);">
                                        <div style="color: var(--green);">
                                            Cet utilisateur est synchronisé avec votre Google Workspace.
                                        </div>
                                    </div>
                                </template>
                                <template v-else-if="props.attribution.tenant_name === 'waiting'">
                                    <div class="bg-gray-200 p-2 rounded-lg">
                                        <div>
                                            Dès qu'une licence GWS sera activée, la synchronisation de cet utilisateur
                                            sera
                                            effectuée, et son adresse mail sera créée.
                                        </div>
                                        <div class="separatorhorizontal my-2"></div>
                                        <div v-if="props.licences_hold" class="card d_container-row cols-2">
                                            <div class="flex gap-4 items-center">
                                                <img :src="props.licences_hold.image_principale" class="w-5 h-5"
                                                    loading="lazy" alt="">

                                                <div class="text12px">{{ props.licences_hold.name }}</div>
                                            </div>
                                            <div class="w_container gap-2 justify-end">
                                                <div class="tagblock en-attente">
                                                    <div class="dot"></div>
                                                    <div class="text12px">En attente</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else>
                                            <div class="text14px">Aucune licence en attente d'activation.</div>
                                            <div class="text14px">
                                                Passer commande sur le catalogue d'une licence Google Workspace pour
                                                activer cet utilisateur.
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="p-2 rounded-lg" style="background-color: var(--red-light);">
                                        <div style="color: var(--red);">
                                            Cet utilisateur n'est pas synchronisé avec votre tenant.
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                    <div class="w_container gap12px w-1/5">
                        <div class="buttoncircle flex-horizontal w-full">
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670914906f34a25480ade8d8_Frame%2035-4.png"
                                loading="lazy" alt="" class="image52x52px">
                            <div class="flex-vertical">
                                <div class="text24px bold-text">{{ props.cout_total.toFixed(2) }}€</div>
                                <div class="text12px grey">Coût collaborateur mensuel</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboardcontainer">
                <div class="dashboard-vertical">
                    <div class="componentcontainer h-1/2">
                        <div class="text20px unbounded">Équipements</div>
                        <div class="horizontal dashboardmenu">
                            <div class="horizontal">
                                <div v-for="tab in equipementsTabs" :key="tab.value"
                                    class="text14px grey400 cursor-pointer tab"
                                    :class="{ 'selected': equipementsTab === tab.value }"
                                    @click="changeEquipementsTab(tab.value)">
                                    {{ tab.label }}
                                </div>
                            </div>
                            <div class="horizontal gap8px">
                                <!-- <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e9770c537d43bfff25_Search-button.png"
                                    loading="lazy" alt="" class="image32x32px clickable">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e94bf30de76454347e_Filter-button.png"
                                    loading="lazy" alt="" class="image32x32px clickable"> -->
                                <div @click="openAttribution('equipements')" class="button ajouter w-button">Ajouter des
                                    équipements
                                </div>
                            </div>
                        </div>

                        <div class="card_container">
                            <div v-if="equipements.length" v-for="equipement in equipements"
                                @click="navigateTo('/mes-equipements/' + equipement.id)"
                                class="card d_container-row cols-4">
                                <div class="w_container gap-2">
                                    <div class="image_container tiny">
                                        <img :src="equipement.image_principale" class="contain" loading="lazy" alt="">
                                    </div>

                                    <div>
                                        <div class="text14px unbounded">{{ equipement.name }}</div>
                                        <div class="text12px gray">N° série : {{ equipement.numero_unique }}</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-end">
                                    <div class="tagblock" :class="slugify(equipement.status)">
                                        <div class="dot"></div>
                                        <div>{{ equipement.status }}</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-start">
                                    <div class="small-black-divider"></div>
                                    <div class="horizontal gap4px">
                                        <div class="text10px">Prix :</div>
                                        <div class="w_container items-center justify-left gap-2 text12px nowrap medium"
                                            style="color: var(--red);" v-if="!equipement.prix">
                                            <span><img class=" w-4" src="/images/warning_red.svg" alt=""></span>
                                            Prix non renseigné
                                        </div>
                                        <div class="text10px bold-text">{{ equipement.prix }}€</div>
                                        <div v-if="equipement.type_contrat == 'achat'" class="text12px gray">Achat
                                        </div>
                                        <div v-else class="text12px gray">/mois</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-end">
                                    <div @click="(event) => { event.stopPropagation(); retierAttribution(equipement) }"
                                        class="lightbutton">
                                        <div class="text14px medium purple nowrap">Retirer</div>
                                    </div>
                                </div>
                            </div>

                            <EmptyMessage v-else :value="'Aucun équipement attribué à cet utilisateur.'"
                                class="static" />
                        </div>
                    </div>
                    <div class="componentcontainer h-1/2">
                        <div class="text20px unbounded">Licences</div>
                        <div class="horizontal dashboardmenu">
                            <div class="horizontal">
                                <div v-for="tab in licencesTabs" :key="tab.value"
                                    class="text14px grey400 cursor-pointer tab"
                                    :class="{ 'selected': licencesTab === tab.value }"
                                    @click="changeLicencesTab(tab.value)">
                                    {{ tab.label }}
                                </div>
                            </div>
                            <div class="horizontal gap8px">
                                <!-- <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e9770c537d43bfff25_Search-button.png"
                                    loading="lazy" alt="" class="image32x32px clickable">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e94bf30de76454347e_Filter-button.png"
                                    loading="lazy" alt="" class="image32x32px clickable"> -->
                                <div v-if="props.attribution.tenant_name !== 'google'"
                                    @click="openAttribution('licences')" class="button ajouter w-button">Ajouter une
                                    licence</div>
                            </div>
                        </div>
                        <div class="card_container">
                            <div v-if="licences.length" v-for="licence in licences"
                                @click="navigateTo('/mes-licences/' + licence.slug)"
                                class="card d_container-row cols-4">
                                <div class="w_container gap-2">
                                    <div class="image_container tiny">
                                        <img :src="licence.image_principale" class="contain" loading="lazy" alt="">
                                    </div>

                                    <div>
                                        <div class="text14px unbounded">{{ licence.name }}</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-end">
                                    <div class="tagblock en-service">
                                        <div class="dot"></div>
                                        <div>Active</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-start">
                                    <div class="small-black-divider"></div>
                                    <div class="horizontal gap4px">
                                        <div class="text10px">Prix :</div>
                                        <div class="text10px bold-text">{{ licence.prix }}€</div>
                                        <div class="text12px gray" v-if="licence.type_licence == 'Mensuel'">/mois</div>
                                        <div class="text12px gray" v-else>/an</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-end" v-if="licence.fournisseur !== 'google'">
                                    <div @click="(event) => { event.stopPropagation(); retierAttribution(licence) }"
                                        class="lightbutton">
                                        <div class="text14px medium purple nowrap">Retirer</div>
                                    </div>
                                </div>
                            </div>

                            <EmptyMessage v-else :value="'Aucune licence attribuée à cet utilisateur.'"
                                class="static" />
                        </div>
                    </div>
                </div>
                <div class="dashboard-horizontal">
                    <div class="componentcontainer h-1/3">
                        <div class="text20px unbounded">Actions rapides</div>
                        <div class="mt-6 flex flex-wrap gap-3 position-relative overflow-auto">
                            <div class="actionrapide" v-if="!props.attribution.tenant_user_id" @click="openOnboarding">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67335656e78806f61b78dd9c_log-in.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Onboarding</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                            <div class="actionrapide" v-else @click="openOffboarding">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67335656e78806f61b78dd9c_log-in.png"
                                        loading="lazy" alt="" class="image24x24px rotate-180">
                                    <div class="text14px">Offboarding</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                            <div class="actionrapide" @click="openUserInformationsPersonnelles">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6733565644a419d7e6e554d5_edit-3.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Editer les informations personnelles</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                            <div class="actionrapide" @click="openCreateSupport">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673356563cbec7624a8aa048_help-circle.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Demander assistance</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                        </div>
                    </div>
                    <div class="componentcontainer h-1/3">
                        <div class="text20px unbounded">Tickets</div>
                        <!-- <div class="horizontal dashboardmenu">
                            <div class="horizontal">
                                <div class="text14px selected tab">Tickets</div>
                                <div class="text14px grey400">Shadow IT</div>
                                <div class="text14px grey400">Alertes de securité</div>
                                <div class="text14px grey400">Mis a jour</div>
                            </div>
                            <div class="horizontal gap8px">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e9770c537d43bfff25_Search-button.png"
                                    loading="lazy" alt="" class="image32x32px">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e94bf30de76454347e_Filter-button.png"
                                    loading="lazy" alt="" class="image32x32px">
                            </div>
                        </div> -->
                        <div class="flex flex-col mt-4 gap-2 h-full overflow-auto">
                            <Link v-if="props.supports.length" :href="'/supports/' + support.id"
                                v-for="support in props.supports" class="card horizontal"
                                style="justify-content: space-between;">
                            <div class="vertical">
                                <div class="text14px">Ticket n°{{ support.numero_support }}</div>
                                <div class="text12px gray">Crée le {{ formattedDate(support.created_at) }}</div>
                            </div>
                            <div class="tagblock" :class="slugify(support.status)">
                                <div class="dot"></div>
                                <div>{{ support.status }}</div>
                            </div>
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                alt="" class="image28x28px">
                            </Link>

                            <EmptyMessage v-else :value="'Aucun ticket associé à cet utilisateur.'" class="static" />
                        </div>
                    </div>
                    <div class="componentcontainer" style="height: 31.5%;">
                        <div class="horizontal">
                            <div class="text20px unbounded">Applications détectées</div>
                        </div>
                        <div class="bg-white mt-4 p-2 rounded-lg overflow-auto" v-if="props.userApps.length">
                            <!-- Header -->
                            <div class="flex font-bold">
                                <div class="flex-1 px-4 py-2 text-left">Application</div>
                                <div class="flex-1 px-4 py-2 text-left">Date d'autorisation</div>
                            </div>
                            <!-- Rows -->
                            <div class="flex" v-for="userApp in props.userApps">
                                <div class="flex-1 px-4 py-2">{{ userApp.title }}</div>
                                <div class="flex-1 px-4 py-2">{{ formattedDate(userApp.created_at) }}</div>
                            </div>
                        </div>

                        <EmptyMessage v-else :value="'Aucune application détectée pour cet utilisateur.'"
                            class="static" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <RetirerAttribution :show="showRetirerAttribution" :currentProduct="currentProduct" :currentUser="props.attribution"
        @closeRetirer="closeRetirerAttribution" @updateAttribution="updateAttribution"></RetirerAttribution>
    <UserAttribution :show="showAttribution" @closeAttribution="closeAttribution" :title="title"
        :equipement_available="equipement_available" @updateAttribution="updateAttribution"></UserAttribution>
    <CreateEmailPro :show="showOnboarding" @close="closeOnboarding"></CreateEmailPro>
    <Offboarding :show="showOffboarding" @close="closeOffboarding"></Offboarding>
    <UserInformationsPersonnelles :show="showUserInformationsPersonnelles" @close="closeUserInformationsPersonnelles">
    </UserInformationsPersonnelles>
    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport" :user="props.attribution">
    </CreateSupport>
</template>

<style scoped>
.relative.group:hover .tooltip {
    opacity: 100 !important;
}

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
