<script setup>
import { onMounted, onUnmounted, ref, computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { vOnClickOutside } from '@vueuse/components';
import { useStore } from "vuex";

import FilterOptionsEquipementsOnboarding from "./FilterOptionsEquipementsOnboarding.vue";
import PassageCommandeOnboarding from "./PassageCommandeOnboarding.vue";

import AddTags from "./AddTags.vue";
import axios from "axios";

const store = useStore();

const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeOnboardCollaborateur']);

const close = () => {
    form.reset();
    currentStep.value = 'information';
    emit('closeOnboardCollaborateur');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const currentStep = ref('information');
const form = useForm({
    fname: '',
    lname: '',
    email: '',
    password: '',
    poste: '',
    date_arrive: '',
    phone: '',
    tags: [],
    pattern: '',
    domain: props.currentDomain.domain,
    tenant: props.currentDomain.tenant,
    email_perso: '',
    licences: [],
    equipements: [],
    licencesMarketPlace: [],
    equipementsMarketPlace: [],
    commande: {},
    processing: false,
});
const currentDomainsList = ref(props.domains[props.currentDomain.tenant]);
const selectDomain = (tenant) => {
    form.tenant = tenant;
    form.domain = props.domains[tenant][0];
    currentDomainsList.value = props.domains[tenant];
}
const errors = ref({
    'information': {
        fname: false,
        lname: false,
    },
    'email': {
        pattern: false,
        email_perso: false,
    },
});
const patterns = computed(() => {
    let firstName = form.fname.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '');
    let lastName = form.lname.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '');
    return [
        firstName + lastName,
        firstName + '.' + lastName,
        firstName + '_' + lastName,
        firstName + '-' + lastName,
        firstName[0] + lastName,
        firstName[0] + '.' + lastName,
        firstName[0] + '_' + lastName,
        firstName[0] + '-' + lastName,
        lastName + firstName,
        lastName + '.' + firstName,
        lastName + '_' + firstName,
        lastName + '-' + firstName,
        lastName + firstName[0],
        lastName + '.' + firstName[0],
        lastName + '_' + firstName[0],
        lastName + '-' + firstName[0],
    ];
});
const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

const validateField = async (value, fieldName) => {
    if (!value) {
        errors.value[currentStep.value][fieldName] = true;
        return false;
    }
    if (fieldName == 'email_perso' && !isValidEmail(value)) {
        errors.value[currentStep.value][fieldName] = true;
        return false;
    }
    if (fieldName == 'pattern' || fieldName == 'domain') {
        let response = await axios.post(route('checkUserEmailTenant'), { email: form.pattern + '@' + form.domain, tenant: form.tenant });
        fieldName = 'pattern';

        if (response.data.exists) {
            errors.value[currentStep.value][fieldName] = true;
            return false;
        }
    }
    errors.value[currentStep.value][fieldName] = false;
    return true;
}
const handleChange = (input) => validateField(form[input.target.id], input.target.id);
const nextStep = async () => {
    let hasError = 0;

    switch (currentStep.value) {
        case 'information':
            for (const fieldName in errors.value[currentStep.value]) {
                if (!await validateField(form[fieldName], fieldName)) {
                    hasError = 1;
                }
            }

            if (!hasError) {
                form.pattern = patterns.value[0];
                validateField(form.pattern, 'pattern');
                currentStep.value = 'email';
            }
            break;
        case 'email':
            for (const fieldName in errors.value[currentStep.value]) {
                if (!await validateField(form[fieldName], fieldName)) {
                    hasError = 1;
                }
            }

            if (!hasError) {
                form.email = form.pattern + '@' + form.domain;
                if (form.tenant === 'microsoft')
                    filteredLicencesAvailable.value = props.licencesAvailable.filter(licence => licence.fournisseur == form.tenant);
                else if (form.tenant === 'google')
                    filteredLicencesAvailable.value = props.licencesAvailable.filter(licence => licence.fournisseur == form.tenant).filter(licence => licence.reference_id.includes('GoogleWorkspace'));

                currentStep.value = props.auth.user.entreprise.ion_id ? 'licences' : 'equipements';
            }

            break;
        case 'licences':
            if (form.tenant == 'google') {
                let hasGoogleWorkspace = form.licences.some(licence => props.licencesAvailable.find(l => l.id == licence)?.reference_id?.includes('GoogleWorkspace'));

                if (!hasGoogleWorkspace) {
                    store.dispatch('updateErrorAnnounce', "Vous devez attribuer une licence Google Workspace à votre collaborateur");
                    return;
                }
            }

            currentStep.value = 'equipements';
            break;
        case 'equipements':
            if (form.equipementsMarketPlace.length || form.licencesMarketPlace.length) {
                currentStep.value = 'commande';
            } else {
                currentStep.value = 'confirmer';
            }
            break;
        case 'commande':
            currentStep.value = 'confirmer';
            break;
        case 'confirmer':
            close();
            break;
    }

    searchTerm.value = '';
    searchTermMarketPlace.value = '';
    showCatalogueLicences.value = false;
    showCatalogueEquipements.value = false;
}
const updateTagsForm = (tags) => {
    form.tags = tags.map(tag => tag.id);
}

const searchTerm = ref('');
const searchTermMarketPlace = ref('');

const filteredLicencesAvailable = ref({});
const filteredLicences = computed(() => props.licencesAvailable.filter(licence => licence.fournisseur == form.tenant).filter(licence => licence.name.toLowerCase().includes(searchTerm.value.toLowerCase())));
const filteredEquipements = computed(() => props.equipementsAvailable.filter(equipement => equipement.name.toLowerCase().includes(searchTerm.value.toLowerCase())));
const filteredLicencesMarketPlace = computed(() => props.licencesMarketPlace.filter(licence => licence.fournisseur == form.tenant).filter(licence => licence.name.toLowerCase().includes(searchTermMarketPlace.value.toLowerCase())));

const filteredEquipementsMarketPlace = ref(props.equipementsMarketPlace);
watch(searchTermMarketPlace, (value) => {
    filteredEquipementsMarketPlace.value = props.equipementsMarketPlace.filter(equipement => equipement.name.toLowerCase().includes(value.toLowerCase()));
});
const updateFilteredEquipements = (value) => filteredEquipementsMarketPlace.value = value;

const addEquipement = (id) => {
    if (!form.equipements.includes(id))
        form.equipements.push(id);
    else
        form.equipements = form.equipements.filter(equipement => equipement !== id);
}
const addLicence = (id) => {
    if (!form.licences.includes(id))
        form.licences.push(id);
    else
        form.licences = form.licences.filter(licence => licence !== id);
}
const addEquipementMarketPlace = (id) => {
    if (!form.equipementsMarketPlace.includes(id))
        form.equipementsMarketPlace.push(id);
    else
        form.equipementsMarketPlace = form.equipementsMarketPlace.filter(equipement => equipement !== id);
}
const addLicenceMarketPlace = (id) => {
    if (!form.licencesMarketPlace.includes(id))
        form.licencesMarketPlace.push(id);
    else
        form.licencesMarketPlace = form.licencesMarketPlace.filter(licence => licence !== id);
}

const showCatalogueLicences = ref(false);
const showCatalogueEquipements = ref(false);

const showFilterOptions = ref(false);
const closeFilterOptions = () => showFilterOptions.value = false;
const toggleFilterOptions = () => showFilterOptions.value = !showFilterOptions.value;

const updateFormCommande = (commande) => {
    form.commande = commande;
    currentStep.value = 'confirmer';
}

const submit = async () => {
    form.processing = true;

    form.post(route('onboardCollaborateur'), {
        onFinish() {
            form.processing = false;
            currentStep.value = 'success';
        }
    });
    // var formData = objectToFormData(form);

    // axios.post(route('onboardCollaborateur'), form)
    //     .then(() => {
    //         form.processing = false;
    //         currentStep.value = 'success';
    //     })
    //     .catch(() => {
    //         form.processing = false;
    //         store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'onboarding du collaborateur");
    //     });
};
</script>

<template>
    <form @submit.prevent="submit" class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer" style="max-width: 800px;">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Onboarder un collaborateur
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer flex-col">
                <!-- HEADER -->
                <div class="w_container _100 justifycenter" :class="currentStep">
                    <div class="allstepcontainer hide">
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">1</div>
                            </div>
                            <div class="text14px stepbar">Informations</div>
                        </div>
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">2</div>
                            </div>
                            <div class="text14px stepbar">Adresse email</div>
                        </div>
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">3</div>
                            </div>
                            <div class="text14px stepbar">Licences</div>
                        </div>
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">4</div>
                            </div>
                            <div class="text14px stepbar">Équipements</div>
                        </div>
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">5</div>
                            </div>
                            <div class="text14px stepbar">Commande</div>
                        </div>
                        <div class="stepcontainer">
                            <div class="circle">
                                <div class="text14px medium white">6</div>
                            </div>
                            <div class="text14px stepbar">Confirmer</div>
                        </div>
                        <div class="vectors-wrapper vector vector1"></div>
                        <div class="vectors-wrapper vector vector2"></div>
                        <div class="vectors-wrapper vector vector3"></div>
                        <div class="vectors-wrapper vector vector4"></div>
                        <div class="vectors-wrapper vector vector5"></div>
                    </div>
                </div>


                <!-- CONTENT -->
                <div class="mt-10" v-if="currentStep == 'information'">
                    <div class="text16px bold-text">Informations personnelles</div>

                    <div class="flex flex-col mt-4">
                        <div class="flex gap-2">
                            <div class="w-full">
                                <label class="text14px" for="fname">Prénom <span class="red">*</span></label>
                                <input type="text" v-model="form.fname" id="fname" @input="handleChange"
                                    :class="{ 'error': errors.information.fname }">
                            </div>
                            <div class="w-full">
                                <label class="text14px" for="lname">Nom <span class="red">*</span></label>
                                <input type="text" v-model="form.lname" id="lname" @input="handleChange"
                                    :class="{ 'error': errors.information.lname }">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="w-full">
                                <label class="text14px" for="lname">Date d'arrivée</label>
                                <input type="date" v-model="form.date_arrive">
                            </div>
                            <div class="w-full">
                                <label class="text14px" for="lname">Date de sortie</label>
                                <input type="date" v-model="form.date_sortie">
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <div class="w-full">
                                <label class="text14px" for="phone">Téléphone</label>
                                <input type="text" v-model="form.phone">
                            </div>
                            <div class="w-full">
                                <label class="text14px" for="fname">Poste</label>
                                <input type="text" v-model="form.poste">
                            </div>
                        </div>

                        <div class="w-full">
                            <AddTags @updateTagsForm="updateTagsForm" />
                        </div>
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <div class="button secondary" @click="close">
                            Annuler
                        </div>

                        <div class="button" @click="nextStep">
                            Continuer
                        </div>
                    </div>
                </div>

                <div class="mt-10" v-if="currentStep == 'email'">
                    <div class="w_container vertical">
                        <div class="w_container aligncenter gap-2">
                            <div class="toggle" v-for="(domain, key) in props.domains" :key="key"
                                :class="{ 'selected': form.tenant == key }" @click="selectDomain(key)">
                                <img :src="'/images/' + key + '-logo.png'" loading="lazy" alt="" class="image20x20px">
                                <div class="text14px capitalize">{{ key }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="w_container flex items-center p-2 gap-2 rounded-lg error mt-4"
                        style="background-color: var(--yellow);" v-if="form.tenant === 'google'">
                        <img class="image16x16px" loading="lazy" width="auto" height="auto" alt=""
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65741c8ac4e2ecd7e67084ef_alert-triangle.svg" />
                        <div class="text14px white">
                            Attention, pour créer une nouvelle adresse email Google, vous devrez obligatoirement lui
                            attribuer une licence Google Workspace à la prochaine étape.
                        </div>
                    </div>

                    <div class="text16px bold-text mt-4">L'adresse email pro de votre collaborateur sera :</div>

                    <div class="flex flex-col mt-4">
                        <div class="w_container gap-2 items-center">
                            <div class="w_container vertical">
                                <div class="text14px">
                                    Pattern
                                    <span class="red">*</span>
                                </div>
                                <select v-model="form.pattern" id="pattern"
                                    :class="{ 'border-error': errors.email.pattern }" @change="handleChange">
                                    <option v-for="pattern in patterns" :value="pattern">
                                        {{ pattern }}
                                    </option>
                                </select>
                            </div>
                            <div class="text16px bold-text mt-5">@</div>
                            <div class="w_container vertical">
                                <div class="text14px">
                                    Domaine
                                    <span class="red">*</span>
                                </div>
                                <!-- <input type="text" v-model="form.domain" readonly> -->
                                <select v-model="form.domain" id="domain" :class="{ 'error': errors.email.domain }"
                                    @change="handleChange">
                                    <option v-for="domain in currentDomainsList" :value="domain">
                                        {{ domain }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div v-if="errors.email.pattern"
                            class="flex items-center space-x-2 p-2 bg-red-100 text-red-600 rounded-md mt-2 w-fit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="text12px">L'adresse email n'est pas disponible. Essayez un autre pattern</span>
                        </div>
                        <div v-else
                            class="flex items-center space-x-2 p-2 bg-green-100 text-green-600 rounded-md w-fit">
                            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2beba3dab88c100b1f006_Vector.png"
                                style="width: 16px;height: 16px;" alt="">
                            <span class="text12px">L'adresse email est disponible !</span>
                        </div>

                        <div class="text16px bold-text mt-6 mb-6">Les instructions de première connexion du
                            collaborateur seront
                            envoyées à :</div>
                        <div class="w_container gap-2 items-center">
                            <div class="w_container vertical">
                                <div class="text14px">
                                    Adresse email personnelle
                                    <span class="red">*</span>
                                </div>
                                <input style="width: 50%;" type="email" id="email_perso" v-model="form.email_perso"
                                    :class="{ 'error': errors.email.email_perso }" @input="handleChange">
                            </div>
                        </div>
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <div class="button" @click="nextStep">
                            Continuer
                        </div>
                    </div>
                </div>

                <div class="mt-10" v-if="currentStep == 'licences' && props.auth.user.entreprise.ion_id">
                    <div class="text16px bold-text">Ajouter vos licences :</div>

                    <div class="flex flex-col mt-4">
                        <div class="searchbar" ref="searchBarContainer">
                            <img class="image20x20px" loading="lazy" width="20" height="20"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                            <input type="text" class="text14px grey600 light w-full p-0" style="margin-bottom: 0;"
                                ref="searchbar" id="searchbar" placeholder="Rechercher" v-model="searchTerm"
                                autocomplete="off">
                        </div>

                        <div class="separatorhorizontal mt-4 mb-4"></div>
                        <div v-if="filteredLicencesAvailable.length" class="w_container vertical gap12px _356px">
                            <div v-for="licence in filteredLicences" :key="licence.id" @click="addLicence(licence.id)"
                                class="containerlicencedispo"
                                :class="{ 'selected': form.licences.includes(licence.id) }">
                                <div class="image_container tiny">
                                    <img :src="licence.image_principale" loading="lazy" alt="" class="contain">
                                </div>
                                <div class="w_container vertical gap24px">
                                    <div class="flex justify-between items-center">
                                        <div class="l_container">
                                            <div class="text16px unbounded bold-text">{{ licence.name }}
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="description_licence_container">
                                                <div class="text14px black">{{ licence.total }}</div>
                                                <div class="text14px black">disponible(s)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="form.tenant === 'microsoft'" class="w_container vertical gap12px _356px">
                            <div class="text14px aligncenter">Vous n'avez pas de
                                licences disponibles. Vous pouvez vous
                                en procurer via le catalogue en cliquant sur le bouton ci-dessous</div>
                        </div>
                        <div v-else-if="form.tenant === 'google'" class="w_container vertical gap12px _356px">
                            <div class="text14px aligncenter">
                                Vous n'avez plus de licence Google Workspace disponible. <br>
                                Avant de continuer l'onboarding, vous devez rajouter des licences Google Workspace. <br>
                                Vous pouvez le faire en cliquant sur "Ajouter des licences" depuis cette page : <a class="underline text-blue-500"
                                    :href="`/mes-licences/${props.licenceGoogleRef.slug}`">Mes licences {{ props.licenceGoogleRef.name }}</a>
                            </div>
                        </div>

                        <div v-if="form.tenant === 'microsoft'" class="button secondary w-fit m-auto mt-4"
                            @click="showCatalogueLicences = true">
                            Choisir mes licences depuis le catalogue Microsoft
                        </div>
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <div v-if="form.tenant !== 'google'" class="button secondary" @click="nextStep">
                            Passer cette étape
                        </div>

                        <div class="button" @click="nextStep">
                            Continuer
                        </div>
                    </div>
                </div>

                <div class="mt-10" v-if="currentStep == 'equipements'">
                    <div class="text16px bold-text">Ajouter des équipements :</div>

                    <div class="flex flex-col mt-4">
                        <div class="searchbar" ref="searchBarContainer">
                            <img class="image20x20px" loading="lazy" width="20" height="20"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                            <input type="text" class="text14px grey600 light w-full p-0" style="margin-bottom: 0;"
                                ref="searchbar" id="searchbar" placeholder="Rechercher" v-model="searchTerm"
                                autocomplete="off">
                        </div>

                        <div class="separatorhorizontal mt-4 mb-4"></div>
                        <div v-if="props.equipementsAvailable.length" class="w_container vertical gap12px _356px">
                            <div v-for="equipement in filteredEquipements" :key="equipement.id"
                                @click="addEquipement(equipement.id)" class="containerlicencedispo"
                                :class="{ 'selected': form.equipements.includes(equipement.id) }">
                                <div class="image_container tiny">
                                    <img :src="equipement.image_principale" loading="lazy" alt="" class="contain">
                                </div>
                                <div class="w_container vertical gap24px">
                                    <div class="flex justify-between items-center">
                                        <div class="l_container flex flex-col">
                                            <div class="text16px unbounded bold-text">{{ equipement.name }}
                                            </div>
                                            <div class="text12px gray">
                                                {{ equipement.numero_unique }}
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="description_licence_container">
                                                <div class="text14px black">{{ equipement.total }}</div>
                                                <div class="text14px black">disponible(s)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="w_container vertical gap12px _356px">
                            <div class="text14px aligncenter">Vous n'avez pas d'équipements disponibles. Vous pouvez
                                vous
                                en procurer via le catalogue en cliquant sur le bouton ci-dessous</div>
                        </div>
                    </div>

                    <div class="button secondary w-fit m-auto mt-4" @click="showCatalogueEquipements = true">
                        Choisir mes équipements depuis le catalogue
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <div class="button secondary" @click="nextStep">
                            Passer cette étape
                        </div>

                        <div class="button" @click="nextStep">
                            Continuer
                        </div>
                    </div>
                </div>

                <div class="mt-10" v-if="currentStep == 'commande'">
                    <PassageCommandeOnboarding @updateFormCommande="updateFormCommande"
                        :equipements="form.equipementsMarketPlace" :licences="form.licencesMarketPlace" />
                </div>

                <div class="mt-4" v-if="currentStep == 'confirmer'">
                    <div class="text16px bold-text">Confirmer les informations sur le nouveau collaborateur :</div>

                    <div class="flex flex-col mt-4">
                        <div class="tagblock violet">
                            1. Informations personnelles
                        </div>
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="flex gap-2 items-center">
                                <div class="text14px bold-text">Prénom :</div>
                                <div class="text14px">{{ form.fname }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text14px bold-text">Nom :</div>
                                <div class="text14px">{{ form.lname }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text14px bold-text">Poste :</div>
                                <div class="text14px">{{ form.poste }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text14px bold-text">Date d'arrivée :</div>
                                <div class="text14px">{{ form.date_arrive }}</div>
                            </div>
                            <div class="flex gap-2">
                                <div class="text14px bold-text">Téléphone :</div>
                                <div class="text14px">{{ form.phone }}</div>
                            </div>
                            <div class="flex gap-2 items-center">
                                <div class="text14px bold-text">Tags :</div>
                                <div v-for="tag in form.tags" class="tagblock w-fit cursor-pointer"
                                    :style="{ 'color': `var(--${props.tags.find(e => e.id == tag).color})`, 'backgroundColor': `var(--${props.tags.find(e => e.id == tag).color}-light)` }">
                                    <div class="texttag">{{ props.tags.find(e => e.id == tag).name }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="tagblock violet mt-4">
                            2. Adresse(s) email(s)
                        </div>
                        <div class="flex items-center gap-4 justify-start">
                            <div class="flex flex-col gap-4">
                                <div class="flex gap-2">
                                    <div class="text14px bold-text">Email pro</div>
                                    <div class="text14px">{{ form.pattern }}@{{ form.domain }}</div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="text14px bold-text">Email perso :</div>
                                    <div class="text14px">{{ form.email_perso }}</div>
                                </div>
                            </div>
                            <div class="toggle selected">
                                <img :src="'/images/' + form.tenant + '-logo.png'" loading="lazy" alt=""
                                    class="image20x20px">
                                <div class="text14px capitalize">{{ form.tenant }}</div>
                            </div>
                        </div>

                        <div class="tagblock violet mt-4">
                            3. Licences
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template v-if="form.licences.length || form.licencesMarketPlace.length">
                                <div v-for="licence in form.licences" :key="licence" class="containerlicencedispo _2">
                                    <div class="image_container tiny">
                                        <img :src="props.licencesAvailable.find(e => e.id == licence).image_principale"
                                            loading="lazy" alt="" class="contain">
                                    </div>

                                    <div class="w_container vertical gap24px">
                                        <div class="flex items-center">
                                            <div class="l_container">
                                                <div class="text14px unbounded bold-text">{{
                                                    props.licencesAvailable.find(e => e.id == licence).name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="licence in form.licencesMarketPlace" :key="licence"
                                    class="containerlicencedispo _2">
                                    <div class="image_container tiny">
                                        <img :src="props.licencesMarketPlace.find(e => e.id == licence).image_principale"
                                            loading="lazy" alt="" class="contain">
                                    </div>
                                    <div class="w_container vertical gap24px">
                                        <div class="flex items-center gap-4">
                                            <div class="l_container">
                                                <div class="text14px unbounded bold-text">{{
                                                    props.licencesMarketPlace.find(e => e.id == licence).name
                                                }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="text14px">Aucune licence assignée</div>
                            </template>
                        </div>

                        <div class="tagblock violet mt-4">
                            4. Équipements
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <template v-if="form.equipements.length || form.equipementsMarketPlace.length">
                                <div v-for="equipement in form.equipements" :key="equipement"
                                    class="containerlicencedispo _2">
                                    <img :src="props.equipementsAvailable.find(e => e.id == equipement).image_principale"
                                        loading="lazy" alt="" class="image40x40px">
                                    <div class="w_container vertical gap24px">
                                        <div class="flex items-center">
                                            <div class="l_container">
                                                <div class="text14px unbounded bold-text">{{
                                                    props.equipementsAvailable.find(e => e.id == equipement).name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-for="equipement in form.equipementsMarketPlace" :key="equipement"
                                    class="containerlicencedispo _2">
                                    <img :src="props.equipementsMarketPlace.find(e => e.id == equipement).image_principale"
                                        loading="lazy" alt="" class="image40x40px">
                                    <div class="w_container vertical gap24px">
                                        <div class="flex items-center gap-4">
                                            <div class="l_container">
                                                <div class="text14px unbounded bold-text">{{
                                                    props.equipementsMarketPlace.find(e => e.id ==
                                                        equipement).name
                                                }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <div class="text14px">Aucun équipement assigné</div>
                            </template>
                        </div>
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <button type="submit" :disabled="form.processing"
                            :class="['button gap-5', form.processing ? 'gray' : '']">
                            <div class="text14px white">
                                Confirmer
                            </div>
                            <span v-if="form.processing" class="loader small"></span>

                        </button>
                    </div>
                </div>

                <div class="mt-4" v-if="currentStep == 'success'">
                    <div class="text16px bold-text mb-4">Félicitations ! {{ form.fname }} {{ form.lname }} a été
                        onboardé(e) avec succès.</div>

                    <div class="flex flex-col gap-4">
                        <div class="text14px">
                            Une adresse email professionnelle lui a été créée :
                            <span class="bold-text">{{ form.email }}</span>.<br><br>
                            Les instructions pour la première connexion lui ont été envoyées à son adresse email
                            personnelle
                            :
                            <span class="bold-text">{{ form.email_perso }}</span>.<br><br>
                        </div>

                        <div class="text14px" v-if="form.licences && form.licences.length">
                            Les licences suivantes lui ont été assignées avec succès :
                            <ul>
                                <li v-for="licence in form.licences" :key="licence">
                                    <span class="bold-text">{{ props.licencesAvailable.find(e => e.id ==
                                        licence).name
                                        }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="text14px" v-if="form.equipements && form.equipements.length">
                            Les équipements suivants lui ont été assignés avec succès :
                            <ul>
                                <li v-for="equipement in form.equipements" :key="equipement.id">
                                    <span class="bold-text">{{ props.equipementsAvailable.find(e => e.id ==
                                        equipement).name }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="text14px" v-if="form.licencesMarketPlace && form.licencesMarketPlace.length">
                            Les licences supplémentaires lui ont été commandées et assignées avec succès :
                            <ul>
                                <li v-for="licence in form.licencesMarketPlace" :key="licence.id">
                                    <span class="bold-text">{{ props.licencesMarketPlace.find(e => e.id ==
                                        licence).name
                                        }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="text14px" v-if="form.equipementsMarketPlace && form.equipementsMarketPlace.length">
                            Les équipements supplémentaires lui ont été commandés et assignés avec succès :
                            <ul>
                                <li v-for="equipement in form.equipementsMarketPlace" :key="equipement.id">
                                    <span class="bold-text">{{ props.equipementsMarketPlace.find(e => e.id ==
                                        equipement).name }}</span>
                                </li>
                            </ul>
                        </div>

                        <div class="text14px">
                            {{ form.fname }} est prêt(e) à commencer.
                            Bienvenue à bord !
                        </div>
                    </div>

                    <div class="flex mt-16 mb-4 gap-2 justify-center">
                        <div class="button" @click="close()">
                            Fermer
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCatalogueLicences" class="componentcontainer flex-col" style="width: 600px;">
            <div class="text16px bold-text">Catalogue > Licences</div>

            <div class="flex flex-col mt-4">
                <div class="searchbar" ref="searchBarContainer">
                    <img class="image20x20px" loading="lazy" width="20" height="20"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                    <input type="text" class="text14px grey600 light w-full p-0" style="margin-bottom: 0;"
                        ref="searchbar" id="searchbar" placeholder="Rechercher" v-model="searchTermMarketPlace"
                        autocomplete="off">
                </div>

                <div class="separatorhorizontal mt-4 mb-4"></div>
                <div class="w_container vertical gap12px _356px">
                    <div v-for="licence in filteredLicencesMarketPlace" :key="licence.id"
                        @click="addLicenceMarketPlace(licence.id)" class="containerlicencedispo"
                        :class="{ 'selected': form.licencesMarketPlace.includes(licence.id) }">
                        <img :src="licence.image_principale" loading="lazy" alt="" class="image40x40px">
                        <div class="w_container vertical gap24px">
                            <div class="flex justify-between items-center">
                                <div class="l_container w-4/5">
                                    <div class="text14px unbounded bold-text">{{ licence.name }}
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="description_licence_container flex-col">
                                        <div class="text14px black bold-text">{{ licence.prix_location }} €</div>
                                        <div class="text12px black">/mois</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showCatalogueEquipements" class="componentcontainer flex-col" style="width: 600px;">
            <div class="text16px bold-text">Catalogue > Equipements</div>

            <div class="flex flex-col mt-4">
                <div class="flex gap-2 position-relative">
                    <div class="searchbar w-full" ref="searchBarContainer">
                        <img class="image20x20px" loading="lazy" width="20" height="20"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                        <input type="text" class="text14px grey600 light w-full p-0" style="margin-bottom: 0;"
                            ref="searchbar" id="searchbar" placeholder="Rechercher" v-model="searchTermMarketPlace"
                            autocomplete="off">
                    </div>
                    <div v-on-click-outside="closeFilterOptions">
                        <div class="buttoncircle" @click="toggleFilterOptions">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2ab9102a4896b638bedf_Vectors-Wrapper.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                        </div>
                        <FilterOptionsEquipementsOnboarding :show="showFilterOptions"
                            @closeFilterOptions="closeFilterOptions"
                            @updateFilteredEquipements="updateFilteredEquipements">
                        </FilterOptionsEquipementsOnboarding>
                    </div>
                </div>

                <div class="separatorhorizontal mt-4 mb-4"></div>
                <div class="w_container vertical gap12px _356px">
                    <div v-for="equipement in filteredEquipementsMarketPlace" :key="equipement.id"
                        @click="addEquipementMarketPlace(equipement.id)" class="containerlicencedispo"
                        :class="{ 'selected': form.equipementsMarketPlace.includes(equipement.id) }">
                        <img :src="equipement.image_principale" loading="lazy" alt="" class="image40x40px">
                        <div class="w_container vertical gap24px">
                            <div class="flex justify-between items-center">
                                <div class="l_container w-4/5">
                                    <div class="text14px unbounded bold-text">{{ equipement.name }}
                                    </div>
                                    <div class="text12px gray">
                                        {{ equipement.proprietes }}
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="description_licence_container flex-col">
                                        <div class="text14px black bold-text">{{ equipement.prix_location }} €</div>
                                        <div class="text12px black">/ mois</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<style scoped>
.information .stepcontainer:nth-child(1) .circle,
.email .stepcontainer:nth-child(-n+2) .circle,
.licences .stepcontainer:nth-child(-n+3) .circle,
.equipements .stepcontainer:nth-child(-n+4) .circle,
.commande .stepcontainer:nth-child(-n+5) .circle,
.confirmer .stepcontainer .circle,
.success .stepcontainer .circle {
    background-color: var(--green);
}

.vector {
    border-top: 2px dashed var(--grey-400);
}

.email .vector1,
.licences .vector1,
.licences .vector2,
.equipements .vector1,
.equipements .vector2,
.equipements .vector3,
.commande .vector1,
.commande .vector2,
.commande .vector3,
.commande .vector4,
.confirmer .vector,
.success .vector {
    border-top: 2px solid var(--green);
}

.vector1 {
    left: 50px;
    width: 16%;
}

.vector2 {
    left: 26%;
    width: 16%;
}

.vector3 {
    left: 44%;
    width: 16%;
}

.vector4 {
    left: 60%;
    width: 16%;
}

.vector5 {
    left: 80%;
    width: 16%;
}

input,
select {
    background-color: #FFF;
    display: block;
    width: 100%;
    height: 38px;
    padding: 8px 12px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 1.42857143;
    vertical-align: middle;
    border: 1px solid #fff;
}

input.error,
select.error {
    border: 1px solid var(--red);
}

input.error:focus,
select.error:focus {
    border: 1px solid var(--red) !important;
}

.searchbar {
    border-radius: 0px;
    padding: 4px;
}

.containerlicencedispo {
    cursor: pointer;
    display: flex;
    overflow: hidden;
    padding: 16px 24px;
    gap: 24px;
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
    width: 100%;
    height: 80px;
    flex: 0 0 80px;
    max-height: 80px;
    justify-content: space-between;
    align-items: center;
    border-style: solid;
    border-width: 1px;
    border-color: rgb(193, 199, 208);
    box-sizing: border-box;
}

.containerlicencedispo._2 {
    padding: 8px;
    height: auto;
    flex: unset;
    width: fit-content !important;
}

.containerlicencedispo.selected {
    border: 2px solid var(--main);
}

.tagblock.violet {
    overflow: visible;
    width: fit-content;
    padding-top: 8px;
    padding-bottom: 8px;
    justify-content: flex-start;
    align-items: center;
    background-color: var(--lightpurple);
    margin-bottom: 20px;
}
</style>
