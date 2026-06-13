<script setup>
import Applications from "../Components/Applications.vue";
import Securite from "../Components/Securite.vue";
import Autopilot from "../Components/Autopilot.vue";

import { ref } from "vue";
import Cookies from "js-cookie";

const tab = ref(Cookies.get('tabMdm') || 'securite');
const changeTab = (tabName) => {
    Cookies.set('tabMdm', tabName);
    tab.value = tabName;
}
</script>

<template>
    <div class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                <div class="text20px unbounded">MDM (Intune)</div>
            </div>
            <div class="w_container _100 gap20px borderbottom mt-4">
                <div class="tabs cursor-pointer" :class="{ 'selected': tab === 'applications' }"
                    @click="changeTab('applications')">
                    <div class="text14px grey400">Applications</div>
                </div>
                <div class="tabs cursor-pointer" :class="{ 'selected': tab === 'securite' }"
                    @click="changeTab('securite')">
                    <div class="text14px grey400">Politique de sécurité</div>
                </div>
                <div class="tabs cursor-pointer" :class="{ 'selected': tab === 'autopilot' }"
                    @click="changeTab('autopilot')">
                    <div class="text14px grey400">Autopilot</div>
                </div>
            </div>

            <Applications v-if="tab === 'applications'" />
            <Securite v-if="tab === 'securite'" />
            <Autopilot v-if="tab === 'autopilot'" @updateTab="changeTab" />
        </div>
    </div>
</template>

<style scoped>
.selected .text14px.grey400 {
    color: var(--main);
}
</style>
