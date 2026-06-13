<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { useStore } from "vuex";
import { typeEquipements } from "@/config";
import { mainCaracteristiquesTechniquesProduct } from '@/config.js'

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeCreate']);

const close = (reset) => {
    emit('closeCreate');
    if (reset) {
        form.reset();
        step.value = 1;
    }
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    name: "",
    numero_unique: "",
    sous_categorie: "ordinateurs",
    status: "En service",
    type_contrat: "location",
    prix: "",
})
const step = ref(1);
const nextStep = () => {
    if (form.name) {
        step.value++;
    }
    if (step.value == 3) {
        for (let item of mainCaracteristiquesTechniquesProduct[form.sous_categorie]) {
            form[item.property] = '';
        }
    }
}

const statuts = [
    { value: "En service", label: "En service" },
    { value: "En réserve", label: "En réserve" },
    { value: "En maintenance", label: "En maintenance" },
    { value: "Hors service", label: "Hors service" }
];

const submit = async () => {
    form.processing = true;
    try {
        const response = await axios.post('/api/createEquipement', form);
        props.mes_equipements = response.data;
        store.dispatch('updateAnnounce', "L'équipement a été créé avec succès");
        close();
        form.reset();
        step.value = 1;
        form.processing = false;
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la création de l'équipement");
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Ajouter un nouvel équipement
                </div>
                <div class="w_container alignright cursor-pointer" @click="close(false)">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <template v-if="step == 1">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Nom <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="createNom" class="text14px w-full" type="text" v-model="form.name"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Numéro de série
                            </div>
                            <div class="textinput">
                                <input id="createNumeroUnique" class="text14px w-full" type="text"
                                    v-model="form.numero_unique" autocomplete="off">
                            </div>
                        </div>

                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Type d'équipement <span class="red">*</span>
                            </div>
                            <select v-model="form.sous_categorie" id="createTypeEquipement"
                                class="textinput cursor-pointer text-capitalize">
                                <option v-for="typeEquipement in typeEquipements" :key="typeEquipement"
                                    :value="typeEquipement.value">{{ typeEquipement.label }}</option>
                            </select>
                        </div>

                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Statut <span class="red">*</span>
                            </div>
                            <select v-model="form.status" id="createTypeEquipement"
                                class="textinput cursor-pointer text-capitalize">
                                <option v-for="status in statuts" :key="status" :value="status.value">{{ status.label }}
                                </option>
                            </select>
                        </div>
                    </template>

                    <template v-if="step == 2">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Type de contrat
                            </div>
                            <select v-model="form.type_contrat" id="createTypeContrat"
                                class="textinput cursor-pointer text-capitalize">
                                <option value="location">Location</option>
                                <option value="achat">Achat</option>
                            </select>
                        </div>
                        <div class="w_container vertical">
                            <div class="text14px medium" v-if="form.type_contrat == 'achat'">
                                Prix d'achat de l'équipement
                            </div>
                            <div class="text14px medium" v-else>
                                Prix de location de l'équipement
                            </div>
                            <div class="textinput">
                                <input id="createPrix" class="text14px w-full" type="text"
                                    v-model="form.prix" autocomplete="off">
                            </div>
                        </div>
                    </template>

                    <template v-if="step == 3">
                        <template v-for="carac in mainCaracteristiquesTechniquesProduct[form.sous_categorie]"
                            :key="carac">
                            <div class="w_container vertical">
                                <div class="text14px medium">
                                    {{ carac.title }}
                                </div>
                                <div class="textinput">
                                    <input :id="`create${carac.property}`" class="text14px w-full" type="text"
                                        v-model="form[carac.property]" autocomplete="off">
                                </div>
                            </div>
                        </template>
                    </template>

                    <div class="w_container vertical gap8px">
                        <div v-if="step != 3" class="bigbutton purple" @click="nextStep">
                            <div class="text14px white">
                                Suivant
                            </div>
                        </div>
                        <button v-else type="submit" :disabled="form.processing"
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
                </div>
            </div>
        </form>
    </div>
</template>
