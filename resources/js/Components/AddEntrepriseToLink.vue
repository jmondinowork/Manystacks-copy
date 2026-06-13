<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { useStore } from 'vuex';
import { onMounted, onUnmounted } from 'vue';

const { props } = usePage();
const store = useStore();

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});


const emit = defineEmits(['close']);

const close = () => {
    emit('close');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    siret: '',
    currentEntrepriseId: props.entreprise.id,
    processing: false,
});

const submit = () => {
    if (!form.siret) {
        return;
    }

    form.processing = true;

    axios.post(route('linkEntreprise', { entreprise: props.entreprise.id }), form)
        .then((response) => {
            store.dispatch('updateAnnounce', "L'entreprise a bien été liée");
            props.entreprise = response.data;
            close();
            form.reset();
        })
        .catch((error) => {
            store.dispatch('updateErrorAnnounce', error.response.data.message);
        });
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Lier une entreprise à {{ props.entreprise.raison_sociale }}
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <form @submit.prevent="submit" class="componentcontainer flex-col gap-2">
                <div class="w_container vertical">
                    <div class="text14px medium">
                        Siret <span class="red">*</span>
                    </div>
                    <div class="textinput">
                        <input id="siret" class="text14px w-full" type="text" v-model="form.siret" autocomplete="off">
                    </div>
                </div>

                <div class="w_container vertical gap8px">
                    <button type="submit" :disabled="form.processing"
                        :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                        <div class="text14px white">
                            Enregistrer
                        </div>
                    </button>
                    <div class="bigbutton" @click="close(true)">
                        <div class="text14px">
                            Annuler
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
