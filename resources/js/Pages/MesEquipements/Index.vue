<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import EquipementsIndexContents from '@/Components/EquipementsIndexContents.vue';
import Visualisation from '@/Components/Visualisation.vue';
import Cookies from 'js-cookie';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useStore } from "vuex";

const { props } = usePage();
const store = useStore();

onMounted(() => {
    if (sessionStorage.getItem('itemDeleted')) {
        store.dispatch('updateAnnounce', "L'équipement a bien été supprimé");
        sessionStorage.removeItem('itemDeleted');
    }
    if (sessionStorage.getItem('itemsDeleted')) {
        store.dispatch('updateAnnounce', "Les équipements ont bien été supprimés");
        sessionStorage.removeItem('itemsDeleted');
    }
    if (sessionStorage.getItem('itemNotDeleted')) {
        store.dispatch('updateErrorAnnounce', 'Aucun élément n\'a été supprimé');
        sessionStorage.removeItem('itemNotDeleted');
    }
});

const selectedItems = ref([]);
const handleEquipementClick = (id) => {
    const index = selectedItems.value.indexOf(id);
    if (index === -1) {
        selectedItems.value.push(id);
    } else {
        selectedItems.value.splice(index, 1);
    }
}
const resetSelected = () => {
    selectedItems.value = [];
};

const display = ref(Cookies.get('display') || 'grid');
const changeDisplay = (type) => {
    display.value = type;
    Cookies.set('display', type);
};
</script>

<template>

    <Head>
        <title>Equipements</title>
        <meta name="description" content="Retrouvez ici toutes vos Equipements">
    </Head>

    <SimpleLayout :selectedItems="selectedItems" @resetSelected="resetSelected">
        <template #default="{ showSelectMultiple }">
            <div v-if="props.mes_equipements.length" class="componentcontainer">
                <div class="w_container vertical alignleft _100 gap24px">
                    <Visualisation :display="display" @changeDisplay="changeDisplay"></Visualisation>

                    <div :class="display == 'grid' ? 'stacksgrid' : 'stacksrow'">
                        <template v-if="!showSelectMultiple">
                            <template v-for="equipement in props.mes_equipements" :key="equipement.id">
                                <template v-if="props.userAuth.role !== 'collaborateur' || equipement.public !== 0">
                                    <Link :href="'/mes-equipements/' + equipement.id" class="position-relative">
                                    <EquipementsIndexContents :equipement="equipement" :display="display" />
                                    </Link>
                                </template>
                            </template>
                        </template>

                        <template v-else>
                            <div v-for="equipement in props.mes_equipements" :key="equipement.id"
                                v-if="!props.userAuth.role !== 'collaborateur' || equipement.public !== 0"
                                @click="handleEquipementClick(equipement.id)"
                                :class="['position-relative', { 'cardselected': selectedItems.includes(equipement.id) }]">
                                <EquipementsIndexContents :equipement="equipement" :display="display" :isSelectable="showSelectMultiple"
                                    :isSelected="selectedItems.includes(equipement.id)" />
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <EmptyPage v-else :section="'mes_equipements'"></EmptyPage>
        </template>
    </SimpleLayout>
</template>
