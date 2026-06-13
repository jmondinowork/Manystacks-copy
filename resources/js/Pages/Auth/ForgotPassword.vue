<script setup>
import Annoucer from '@/Components/Annoucer.vue';
import ErrorAnnouncer from '@/Components/ErrorAnnouncer.vue';
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';

import { useForm } from '@inertiajs/vue3';
import { useStore } from 'vuex';

const store = useStore();
defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});


const submit = () => {
    form.processing = true;

    axios.post(route('password.email'), form)
        .then(response => {
            store.dispatch('updateAnnounce', response.data.message);
        })
        .catch(error => {
            store.dispatch('updateErrorAnnounce', error.response.data.message);
        })
        .finally(() => {
            form.processing = false;
        });
};
</script>

<template>
    <div class="componentmdp-oublie">
        <form @submit.prevent="submit" class="containertexte">
            <div class="containermdp-text">
                <ApplicationLogo class="image52x52px" />
                <div class="text20px bold-text">Mot de passe oublié ?</div>
            </div>
            <div class="containermdp-text">
                <div class="text14px text-align-center paragraphmdp">Vous avez oublié votre mot de passe ? Pas de
                    problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de
                    réinitialisation qui vous permettra d'en choisir un nouveau.</div>
            </div>
            <input id="email" type="email" class="form_input w-full" v-model="form.email" required autofocus
                autocomplete="username">
            <button type="submit" :disabled="form.processing" class="button continuer w-button">Récupérer votre mot de
                passe</button>
        </form>
    </div>

    <Annoucer></Annoucer>
    <ErrorAnnouncer></ErrorAnnouncer>
</template>

<style scoped>
.form_input {
    width: 100%;
    height: 60px;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--main);
    border-right-color: var(--main);
    border-bottom-color: var(--main);
    border-left-color: var(--main);
    border-radius: 4px;
    background-color: rgb(247, 248, 249);
}

.form_input:focus {
    border-color: rgb(112, 112, 255) !important;
}

.componentmdp-oublie {
    display: flex;
    overflow: visible;
    padding: 20px;
    width: 100%;
    height: 100vh;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--grey-100);
    border-right-color: var(--grey-100);
    border-bottom-color: var(--grey-100);
    border-left-color: var(--grey-100);
    border-radius: 12px;
    background-color: var(--grey-50);
}

.containertexte {
    display: flex;
    width: 700px;
    height: 600px;
    max-height: 100%;
    max-width: 100%;
    min-height: 50%;
    min-width: 30%;
    padding: 40px 62px;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--main);
    border-right-color: var(--main);
    border-bottom-color: var(--main);
    border-left-color: var(--main);
    border-radius: 8px;
    background-color: rgb(255, 255, 255);
}

.containermdp-text {
    display: flex;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
    gap: 24px;
}
</style>
