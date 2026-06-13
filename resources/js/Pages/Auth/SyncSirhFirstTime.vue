<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import Integration from "@/Components/Integration.vue";
import Annoucer from '@/Components/Annoucer.vue';
import ErrorAnnouncer from '@/Components/ErrorAnnouncer.vue';
import { ref, onMounted } from "vue";
import { Link, usePage } from '@inertiajs/vue3';
import { useStore } from 'vuex';

const { props } = usePage();
const store = useStore();

const currentIntegration = ref(null);

const showIntegration = ref(false);
const openIntegration = (integration) => {
    currentIntegration.value = integration;
    showIntegration.value = true;
}
const closeIntegration = () => showIntegration.value = false;
const redirect_to = window.location.origin + "/syncSirhFirstTime";
const isServiceConnected = (name) => {
    return props.userAuth.oauth.includes(name);
}

onMounted(() => {
    if (props.flash.success)
        store.dispatch('updateAnnounce', props.flash.success);
    if (props.flash.error)
        store.dispatch('updateErrorAnnounce', props.flash.error);
})
</script>


<template>
    <div class="componentcc-s1">
        <div class="containertitre">
            <ApplicationLogo class="image57x34" />

            <div class="contenttext">
                <div class="text24px unbounded bold-text">Integrez maintenant votre SIRH</div>
                <div class="text14px text-center">Pour commencer, connectez-vous à votre SIRH pour synchroniser vos
                    collaborateurs.</div>
            </div>
        </div>
        <div class="containeritpro">
            <!-- <div class="searchbar greystroke bigger">
            <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/670532bb03a6463f99654e79_search.png" loading="lazy" alt="" class="image24x24px">
        </div> -->
            <div v-for="integration in props.integrations" class="containerit horizontal">
                <img :src="integration.logo" loading="lazy" width="76" alt="" class="image76px">
                <div class="vertical descriptionit">
                    <div class="text20px bold-text">{{ integration.title }}</div>
                    <div class="text14px">{{ integration.description }}</div>
                </div>
                <div class="button syncroniser w-button" v-if="!isServiceConnected(integration.name)"
                    @click="openIntegration(integration)">Syncroniser</div>
                <div class="ml-auto flex gap-2 items-center" v-else>
                    <img class="image24x24px" loading="lazy" width="auto" height="auto" alt=""
                        src="/images/synchronise-icon.svg" />
                    <div class="text14px medium" style="color: #60CF82;">
                        Synchronisé
                    </div>
                </div>
            </div>
            <div class="containercta-s1">
                <Link :href="route('dashboard')" class="lightbutton">
                <div class="text14px medium purple nowrap">
                    Étape suivante
                </div>
                </Link>
            </div>
        </div>
    </div>

    <Integration @closeIntegration="closeIntegration" v-if="showIntegration" :integration="currentIntegration"
        :redirect_to="redirect_to" />
    <Annoucer />
    <ErrorAnnouncer />
</template>

<style scoped>
.componentcc-s1 {
    display: flex;
    overflow: visible;
    padding: 20px;
    width: 100%;
    height: 100vh;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--grey-100);
    border-right-color: var(--grey-100);
    border-bottom-color: var(--grey-100);
    border-left-color: var(--grey-100);
    border-radius: 12px;
    background-color: var(--grey-50);
}

.containertitre {
    display: flex;
    width: 500px;
    height: 220px;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
}

.image57x34 {
    width: 57px;
    height: 34px;
    min-height: 34px;
    min-width: 57px;
}

.contenttext {
    display: flex;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    gap: 14px;
}

.text24px.unbounded {
    line-height: 140%;
    font-weight: 500;
}

.text14px {
    font-size: 14px;
    line-height: 140%;
    width: auto;
    height: auto;
    text-align: left;
}

.vertical {
    display: flex;
    flex-flow: column;
    gap: 8px;
}

.containercta-s1 {
    display: flex;
    width: 100%;
    height: auto;
    margin-top: 32px;
    margin-bottom: 32px;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

.containeritpro {
    display: flex;
    width: 1060px;
    max-width: 100%;
    margin-top: 80px;
    padding: 40px;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    border-radius: 12px;
    box-shadow: rgba(0, 0, 0, 0.05) -8px -8px 12px 0px, rgba(0, 0, 0, 0.15) 8px 8px 12px 0px;
}

.containerit {
    width: 100%;
    height: 60%;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--grey400);
    border-right-color: var(--grey400);
    border-bottom-color: var(--grey400);
    border-left-color: var(--grey400);
    border-radius: 8px;
}

.containerit.horizontal {
    padding: 16px;
    gap: 24px;
    background-color: rgb(255, 255, 255);
}

.horizontal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}

.button.is-secondary.bigger {
    padding: 10px;
    font-size: 14px;
}

.button.is-secondary {
    border-style: solid;
    background-color: transparent;
    color: rgb(23, 30, 41);
    border-width: 1px;
    border-color: rgb(153, 153, 153);
}
</style>
