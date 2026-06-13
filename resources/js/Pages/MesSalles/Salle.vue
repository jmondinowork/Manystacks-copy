<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import UserAttribution from '@/Components/UserAttribution.vue';
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


const title = ref('équipements');
const equipement_available = ref(props.equipement_available);
const showAttribution = ref(false);
const closeAttribution = () => showAttribution.value = false;
const openAttribution = () => showAttribution.value = true;


const calculateTotalPrice = computed(() => {
    let total = 0;
    props.attribution.commande_products.forEach(product => {
        if (product.type_contrat !== 'achat') {
            let price = parseFloat(product.prix);
            if (price) {
                total += price;
            }
        }
    });
    return total.toFixed(2);
})

onMounted(() => {
    if (localStorage.getItem('attribution')) {
        store.dispatch('updateAnnounce', localStorage.getItem('attribution'));
        localStorage.removeItem('attribution');
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
                <div class="text14px bold-text">Retour à mes salles</div>
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
                    </div>
                    <div class="w_container gap12px w-1/5">
                        <div class="buttoncircle flex-horizontal w-full">
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670914906f34a25480ade8d8_Frame%2035-4.png"
                                loading="lazy" alt="" class="image52x52px">
                            <div class="flex-vertical">
                                <div class="text24px bold-text">{{ props.cout_total }}€</div>
                                <div class="text12px grey">Coût salle mensuel</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboardcontainer">
                <div class="dashboard-vertical">
                    <div class="componentcontainer h-full">
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
                            <div v-for="equipement in equipements"
                                @click="navigateTo('/mes-equipements/' + equipement.id)"
                                class="card d_container-row cols-4">
                                <div class="w_container gap-2">
                                    <div class="image_container tiny">
                                        <img :src="equipement.image_principale" loading="lazy" alt="">
                                    </div>

                                    <div>
                                        <div class="text14px unbounded">{{ equipement.name }}</div>
                                        <div class="text12px gray">N° série : {{ equipement.numero_unique }}</div>
                                    </div>
                                </div>
                                <div class="w_container justify-end">
                                    <div class="tagblock" :class="slugify(equipement.status)">
                                        <div class="dot"></div>
                                        <div>{{ equipement.status }}</div>
                                    </div>
                                </div>
                                <div class="w_container gap-2 justify-start">
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
                        </div>
                    </div>
                </div>
                <div class="dashboard-horizontal">
                    <div class="componentcontainer h-1/2">
                        <div class="text20px unbounded">Actions rapides</div>
                        <div class="mt-6 flex flex-col gap-4 position-relative">
                            <div class="actionrapide">
                                <div class="horizontal" @click="openUserInformationsPersonnelles">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6733565644a419d7e6e554d5_edit-3.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Editer les informations</div>
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
                    <div class="componentcontainer h-1/2">
                        <div class="text20px unbounded">Tickets</div>
                        <div class="horizontal dashboardmenu">
                            <!-- <div class="horizontal">
                                <div class="text14px selected tab">Tickets</div>
                                <div class="text14px grey400">Shadow IT</div>
                                <div class="text14px grey400">Alertes de securité</div>
                                <div class="text14px grey400">Mis a jour</div>
                            </div> -->
                            <div class="horizontal gap8px">
                                <!-- <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e9770c537d43bfff25_Search-button.png"
                                    loading="lazy" alt="" class="image32x32px">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/673354e94bf30de76454347e_Filter-button.png"
                                    loading="lazy" alt="" class="image32x32px"> -->
                            </div>
                        </div>
                        <div class="flex flex-col mt-4 gap-2 h-full overflow-auto">
                            <Link :href="'/supports/' + support.id" v-for="support in props.supports"
                                class="card horizontal" style="justify-content: space-between;">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <RetirerAttribution :show="showRetirerAttribution" :currentProduct="currentProduct" :currentUser="props.attribution"
        @closeRetirer="closeRetirerAttribution" @updateAttribution="updateAttribution"></RetirerAttribution>
    <UserAttribution :show="showAttribution" @closeAttribution="closeAttribution" :title="title"
        :equipement_available="equipement_available" @updateAttribution="updateAttribution"></UserAttribution>
    <UserInformationsPersonnelles :show="showUserInformationsPersonnelles" @close="closeUserInformationsPersonnelles">
    </UserInformationsPersonnelles>
    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport" :user="props.attribution">
    </CreateSupport>
</template>

<style scoped>
.maincontainer {
    display: flex;
    overflow: auto;
    width: auto;
    flex-flow: column;
    gap: 8px;
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
    width: 50%;
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
