<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();

const form = useForm({
    commandeId: props.commande.id,
    liensLivraison: props.allProducts.map(commandeProduct => ({
        id: commandeProduct.id,
        lienLivraison: commandeProduct.lien_livraison
    })),
    numerosUnique: props.allProducts.map(commandeProduct => ({
        id: commandeProduct.id,
        numeroUnique: commandeProduct.numero_unique
    }))
});

const submit = () => {
    form.processing = true;
    form.post(route('commande.livraison'), {
        onFinish: () => {
            store.dispatch('updateAnnounce', "Les informations de livraison ont été enregistrées avec succès.");
            form.processing = false;
            // window.location.reload(true)
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="text16px medium mb-4">Renseigne les liens de livraison pour chaque produit :</div>
        <div class="w_container vertical gap20px">
            <div class="w_container vertical gap12px white round padding20px">
                <template v-for="(commande, index) in props.allProducts" :key="commande.id">
                    <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                        <div class="w_container aligncenter gap16px">
                            <div class="w_container _80x80 grey">
                                <div class="productimagecontainer"
                                    :style="{ 'background-image': 'url(' + commande.image_principale + ')' }">
                                </div>
                            </div>
                            <div class="w_container vertical gap2px">
                                <div class="text16px medium">{{ commande.name }}</div>
                                <div class="text14px">{{ commande.ref_fournisseur }}</div>
                            </div>
                        </div>
                        <div class="gap2px">
                            <div class="text14px">
                                {{ commande.adresse_livraison.adresse }}
                            </div>
                            <div class="text14px">
                                {{ commande.adresse_livraison.code_postal + ' '
                                    + commande.adresse_livraison.ville + ', '
                                + commande.adresse_livraison.pays }}
                            </div>
                        </div>
                    </div>
                    <div class="textinput">
                        <input class="text14px w-full" type="text"
                            v-model="form.numerosUnique.find(item => item.id === commande.id).numeroUnique"
                            placeholder="Numéro unique du produit">
                    </div>
                    <div class="textinput">
                        <input class="text14px w-full" type="text"
                            v-model="form.liensLivraison.find(item => item.id === commande.id).lienLivraison"
                            placeholder="Lien de livraison du produit">
                    </div>

                    <div v-if="index < props.allProducts.length - 1" class="separatorhorizontal"></div>
                </template>
            </div>
            <div class="w_container vertical gap8px" style="margin-bottom: 20px;">
                <button type="submit" :disabled="form.processing"
                    :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                    <div class="text14px white">
                        Enregistrer
                    </div>
                </button>
            </div>
        </div>

    </form>
</template>

<style scoped>
.textinput,
.textinput .text14px {
    background-color: #F7F8F9;
}

.textinput .text14px::placeholder {
    font-size: 14px;
}
</style>
