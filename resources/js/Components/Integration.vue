<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { onMounted, onUnmounted, ref } from "vue";

const emit = defineEmits(['closeIntegration']);

const close = () => {
    emit('closeIntegration');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const store = useStore();
const { props } = usePage();
const data = defineProps({
    integration: {
        type: Object,
        default: []
    }
});

const showIntegrationShit = ref(false);
const showIntegrationOauth = ref(false);

const syncIntegration = () => {
    if (data.integration.method == 'oauth')
        showIntegrationOauth.value = true;
    else
        showIntegrationShit.value = true;
}


const formShit = useForm({
    domain: '',
    apikey: '',
    name: '',
    processing: false,
})

const syncOauth = () => {
    const url = `/${data.integration.name}/auth/redirect?redirect_to=${window.location.pathname}`;
    window.location.href = url;
}

const errors = ref({
    domain: false,
    apikey: false,
});
const submitShit = () => {
    formShit.processing = true;

    if (formShit.domain && formShit.apikey) {
        if (formShit.domain.endsWith('/'))
            formShit.domain = formShit.domain.slice(0, -1);
        formShit.name = data.integration.name;

        try {
            axios.post(route('store_integrationshit'), formShit)
            .then(response => {
                store.dispatch('updateAnnounce', response.data.message);
                formShit.processing = false;
                formShit.reset();
                props.userAuth.oauth.push(data.integration.name);
                close();
            })
            .catch(error => {
                store.dispatch('updateErrorAnnounce', error.response.data.message);
                formShit.processing = false;
            });
        } catch (error) {
            formShit.processing = false;
        }
    }
    else {
        formShit.processing = false;
        errors.value.domain = !formShit.domain;
        errors.value.apikey = !formShit.apikey;
    }
}
</script>

<template>
    <div class="darkmodalbackground show">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="flex gap-4 items-center">
                    <img :src="data.integration.logo" :alt="data.integration.name + ' Logo'" class="w-16">
                    <div class="text20px unbounded text-capitalize">
                        Connectez votre compte {{ data.integration.title }} à Manystacks
                    </div>
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer" style="min-height: 400px;">
                <div class="w_container vertical" v-if="!showIntegrationShit && !showIntegrationOauth">
                    <template v-if="data.integration.type === 'tenant'">
                        <div class="text16px bold-text border-b">
                            Informations récupérées en lecture
                        </div>
                        <ul class="ml-4 list-disc">
                            <li>Nom, prénom</li>
                            <li>Adresse email professionnelle</li>
                            <li>Rôle dans l’organisation</li>
                            <li>Numéro de téléphone</li>
                            <li>Photo de profil</li>
                            <li>Informations sur les licences attribuées aux utilisateurs</li>
                        </ul>

                        <div class="text16px bold-text mt-4 border-b">
                            Clé d’accès permettant d’effectuer des
                            actions telles que :
                        </div>
                        <ul class="ml-4 list-disc">
                            <li>Créer ou supprimer des comptes utilisateurs</li>
                            <li>Attribuer ou retirer des licences aux utilisateurs</li>
                            <li>Lister les applications sur lesquelles les utilisateurs ont des comptes</li>
                            <li>Gérer les groupes et leurs membres</li>
                            <li>Récupérer la liste des machines connectées au tenant</li>
                            <li>Appliquer et modifier des configurations MDM (Mobile Device Management)</li>
                            <li>Gérer à distance les machines (verrouillage, réinitialisation, redémarrage,
                                déploiement MDM)</li>
                            <li>Consulter les informations sur les machines (applications installées, etc.)</li>
                        </ul>

                        <div class="space-y-2">
                            <h2 class="text16px bold-text mt-4 border-b">
                                Notre Objectif
                            </h2>
                            <p class="mb-2">
                                Ces données centralisent <span class="font-bold">la gestion des collaborateurs</span> et
                                de
                                leurs ressources dans Manystacks, facilitant <span class="font-bold">la
                                    création et la suppression</span> de comptes, <span class="font-bold">la gestion des
                                    licences</span> et l’administration des appareils.
                            </p>

                            <p class="mb-2">
                                Ces permissions étendues permettent d’<span class="font-bold">automatiser et de
                                    simplifier</span> l’administration de votre environnement, évitant d’<span
                                    class="font-bold">intervenir manuellement</span> sur chaque plateforme. Votre espace
                                Manystacks <span class="font-bold">centralise</span> ainsi toutes les actions de
                                gestion.
                            </p>


                            <p class="bg-gray-50 border border-gray-200 p-4 rounded-md mt-4">
                                Manystacks devient ainsi votre <span class="font-semibold">point de contrôle
                                    unique</span>,
                                fiable et toujours à
                                jour.
                            </p>
                        </div>
                    </template>
                    <template v-else>
                        <div class="text16px bold-text mt-4 border-b">
                            Informations récupérées en lecture seule
                        </div>
                        <ul class="ml-4 list-disc">
                            <li><span>Nom, prénom</span></li>
                            <li><span>Adresse email</span> (pro & perso)</li>
                            <li><span>Numéro de téléphone</span></li>
                            <li><span>Dates d’arrivée et de sortie</span></li>
                        </ul>

                        <div class="space-y-2">
                            <h2 class="text16px bold-text mt-4 border-b">
                                Notre Objectif
                            </h2>
                            <p>
                                Ces informations nous permettent de <span class="bold-text">créer et mettre à
                                    jour</span> la liste de vos
                                collaborateurs dans Manystacks, sans modifier votre SIRH.
                            </p>
                            <p>
                                Elles <span class="bold-text">centralisent la gestion</span>
                                des données et des accès, évitant de répéter les mêmes actions dans chaque application.
                            </p>
                            <p class="bg-gray-50 border border-gray-200 p-4 rounded-md mt-4">
                                Manystacks devient ainsi votre <span class="font-semibold">point de contrôle
                                    unique</span>,
                                fiable et toujours à
                                jour.
                            </p>
                        </div>
                    </template>

                    <div class="mt-6 justify-end flex">
                        <div class="button" @click="syncIntegration">
                            Accepter et continuer
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitShit" class="w_container vertical gap20px" v-else-if="showIntegrationShit">
                    <template v-if="data.integration.name == 'lucca'">
                        <div class="w_container vertical">
                            <div class="text16px medium">
                                Nom de domaine <span class="red">*</span>
                            </div>
                            <div class="text14px gray">
                                Ajouter le nom de domaine de votre compte Lucca ci dessous:
                            </div>
                            <div class="textinput" :class="{ 'error': errors.domain }">
                                <input id="domain" class="text14px w-full" type="text" v-model="formShit.domain"
                                    autocomplete="off" placeholder="https://exemple.ilucca.net">
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text16px medium">
                                Générer une clé API
                            </div>
                            <div class="text14px gray">
                                Connectez-vous à Lucca et accédez à la page des clés API en cliquant sur l’icône
                                d’engrenage dans le coin supérieur droit de l’écran.
                            </div>
                            <div class="text14px gray">
                                Cliquez sur « Générer une nouvelle clé API ». Sélectionnez « Timmi Absences → Consulter
                                les absences » et « Co-workers → Consulter / créer / modifier les utilisateurs ».
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text16px medium">
                                Clé API <span class="red">*</span>
                            </div>
                            <div class="text14px gray">
                                Cliquez sur « Générer une nouvelle clé API », copiez la clé qui apparaît, puis collez-la
                                ci-dessous.
                            </div>
                            <div class="textinput" :class="{ 'error': errors.apikey }">
                                <input id="apikey" class="text14px w-full" type="text" v-model="formShit.apikey"
                                    autocomplete="off" placeholder="Votre Clé API Lucca">
                            </div>
                        </div>
                    </template>
                    <div class="flex mb-4 gap-2 justify-end">
                        <button type="submit" :disabled="formShit.processing"
                            :class="['button oauth', formShit.processing ? 'gray' : '']">
                            <img :src="data.integration.logo" :alt="data.integration.name + ' Logo'" class="w-6">
                            <div class="text14px black">
                                Se connecter à {{ data.integration.name }}
                            </div>
                            <span v-if="formShit.processing" class="loader small"></span>

                        </button>
                    </div>
                </form>

                <div class="w_container vertical gap20px justify-center" v-else-if="showIntegrationOauth">
                    <div class="text16px medium text-center">
                        Se connecter avec {{ data.integration.title }}
                    </div>
                    <div class="text14px gray text-center">
                        La configuration d’une connexion avec {{ data.integration.name }} nécessite <br> de vous
                        connecter à
                        votre compte.
                    </div>

                    <div class="flex gap-2 justify-center mt-4">
                        <div class="button oauth" @click="syncOauth()">
                            <img :src="data.integration.logo" :alt="data.integration.name + ' Logo'" class="w-6">
                            <div class="text14px black">
                                Se connecter avec {{ data.integration.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.error {
    border: 1px solid var(--red);
}
</style>
