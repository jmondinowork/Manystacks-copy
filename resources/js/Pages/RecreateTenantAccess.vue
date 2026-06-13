<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';

import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue';

const { props } = usePage()

const currentintegration = computed(() => {
    return props.integrations.find(integration => integration.name === props.tenant)
})

const syncOauth = () => {
    const url = `/${props.tenant}/auth/redirect?redirect_to=${props.redirect_to}`;
    window.location.href = url;
}
</script>

<template>
    <div class="componentcc-s1">
        <div class="containertitre">
            <ApplicationLogo class="image57x34" />

            <div class="contenttext">
                <div class="text24px unbounded bold-text text-center">Mise à jour des accès pour {{
                    props.tenant.toUpperCase() }}</div>
                <div class="text16px text-center"> Votre application a évolué et nécessite désormais des permissions
                    supplémentaires
                    pour continuer à fonctionner correctement.
                </div>
                <div class="text16px text-center">
                    En cliquant sur le bouton ci-dessous, vous serez redirigé vers la page de connexion de {{
                    props.tenant }} pour mettre à jour vos accès.
                </div>
            </div>
        </div>
        <div class="containeritpro">
            <div class="containerit horizontal">
                <img :src="currentintegration.logo" loading="lazy" width="76" alt="" class="image76px">
                <div class="vertical descriptionit">
                    <div class="text20px bold-text">{{ currentintegration.title }}</div>
                    <div class="text14px">{{ currentintegration.description }}</div>
                </div>
                <div class="button syncroniser w-button text-center" @click="syncOauth">Mettre à jour mes accès
                </div>
            </div>
        </div>
    </div>
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

img {
    max-width: 100%;
    vertical-align: middle;
    display: inline-block;
}

.vertical.descriptionit {
    width: 70%;
    padding-left: 12px;
}
</style>
