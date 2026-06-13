<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import { userInitials } from '@/functions';

import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const { props } = usePage();

const currentPathSegment = computed(() => window.location.pathname.split('/')[1]);
const isCommandesSelected = () => currentPathSegment.value === 'commandesAdmin';
const isSupportSelected = () => currentPathSegment.value === 'supportsAdmin';
const isUsersSelected = () => currentPathSegment.value === 'usersAdmin';
</script>

<template>
    <div class="componentcontainer navbar">
        <div class="navbartopcontainer">
            <Link :href="'/'" style="width: 80%;">
            <ApplicationLogo></ApplicationLogo>
            </Link>

            <div class="navbarelementcontainer">
                <Link :href="route('commandesAdmin')"
                    :class="{ 'navbarelementselected': isCommandesSelected(), 'navbarelementunselected': !isCommandesSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isCommandesSelected() ? 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f94affd5cf35fbdf7d96_Vectors-Wrapper.svg' : 'https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6565f7c8634c1b2a648edb10_Vectors-Wrapper.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white">Commandes</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('supportsAdmin')"
                    :class="{ 'navbarelementselected': isSupportSelected(), 'navbarelementunselected': !isSupportSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isSupportSelected() ? '/images/support-icon-selected.svg' : '/images/support-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Support</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>

                <Link :href="route('usersAdmin')"
                    :class="{ 'navbarelementselected': isUsersSelected(), 'navbarelementunselected': !isUsersSelected() }">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    :src="isUsersSelected() ? '/images/mon-equipe-purple-icon.svg' : '/images/mon-equipe-icon.svg'" />
                <div class="navbarelementhover">
                    <div class="text14px white medium text-nowrap">Utilisateurs</div>
                    <img class="image12x12px" loading="lazy" width="auto" height="auto"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566f2fdc265148e9de148d7_arrow%20diag.svg" />
                </div>
                </Link>
            </div>

        </div>

        <div class="flex flex-col gap-4">
            <Link :href="'/profile/mon-compte'" v-if="!props.userAuth.profile_img"
                class="avatarcontainer cursor-default">
            <div class="text16px medium white">{{ userInitials(props.userAuth.name) }}</div>
            </Link>
            <Link :href="'/profile/mon-compte'" v-else class="avatarcontainer_img cursor-default"
                :style="{ 'background-image': 'url(' + props.userAuth.profile_img + ')' }">
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
