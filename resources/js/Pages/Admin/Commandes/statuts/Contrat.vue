<script setup>
import { usePage, useForm } from '@inertiajs/vue3';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';

const { props } = usePage();

const todayDate = format(new Date(), 'dd/MM/yyyy', { locale: fr });
const futureDate = format(new Date(new Date().setFullYear(new Date().getFullYear() + 3)), 'dd/MM/yyyy', { locale: fr });
const form = useForm({
    id: props.commande.id,
    contrat_signe: null,
    date_debut: todayDate,
    date_fin: futureDate
});

const handleFileChange = (event) => form.contrat_signe = event.target.files[0];

const submit = () => {
    form.processing = true;
    form.post(route('commande.contrat'), {
        onFinish: () => window.location.reload(true),
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="w_container vertical gap20px">
            <div class="w_container vertical">
                <div class="text14px medium">
                    Contrat
                </div>
                <input type="file" :name="'contrat'" :id="'contrat'" @input="handleFileChange($event)">
            </div>
            <div class="w_container vertical">
                <div class="text14px medium">
                    Date de début
                </div>
                <div class="textinput">
                    <input class="text14px w-full" type="text" v-model="form.date_debut">
                </div>
            </div>
            <div class="w_container vertical">
                <div class="text14px medium">
                    Date de fin
                </div>
                <div class="textinput">
                    <input class="text14px w-full" type="text" v-model="form.date_fin">
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
