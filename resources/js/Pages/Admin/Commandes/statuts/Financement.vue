<script setup>
import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();

const form = useForm({
    financeur: '',
    lien_contrat: '',
    id: props.commande.id
});

const submit = () => {
    form.processing = true;
    form.post(route('commande.financeur'), {
        onFinish: () => window.location.reload(true),
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="w_container vertical gap20px">
            <div class="w_container vertical">
                <div class="text14px medium">
                    Financeur
                </div>
                <div class="textinput">
                    <input class="text14px w-full" type="text" v-model="form.financeur">
                </div>
            </div>
            <div class="w_container vertical">
                <div class="text14px medium">
                    Lien signature
                </div>
                <div class="textinput">
                    <input class="text14px w-full" type="text" v-model="form.lien_contrat">
                </div>
            </div>
            <div class="w_container vertical gap8px">
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
