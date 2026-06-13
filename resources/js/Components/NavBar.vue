<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import { userInitials } from '@/functions';

import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { props } = usePage();

const currentPathSegment = computed(() => window.location.pathname.split('/')[1]);
const additionalCataloguePaths = ['panier', 'mes-stacks', 'mon-catalogue', 'passer-commande'];
const isDashboardSelected = () => currentPathSegment.value === 'dashboard';
const isCatalogueSelected = () => currentPathSegment.value === 'catalogue' || additionalCataloguePaths.includes(currentPathSegment.value);
const isCommandesSelected = () => currentPathSegment.value === 'mes-commandes';
const isEquipementsSelected = () => currentPathSegment.value === 'mes-equipements';
const isEquipeSelected = () => currentPathSegment.value === 'mon-equipe';
const isSallesSelected = () => currentPathSegment.value === 'mes-salles';
const isContratsSelected = () => currentPathSegment.value === 'mes-contrats';
const isSupportSelected = () => currentPathSegment.value === 'supports';
const isLicencesSelected = () => currentPathSegment.value === 'mes-licences';
</script>

<template>
    <div class="componentcontainer navbar">
        <div class="navbartopcontainer">
            <Link :href="route('dashboard')" style="width: 80%;">
            <ApplicationLogo></ApplicationLogo>
            </Link>

            <div class="navbarelementcontainer">
                <Link v-if="props.userAuth.role != 'collaborateur'" :href="route('dashboard')"
                    :class="{ 'navbarelementselected': isDashboardSelected(), 'navbarelementunselected': !isDashboardSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isDashboardSelected() ? '/images/dashboard-icon-purple.svg' : '/images/dashboard-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium">Dashboard</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('catalogue')"
                    :class="{ 'navbarelementselected': isCatalogueSelected(), 'navbarelementunselected': !isCatalogueSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isCatalogueSelected() ? 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f9337d1129030a6fef25_Vectors-Wrapper.svg' : 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f7c77ff69649417a1115_Vectors-Wrapper.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium">Catalogue</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('mes-equipements')"
                    :class="{ 'navbarelementselected': isEquipementsSelected(), 'navbarelementunselected': !isEquipementsSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isEquipementsSelected() ? 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae3739be4a98689d32fa9c_Vectors-Wrapper.svg' : 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae3739f43cd2e616fd8692_Vectors-Wrapper.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Équipements</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link v-if="props.userAuth.role != 'collaborateur'" :href="route('mes-licences')"
                    :class="{ 'navbarelementselected': isLicencesSelected(), 'navbarelementunselected': !isLicencesSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isLicencesSelected() ? '/images/licence-icon-white.svg' : '/images/licence-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Licences</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('mon-equipe')"
                    :class="{ 'navbarelementselected': isEquipeSelected(), 'navbarelementunselected': !isEquipeSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isEquipeSelected() ? '/images/mon-equipe-purple-icon.svg' : '/images/mon-equipe-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Équipe</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('mes-salles')"
                    :class="{ 'navbarelementselected': isSallesSelected(), 'navbarelementunselected': !isSallesSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isSallesSelected() ? '/images/mes-salles-purple-icon.svg' : '/images/mes-salles-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Salles</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link v-if="props.userAuth.role != 'collaborateur'" :href="route('mes-commandes')"
                    :class="{ 'navbarelementselected': isCommandesSelected(), 'navbarelementunselected': !isCommandesSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isCommandesSelected() ? 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f94affd5cf35fbdf7d96_Vectors-Wrapper.svg' : 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f7c8634c1b2a648edb10_Vectors-Wrapper.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Commandes</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link v-if="props.userAuth.role != 'collaborateur'" :href="route('mes-contrats')"
                    :class="{ 'navbarelementselected': isContratsSelected(), 'navbarelementunselected': !isContratsSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isContratsSelected() ? 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae37380ce2184ee0a9d3af_Vectors-Wrapper.svg' : 'https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae373bc6cac11dc807136e_Vectors-Wrapper.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Contrats</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('supports')"
                    :class="{ 'navbarelementselected': isSupportSelected(), 'navbarelementunselected': !isSupportSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isSupportSelected() ? '/images/support-icon-selected.svg' : '/images/support-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Support</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <Link v-if="!props.userAuth.profile_img" :href="'/profile/mon-compte'" class="avatarcontainer">
            <div class="text16px medium white">{{ userInitials(props.userAuth.name) }}</div>
            </Link>
            <Link v-else :href="'/profile/mon-compte'">
            <div class="avatarcontainer_img" :style="{ 'background-image': 'url(' + props.userAuth.profile_img + ')' }">
            </div>
            </Link>
        </div>
    </div>
</template>

<style scoped>
.navbarelementunselected:hover .navbarelementhover {
    display: flex;
}

.avatarcontainer_img {
    background-position: center;
    background-size: cover;
    width: 40px;
    height: 40px;
    min-height: 40px;
    min-width: 40px;
    cursor: pointer;
    border-radius: 100000px;
    justify-content: center;
    align-items: center;
    display: flex;
}
</style>
