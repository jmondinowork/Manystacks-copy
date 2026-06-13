<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { onMounted, onUnmounted, watch } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    object: {
        type: String,
        default: "",
    },
    equipement: {
        type: Object,
        default: null,
    },
    commande: {
        type: Object,
        default: null,
    },
    user: {
        type: Object,
        default: null,
    },
});
const emit = defineEmits(['closeCreateSupport', 'updateCurrentTicketCreated']);

const close = () => {
    form.reset();
    emit('closeCreateSupport');
}
const updateCurrentTicketCreated = (ticket) => {
    emit('updateCurrentTicketCreated', ticket);
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const subjects = computed(() => {
    if (data.user) {
        if (data.user.type === 'Personne') {
            return {
                "equipement": "J'ai un problème avec un de mes équipements",
                "licence": "J'ai un problème avec une de mes licences",
                "compte": "J'ai un problème avec mon compte",
                "manystacks": "J'ai une question pour Manystacks",
                "autre": "Autre"
            }
        }
        else if (data.user.type === 'Salle') {
            return {
                "equipement": "J'ai un problème avec un de mes équipements",
                "manystacks": "J'ai une question pour Manystacks",
                "autre": "Autre"
            }
        }
    }
    return {
        "commande": "J'ai un problème avec ma commande",
        "contrat": "J'ai un problème avec mon contrat",
        "equipement": "J'ai un problème avec un de mes équipements",
        "licence": "J'ai un problème avec une de mes licences",
        "compte": "J'ai un problème avec mon compte",
        "manystacks": "J'ai une question pour Manystacks",
        "autre": "Autre"
    }
});

const form = useForm({
    object: data.object,
    message: "",
    from: "user",
    commande_id: data.commande ? data.commande.id : "",
    equipement_id: data.equipement ? data.equipement.id : "",
    user_id: data.user ? data.user.id : null,
})
watch(() => data.equipement ? data.equipement.id : null, (newValue) => {
    form.equipement_id = newValue;
});
watch(() => form.object, (newVal, oldVal) => {
    if (newVal !== oldVal) {
        form.commande_id = "";
        form.equipement_id = "";
    }
});
watch(() => data.object, (newValue) => {
    form.object = newValue;
});

const filteredEquipements = computed(() => {
    const filtered = form.object == subjects.value['equipement']
        ? props.equipements.filter(equipement => equipement.categorie !== 'licences')
        : props.equipements.filter(equipement => equipement.categorie === 'licences');

    const uniqueEquipements = [];
    const names = new Set();

    filtered.forEach(equipement => {
        if (!names.has(equipement.name)) {
            names.add(equipement.name);
            uniqueEquipements.push(equipement);
        }
    });

    return uniqueEquipements;
});
const submit = async () => {
    if (!form.object || !form.message) {
        store.dispatch('updateErrorAnnounce', 'Veuillez remplir tous les champs obligatoires');
        return;
    }
    if ((form.object === subjects.value['commande'] || form.object === subjects.value['contrat']) && !form.commande_id) {
        store.dispatch('updateErrorAnnounce', 'Veuillez sélectionner une commande ou un contrat');
        return;
    }

    if (form.object === subjects.value['equipement'] && !form.equipement_id) {
        store.dispatch('updateErrorAnnounce', 'Veuillez sélectionner un équipement');
        return;
    }
    if (form.object === subjects.value['licence'] && !form.equipement_id) {
        store.dispatch('updateErrorAnnounce', 'Veuillez sélectionner une licence');
        return;
    }
    try {
        form.processing = true;
        const response = await axios.post('/api/createSupport', form);
        props.supports = response.data.supports;
        updateCurrentTicketCreated(response.data.ticket);
        form.reset();
        store.dispatch('updateAnnounce', 'Votre ticket de support a bien été créé');
        form.processing = false;
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', 'Une erreur s\'est produite lors de la création du ticket de support');
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Créer un ticket de support
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical" v-if="data.user">
                        <div class="text14px medium">
                            Pour
                        </div>
                        <div class="textinput text14px medium cursor-default">
                            {{ data.user.name }}
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Sujet <span class="red">*</span>
                        </div>
                        <select v-if="!data.object" class="textinput cursor-pointer text14px w-full"
                            v-model="form.object">
                            <option disabled value="">{{ 'Veuillez sélectionner parmi la liste' }}</option>
                            <option v-for="subject in subjects" :value="subject" :key="subject">
                                {{ subject }}
                            </option>
                        </select>
                        <div v-else class="textinput text14px medium cursor-default">
                            {{ data.object }}
                        </div>
                    </div>

                    <div class="w_container vertical"
                        v-if="form.object == subjects['commande'] || form.object == subjects['contrat']">
                        <div v-if="form.object == subjects['commande']" class="text14px medium">
                            Commande concernée <span class="red">*</span>
                        </div>
                        <div v-else class="text14px medium">
                            Contrat concerné <span class="red">*</span>
                        </div>

                        <select v-if="!data.commande" class="textinput cursor-pointer text14px w-full"
                            v-model="form.commande_id">
                            <option v-if="form.object == subjects['commande']" disabled value="">
                                {{ 'Veuillez sélectionner une commande' }}
                            </option>
                            <option v-else disabled value="">
                                {{ 'Veuillez sélectionner un contrat' }}
                            </option>
                            <option v-for="commande in props.commandes" :value="commande.id" :key="commande.id">
                                {{ commande.reference_commande + ' - ' + commande.products_count + ' équipements' }}
                            </option>
                        </select>
                        <div v-else class="textinput text14px medium cursor-default">
                            {{ data.commande.reference_commande + ' - ' + data.commande.commande_products.length
                                + ' équipements' }}
                        </div>
                    </div>

                    <div class="w_container vertical"
                        v-if="form.object == subjects['equipement'] || form.object == subjects['licence']">
                        <div v-if="form.object == subjects['equipement']" class="text14px medium">
                            Equipement concerné <span class="red">*</span>
                        </div>
                        <div v-else class="text14px medium">
                            Licence concernée <span class="red">*</span>
                        </div>
                        <select v-if="!data.equipement" class="textinput cursor-pointer text14px w-full"
                            v-model="form.equipement_id">
                            <option disabled value="">{{ 'Veuillez sélectionner un élement' }}</option>
                            <option v-for="equipement in filteredEquipements" :value="equipement.id"
                                :key="equipement.id">
                                {{ equipement.name }}
                            </option>
                        </select>
                        <div v-else class="textinput text14px medium cursor-default">
                            {{ data.equipement.name }}
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Message <span class="red">*</span>
                        </div>
                        <textarea class="text14px w-full textinput h-52" id="createMessage" autocomplete="off"
                            v-model="form.message"></textarea>
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
    </div>
</template>

<style scoped>
.darkmodalbackground {
    position: fixed;
}
</style>
