<script setup>
import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();

const formValidation = useForm({
    id: props.commande.id
});
const submitValidation = () => {
    formValidation.post(route('commande.validation'), {
        onFinish: () => window.location.reload(true),
    });
};

const formSign_again = useForm({
    id: props.commande.id,
    lien_contrat: null
})
const submitSign_again = () => {
    formSign_again.post(route('commande.sign_again'), {
        onFinish: () => window.location.reload(true),
    });
}
</script>

<template>
    <div class="flex flex-col justify-between h-full">
        <form @submit.prevent="submitValidation">
            <div class="text16px medium mb-4">
                La signature du contrat a bien été effectué par le client :
            </div>
            <button type="submit" :disabled="formValidation.processing" class="bigbutton purple">
                <div class="text14px white">
                    Attester la signature du contrat
                </div>
            </button>
        </form>

        <form @submit.prevent="submitSign_again">
            <div class="w_container vertical gap20px">
                <div class="w_container vertical">
                    <div class="text16px medium">
                        Le client a cliqué sur le cta de signature mais n'a pas signé le contrat :
                    </div>
                    <div class="text14px medium mb-4">
                        Changer le lien de signature (laisser vide si aucun changement)
                    </div>
                    <div class="textinput">
                        <input class="text14px w-full" type="text" v-model="formSign_again.lien_contrat" placeholder="Nouveau lien">
                    </div>
                </div>
                <div class="w_container vertical gap8px">
                    <button type="submit" :disabled="formSign_again.processing" class="bigbutton purple">
                        <div class="text14px white">
                            Redemander une signature
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
