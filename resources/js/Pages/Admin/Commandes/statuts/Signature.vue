import { initCustomFormatter } from 'vue';
<script setup>
import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();

const form = useForm({
    id: props.commande.id
});

const submit = () => {
    form.processing = true;
    form.post(route('commande.validation'), {
        onFinish: () => window.location.reload(true),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col justify-between h-full">
        <div>
            <div class="text16px medium">
                Aucune action a faire ici, on attend que le client sur le cta de signature sur son hub.
            </div>
        </div>
        <div class="w_container vertical gap8px">
            <div class="text14px">
                Si jamais le client a signé le contrat sans cliquer sur le cta du hub on peut attester manuellement de
                la
                signature en cliquant ci-dessous 👇.
            </div>
            <button type="submit" :disabled="form.processing"
                :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                <div class="text14px white">
                    Attester la signature du contrat
                </div>
            </button>
        </div>
    </form>
</template>
