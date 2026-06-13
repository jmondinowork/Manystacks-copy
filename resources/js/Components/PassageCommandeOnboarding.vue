<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { userInitials } from '@/functions';

import AttributeLivraisonCommande from '@/Components/AttributeLivraisonCommande.vue';

const { props } = usePage();

const emit = defineEmits(['updateFormCommande']);
const updateFormCommande = (form) => {
    emit('updateFormCommande', form);
}
const data = defineProps({
    equipements: {
        type: Object,
        default: null,
    },
    licences: {
        type: Object,
        default: null,
    }
});
const currentStep = ref(data.equipements.length ? 'entreprise' : 'lincenceOnly');
const defaultEntrepriseInfo = {
    raison_sociale: '',
    adresse: '',
    complement_adresse: '',
    code_postal: '',
    ville: '',
    pays: 'France'
};
const defaultSignataire = {
    id: 0,
    prenom: "",
    nom: "",
    telephone: "",
    mail: "",
    date_naissance: "",
    ville_naissance: "",
    representant_legal: true,
    piece_identite_recto: false,
    piece_identite_verso: false,
    pouvoir: false,
    iban: false,
};
const entrepriseInfo = computed(() => { return props.entrepriseInfo ? props.entrepriseInfo.entreprise : defaultEntrepriseInfo });
const signataireinfo = computed(() => { return props.selectedSignataire ? props.selectedSignataire : defaultSignataire });

const form = useForm({
    entreprise: {
        siret: entrepriseInfo.value.siret,
        raison_sociale: entrepriseInfo.value.raison_sociale,
        adresse: entrepriseInfo.value.adresse,
        complement_adresse: entrepriseInfo.value.complement_adresse,
        code_postal: entrepriseInfo.value.code_postal,
        ville: entrepriseInfo.value.ville,
        pays: entrepriseInfo.value.pays
    },
    livraison: {
        user: {
            name: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').name,
            profile_img: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').profile_img,
            id: props.collaborateurs.find(user => user.role === 'admin' || user.role === 'superadmin').id
        },
        address: {
            titre: props.adresses.find(address => address.default === 1).titre,
            id: props.adresses.find(address => address.default === 1).id
        },
    },
    signataire: {
        id: signataireinfo.value.id,
        prenom: signataireinfo.value.prenom,
        nom: signataireinfo.value.nom,
        telephone: signataireinfo.value.telephone,
        mail: signataireinfo.value.mail,
        date_naissance: signataireinfo.value.date_naissance,
        ville_naissance: signataireinfo.value.ville_naissance,
        representant_legal: signataireinfo.value.representant_legal,
        piece_identite_recto: signataireinfo.value.piece_identite_recto,
        piece_identite_verso: signataireinfo.value.piece_identite_verso,
        pouvoir: signataireinfo.value.pouvoir,
        iban: signataireinfo.value.iban,
        rgpd: true
    }
});
const errors = ref({
    'entreprise': {
        'siret': '',
        'raison_sociale': '',
        'adresse': '',
        'code_postal': '',
        'ville': ''
    },
    'signataire': {
        'prenom': '',
        'nom': '',
        'telephone': '',
        'mail': '',
        'date_naissance': '',
        'ville_naissance': '',
        'piece_identite_recto': '',
        'piece_identite_verso': '',
        'pouvoir': '',
        'iban': '',
    }
});
const validateField = (value, fieldName) => {
    if (fieldName == 'pouvoir' && form.signataire['representant_legal'] == true) {
        errors.value[currentStep.value][fieldName] = '';
        return true;
    }
    if (!value) {
        errors.value[currentStep.value][fieldName] = 'Ce champ est requis';
        return false;
    }
    errors.value[currentStep.value][fieldName] = '';
    return true;
}
const handleChange = (input) => validateField(form[currentStep.value][input.target.id], input.target.id);

const nextStep = () => {
    let hasError = 0;

    switch (currentStep.value) {
        case 'lincenceOnly':
            updateFormCommande(form);
            break;
        case 'entreprise':
            for (const fieldName in errors.value[currentStep.value]) {
                if (!validateField(form.entreprise[fieldName], fieldName)) {
                    hasError = 1;
                }
            }

            if (hasError === 0) {
                currentStep.value = 'livraison';
            }
            break;
        case 'livraison':
            currentStep.value = 'signataire';
            break;
        case 'signataire':
            for (const fieldName in errors.value[currentStep.value]) {
                if (!validateField(form.signataire[fieldName], fieldName)) {
                    hasError = 1;
                }
            }

            if (hasError === 0) {
                updateFormCommande(form);
            }
            break;
    }
}

const showAttribute = ref(false);
const openAttribute = () => showAttribute.value = true;
const closeAttribute = () => showAttribute.value = false;

const onAttributeSelected = (user, address) => {
    form.livraison.user = user;
    form.livraison.address = address;
    closeAttribute();
}

const showDocument = ref(false);
const filePreview = ref(null);
const isPdfContent = ref(false);
const isLoading = ref(false);
const isInput = ref(false);
const fileFields = computed(() => {
    let fields = [
        { name: 'piece_identite_recto', title: 'Pièce d’identité du signataire (recto)' },
        { name: 'piece_identite_verso', title: 'Pièce d’identité du signataire (verso)' },
        { name: 'iban', title: "IBAN de l'entreprise" },
    ];
    if (!form.signataire.representant_legal) {
        fields.push({ name: 'pouvoir', title: 'Pouvoir du signataire signé' });
    }

    return fields;
})
const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const handleFileChange = (event) => {
    const file = event.target.files[0];
    const name = event.target.name;

    errors.value.signataire[event.target.name] = '';

    if (file)
        form.signataire[name] = file;
};
const showPreviewDocument = (fileName) => {
    if (form.signataire[fileName] instanceof File) {
        isLoading.value = true;

        const reader = new FileReader();
        reader.onload = (e) => {
            if (form.signataire[fileName].type === 'application/pdf') {
                isLoading.value = false;
                filePreview.value = `<object data="${e.target.result}" type="application/pdf" width="500px" height="800px"></object>`;
                isPdfContent.value = true;
            } else {
                isLoading.value = false;
                filePreview.value = e.target.result;
                isPdfContent.value = false
            }
        };
        isInput.value = true;
        reader.readAsDataURL(form.signataire[fileName]);

    } else {
        let url = form.signataire[fileName].url;
        let extension = url.split('.').pop();

        isPdfContent.value = extension === 'pdf' ? true : false;
        isInput.value = false;
        filePreview.value = url;
    }
    showDocument.value = true
}

const signatairePopup = ref(false);

const resetSignataire = () => {
    Object.assign(form.signataire, defaultSignataire);
    signatairePopup.value = false;
}
const selectSignataire = (signataireId) => {
    const signataire = props.signataires.find(s => s.id === signataireId);
    Object.assign(form.signataire, signataire);
    signatairePopup.value = false;
}

const total = computed(() => {
    let total = 0;

    if (data.licences.length) {
        for (const licence of data.licences) {
            total += parseFloat(props.licencesMarketPlace.find(e => e.id == licence).prix_location);
        }
    }
    if (data.equipements.length) {
        for (const equipement of data.equipements) {
            total += parseFloat(props.equipementsMarketPlace.find(e => e.id == equipement).prix_location);
        }
    }

    return total.toFixed(2);
})
</script>

<template>
    <div class="flex justify-between">
        <div class="text16px bold-text">Passage de commande :</div>
        <div class="text14px gray bold-text">Total : {{ total }} €/mois</div>
    </div>

    <!-- HEADER -->
    <div class="flex flex-wrap gap-2 my-6">
        <div v-for="licence in data.licences" :key="licence" class="containerlicencedispo _2">
            <img :src="props.licencesMarketPlace.find(e => e.id == licence).image_principale" loading="lazy" alt=""
                class="image40x40px">
            <div class="w_container vertical gap24px">
                <div class="flex items-center">
                    <div class="flex flex-col gap-2">
                        <div class="text14px unbounded bold-text">{{
                            props.licencesMarketPlace.find(e => e.id == licence).name }}
                        </div>
                        <div class="text14px">
                            {{ props.licencesMarketPlace.find(e => e.id == licence).prix_location }} €/mois | quantité : 1
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-for="equipement in data.equipements" :key="equipement" class="containerlicencedispo _2">
            <img :src="props.equipementsMarketPlace.find(e => e.id == equipement).image_principale" loading="lazy"
                alt="" class="image40x40px">
            <div class="w_container vertical gap24px">
                <div class="flex items-center gap-4">
                    <div class="flex flex-col gap-2">
                        <div class="text14px unbounded bold-text">{{
                            props.equipementsMarketPlace.find(e => e.id == equipement).name
                        }}
                        </div>
                        <div class="text14px">
                            {{ props.equipementsMarketPlace.find(e => e.id == equipement).prix_location }} €/mois | quantité : 1
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <template v-if="currentStep == 'entreprise'">
        <div class="text14px mb-8 bold-text">1. Renseignez les informations sur votre entreprise</div>
        <div class="w_container gap16px mb-2">
            <div class="w_container vertical">
                <label for="siret" class="text14px medium">SIRET</label>
                <div class="textinput text14px cursor-not-allowed">{{ form.entreprise.siret }}</div>
            </div>
            <div class="w_container vertical">
                <label for="raison_sociale" class="text14px medium">Raison sociale <span class="red">*</span></label>
                <input type="text" @input="handleChange($event)" class="textinput text14px"
                    :class="{ 'input-error': errors.entreprise.raison_sociale }" id="raison_sociale"
                    v-model="form.entreprise.raison_sociale" autocomplete="organization">
                <div v-if="errors.entreprise.raison_sociale" class="error">{{ errors.entreprise.raison_sociale }}</div>
            </div>
        </div>

        <div class="w_container gap16px mb-2">
            <div class="w_container vertical">
                <label for="adresse" class="text14px medium">Adresse <span class="red">*</span></label>
                <input type="text" @input="handleChange($event)" class="textinput text14px"
                    :class="{ 'input-error': errors.entreprise.adresse }" id="adresse" v-model="form.entreprise.adresse"
                    autocomplete="street-address">
                <div v-if="errors.entreprise.adresse" class="error">{{ errors.entreprise.adresse }}</div>
            </div>

            <div class="w_container vertical">
                <label for="complement_adresse" class="text14px medium">Complément d’adresse</label>
                <input type="text" class="textinput text14px" id="complement_adresse"
                    v-model="form.entreprise.complement_adresse">
            </div>
        </div>

        <div class="w_container gap16px mb-2">
            <div class="w_container vertical">
                <label for="code_postal" @input="handleChange($event)" class="text14px medium">Code
                    Postal
                    <span class="red">*</span></label>
                <input type="text" class="textinput text14px" :class="{ 'input-error': errors.entreprise.code_postal }"
                    id="code_postal" v-model="form.entreprise.code_postal" autocomplete="postal-code">
                <div v-if="errors.entreprise.code_postal" class="error">{{ errors.entreprise.code_postal }}</div>
            </div>
            <div class="w_container vertical">
                <label for="ville" @input="handleChange($event)" class="text14px medium">Ville <span
                        class="red">*</span></label>
                <input type="text" class="textinput text14px" :class="{ 'input-error': errors.entreprise.ville }"
                    id="ville" v-model="form.entreprise.ville" autocomplete="on">
                <div v-if="errors.entreprise.ville" class="error">{{ errors.entreprise.ville }}</div>
            </div>
        </div>


        <div class="w_container vertical">
            <div class="text14px medium">Pays</div>
            <div class="textinput text14px justify-start cursor-not-allowed">
                <img src="/images/fr-flags.png" loading="lazy" alt="" class="flags">
                <div class="text14px medium nowrap pl-2">France</div>
            </div>
        </div>
    </template>

    <template v-if="currentStep == 'livraison'">
        <div class="text14px mb-8 bold-text">2. Confirmer l'adresse de livraison</div>

        <div class="w_container vertical gap16px white round padding12px">
            <div class="w_container vertical gap12px">
                <div class="w_container aligncenter justifyspacebetween">
                    <div class="text14px medium">Adresse d’envoi <span class="red">*</span></div>
                </div>
                <div @click="openAttribute"
                    class="w_container justifyspacebetween _100 height40px aligncenter padding12px backgroundgrey cursor-pointer">
                    <div class="w_container aligncenter">
                        <img v-if="form.livraison.user.profile_img" class="avatarcontainer"
                            :src="form.livraison.user.profile_img" alt="">
                        <div v-else class="avatarcontainer">
                            <div class="text16px medium white">{{
                                userInitials(form.livraison.user.name) }}</div>
                        </div>
                        <div class="text14px medium nowrap p-2">
                            {{ form.livraison.user.name + ' - ' +
                                form.livraison.address.titre }}
                        </div>
                    </div>
                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                        loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                </div>
            </div>

        </div>
    </template>

    <template v-if="currentStep == 'signataire'">
        <div class="text14px mb-8 bold-text">3. Renseignez les information du signataire</div>

        <div class="w_container vertical gap24px overflowauto">
            <div class="w_container vertical round gap24px">
                <div v-if="props.signataires.length" class="w_container vertical">
                    <div class="text14px medium">Choisir un signataire</div>
                    <div class="textinput cursor-pointer bg-white" @click="signatairePopup = !signatairePopup">
                        <div class="text14px medium nowrap">{{ form.signataire.nom + " " + form.signataire.prenom }}
                        </div>
                        <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                            loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                    </div>

                    <div v-if="signatairePopup" class="selectadresschoice">
                        <div @click="resetSignataire" class="w_container vertical gap4px padding12px grey clickable">
                            <div class="text14px medium">Nouveau signataire</div>
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div class="w_container vertical overflowauto">
                            <div v-for="signataire in 
                            " :key="signataire"
                                @click="selectSignataire(signataire.id)"
                                class="w_container vertical gap4px padding12px grey clickable">
                                <div class="text14px medium">{{ signataire.nom + ' ' + signataire.prenom }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="w_container gap16px">
                    <div class="w_container vertical ">
                        <label for="prenom" class="text14px medium">Prénom <span class="red">*</span></label>
                        <input v-model="form.signataire.prenom" id="prenom" class="textinput text14px"
                            :class="{ 'input-error': errors.signataire.prenom }" @input="handleChange($event)"
                            autocomplete="given-name">
                        <div v-if="errors.signataire.prenom" class="error">{{ errors.signataire.prenom }}</div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">Nom <span class="red">*</span></div>
                        <input v-model="form.signataire.nom" id="nom" class="textinput text14px"
                            :class="{ 'input-error': errors.signataire.nom }" @input="handleChange($event)"
                            autocomplete="family-name">
                        <div v-if="errors.signataire.nom" class="error">{{ errors.signataire.nom }}</div>
                    </div>
                </div>
                <div class="w_container gap16px">
                    <div class="w_container vertical">
                        <div class="text14px medium">Téléphone <span class="red">*</span></div>
                        <input v-model="form.signataire.telephone" id="telephone" class="textinput text14px"
                            :class="{ 'input-error': errors.signataire.telephone }" @input="handleChange($event)"
                            autocomplete="tel">
                        <div v-if="errors.signataire.telephone" class="error">{{ errors.signataire.telephone }}</div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">Mail <span class="red">*</span></div>
                        <input v-model="form.signataire.mail" id="mail" class="textinput text14px"
                            :class="{ 'input-error': errors.signataire.mail }" @input="handleChange($event)"
                            autocomplete="email">
                        <div v-if="errors.signataire.mail" class="error">{{ errors.signataire.mail }}</div>
                    </div>
                </div>
                <div class="w_container gap16px">
                    <div class="w_container vertical">
                        <div class="text14px medium">Date de naissance <span class="red">*</span></div>
                        <input v-model="form.signataire.date_naissance" id="date_naissance"
                            :class="{ 'input-error': errors.signataire.date_naissance }" @input="handleChange($event)"
                            class="textinput text14px" autocomplete="bday">
                        <div v-if="errors.signataire.date_naissance" class="error">{{ errors.signataire.date_naissance
                            }}
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">Ville de naissance <span class="red">*</span></div>
                        <input v-model="form.signataire.ville_naissance" id="ville_naissance"
                            :class="{ 'input-error': errors.signataire.ville_naissance }" @input="handleChange($event)"
                            class="textinput text14px" autocomplete="address-level2">
                        <div v-if="errors.signataire.ville_naissance" class="error">{{ errors.signataire.ville_naissance
                            }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="separatorhorizontal"></div>
            <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                <div class="text14px semibold">Le signataire est-il le représentant légal ?</div>
                <div class="w_container white round toggles">
                    <div class="toggle" @click="form.signataire.representant_legal = true"
                        :class="{ 'selected': form.signataire.representant_legal }">
                        <div class="text14px">Oui</div>
                    </div>
                    <div class="toggle" @click="form.signataire.representant_legal = false"
                        :class="{ 'selected': !form.signataire.representant_legal }">
                        <div class="text14px">Non</div>
                    </div>
                </div>
            </div>

            <div class="separatorhorizontal"></div>
            <div class="w_container vertical round gap12px">
                <img src="" alt="">
                <template v-for="(file, index) in fileFields" :key="file.title">
                    <div class="w_container aligncenter justifyspacebetween"
                        :class="form.signataire[file.name] ? 'inputFilled' : 'inputUnfilled'">
                        <div class="frame-167">
                            <img class="image20x20px"
                                :src="!form.signataire[file.name] ? 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6579d83c1b34b07a9544b00d_uncheck_grey_icon.svg' : 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579da913b466da17412d5ae_check_green_icon.svg'"
                                alt="" loading="lazy" width="auto" height="auto" />
                            <div class="w_container vertical gap2px">
                                <div class="w_container aligncenter gap8px tooltip position-relative">
                                    <div class="text14px medium">{{ file.title }}</div>
                                    <span class="red">*</span>
                                </div>
                                <div v-if="errors.signataire[file.name]" class="error">{{ errors.signataire[file.name]
                                    }}
                                </div>
                                <div v-if="form.signataire[file.name]" class="w_container aligncenter gap4px clickable"
                                    @click="showPreviewDocument(file.name)">
                                    <div class="text12px medium purple">{{ form.signataire[file.name].name }}</div>
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                </div>
                            </div>
                        </div>
                        <div class="w_container aligncenter gap8px addInputButton">
                            <div class="lightbutton" @click="() => triggerFileInput(file.name)">
                                <div class="frame-164">
                                    <div class="text14px medium purple">Ajouter</div>
                                </div>
                            </div>
                        </div>
                        <div class="w_container aligncenter gap8px modifyInputButton">
                            <div class="lightbutton" @click="() => triggerFileInput(file.name)">
                                <div class="frame-164">
                                    <div class="text14px medium purple">Modifier</div>
                                </div>
                            </div>
                            <div class="lightbutton" @click="() => form.signataire[file.name] = null">
                                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579db02389fa9aac727b450_trash_purple.svg"
                                    loading="lazy" alt="" class="image16x16px">
                            </div>
                        </div>
                        <input class="d-none" type="file" :name="file.name" :id="file.name" :ref="file.ref"
                            @input="handleFileChange($event)">
                    </div>

                    <div v-if="index < fileFields.length - 1" class="separatorhorizontal"></div>
                </template>
            </div>
        </div>

    </template>

    <div class="flex mt-16 mb-4 gap-2 justify-center">
        <div class="button" @click="nextStep">
            Continuer
        </div>
    </div>

    <div class="darkmodalbackground" :class="{ 'show': showDocument }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Aperçu
                </div>
                <div class="w_container alignright cursor-pointer" @click="showDocument = false">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div v-if="!isLoading" class="componentcontainer justify-center max-h-full">
                <div v-if="isInput && isPdfContent" v-html="filePreview"></div>
                <div v-else-if="!isInput && isPdfContent" style="height: 600px; width: 400px;">
                    <embed :src="filePreview" type="application/pdf" style="width:100%;height:100%;">
                </div>
                <img v-else :src="filePreview" alt="">
            </div>
        </div>
    </div>
    <AttributeLivraisonCommande :currentUser="form.livraison.user" :currentAddress="form.livraison.address"
        @onAttributeSelected="onAttributeSelected" @closeAttribute="closeAttribute" :show="showAttribute" />
</template>

<style scoped>
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
    padding: 12px;
    height: auto;
    flex: unset;
    width: fit-content !important;
}

.containerlicencedispo.selected {
    border: 2px solid var(--main);
}

form {
    height: calc(100vh - 126px);
}

.toggle.selected div {
    color: var(--main);
}

.modifyInputButton,
.addInputButton {
    display: none;
}

.inputFilled .modifyInputButton,
.inputUnfilled .addInputButton {
    display: flex;
}

input.error:focus,
select.error:focus {
    border: 1px solid var(--red) !important;
}
</style>
