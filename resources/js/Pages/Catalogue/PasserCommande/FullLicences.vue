<script setup>
import PasserCommandeLayout from '@/Layouts/PasserCommandeLayout.vue';

import { usePage, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { props } = usePage();

const total = computed(() => {
    const sum = props.panier.reduce((acc, item) => acc + (item.prix ? item.prix : item.product.prix_location) * item.quantity, 0);
    return sum.toFixed(2);
});
const commandesLength = computed(() => {
    const length = props.panier.reduce((total, item) => total + parseInt(item.quantity, 10), 0);
    return length > 1 ? `${length} articles` : `${length} article`;
});

const form = useForm({
    rgpd: false,
    processing: false
});
const errors = ref({
    'rgpd': ''
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
            form.processing = false;
            return;
        }
    }

    if (hasError === 0)
        form.post(route('store-full-licences'));
}
</script>

<template>
    <PasserCommandeLayout :full_licence="true">
        <form @submit.prevent="submit">
            <div class="orderingcontainer h-full">
                <div class="componentcontainer height100 alignstretch minmax100">
                    <div class="w_container vertical gap24px overflowauto">
                        <div class="w_container vertical gap16px">
                            <Link :href="route('panier')" class="w_container aligncenter gap8px clickable w-fit">
                            <img class="image16x16px"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65787c766a0ec3c317110ab2_Vectors-Wrapper.svg"
                                loading="lazy" width="16" height="16" />
                            <div class="text14px semibold">Retour</div>
                            </Link>
                            <div class="text20px unbounded">Vos licences</div>
                        </div>
                        <div class="separatorhorizontal"></div>
                    </div>
                </div>

                <!-- Recapitulatif -->
                <div class="componentcontainer height100 alignstretch minmax100">
                    <div class="recap_container">
                        <div class="text20px unbounded">Récapitulatif</div>
                        <div class="recapsmallcontainer">

                            <div v-for="commande in props.panier" :key="commande.id"
                                class="w_container vertical gap12px">
                                <div class="w_container vertical gap16px white round padding12px">
                                    <div class="w_container aligncenter gap16px">
                                        <div class="w_container _80x80 grey">
                                            <div class="productimagecontainer"
                                                :style="{ 'background-image': 'url(' + (commande.product?.image_principale || commande.image_principale) + ')' }">
                                            </div>
                                        </div>
                                        <div class="w_container vertical gap2px">
                                            <div class="text16px medium">{{ commande.product?.name || commande.name }}
                                            </div>
                                            <div class="text14px">{{ commande.product?.proprietes || commande.proprietes
                                                }}</div>
                                        </div>
                                    </div>
                                    <div class="separatorhorizontal"></div>
                                    <div class="w_container justifyspacebetween">
                                        <div class="text20px unbounded">x {{ commande.quantity }}</div>
                                        <div class="w_container alignend">
                                            <div class="text20px unbounded">{{ commande.product.prix_location }} €</div>
                                            <div class="text14px unbounded">/mois</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="w_container vertical gap12px">
                            <div class="text14px">{{ commandesLength }}</div>
                            <div class="w_container aligncenter justifyspacebetween">
                                <div class="text14px semibold">Sous-total :</div>
                                <div class="w_container alignend">
                                    <div class="text20px unbounded">{{ total }} €</div>
                                    <div class="text14px unbounded">/mois</div>
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
