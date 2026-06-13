<script setup>
import PasserCommandeLayout from '@/Layouts/PasserCommandeLayout.vue';
import RecapitulatifCommandes from '@/Components/RecapitulatifCommandes.vue';

import { usePage, useForm, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { vOnClickOutside } from '@vueuse/components'

const { props } = usePage();

function getDefaultUserInfo() {
    return {
        entreprise: {
            raison_sociale: '',
            adresse: '',
            complement_adresse: '',
            code_postal: '',
            ville: '',
            pays: 'France'
        }
    };
}
const userInfo = computed(() => { return props.userInfo.entreprise ? props.userInfo : getDefaultUserInfo() })

const form = useForm({
    siret: userInfo.value.entreprise.siret,
    raison_sociale: userInfo.value.entreprise.raison_sociale,
    adresse: userInfo.value.entreprise.adresse,
    complement_adresse: userInfo.value.entreprise.complement_adresse,
    code_postal: userInfo.value.entreprise.code_postal,
    ville: userInfo.value.entreprise.ville,
    pays: userInfo.value.entreprise.pays
});
const errors = ref({
    'siret': '',
    'raison_sociale': '',
    'adresse': '',
    'code_postal': '',
    'ville': ''
});

const validateField = (value, fieldName) => {
    if (!value) {
        errors.value[fieldName] = 'Ce champ est requis';
        return false;
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
        }
    }

    if (hasError === 0) {
        form.post(route('store_entreprise_informations'));
        form.processing = false;
    }
}
</script>

<template>
    <PasserCommandeLayout>
        <form @submit.prevent="submit">
            <div class="orderingcontainer h-full">
                <div class="componentcontainer height100">
                    <div class="w_container vertical gap24px">
                        <div class="w_container vertical gap16px">
                            <Link :href="route('panier')" class="w_container aligncenter gap8px clickable w-fit">
                            <img class="image16x16px" loading="lazy" width="16" height="16"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65787c766a0ec3c317110ab2_Vectors-Wrapper.svg" />
                            <div class="text14px semibold">Retour au panier</div>
                            </Link>
                            <div class="text20px unbounded">Renseignez les informations sur votre entreprise</div>
                            <div class="text14px medium">Nous avons besoin de ces informations pour votre contrat de
                                location
                            </div>
                        </div>

                        <div class="separatorhorizontal"></div>

                        <div class="w_container gap16px">
                            <div class="w_container vertical">
                                <label for="siret" class="text14px medium">SIRET</label>
                                <div class="textinput text14px cursor-not-allowed">{{ form.siret }}</div>
                            </div>
                            <div class="w_container vertical">
                                <label for="raison_sociale" class="text14px medium">Raison sociale <span
                                        class="red">*</span></label>
                                <input type="text" @input="handleChange($event)" class="textinput text14px"
                                    :class="{ 'input-error': errors.raison_sociale }" id="raison_sociale"
                                    v-model="form.raison_sociale" autocomplete="organization">
                                <div v-if="errors.raison_sociale" class="error">{{ errors.raison_sociale }}</div>
                            </div>
                        </div>

                        <div class="w_container vertical">
                            <label for="adresse" class="text14px medium">Adresse <span class="red">*</span></label>
                            <input type="text" @input="handleChange($event)" class="textinput text14px"
                                :class="{ 'input-error': errors.adresse }" id="adresse" v-model="form.adresse"
                                autocomplete="street-address">
                            <div v-if="errors.adresse" class="error">{{ errors.adresse }}</div>
                        </div>

                        <div class="w_container vertical">
                            <label for="complement_adresse" class="text14px medium">Complément d’adresse</label>
                            <input type="text" class="textinput text14px" id="complement_adresse"
                                v-model="form.complement_adresse">
                        </div>

                        <div class="w_container gap16px">
                            <div class="w_container vertical">
                                <label for="code_postal" @input="handleChange($event)" class="text14px medium">Code
                                    Postal
                                    <span class="red">*</span></label>
                                <input type="text" class="textinput text14px"
                                    :class="{ 'input-error': errors.code_postal }" id="code_postal"
                                    v-model="form.code_postal" autocomplete="postal-code">
                                <div v-if="errors.code_postal" class="error">{{ errors.code_postal }}</div>
                            </div>
                            <div class="w_container vertical">
                                <label for="ville" @input="handleChange($event)" class="text14px medium">Ville <span
                                        class="red">*</span></label>
                                <input type="text" class="textinput text14px" :class="{ 'input-error': errors.ville }"
                                    id="ville" v-model="form.ville" autocomplete="on">
                                <div v-if="errors.ville" class="error">{{ errors.ville }}</div>
                            </div>
                        </div>


                        <div class="w_container vertical">
                            <div class="text14px medium">Pays</div>
                            <div class="textinput text14px justify-start cursor-not-allowed">
                                <img src="/images/fr-flags.png" loading="lazy" alt="" class="flags">
                                <div class="text14px medium nowrap pl-2">France</div>
                            </div>
                        </div>

                    </div>
                </div>

                <RecapitulatifCommandes :commandes="props.panier.panier_products">
                    <button type="submit" :disabled="form.processing"
                        :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                        <div class="text14px white">Continuer</div>
                    </button>
                </RecapitulatifCommandes>
            </div>
        </form>
    </PasserCommandeLayout>
</template>


<style scoped>
.componentcontainer {
    overflow: auto;
}

form {
    height: calc(100vh - 126px);
}

.select-item:hover {
    background-color: #F7F8F9;
}

.select-item {
    padding: 12px;
}

.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}

.textinput.pays {
    cursor: pointer !important;
}
</style>
