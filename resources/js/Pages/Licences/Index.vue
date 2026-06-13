<script setup>
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';
import Visualisation from '@/Components/Visualisation.vue';
import { userInitials } from '@/functions';
import { ref } from 'vue';
import Cookies from 'js-cookie';

import { Head, usePage, Link } from '@inertiajs/vue3';

const { props } = usePage();

const hasLicences = props.mes_licences && Object.keys(props.mes_licences).length > 0;

const display = ref(Cookies.get('display') || 'grid');
const changeDisplay = (type) => {
    display.value = type;
    Cookies.set('display', type);
};
</script>

<template>

    <Head>
        <title>Licences</title>
        <meta name="description" content="Retrouvez ici toutes vos Licences">
    </Head>

    <SimpleLayout>
        <div v-if="hasLicences" class="componentcontainer">
            <div class="w_container vertical alignleft _100 gap24px">
                <Visualisation :display="display" @changeDisplay="changeDisplay"></Visualisation>

                <div class="stacksgrid" v-if="display == 'grid'">
                    <Link v-for="licence in props.mes_licences" :href="'mes-licences/' + licence.slug"
                        class="stacklicencecontainer">
                    <div class="image_container big">
                        <img :src="licence.image_principale" loading="lazy" alt="">
                    </div>
                    <div class="w_container vertical gap24px">
                        <div class="w_container vertical gap8px">
                            <div class="w_container vertical alignleft">
                                <div class="text20px unbounded">{{ licence.name }}</div>
                            </div>
                            <div class="d_container_test flex-col">
                                <div class="flex flex-col gap-2">
                                    <div class="flex gap-2">
                                        <div class="text14px black">Disponible(s):</div>
                                        <div class="text14px black  bold-text">{{ licence.available }}</div>
                                        <div class="divider_black mx-2"></div>
                                        <div class="text14px black">Assignée(s):</div>
                                        <div class="text14px black bold-text">{{ licence.assigned }}</div>
                                    </div>
                                    <div class="flex gap-2" v-if="licence.on_hold">
                                        <div class="text14px black">En attente:</div>
                                        <div class="text14px black bold-text">{{ licence.on_hold }}</div>
                                    </div>
                                </div>
                                <!-- <div class="description_licence_container">
                                    <div class="text14px black">Prix total:</div>
                                    <div class="text14px black bold-text">{{ (licence.prix_u * licence.total).toFixed(2)
                                        }}€</div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
                <div class="stacksrow" v-else>
                    <Link v-for="licence in props.mes_licences" :href="'mes-licences/' + licence.slug"
                        class="stacklicencerow">
                    <div class="image_container small">
                        <img :src="licence.image_principale" loading="lazy" alt="">
                    </div>
                    <div class="w_container vertical gap-6">
                        <div class="w_container vertical">
                            <div class="d_container-row cols-3">
                                <div class="description_licence_container">
                                    <div class="text20px unbounded">{{ licence.name }}</div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="text14px black bold-text">{{ licence.available }}</div>
                                    <div class="text14px black">disponible(s)</div>
                                </div>
                                <div class="description_licence_container justify-end">
                                    <div class="flex" v-if="licence.assignedUsers.length">
                                        <!-- On itère sur les 3 premiers utilisateurs si il y en a plus de 4, sinon sur les 4 -->
                                        <template
                                            v-for="(assignedUser, index) in licence.assignedUsers.slice(0, licence.assignedUsers.length > 4 ? 3 : 4)"
                                            :key="assignedUser.id">
                                            <div :class="['avatarcontainer', { over: index !== 0 }]"
                                                v-if="!assignedUser.profile_img">
                                                <div class="text12px medium white">{{ userInitials(assignedUser.name) }}
                                                </div>
                                            </div>
                                            <img v-else class="rounded-full"
                                                :class="['image24x24px', { over: index !== 0 }]"
                                                :src="assignedUser.profile_img" alt="" />
                                        </template>
                                        <!-- Si plus de 4 utilisateurs, on ajoute un avatar indiquant "..." -->
                                        <div v-if="licence.assignedUsers.length > 4" class="avatarcontainer bg-transparent over">
                                            <div class="text12px medium black">...</div>
                                        </div>
                                    </div>
                                    <div class="text14px black bold-text">{{ licence.assigned }}</div>
                                    <div class="text14px black">assignée(s)</div>
                                </div>

                            </div>
                        </div>
                    </div>
                    </Link>
                </div>
            </div>
        </div>

        <EmptyPage v-else :section="'mes_licences'"></EmptyPage>
    </SimpleLayout>
</template>

<style scoped>
.avatarcontainer {
    width: 24px;
    height: 24px;
    min-height: 24px;
    min-width: 24px;
}

.avatarcontainer.over {
    margin-left: -6px;
}
</style>
