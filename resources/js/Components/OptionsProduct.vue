<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, onUnmounted, ref } from "vue";
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
    },
    action: {
        type: String
    },
    equipement: {
        type: Object
    },
    displayOnly: {
        type: Boolean,
        default: false
    },
});
const emit = defineEmits(['closeOptions', 'updateEquipement', 'updateStatus']);

const close = () => {
    emit('closeOptions');
}
const updateStatus = () => {
    emit('updateStatus');
}
const updateEquipement = (equipement) => {
    emit('updateEquipement', equipement);
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const title = computed(() => {
    switch (data.action) {
        case 'En réserve':
            return 'Mettre en réserve';
        case 'En maintenance':
            return 'Mettre en maintenance';
        case 'Hors service':
            return 'Mettre hors service';
        case 'En service':
            return 'Mettre en service';
    }
});
const description = computed(() => {
    switch (data.action) {
        case 'En réserve':
            return "Vous êtes sur le point de placer l'équipement <b>en réserve</b>. Il sera retiré de l'utilisateur actuel.<br><br>Souhaitez-vous continuer ?";
        case 'En maintenance':
            return "Vous êtes sur le point de placer l'équipement <b>en maintenance</b>. Il sera retiré de l'utilisateur actuel.<br><br>Souhaitez-vous continuer ?";
        case 'Hors service':
            return "Vous êtes sur le point de placer l'équipement <b>hors service</b>. Il sera retiré de l'utilisateur actuel.<br><br>Souhaitez-vous continuer ?";
        case 'En service':
            return "Vous êtes sur le point de placer l'équipement <b>en service</b>.<br><br>Souhaitez-vous continuer ?";
    }
})
const submit = async () => {
    if (data.displayOnly) {
        updateStatus();
        return;
    }
    try {
        const response = await axios.post('/api/editEquipement', {
            id: data.equipement.id,
            status: data.action,
            user_attribution: props.attribution ? props.attribution.id : null,
        });

        updateEquipement(response.data.equipement);

        props.attribution = response.data.attribution;
        props.equipement = response.data.equipement;
        props.attributions = response.data.attributions_available;
        props.historiques = response.data.historiques;
        props.equipement_available = response.data.equipement_available;
        store.dispatch('updateAnnounce', "Le statut de l'équipement a été modifié avec succès");
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la modification du statut de l'équipement");
    }
}
</script>

<template>
    <div class="darkmodalbackground position-fixed" :class="{ 'show': data.show }">
        <div class="modalcontainer small">
            <div class="componentcontainer">
                <div class="w_container _100 justifyspacebetween aligncenter">
                    <div class="text20px unbounded">{{ title }}</div>
                    <img @click="close"
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8bbf4430c3c231a740166_Vectors-Wrapper.svg"
                        loading="lazy" width="24" height="24" alt="" class="image28x28px cursor-pointer">
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div v-html="description"></div>
                    <div class="w_container vertical gap8px">
                        <div @click="submit" class="bigbutton purple">
                            <div class="text14px white">Continuer</div>
                        </div>
                        <div @click="close" class="bigbutton">
                            <div class="text14px">Annuler</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
