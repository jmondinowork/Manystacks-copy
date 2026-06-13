<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import AttributionIndexContents from '@/Components/AttributionIndexContents.vue';
import Visualisation from '@/Components/Visualisation.vue';
import Cookies from 'js-cookie';

import { onMounted, ref } from 'vue';
import { useStore } from "vuex";
import { Head, usePage, Link } from '@inertiajs/vue3';

const { props } = usePage();
const store = useStore();

const linkToEquipement = (attribution) => {
    if (attribution.type == 'Personne')
        return 'mon-equipe/' + attribution.id;
    else if (attribution.type == 'Salle')
        return 'mes-salles/' + attribution.id;
};
const currentPathSegment = window.location.pathname.split('/')[1].replace('-', '_');
const title = currentPathSegment.split('_')[1]

onMounted(() => {
    if (sessionStorage.getItem('itemDeleted')) {
        const lastSegment = window.location.pathname.split('/').pop();
        let msg;
        if (lastSegment == 'mon-equipe')
            msg = "Le collaborateur a bien été supprimé";
        else if (lastSegment == 'mes-salles')
            msg = "La salle a bien été supprimée";

        store.dispatch('updateAnnounce', msg);
        sessionStorage.removeItem('itemDeleted');
    }
    if (sessionStorage.getItem('itemsDeleted')) {
        const lastSegment = window.location.pathname.split('/').pop();
        let msg;
        if (lastSegment == 'mon-equipe')
            msg = "Les collaborateurs ont bien été supprimés";
        else if (lastSegment == 'mes-salles')
            msg = "Les salles ont bien été supprimées";

        store.dispatch('updateAnnounce', msg);
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
        <title>{{ title }}</title>
        <meta name="description" content="Retrouvez ici toutes vos " + title>
    </Head>

    <SimpleLayout :selectedItems="selectedItems" @resetSelected="resetSelected">
        <template #default="{ showSelectMultiple }">
            <div v-if="props.mes_attributions.length" class="componentcontainer">
                <div class="w_container vertical alignleft _100 gap24px">
                    <Visualisation :display="display" @changeDisplay="changeDisplay"></Visualisation>

                    <div :class="display == 'grid' ? 'stacksgrid' : 'stacksrow'">
                        <template v-if="!showSelectMultiple">
                            <Link :href="linkToEquipement(attribution)" v-for="attribution in props.mes_attributions"
                                :key="attribution.id">
                            <AttributionIndexContents :attribution="attribution" :display="display" />
                            </Link>
                        </template>

                        <template v-else>
                            <div v-for="attribution in props.mes_attributions" :key="attribution.id"
                                @click="handleEquipementClick(attribution.id)"
                                :class="{ 'cardselected': selectedItems.includes(attribution.id) }">
                                <AttributionIndexContents :attribution="attribution" :display="display"
                                    :isSelectable="showSelectMultiple"
                                    :isSelected="selectedItems.includes(attribution.id)" />
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <EmptyPage v-else :section="currentPathSegment"></EmptyPage>

        </template>
    </SimpleLayout>
</template>
