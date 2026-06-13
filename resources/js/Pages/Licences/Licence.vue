<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import LicenceAttribution from "@/Components/LicenceAttribution.vue";
import CreateSupport from "@/Components/CreateSupport.vue";
import { userInitials, navigateTo, formattedDateHour, formattedDate } from "@/functions.js";

import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useStore } from "vuex";
import { ref, computed, watch } from 'vue';
import axios from "axios";

const store = useStore();
const { props } = usePage();

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;

const showAttribution = ref(false);
const closeAttribution = () => showAttribution.value = false;
const openAttribution = () => showAttribution.value = true;

const currentLicenceId = ref(null);
const showConfirmUnassign = ref(false);
const closeConfirmUnassign = () => showConfirmUnassign.value = false;
const openConfirmUnassign = (licence_id) => { showConfirmUnassign.value = true; currentLicenceId.value = licence_id };

const licenceRef = ref(props.licences[0]);

props.equipement = licenceRef.value;
const available = computed(() => props.licences.filter(licence => licence.status === "active" && licence.user_attributed_id === null));
const assigned = computed(() => props.licences.filter(licence => licence.user_attributed_id !== null && licence.status === "active"));
const provisioning = computed(() => props.licences.filter(licence => licence.status === "provisioning"));
const prixTotal = computed(() => {
    return (props.licences.length * licenceRef.value.prix).toFixed(2)
});

const unassign = async (licenceId) => {
    try {
        const response = await axios.post('/api/unassignLicence', {
            id: licenceId,
        });

        props.licences = response.data.licences;
        props.users = response.data.users;

        closeConfirmUnassign();
        const user = response.data.user;
        store.dispatch('updateAnnounce', `${user.name} n'a plus la licence ${licenceRef.value.name}`);
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite");
    }
}

const getImageSrc = () => {
    return increaseSeats.value == 1 || !increaseSeats.value ?
        "/images/minus-circle-grey.png" :
        "/images/minus-circle.svg";

}
const increaseSeats = ref(1);
const incrementQuantity = () => increaseSeats.value++;
const decrementQuantity = () => {
    if (increaseSeats.value > 1)
        increaseSeats.value--;
}
const calculateWidth = () => `${increaseSeats.value.toString().length}ch`;
const validateInput = () => {
    if (!increaseSeats.value || isNaN(increaseSeats.value) || parseInt(increaseSeats.value) < 1)
        increaseSeats.value = 1
}

const addToCart = async () => {
    const form = useForm({
        quantity: increaseSeats.value,
        product_id: licenceRef.value.product_id
    });

    try {
        const response = await axios.post('/api/addToPanier', form);
        let fullLicence = true;

        response.data.panier_products.forEach(item => {

            if (item.product.categorie != 'licences') {
                fullLicence = false;
                return;
            }
        });

        fullLicence ? window.location.href = route('order-full-licences') : window.location.href = route('entreprise');
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'ajout au panier");
    }
}
</script>

<template>
    <AppLayout>
        <div class="componentcontainer">
            <div class="w_container _100 vertical gap12px">
                <div class="w_container justify-between">
                    <Link :href="route('mes-licences')" class="w_container aligncenter gap8px clickable w-fit">
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6582adf6f93be72dd31f4776_Vectors-Wrapper.svg"
                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                    <div class="text14px semibold">Retour aux licences</div>

                    </Link>
                    <div class="lightbutton" @click="openCreateSupport">
                        <img src="/images/signaler_icon.svg" class="image20x20px cursor-pointer" alt="">
                    </div>
                </div>
                <div class="w_container aligncenter justifyspacebetween thenvertical">
                    <div class="w_container aligncenter gap12px">
                        <div class="_60x60px white">
                            <div class="productimagecontainer"
                                :style="{ 'background-image': 'url(' + licenceRef.image_principale + ')' }">
                            </div>
                        </div>
                        <div class="w_container vertical gap4px">
                            <div class="frame-209">
                                <div class="text20px unbounded">{{ licenceRef.name }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="ajouter-licences" v-if="licenceRef.product_id">
                        <div class="text16px bold-text">Ajouter des licences</div>
                        <div class="button_ajouter-licences">
                            <img @click="decrementQuantity()" :src="getImageSrc()" loading="lazy" alt=""
                                class="image20x20px cursor-pointer">
                            <input type="number" min="1" v-model="increaseSeats" @input="validateInput()"
                                class="text16px h-5 p-0" :style="{ width: calculateWidth() }" />
                            <img @click="incrementQuantity()"
                                src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f176f982e38482d9311975_plus-circle.png"
                                loading="lazy" alt="" class="image20x20px cursor-pointer">
                        </div>
                        <div class="buttoncircle purple gap-0" @click="addToCart()">
                            <img class="image20x20px" loading="lazy" width="20" height="20"
                                src="/images/shopping-cart.svg">
                            <img class="image20x20px" loading="lazy" width="20" height="20"
                                src="/images/chevron-right.svg">
                        </div>
                    </div>

                </div>
                <div class="d_container_test">
                    <div class="description_licence_container">
                        <div class="text14px black  bold-text">{{ available.length }}</div>
                        <div class="text14px black">disponible(s)</div>
                        <div class="divider_black"></div>
                    </div>
                    <div class="description_licence_container" v-if="provisioning.length">
                        <div class="text14px black bold-text">{{ provisioning.length }}</div>
                        <div class="text14px black">En attente</div>
                        <div class="divider_black"></div>
                    </div>
                    <div class="description_licence_container">
                        <div class="text14px black bold-text">{{ assigned.length }}</div>
                        <div class="text14px black">assignée(s)</div>
                        <div class="divider_black"></div>
                    </div>
                    <div class="description_licence_container">
                        <div class="text14px black">Prix unitaire:</div>
                        <div class="text14px black bold-text">{{ licenceRef.prix }}€</div>
                        <div class="divider_black"></div>
                    </div>
                    <div class="description_licence_container">
                        <div class="text14px black">Prix total:</div>
                        <div class="text14px black bold-text">{{ prixTotal }}€</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="componentcontainer">
            <div class="w_container _100 vertical gap12px">
                <div class="d-flex justify-between items-center">
                    <div class="frame-212">
                        <div class="text16px">Attributions</div>
                    </div>
                    <div class="buttoncircle purple gap-2" @click="openAttribution"
                        v-if="licenceRef.fournisseur == 'microsoft'">
                        <div class="text14px medium white nowrap">Attribuer une licence</div>
                        <img class="image20x20px" loading="lazy" width="20" height="20" src="/images/plus-circle.svg">
                    </div>
                </div>
                <div class="stacksgrid">
                    <template v-for="li in props.licences">
                        <div @click="navigateTo('/mon-equipe/' + li.user_attributed_id)" v-if="li.user_attributed_id"
                            class="stackattributionscontainer">
                            <div class="a_container">
                                <div class="image_container small">
                                    <img v-if="li.user_attributed.profile_img" :src="li.user_attributed.profile_img"
                                        loading="lazy" alt="" class="rounded-full">
                                    <div v-else class="avatarcircle">
                                        <div class="text40px white">{{ userInitials(li.user_attributed.name) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="name_container">
                                    <div class="text18px bold-text gray">{{ li.user_attributed.name }}</div>
                                    <div class="text12px gray">{{ li.user_attributed.email }}</div>
                                    <div class="info_container">
                                        <div v-for="tag in li.user_attributed.tags.slice(0, 2)" :key="tag.id"
                                            class="tagblock text12px"
                                            :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                            <div class="texttag">
                                                {{ tag.name }}
                                            </div>
                                        </div>
                                        <div v-if="li.user_attributed.tags.length" class="divider_gray"></div>
                                        <div class="text12px lightgray">{{ li.user_attributed.poste }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="w_container vertical gap24px">
                                <div class="w_container vertical gap8px">
                                    <div class="divider_gray_horizontal"></div>
                                    <div class="d_container_vertical">
                                        <div class="description_licence_container">
                                            <div class="text14px black">Date d’attribution :</div>
                                            <div class="text14px black bold-text">{{ formattedDateHour(li.updated_at) }}
                                            </div>
                                        </div>
                                        <!-- <div class="description_licence_container">
                                            <div class="text14px black">Date de renouvellement :</div>
                                            <div class="text14px black bold-text">---</div>
                                        </div> -->
                                        <!-- <div class="description_licence_container">
                                            <div class="text14px black">Dernière utilisation :</div>
                                            <div class="text14px black bold-text">---</div>
                                        </div> -->
                                        <div class="description_licence_container">
                                            <div class="text14px black">Date d'achat :</div>
                                            <div class="text14px black bold-text">{{
                                                formattedDateHour(li.date_debut_licence) }}</div>
                                        </div>
                                        <div class="description_licence_container">
                                            <div class="text14px black">Renouvellement :</div>
                                            <div class="text14px black bold-text">{{ li.type_licence }}</div>
                                        </div>
                                    </div>
                                    <div class="buttoncircle purple w-fit mt-3 mb-3"
                                        v-if="licenceRef.fournisseur !== 'google'"
                                        @click="(event) => { event.stopPropagation(); openConfirmUnassign(li.id) }">
                                        <div class="text14px white">Retirer la licence</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </AppLayout>

    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
        :object="'J\'ai un problème avec une de mes licences'" :equipement="licenceRef"></CreateSupport>
    <LicenceAttribution :show="showAttribution" :licencesAvailable="available" @closeAttribution="closeAttribution">
    </LicenceAttribution>


    <div class="darkmodalbackground" :class="{ 'show': showConfirmUnassign }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Retrait de la licence {{ licenceRef.name }}
                </div>
                <div class="w_container alignright cursor-pointer" @click="closeConfirmUnassign">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="text14px medium">
                        Si vous retirez cette licence, l'utilisateur n'aura plus accès aux services associés.
                        Etes-vous sûr de vouloir continuer ?
                    </div>

                    <div class="w_container vertical gap8px">
                        <div @click="unassign(currentLicenceId)" class="bigbutton purple">
                            <div class="text14px white">
                                Retirer la licence
                            </div>
                        </div>
                        <div class="bigbutton" @click="closeConfirmUnassign">
                            <div class="text14px">
                                Annuler
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.avatarcircle,
.avatarcircle_img {
    width: 52px;
    height: 52px;
    min-width: 52px;
    min-height: 52px;
}

.avatarcircle .text40px {
    font-size: 20px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.lightgray {
    color: var(--grey-400);
}
</style>
