<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';

import { userInitials } from '@/functions';
import { Head, usePage, Link } from '@inertiajs/vue3';

const { props } = usePage();

</script>

<template>

    <Head>
        <title>Utilisateurs</title>
    </Head>

    <SimpleLayout>
        <div v-if="props.usersAdmin.length" class="componentcontainer">
            <div class="w_container vertical alignleft _100 gap24px">

                <div class="stacksgrid">
                    <Link :href="'/superadmin/entreprise/' + entreprise.id" class="stackunitcontainer cursor-pointer" v-for="entreprise in props.usersAdmin" style="align-items: flex-start !important;">
                        <div class="w_container vertical gap12px">
                            <div class="stacktitlecontainer">
                                <div class="w_container vertical gap16px">
                                    <div class="w_container gap16px" style="align-items: flex-start !important;">
                                        <div v-if="entreprise.profile_img" class="avatarcircle_img"
                                            :style="{ 'background-image': 'url(' + entreprise.profile_img + ')' }">
                                        </div>
                                        <div v-else class="avatarcircle">
                                            <div class="text40px white">{{ userInitials(entreprise.raison_sociale) }}</div>
                                        </div>
                                        <div class="w_container vertical gap4px">
                                            <div class="text20px unbounded text-pretty">{{ entreprise.raison_sociale }}</div>
                                            <div class="w_container gap12px items-center">
                                                <div class="text14px">
                                                    {{ entreprise.siret }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'usersadmin'"></EmptyPage>
    </SimpleLayout>
</template>

<style scoped>
.avatarcircle,
.avatarcircle_img,
.circle40px {
    width: 50px;
    height: 50px;
    min-width: 50px;
    min-height: 50px;
}

.avatarcircle .text40px {
    font-size: 20px;
}
</style>
