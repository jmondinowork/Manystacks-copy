<script setup>
import NavBar from '@/Components/NavBar.vue';
import NavbarAdmin from '@/Components/NavbarAdmin.vue';
import Annoucer from '@/Components/Annoucer.vue';
import ErrorAnnouncer from '@/Components/ErrorAnnouncer.vue';
import BienvenueModalVue from '@/Components/BienvenueModal.vue';

import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const data = defineProps({
    layoutKey: {
        type: String,
        default: '',
    }
});

</script>

<template>
    <div class="body">
        <NavbarAdmin v-if="data.layoutKey === 'superadmin'"></NavbarAdmin>
        <NavBar v-else></NavBar>

        <div class="maincontainer">
            <slot></slot>

        </div>

        <Annoucer></Annoucer>
        <ErrorAnnouncer></ErrorAnnouncer>
    </div>

    <BienvenueModalVue v-if="props.auth.user.bienvenue"></BienvenueModalVue>
</template>
