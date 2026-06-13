<script setup>
import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeCreate', 'updateAdresseCreated']);

const close = () => {
    form.reset();
    emit('closeCreate');
}
const updateAdresseCreated = () => {
    emit('updateAdresseCreated');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const form = useForm({
    id: null,
    titre: "",
    adresse: "",
    complement_adresse: "",
    code_postal: "",
    ville: "",
    pays: "France"
})

const submit = async () => {
    form.processing = true;
    try {
        const response = await axios.post('/api/store_adresse', form);
        props.adresses = response.data;
        form.reset();
        updateAdresseCreated();
        store.dispatch('updateAnnounce', "L'adresse a été créée avec succès");
        close();
        form.processing = false;
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la création de l'adresse");
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Ajouter une nouvelle adresse
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Titre <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="createTitre" class="text14px w-full" type="text" v-model="form.titre"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Adresse <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="createAdresse" class="text14px w-full" type="text" v-model="form.adresse"
                                autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Complément d’adresse
                        </div>
                        <div class="textinput">
                            <input id="createComplementAdresse" class="text14px w-full" type="text"
                                v-model="form.complement_adresse" autocomplete="off">
                        </div>
                    </div>
                    <div class="w_container gap16px">
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Code postal <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="createCodePostal" class="text14px w-full" type="text"
                                    v-model="form.code_postal" autocomplete="off">
                            </div>
                        </div>
                        <div class="w_container vertical">
                            <div class="text14px medium">
                                Ville <span class="red">*</span>
                            </div>
                            <div class="textinput">
                                <input id="createVille" class="text14px w-full" type="text" v-model="form.ville"
                                    autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Pays <span class="red">*</span>
                        </div>
                        <div class="textinput">
                            <input id="createPays" class="text14px w-full" type="text" v-model="form.pays"
                                autocomplete="off">
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
    </div>
</template>
