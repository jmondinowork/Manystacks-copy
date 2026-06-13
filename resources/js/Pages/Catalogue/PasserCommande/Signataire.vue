<script setup>
import PasserCommandeLayout from '@/Layouts/PasserCommandeLayout.vue';

import { usePage, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const { props } = usePage();


const productPriceLocation = (item) => item.product.prix_location;
const productPriceAchat = (item) => item.product.prix_achat;
const totalLocation = computed(() => {
    let totalPrice = 0;

    for (const address in props.pendingCommande) {
        const orders = props.pendingCommande[address];

        orders.filter(order => order.type_contrat === 'location').forEach(order => {
            const pricePerItem = order.product.prix_location;
            totalPrice += order.quantity * pricePerItem;
        });
    }

    return totalPrice.toFixed(2);
})

const totalAchat = computed(() => {
    let totalPrice = 0;

    for (const address in props.pendingCommande) {
        const orders = props.pendingCommande[address];

        orders.filter(order => order.type_contrat === 'achat').forEach(order => {
            const pricePerItem = order.product.prix_achat;
            totalPrice += order.quantity * pricePerItem;
        });
    }

    return totalPrice.toFixed(2);
})
const panierLength = computed(() => {
    let totalProducts = 0;

    for (const address in props.pendingCommande) {
        const orders = props.pendingCommande[address];
        orders.forEach(order => {
            totalProducts += order.quantity;
        });
    }
    return totalProducts > 1 ? `${totalProducts} articles` : `${totalProducts} article`;
});

const signatairePopup = ref(false);
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
    rgpd: false
};
const resetSignataire = () => {
    Object.assign(form, defaultSignataire);
    signatairePopup.value = false;
}
const selectSignataire = (signataireId) => {
    const signataire = props.signataires.find(s => s.id === signataireId);
    Object.assign(form, signataire, { rgpd: false });
    signatairePopup.value = false;
}

const form = useForm(props.selectedSignataire ? props.selectedSignataire : defaultSignataire);

const showDocument = ref(false);
const filePreview = ref(null);
const isPdfContent = ref(false);
const isLoading = ref(false);
const isInput = ref(false);
const fileFields = computed(() => {
    let fields = [
        { name: 'piece_identite_recto', title: 'Pièce d’identité du signataire (recto)', description: "lorem ipsum" },
        { name: 'piece_identite_verso', title: 'Pièce d’identité du signataire (verso)', description: "lorem ipsum" },
        { name: 'iban', title: "IBAN de l'entreprise", description: "lorem ipsum" },
    ];
    if (!form.representant_legal) {
        fields.push({ name: 'pouvoir', title: 'Pouvoir du signataire signé', description: "lorem ipsum" });
    }

    return fields;
})
const triggerFileInput = (fileID) => document.getElementById(fileID).click();
const handleFileChange = (event) => {
    const file = event.target.files[0];
    const name = event.target.name;
    errors.value[event.target.name] = '';
    if (file)
        form[name] = file;
};
const showPreviewDocument = (fileName) => {
    if (form[fileName] instanceof File) {
        isLoading.value = true;

        const reader = new FileReader();
        reader.onload = (e) => {
            if (form[fileName].type === 'application/pdf') {
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
        reader.readAsDataURL(form[fileName]);

    } else {
        let url = form[fileName].url;
        let extension = url.split('.').pop();

        isPdfContent.value = extension === 'pdf' ? true : false;
        isInput.value = false;
        filePreview.value = url;
    }
    showDocument.value = true
}

const errors = ref({
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
    'rgpd': ''
});

const validateField = (value, fieldName) => {
    if (!value) {
        if (fieldName == 'pouvoir' && form['representant_legal'] == true) {
            errors.value[fieldName] = '';
            return true;
        }
        else {
            errors.value[fieldName] = 'Ce champ est requis';
            return false;
        }
    }
    errors.value[fieldName] = '';
    return true;
}
const handleChange = (input) => validateField(form[input.target.id], input.target.id);
const submit = () => {
    form.processing = true;
    let hasError = 0;

    for (const fieldName in errors.value) {
        if (!validateField(form[fieldName], fieldName)) {
            hasError = 1;
            form.processing = false;
            return;
        }
    }

    if (hasError === 0) {
        form.post(route('store_signataire'));
    }

}
</script>

<template>
    <PasserCommandeLayout>
        <form @submit.prevent="submit">
            <div class="orderingcontainer h-full">
                <div class="componentcontainer height100 alignstretch minmax100">
                    <div class="w_container vertical gap24px overflowauto">
                        <div class="w_container vertical gap16px">
                            <Link :href="route('livraison')" class="w_container aligncenter gap8px clickable w-fit">
                            <img class="image16x16px"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65787c766a0ec3c317110ab2_Vectors-Wrapper.svg"
                                loading="lazy" width="16" height="16" />
                            <div class="text14px semibold">Retour</div>
                            </Link>
                            <div class="text20px unbounded">Renseignez les informations administratives</div>
                            <div class="text14px medium">Nous avons besoin de ces informations pour votre contrat de
                                location
                            </div>
                        </div>
                        <div class="separatorhorizontal"></div>

                        <div class="w_container vertical white round gap24px padding12px">

                            <div v-if="props.signataires.length" class="w_container vertical">
                                <div class="text14px medium">Choisir un signataire</div>
                                <div class="textinput grey cursor-pointer" @click="signatairePopup = !signatairePopup">
                                    <div class="text14px medium nowrap">{{ form.nom + " " + form.prenom }}</div>
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65699264fb6c60187bda0213_Vectors-Wrapper.svg"
                                        loading="lazy" width="20" height="20" alt="" class="vectors-wrapper-5">
                                </div>

                                <div v-if="signatairePopup" class="selectadresschoice">
                                    <div @click="resetSignataire"
                                        class="w_container vertical gap4px padding12px grey clickable">
                                        <div class="text14px medium">Nouveau signataire</div>
                                    </div>
                                    <div class="separatorhorizontal"></div>
                                    <div class="w_container vertical overflowauto">
                                        <div v-for="signataire in props.signataires" :key="signataire"
                                            @click="selectSignataire(signataire.id)"
                                            class="w_container vertical gap4px padding12px grey clickable">
                                            <div class="text14px medium">{{ signataire.nom + ' ' + signataire.prenom }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="text16px medium">Information du signataire</div>
                            <div class="w_container gap16px">
                                <div class="w_container vertical">
                                    <label for="prenom" class="text14px medium">Prénom <span
                                            class="red">*</span></label>
                                    <input v-model="form.prenom" id="prenom" class="textinput grey text14px"
                                        :class="{ 'input-error': errors.prenom }" @input="handleChange($event)"
                                        autocomplete="given-name">
                                    <div v-if="errors.prenom" class="error">{{ errors.prenom }}</div>
                                </div>
                                <div class="w_container vertical">
                                    <div class="text14px medium">Nom <span class="red">*</span></div>
                                    <input v-model="form.nom" id="nom" class="textinput grey text14px"
                                        :class="{ 'input-error': errors.nom }" @input="handleChange($event)"
                                        autocomplete="family-name">
                                    <div v-if="errors.nom" class="error">{{ errors.nom }}</div>
                                </div>
                            </div>
                            <div class="w_container gap16px">
                                <div class="w_container vertical">
                                    <div class="text14px medium">Téléphone <span class="red">*</span></div>
                                    <input v-model="form.telephone" id="telephone" class="textinput grey text14px"
                                        :class="{ 'input-error': errors.telephone }" @input="handleChange($event)"
                                        autocomplete="tel">
                                    <div v-if="errors.telephone" class="error">{{ errors.telephone }}</div>
                                </div>
                                <div class="w_container vertical">
                                    <div class="text14px medium">Mail <span class="red">*</span></div>
                                    <input v-model="form.mail" id="mail" class="textinput grey text14px"
                                        :class="{ 'input-error': errors.mail }" @input="handleChange($event)"
                                        autocomplete="email">
                                    <div v-if="errors.mail" class="error">{{ errors.mail }}</div>
                                </div>
                            </div>
                            <div class="w_container gap16px">
                                <div class="w_container vertical">
                                    <div class="text14px medium">Date de naissance <span class="red">*</span></div>
                                    <input v-model="form.date_naissance" id="date_naissance"
                                        :class="{ 'input-error': errors.date_naissance }" @input="handleChange($event)"
                                        class="textinput grey text14px" autocomplete="bday">
                                    <div v-if="errors.date_naissance" class="error">{{ errors.date_naissance }}</div>
                                </div>
                                <div class="w_container vertical">
                                    <div class="text14px medium">Ville de naissance <span class="red">*</span></div>
                                    <input v-model="form.ville_naissance" id="ville_naissance"
                                        :class="{ 'input-error': errors.ville_naissance }" @input="handleChange($event)"
                                        class="textinput grey text14px" autocomplete="address-level2">
                                    <div v-if="errors.ville_naissance" class="error">{{ errors.ville_naissance }}</div>
                                </div>
                            </div>
                        </div>


                        <div class="separatorhorizontal"></div>
                        <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                            <div class="text14px semibold">Le signataire est-il le représentant légal ?</div>
                            <div class="w_container white round toggles">
                                <div class="toggle" @click="form.representant_legal = true"
                                    :class="{ 'selected': form.representant_legal }">
                                    <div class="text14px">Oui</div>
                                </div>
                                <div class="toggle" @click="form.representant_legal = false"
                                    :class="{ 'selected': !form.representant_legal }">
                                    <div class="text14px">Non</div>
                                </div>
                            </div>
                        </div>

                        <div class="separatorhorizontal"></div>
                        <div class="w_container vertical white round gap12px padding12px">
                            <img src="" alt="">
                            <template v-for="(file, index) in fileFields" :key="file.title">
                                <div class="w_container aligncenter justifyspacebetween"
                                    :class="form[file.name] ? 'inputFilled' : 'inputUnfilled'">
                                    <div class="frame-167">
                                        <img class="image20x20px"
                                            :src="!form[file.name] ? 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6579d83c1b34b07a9544b00d_uncheck_grey_icon.svg' : 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579da913b466da17412d5ae_check_green_icon.svg'"
                                            alt="" loading="lazy" width="auto" height="auto" />
                                        <div class="w_container vertical gap2px">
                                            <div class="w_container aligncenter gap8px tooltip position-relative">
                                                <div class="text14px medium">{{ file.title }}</div>
                                                <span class="red">*</span>
                                                <img class="image20x20px"
                                                    src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6579d82522c5dfb065238c24_info_grey_icon.svg"
                                                    alt="" loading="lazy" width="auto" height="auto" />
                                                <div class="w_container popuphover">
                                                    <div class="text13px grey600 _100">{{ file.description }}</div>
                                                </div>
                                            </div>
                                            <div v-if="errors[file.name]" class="error">{{ errors[file.name] }}</div>
                                            <div v-if="form[file.name]" class="w_container aligncenter gap4px clickable"
                                                @click="showPreviewDocument(file.name)">
                                                <div class="text12px medium purple">{{ form[file.name].name }}</div>
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
                                        <div class="lightbutton" @click="() => form[file.name] = null">
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
                </div>

                <!-- Recapitulatif -->
                <div class="componentcontainer height100 alignstretch minmax100">
                    <div class="recap_container">
                        <div class="text20px unbounded">Récapitulatif</div>
                        <div class="recapsmallcontainer">
                            <div class="w_container vertical gap12px">
                                <div v-for="(item, index) in props.pendingCommande" :key="index"
                                    class="w_container vertical gap16px white round padding12px">
                                    <div class="w_container vertical gap4px">
                                        <div class="text14px semibold">Expédier au</div>
                                        <div class="text16px">{{ index }}</div>
                                    </div>
                                    <div class="separatorhorizontal"></div>
                                    <div v-for="commande in item" :key="commande.id"
                                        class="w_container aligncenter gap16px">
                                        <div class="w_container _80x80 grey">
                                            <div class="productimagecontainer"
                                                :style="{ 'background-image': 'url(' + commande.product.image_principale + ')' }">
                                            </div>
                                        </div>
                                        <div class="w_container vertical gap2px">
                                            <div class="text16px medium">{{ commande.product.name }}</div>
                                            <div class="w_container justifyspacebetween">
                                                <div class="text20px unbounded">x {{ commande.quantity }}</div>
                                                <div class="w_container alignend"
                                                    v-if="commande.type_contrat === 'location'">
                                                    <div class="text20px unbounded">{{ productPriceLocation(commande) }}
                                                        €</div>
                                                    <div class="text14px unbounded">/mois</div>
                                                </div>
                                                <div class="w_container alignend" v-else>
                                                    <div class="text20px unbounded">{{ productPriceAchat(commande) }} €
                                                    </div>
                                                    <div class="text14px unbounded">/unité</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w_container vertical gap12px">
                            <div class="text14px">{{ panierLength }}</div>
                            <div class="w_container aligncenter justifyspacebetween" v-if="totalLocation > 0">
                                <div class="text14px semibold">Sous-total :</div>
                                <div class="w_container alignend">
                                    <div class="text20px unbounded">{{ totalLocation }} €</div>
                                    <div class="text14px unbounded">/mois</div>
                                </div>
                            </div>
                            <div class="w_container aligncenter justifyspacebetween" v-if="totalAchat > 0">
                                <div class="text14px semibold">Sous-total :</div>
                                <div class="w_container alignend">
                                    <div class="text20px unbounded">{{ totalAchat }} €</div>
                                    <div class="text14px unbounded"> En achat</div>
                                </div>
                            </div>
                            <div class="w_container gap8px _100">
                                <input type="checkbox" v-model="form.rgpd" id="rgpd" class="checkboxunselected"
                                    :class="{ 'input-error': errors.rgpd }" @change="handleChange($event)" />
                                <label class="text14px italic">
                                    J'ai lu et j'accepte les <span class="text-span-4">conditions générales</span>, la
                                    <span class="text-span-5">politique de protection des données</span>, et les <span
                                        class="text-span-6">conditions générales de vente</span>.
                                </label>
                            </div>
                            <div v-if="errors.rgpd" class="error">{{ errors.rgpd }}</div>
                            <button type="submit" :disabled="form.processing"
                                :class="['bigbutton gap-5', form.processing ? 'gray' : 'purple']">
                                <div class="text14px white">Passer commande</div>
                                <span v-if="form.processing" class="loader small"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </PasserCommandeLayout>

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
</template>

<style scoped>
form {
    height: calc(100vh - 126px);
}

.toggle.selected div {
    color: var(--main);
}

.popuphover {
    display: none;
    top: -30px;
    right: 0;
}

.tooltip:hover .popuphover {
    display: block;
}

.modifyInputButton,
.addInputButton {
    display: none;
}

.inputFilled .modifyInputButton,
.inputUnfilled .addInputButton {
    display: flex;
}
</style>
