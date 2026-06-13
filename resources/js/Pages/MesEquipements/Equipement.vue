<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import OptionsProduct from "@/Components/OptionsProduct.vue";
import ProductAttribution from "@/Components/ProductAttribution.vue";
import CreateSupport from "@/Components/CreateSupport.vue";
import OptionsButton from "@/Components/OptionsButton.vue";
import DeleteRecord from "@/Components/DeleteRecord.vue";
import InstallPrintEscalator from "@/Components/InstallPrintEscalator.vue";
import EmptyMessage from "@/Components/EmptyMessage.vue";
import Enrollment from "@/Components/Enrollment.vue";
import { caracteristiquesTechniquesProduct } from '@/config.js'
import { formattedDateHour } from '@/functions.js';
import { formattedDate, downloadImage, slugify, userInitials } from '@/functions.js';

import { Link, useForm, usePage } from '@inertiajs/vue3';
import { watch, ref, computed, onMounted } from 'vue';
import { debounce } from 'lodash';
import { useStore } from 'vuex';
import axios from "axios";

const store = useStore();
const { props } = usePage();

const prixContrat = computed(() => {
    return props.equipement.commande.commande_products.reduce((acc, produit) => acc + produit.prix * produit.quantity, 0);
});

const noteForm = useForm({
    id: props.equipement.id,
    note: props.equipement.note,
});
watch(() => noteForm.note, debounce(() => {
    axios.post('/api/editEquipement', noteForm)
        .then(response => {
            props.equipement = response.data.equipement;
            props.historiques = response.data.historiques;
            store.dispatch('updateAnnounce', "Note enregistrée avec succès");
        })
        .catch(error => {
            store.dispatch('updateErrorAnnounce', "Erreur lors de l'enregistrement de la note");
        });
}, 300));

const showPrint = ref(false);
const closePrint = () => showPrint.value = false;
const openPrint = () => showPrint.value = true;

const actionOptions = ref(null);
const showOptions = ref(false);
const closeOptions = () => {
    showOptions.value = false;
}
const openOptions = (action) => {
    actionOptions.value = action;
    showOptions.value = true;
}

const showAttribution = ref(false);
const closeAttribution = () => showAttribution.value = false;
const openAttribution = (event) => {
    event.stopPropagation();
    showAttribution.value = true;
};

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;

const tab = ref('activite');
const changeTab = (currentTab) => {
    tab.value = currentTab;
}

const productFeatures = caracteristiquesTechniquesProduct[props.equipement.sous_categorie];
const caracteristiques = useForm({
    id: props.equipement.id,
    image_principale: props.equipement.image_principale,
});
onMounted(() => {
    Object.values(productFeatures).forEach(feature => {
        caracteristiques[feature.property] = props.equipement[feature.property];
    });
});
const modifyCaracteristique = ref(false);
const previewfile = ref(props.equipement.image_principale);
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewfile.value = e.target.result;
        };
        reader.readAsDataURL(file);
        caracteristiques.image_principale = file;
    }
};
const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const submitCaracteristique = () => {
    let formData = new FormData();
    Object.keys(caracteristiques).forEach(key => {
        if (caracteristiques[key])
            formData.append(key, caracteristiques[key]);
        else
            formData.append(key, '');
    });

    axios.post(route('mes-equipements.editCaracteristiqueImported'), formData, {})
        .then((response) => {
            modifyCaracteristique.value = false;
            store.dispatch('updateAnnounce', "Caractéristiques modifiées avec succès");
            props.equipement = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
}

const formattedText = (property) => {
    if (!props.equipement[property]) return '';
    return props.equipement[property].replace(/\n/g, '<br>');
}

const calculStoragePercentage = computed(() => {
    if (props.equipement.deviceDatas) {
        let usedStorage = props.equipement.deviceDatas.totalStorageSpaceInBytes - props.equipement.deviceDatas.freeStorageSpaceInBytes;
        return Math.round((usedStorage / props.equipement.deviceDatas.totalStorageSpaceInBytes) * 100);
    }
    return 20;
});

const machinePercentages = ref([
    // {
    //     'title': 'Santé de la batterie',
    //     'percentage': 80,
    //     'image': 'https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a63ff8ffbe804cfc3be6_battery.png'
    // },
    {
        'title': 'Stockage',
        'percentage': calculStoragePercentage.value,
        'image': 'https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a63f797d9a34df4b9407_save.png'
    },
    // {
    //     'title': 'Memoire RAM',
    //     'percentage': 40,
    //     'image': 'https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a63f797d9a34df4b93f7_hard-drive.png'
    // },
    // {
    //     'title': 'CPU',
    //     'percentage': 70,
    //     'image': 'https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a63f523e4785d6acc1e4_monitor.png'
    // }
]);

const colorPercentage = (percentage, light = false) => {
    if (percentage < 50) return light ? 'rgba(74, 222, 128, .1)' : 'rgba(74, 222, 128)'
    if (percentage < 80) return light ? 'rgba(240, 181, 65, .1)' : 'rgba(240, 181, 65)';
    return light ? 'rgba(222, 99, 77, .1)' : 'rgba(222, 99, 77)';
}

const showEnrollment = ref(false);
const closeEnrollment = () => showEnrollment.value = false;
const openEnrollment = () => showEnrollment.value = true;

onMounted(() => {
    if (localStorage.getItem('enrolled')) {
        localStorage.removeItem('enrolled');
        store.dispatch('updateAnnounce', "Enrôlement effectué avec succès");
    }
});
const launchEnrollment = () => {
    if (!props.equipement.numero_unique)
        openEnrollment();

    else {
        axios.post(route('enrollEquipement'), { id: props.equipement.id })
            .then(() => {
                localStorage.setItem('enrolled', true);
                window.location.reload();
            })
            .catch(error => {
                store.dispatch('updateErrorAnnounce', "Erreur lors de l'enrôlement");
                openEnrollment();
            });
    }
}
const actionDevice = (action) => {
    try {
        axios.post(route('actionEquipement'), { id: props.equipement.id, action: action })
            .then(response => {
                store.dispatch('updateAnnounce', "Action effectuée avec succès");
            })
            .catch(error => {
                store.dispatch('updateErrorAnnounce', "Erreur lors de l'action");
            });

    } catch (error) {
    }
}
console.log(props);

const ismdm = computed(() => {
    return props.equipement.sous_categorie === 'ordinateurs' && props.userAuth.oauth.includes('microsoft');
});
const isEnrolled = computed(() => {
    return props.equipement.enrollment_id;
});
</script>

<template>
    <AppLayout>
        <div class="flex gap-2">
            <div class="flex flex-col gap-2 w-2/3">
                <div class="componentcontainer">
                    <div class="w_container _100 vertical gap12px">
                        <Link :href="route('mes-equipements')" class="w_container aligncenter gap8px clickable w-fit">
                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6582adf6f93be72dd31f4776_Vectors-Wrapper.svg"
                            loading="lazy" width="16" height="16" alt="" class="image16x16px">
                        <div class="text14px semibold">Retour aux équipements</div>
                        </Link>
                        <div class="w_container aligncenter justifyspacebetween thenvertical">
                            <div class="w_container aligncenter gap12px">
                                <div class="_60x60px white">
                                    <div class="productimagecontainer"
                                        :style="{ 'background-image': 'url(' + props.equipement.image_principale + ')' }">
                                    </div>
                                </div>
                                <div class="w_container vertical gap4px">
                                    <div class="frame-209">
                                        <div class="text20px unbounded">{{ props.equipement.name }}</div>
                                    </div>
                                    <div class="tagblock w-fit" :class="slugify(props.equipement.status)">
                                        <div class="texttag"><span class="text-span-9">•&nbsp;</span> {{
                                            props.equipement.status
                                        }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <OptionsButton v-if="props.userAuth.role != 'collaborateur'">
                                <div v-if="props.equipement.status !== 'En service'" @click="openOptions('En service')"
                                    class="optionunit">
                                    <img src="/images/en-service-icon.svg" loading="lazy" width="24" height="24" alt=""
                                        class="image20x20px">
                                    <div class="text14px nowrap">Mettre en service</div>
                                </div>
                                <div v-if="props.equipement.status !== 'En réserve'" @click="openOptions('En réserve')"
                                    class="optionunit">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e62670173b4bfc36b_Vectors-Wrapper.svg"
                                        loading="lazy" width="24" height="24" alt="" class="image20x20px">
                                    <div class="text14px nowrap">Mettre en réserve</div>
                                </div>
                                <div v-if="props.equipement.status !== 'En maintenance'"
                                    @click="openOptions('En maintenance')" class="optionunit">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e1a00956a6c4065ec_Vectors-Wrapper.svg"
                                        loading="lazy" width="24" height="24" alt="" class="image20x20px">
                                    <div class="text14px nowrap">Mettre en maintenance</div>
                                </div>
                                <div v-if="props.equipement.status !== 'Hors service'"
                                    @click="openOptions('Hors service')" class="optionunit">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55febe7bfcd9035b643_Vectors-Wrapper.svg"
                                        loading="lazy" width="24" height="24" alt="" class="image20x20px">
                                    <div class="text14px nowrap">Mettre hors service</div>
                                </div>
                                <div class="optionunit" @click="openCreateSupport">
                                    <img src="/images/signaler_icon.svg" loading="lazy" width="24" height="24" alt=""
                                        class="image20x20px">
                                    <div class="text14px nowrap">Signaler un incident sur cet équipement</div>
                                </div>
                                <DeleteRecord v-if="!props.equipement.ref_fournisseur" :table="'commandeProduct'"
                                    :ids="[props.equipement.id]" :title="'cet équipement'">
                                </DeleteRecord>
                                <div v-if="props.equipement.sous_categorie == 'imprimantes'" @click="openPrint"
                                    class="optionunit">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e1a00956a6c4065ec_Vectors-Wrapper.svg"
                                        loading="lazy" width="24" height="24" alt="" class="image20x20px">
                                    <div class="text14px nowrap">Installer l'imprimante</div>
                                </div>
                            </OptionsButton>
                            <OptionsButton v-else-if="props.equipement.sous_categorie == 'imprimantes'">
                                <div @click="openPrint" class="optionunit">
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8b55e1a00956a6c4065ec_Vectors-Wrapper.svg"
                                        loading="lazy" width="24" height="24" alt="" class="image20x20px">
                                    <div class="text14px nowrap">Installer l'imprimante</div>
                                </div>
                            </OptionsButton>

                        </div>
                    </div>
                </div>
                <div class="componentcontainer flex-col gap-4" style="height: calc(100vh - 163px);">
                    <div class="w_container _100 gap20px borderbottom">
                        <div class="tabs" :class="{ 'selected': tab === 'activite' }" @click="changeTab('activite')">
                            <img v-if="tab == 'activite'" src="/images/activite-selected.svg" alt="">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb2656a7a5789a10c56b51_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <div class="text14px grey400">Activité</div>
                        </div>
                        <div v-if="props.userAuth.role != 'collaborateur'" class="tabs"
                            :class="{ 'selected': tab === 'financement' }" @click="changeTab('financement')">
                            <img v-if="tab == 'financement'" src="/images/financement-selected.svg" alt="">
                            <img v-else
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb265669da30c0c51a6313_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <div class="text14px grey400">Financement</div>
                        </div>
                        <div class="tabs" :class="{ 'selected': tab === 'caracteristiques' }"
                            @click="changeTab('caracteristiques')">
                            <img v-if="tab == 'caracteristiques'"
                                src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65cb26578a76437dbc751412_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <img v-else src="/images/caracteristique.svg" alt="">
                            <div class="text14px grey400">Caractéristiques</div>
                        </div>
                    </div>
                    <div v-if="tab == 'activite'" class="historique-note gap-4">
                        <div class="w_container vertical gap16px">
                            <div class="text16px medium">Historique</div>
                            <div class="history" v-if="props.historiques.length">
                                <div v-for="(historique, index) in props.historiques" :key="historique.id"
                                    class="w_container vertical white round gap12px padding12px">
                                    <div class="frame-161">
                                        <div class="frame-224">
                                            <div class="frame-225">
                                                <div class="text14px medium">{{ historique.title }}</div>
                                            </div>
                                            <div class="frame-226">
                                                <div class="text14px grey400 text-no-wrap">
                                                    {{ formattedDateHour(historique.created_at) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text14px">{{ historique.description }}</div>
                                    <div class="littlecircle green absolute"></div>
                                    <div class="stepline history" :class="{ 'end': index == 0 }"></div>
                                </div>
                            </div>

                            <EmptyMessage v-else :value="'Aucun historique disponible pour le moment'" class="static" />
                        </div>
                        <div class="w_container vertical gap16px position-relative"
                            v-if="ismdm">
                            <div class="text16px medium">Data</div>

                            <div class="performance_e" :class="{ 'blur': !isEnrolled }">
                                <div class="flex flex-col gap-2 items-center" v-for="machine in machinePercentages">
                                    <div class="circular-progress" :style="{
                                        '--percentage': machine.percentage,
                                        '--progress-color': colorPercentage(machine.percentage),
                                        '--progress-color-light': colorPercentage(machine.percentage, true)
                                    }">
                                        <div class="circle-background"></div>
                                        <div class="circle-progress"></div>
                                        <div class="circle-content">
                                            <span>{{ machine.percentage }}%</span>
                                        </div>
                                    </div>
                                    <div class="horizontal gap4px">
                                        <img :src="machine.image" loading="lazy" alt="" class="image20x20px">
                                        <div class="text14px">{{ machine.title }}</div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-4 items-center"
                                    v-if="props.equipement.deviceDatas && props.equipement.deviceDatas.complianceState === 'compliant'">
                                    <img src="/images/shield-icon.svg" style="width: 130px;height: 130px;" alt="">
                                    <div class="text14px">Compliant</div>
                                </div>
                                <div class="flex flex-col gap-4 items-center" v-else>
                                    <img src="/images/shieldoff-icon.svg" style="width: 130px;height: 130px;" alt="">
                                    <div class="text14px">Non Compliant</div>
                                </div>
                            </div>

                            <EmptyMessage v-if="!isEnrolled" :value="'enrollment'" />
                        </div>
                    </div>

                    <div v-else-if="tab == 'financement'">
                        <div class="w_container vertical gap12px">
                            <div class="text16px medium">Informations financières</div>
                            <div class="componentcontainer bg-white">
                                <div class="w_container vertical gap24px" v-if="props.equipement.commande">
                                    <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                                        <div class="w_container white round toggles">
                                            <div class="toggle selected">
                                                <div class="text14px capitalize">{{ props.equipement.type_contrat }}</div>
                                            </div>
                                        </div>

                                        <Link :href="'/mes-contrats/' + props.equipement.commande.reference_commande"
                                            v-if="props.userAuth.role != 'collaborateur'" class="flex gap-2">
                                        <div class="text14px medium purple">Accédez au contrat
                                        </div>
                                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                            loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                        </Link>

                                    </div>
                                    <div class="w_container vertical">
                                        <template v-if="props.equipement.commande.financeur && props.equipement.type_contrat == 'location'">
                                            <div class="text14px medium">Financeur</div>
                                            <div class="textinput">
                                                <div class="text14px">{{ props.equipement.commande.financeur }}</div>
                                            </div>
                                        </template>
                                        <template v-if="props.equipement.type_contrat == 'location'">
                                            <div class="w_container gap16px">
                                                <div class="w_container vertical">
                                                    <div class="text14px medium">Mensualité de l'équipement</div>
                                                    <div class="textinput">
                                                        <div class="text14px">
                                                            {{ props.equipement.prix }} €
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w_container gap16px">
                                                <div class="w_container vertical">
                                                    <div class="text14px medium">Date de début du contrat</div>
                                                    <div class="textinput">
                                                        <div class="text14px">
                                                            {{
                                                                formattedDate(props.equipement.commande.date_debut_contrat)
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="w_container vertical">
                                                    <div class="text14px medium">Date de fin du contrat</div>
                                                    <div class="textinput">
                                                        <div class="text14px">
                                                            {{ formattedDate(props.equipement.commande.date_fin_contrat)
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div class="w_container gap16px">
                                                <div class="w_container vertical">
                                                    <div class="text14px medium">Prix d'achat de l'équipement</div>
                                                    <div class="textinput">
                                                        <div class="text14px">
                                                            {{ props.equipement.prix }} €
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w_container gap16px">
                                                <div class="w_container vertical">
                                                    <div class="text14px medium">Date de début du contrat</div>
                                                    <div class="textinput">
                                                        <div class="text14px">
                                                            {{
                                                                formattedDate(props.equipement.commande.date_debut_contrat)
                                                            }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <EmptyMessage v-else value="Cet équipement n'est rattaché à aucun contrat. Vous pouvez en
                                    créer un en vous rendant sur la page 'Contrats'." class="static" />
                            </div>
                        </div>
                    </div>

                    <div v-else-if="tab == 'caracteristiques'">
                        <div class="w_container vertical gap12px">
                            <div class="text16px medium">Caractéristiques techniques</div>
                            <div class="componentcontainer bg-white flex-col gap-4 overflow-auto"
                                style="max-height: 72vh;" :class="{ 'modify': modifyCaracteristique }">
                                <div class="text14px medium">Image de l'équipement</div>
                                <div class="w_container justify-between items-center">
                                    <div class="w_container items-center gap-4">
                                        <div class="white" style="width: 100px; height: 100px;">
                                            <div class="productimagecontainer w-full h-full"
                                                :style="{ 'background-image': 'url(' + previewfile + ')' }">
                                            </div>
                                        </div>
                                        <div v-if="modifyCaracteristique"
                                            class="w_container aligncenter gap8px clickable"
                                            @click="triggerFileInput('equipement_img_input')">
                                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65a2c627aaa0abee4d19e582_Vectors-Wrapper.svg"
                                                loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                            <div class="text14px medium purple">Charger une image</div>
                                        </div>

                                        <input :ref="'fileInput'" class="d-none" :name="'equipement_img_input'"
                                            :id="'equipement_img_input'" type="file" @change="handleFileChange">
                                    </div>
                                    <div v-if="!props.equipement.ref_fournisseur && props.userAuth.role != 'collaborateur'"
                                        class="lightbutton" @click="modifyCaracteristique = !modifyCaracteristique">
                                        <div v-if="!modifyCaracteristique" class="text14px medium purple nowrap">
                                            Modifier
                                            les
                                            caractéristiques</div>
                                        <div v-else class="text14px medium purple nowrap">Annuler la modification</div>
                                    </div>
                                </div>

                                <div class="w_container vertical gap16px">
                                    <div
                                        class="grid gap-x-20 gap-y-2 grid-cols-[minmax(0,max-content)_1fr] caracteristics">
                                        <template
                                            v-for="carac in caracteristiquesTechniquesProduct[props.equipement.sous_categorie]"
                                            :key="carac">
                                            <template v-if="props.equipement[carac.property]">
                                                <template v-if="!modifyCaracteristique">
                                                    <div class="text14px grey400">{{ carac.title }}</div>
                                                    <div class="text14px" v-html="formattedText(carac.property)"></div>
                                                    <div class="separatorhorizontal col-span-2"></div>
                                                </template>
                                                <template v-else>
                                                    <div class="text14px grey400">{{ carac.title }}</div>
                                                    <input v-model="caracteristiques[carac.property]" class="textinput"
                                                        type="text">
                                                    <div class="separatorhorizontal col-span-2"></div>
                                                </template>
                                            </template>
                                            <template v-else-if="modifyCaracteristique">
                                                <div class="text14px grey400">{{ carac.title }}</div>
                                                <input v-model="caracteristiques[carac.property]" class="textinput"
                                                    type="text">
                                                <div class="separatorhorizontal col-span-2"></div>
                                            </template>
                                        </template>
                                    </div>
                                </div>

                                <div v-if="modifyCaracteristique" class="w_container justify-end">
                                    <div class="bigbutton purple w-auto" @click="submitCaracteristique()">
                                        <div class="text14px white"> Enregistrer </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-2 w-1/3" style="height: calc(100vh - 16px);">
                <div class="componentcontainer flex-col h-1/3 position-relative"
                    v-if="ismdm">
                    <div class="text20px unbounded">Actions rapides</div>
                    <div class="mt-6 flex flex-col">
                        <div class="actionrapide first" v-if="!isEnrolled"
                            @click="launchEnrollment">
                            <div class="horizontal">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a2146982252dc13c0242_arrow-up-right.png"
                                    loading="lazy" alt="" class="image24x24px">
                                <div class="text14px">Enroller la machine</div>
                            </div>
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                loading="lazy" alt="" class="image16x16px">
                        </div>
                        <div class="flex flex-col gap-4 position-relative"
                            :class="{ 'blur': !isEnrolled }">
                            <!-- <div class="actionrapide" @click="actionDevice('sync')">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734b023da67ea2bf3c7a6a8_alert-triangle.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Synchroniser</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                            <div class="actionrapide" @click="actionDevice('reset')">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734b02296b6fd79b0beda56_repeat.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Réinitialiser</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div> -->
                            <div class="actionrapide" @click="actionDevice('reboot')">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734b023ad77c756623421ee_tool.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Redémarrer</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                            <!-- <div class="actionrapide" @click="actionDevice('block')">
                                <div class="horizontal">
                                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734b022a63af56e11b02332_x-square.png"
                                        loading="lazy" alt="" class="image24x24px">
                                    <div class="text14px">Bloquer</div>
                                </div>
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                    loading="lazy" alt="" class="image16x16px">
                            </div> -->
                        </div>
                    </div>

                    <EmptyMessage v-if="!isEnrolled" style="top:60%" :value="'enrollment'" />
                </div>

                <div class="componentcontainer flex-col h-1/3 position-relative"
                    v-if="ismdm">
                    <div class="text20px unbounded">Actions rapides récentes</div>
                    <div class="alertecontainer" :class="{ 'blur': !isEnrolled }"
                        v-if="props.equipement.deviceDatas && props.equipement.deviceDatas.deviceActionResults.length">
                        <div class="horizontal" style="width: 100%;">
                            <div class="flex flex-col gap-2 w-full"
                                v-for="deviceActionResults in props.equipement.deviceDatas.deviceActionResults">
                                <div class="flex justify-between">
                                    <div class="flex flex-col w-1/2">
                                        <div class="text14px">Action</div>
                                        <div class="text14px gray">
                                            {{ deviceActionResults.actionName }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-1/2">
                                        <div class="text14px">Statut</div>
                                        <div class="text14px gray">
                                            {{ deviceActionResults.actionState }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-between">
                                    <div class="flex flex-col w-1/2">
                                        <div class="text14px">Lancement</div>
                                        <div class="text14px gray">
                                            {{ formattedDateHour(deviceActionResults.startDateTime) }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-1/2">
                                        <div class="text14px">Dernière mise à jour</div>
                                        <div class="text14px gray">
                                            {{
                                                formattedDateHour(deviceActionResults.lastUpdatedDateTime)
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <EmptyMessage v-else :value="'Aucune action rapide récente'" class="static" />

                    <EmptyMessage v-if="!isEnrolled" :value="'enrollment'" />

                </div>

                <!-- <div class="componentcontainer flex-col h-1/3 position-relative">
                    <div class="text20px unbounded">Alertes de securité</div>
                    <div class="alertecontainer" :class="{ 'blur': !isEnrolled }">
                        <div class="horizontal">
                            <div class="horizontal gap8px">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/6734a21b6cfb2042334462b0_alert-triangle.png"
                                    loading="lazy" alt="" class="image20x20px">
                                <div class="text14px">alerte detectée</div>
                            </div>
                        </div>
                        <div class="horizontal">
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670915219aa70f5ad571f1b9_chevron-right%20(1).png"
                                loading="lazy" alt="" class="image16x16px">
                        </div>
                    </div>

                    <EmptyMessage v-if="!isEnrolled" />
                </div> -->
                <div class="componentcontainer flex-col h-1/3"
                    :class="{ 'h-full': props.equipement.sous_categorie !== 'ordinateurs' }">
                    <div class="horizontal">
                        <div class="text20px unbounded">Attribution</div>
                    </div>
                    <template v-if="props.equipement.user_attributed_id">
                        <Link :href="'/mon-equipe/' + props.equipement.user_attributed_id" class="attribuea-container">
                        <div class="horizontal">
                            <div class="image_container tiny">
                                <img v-if="props.equipement.user_attributed.profile_img"
                                    :src="props.equipement.user_attributed.profile_img" loading="lazy" alt=""
                                    class="imageutilisateur">
                                <div v-else class="avatarcircle">
                                    <div class="text16px medium white">{{
                                        userInitials(props.equipement.user_attributed.name) }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="text14px unbounded">{{ props.equipement.user_attributed.name }}</div>
                                <div class="text12px gray">{{ props.equipement.user_attributed.email }}</div>
                            </div>
                            <div class="horizontal gap8px">
                                <div v-for="tag in props.equipement.user_attributed.tags.slice(0, 1)" :key="tag.id"
                                    class="tagblock w-fit cursor-pointer"
                                    :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                    <div class="texttag">
                                        {{ tag.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        </Link>
                        <div class="bigbutton purple mt-6" @click="openAttribution"
                            v-if="props.userAuth.role != 'collaborateur' && !isEnrolled">
                            <div class="text14px medium white nowrap">Modifier l’attribution</div>
                        </div>
                    </template>
                    <template v-else>
                        <EmptyMessage :value="'Cet équipement n\'est pas attribué.'" class="static" />

                        <div class="bigbutton purple mt-6" @click="openAttribution"
                            v-if="props.userAuth.role != 'collaborateur'">
                            <div class="text14px medium white nowrap">Attribuer l’équipement</div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </AppLayout>


    <OptionsProduct :show="showOptions" :action="actionOptions" @closeOptions="closeOptions"
        :equipement="props.equipement">
    </OptionsProduct>
    <ProductAttribution :show="showAttribution" @closeAttribution="closeAttribution">
    </ProductAttribution>
    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
        :object="'J\'ai un problème avec un de mes équipements'" :equipement="props.equipement"></CreateSupport>
    <InstallPrintEscalator :show="showPrint" @closePrint="closePrint"></InstallPrintEscalator>
    <Enrollment :show="showEnrollment" @closeEnrollment="closeEnrollment" :equipement="props.equipement"
        @launchEnrollment="launchEnrollment"></Enrollment>
</template>

<style scoped>
.circular-progress {
    --size: 130px;
    --percentage: 50;
    --progress-color: #4ade80;
    --progress-color-light: rgba(74, 222, 128, 0.1);

    position: relative;
    width: var(--size);
    height: var(--size);
}

.circle-background,
.circle-progress {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
}

.circle-background {
    background-color: var(--progress-color);
    opacity: 0.1;
}

.circle-progress {
    background: conic-gradient(var(--progress-color) 0% calc(var(--percentage) * 1%),
            var(--progress-color-light) calc(var(--percentage) * 1%) 100%);
}

.circle-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    border-radius: 50%;
    width: calc(var(--size) - 32px);
    height: calc(var(--size) - 32px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.circle-content span {
    font-size: 1.5rem;
    font-weight: bold;
}

.caracteristics .separatorhorizontal:last-child {
    display: none;
}

.carac_container {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
}

.carac_container .textinput {
    height: unset;
}

.toggle {
    background-color: var(--grey-50);
}

.modify .textinput {
    background-color: #F7F8F9;
}

.modify input {
    background-color: #F7F8F9;
    font-size: 14px;
}

.textinput {
    background-color: #EEF0F3;
}


.tabs.selected .text14px {
    color: var(--main);
}

.tabs {
    cursor: pointer;
}

.stackimage {
    max-width: 300px;
    padding-bottom: 0;
}

.avatarcircle,
.avatarcircle_img {
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
}

.avatarcircle .text40px {
    font-size: 18px;
}

.avatarcircle._2,
.avatarcircle_img._2 {
    width: 100px;
    height: 100px;
    min-width: 100px;
    min-height: 100px;
}

.avatarcircle._2 .text40px {
    font-size: 40px;
}

.avatarcircle_img2 {
    background-position: center;
    background-size: cover;
    width: 100px;
    height: 100px;
    grid-column-gap: 10px;
    grid-row-gap: 10px;
    border-radius: 10000px;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    display: flex;
}

.actionrapide.first {
    border: 1px solid var(--main);
    margin-bottom: 4px;
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

.horizontal.gap4px {
    gap: 4px;
    justify-content: center;
    margin-top: 10px;
    align-items: center;
}

.alertecontainer {
    display: flex;
    width: 100%;
    margin-top: 16px;
    padding: 24px;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    background-color: var(--white);
}

.attribuea-container {
    width: 100%;
    margin-top: 16px;
    padding: 24px;
    border-radius: 8px;
    background-color: var(--white);
}

.performance_e {
    display: flex;
    width: 100%;
    padding: 24px;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    background-color: var(--white);
}
</style>
