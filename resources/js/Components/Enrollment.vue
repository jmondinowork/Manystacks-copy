<script setup>
import { onMounted, onUnmounted } from "vue";
import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentProduct: {
        type: Object,
        default: []
    }
});
const form = useForm({
    numero_unique: "",
    id: props.equipement.id,
    image_principale: props.equipement.image_principale,
});
const emit = defineEmits(['closeEnrollment', 'launchEnrollment']);

const close = () => {
    emit('closeEnrollment');
}
const lauch = () => {
    emit('launchEnrollment');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const submit = async () => {
    if (form.numero_unique) {
        form.processing = true;
        try {
            const response = await axios.post(route('mes-equipements.editCaracteristiqueImported'), form);
            props.equipement = response.data;
            form.reset();
            close();
            lauch();
            form.processing = false;

        } catch (error) {
        }
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Enroller votre machine avec votre compte Microsoft professionnel
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <form @submit.prevent="submit" v-if="!props.equipement.numero_unique">
                <div class="componentcontainer">
                    <div class="w_container vertical gap20px">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Pour que Manystacks detecte votre machine, il est nécessaire de renseigner son numéro de
                                série.
                            </div>
                            <div class="textinput">
                                <input id="numero_unique" class="text14px w-full" type="text"
                                    v-model="form.numero_unique" autocomplete="off" placeholder="Numéro de série">
                            </div>
                        </div>

                        <div class="w_container vertical gap8px">
                            <button type="submit" :disabled="form.processing"
                                :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                                <div class="text14px white">
                                    Enregistrer
                                </div>
                            </button>
                            <div class="bigbutton" @click="close">
                                <div class="text14px">
                                    Annuler
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
            <div v-else class="componentcontainer">
                <div class="space-y-4">
                    <div class="text14px">Pour connecter votre machine à votre compte Microsoft professionnel :</div>
                    <div class="text14px bold">Sur une machine neuve :</div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Au démarrage, sélectionnez "Se connecter avec un compte pro ou scolaire"
                            puis connectez-vous avec le compte Microsoft professionnel de la personne assignée. Suivez
                            les étapes de la configuration Out Of The Box jusqu'au démarrage de l'appareil</div>
                    </div>

                    <div class="text14px bold">Sur une machine existante :</div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Allez dans <strong>Démarrer > Paramètres > Comptes</strong>.</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Sélectionnez <strong>Accès professionnel ou scolaire</strong>.</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Cliquez sur <strong>Joindre cet appareil à Microsoft Entra ID</strong>.
                        </div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Connectez-vous avec l'adresse e-mail <b>Microsoft</b> de l'utilisateur
                            assigné à la machine.</div>
                    </div>
                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text14px">Suivez les instructions jusqu'à la fin de la procédure.</div>
                    </div>

                    <div class="text14px mt-6">Une fois terminé, revenez sur cette page et votre machine sera
                        automatiquement enroller ici, sinon cliquez une nouvelle fois sur "Enroller la machine" </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bold {
    font-weight: 700;
}
</style>
