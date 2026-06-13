<script setup>
import { usePage, Link } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref, computed } from "vue";

const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeTechnique']);

const close = () => {
    emit('closeTechnique');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const productFeatures = [
    { property: 'ram', title: 'RAM', unit: 'Go' },
    { property: 'stockage', title: 'Stockage', unit: 'Go' },
    { property: 'processeur', title: 'Processeur', unit: '' },
    { property: 'taille_ecran', title: 'Taille écran', unit: '"' },
    { property: 'carte_graphique', title: 'Carte graphique', unit: '' },
    { property: 'connectivite', title: 'Connectivité', unit: '' },
    { property: 'etat', title: 'Etat', unit: '' }
];

const featuresToShow = ref(productFeatures
    .filter(feature => props.equipement[feature.property] !== null)
    .map(feature => ({
        title: feature.title,
        value: props.equipement[feature.property],
        unit: feature.unit
    })));
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer">
                <div class="w_container _100 justifyspacebetween aligncenter">
                    <div class="text20px unbounded">Caractéristiques techniques</div>
                    <img @click="close"
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8bbf4430c3c231a740166_Vectors-Wrapper.svg"
                        loading="lazy" width="24" height="24" alt="" class="image28x28px cursor-pointer">
                </div>
            </div>
            <div class="componentcontainer caracteristics">
                <div class="w_container vertical">
                    <div class="text24px unbounded medium">{{ props.equipement.name }}</div>
                    <div class="w_container vertical gap8px">
                        <div class="w_container aligncenter verticalonphone">
                            <div class="w_container vertical nogap">
                                <div class="text14px grey400 _100">Prix</div>
                                <Link :href="'/mes-contrats/' + props.equipement.commande.reference_commande" class="w_container aligncenter gap4px clickable">
                                <div class="text14px medium purple nowrap">Accéder au contrat</div><img
                                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                </Link>
                            </div>
                            <div class="w_container _100 height100 aligncenter padding12px">
                                <div class="text14px unbounded">{{ props.equipement.prix }} €</div>
                                <div class="text14px medium nowrap">&nbsp;/&nbsp;mois</div>
                            </div>
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div v-for="feature in featuresToShow" :key="feature.id"
                            class="w_container aligncenter verticalonphone">
                            <div class="text14px grey400 _100">{{ feature.title }}</div>
                            <div class="w_container justifyspacebetween _100 height40px aligncenter padding12px">
                                <div class="text14px medium nowrap">{{ feature.value + ' ' + feature.unit }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
