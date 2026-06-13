<script setup>
import { usePage } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { ref, onMounted } from "vue";
import Integration from "@/Components/Integration.vue";
import Cookies from "js-cookie";

const props = usePage().props;
const store = useStore();
const currentIntegration = ref(null);

const showIntegration = ref(false);
const openIntegration = (integration) => {
    currentIntegration.value = integration;
    showIntegration.value = true;
}
const closeIntegration = () => showIntegration.value = false;

const isServiceConnected = (name) => {
    return props.userAuth.oauth.includes(name);
}

const tab = ref(Cookies.get('tab') || 'tenant');
const changeTab = (tabName) => {
    Cookies.set('tab', tabName);
    tab.value = tabName;
}

onMounted(() => {
    if (props.flash.success)
        store.dispatch('updateAnnounce', props.flash.success);
    if (props.flash.error) {
        store.dispatch('updateErrorAnnounce', props.flash.error);
    }
})
</script>

<template>
    <div class="componentcontainer height100 alignstretch overflowhide">
        <div class="w_container vertical gap12px overflowauto">
            <div class="w_container aligncenter justifyspacebetween orderbecomevertical">
                <div class="text20px unbounded">Mes intégrations</div>
            </div>
            <div class="w_container _100 gap20px borderbottom mt-4">
                <div class="tabs cursor-pointer" :class="{ 'selected': tab === 'tenant' }" @click="changeTab('tenant')">
                    <div class="text14px grey400">Tenant</div>
                </div>
                <div class="tabs cursor-pointer" :class="{ 'selected': tab === 'sirh' }" @click="changeTab('sirh')">
                    <div class="text14px grey400">SIRH</div>
                </div>
            </div>
            <div class="adressescontainer" v-if="tab === 'tenant'">
                <div v-for="integration in props.integrations.tenant" :key="integration.name"
                    class="flex items-center p-4 border rounded-lg shadow-lg bg-white gap-2" style="height: 100px;">
                    <img :src="integration.logo" :alt="integration.name + ' Logo'" class="h-full">
                    <div class="ml-4 flex-1">
                        <h2 class="text-lg font-semibold text-gray-700">{{ integration.title }}</h2>
                        <p class="text-sm text-gray-500">{{ integration.description }}</p>
                    </div>
                    <div class="ml-auto" v-if="!isServiceConnected(integration.name)">
                        <div class="bigbutton purple" @click="openIntegration(integration)">
                            <div class="text14px white">
                                Syncroniser
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto flex gap-2 items-center" v-else>
                        <img class="image24x24px" loading="lazy" width="auto" height="auto" alt=""
                            src="/images/synchronise-icon.svg" />
                        <div class="text14px medium" style="color: #60CF82;">
                            Synchronisé
                        </div>
                    </div>
                </div>
            </div>
            <div class="adressescontainer" v-if="tab === 'sirh'">
                <div v-for="integration in props.integrations.sirh" :key="integration.name"
                    class="flex items-center p-4 border rounded-lg shadow-lg bg-white gap-2" style="height: 100px;">
                    <img :src="integration.logo" :alt="integration.name + ' Logo'" class="h-full">
                    <div class="ml-4 flex-1">
                        <h2 class="text-lg font-semibold text-gray-700">{{ integration.title }}</h2>
                        <p class="text-sm text-gray-500">{{ integration.description }}</p>
                    </div>
                    <div class="ml-auto" v-if="!isServiceConnected(integration.name)">
                        <div class="bigbutton purple" @click="openIntegration(integration)">
                            <div class="text14px white">
                                Syncroniser
                            </div>
                        </div>
                    </div>
                    <div class="ml-auto flex gap-2 items-center" v-else>
                        <img class="image24x24px" loading="lazy" width="auto" height="auto" alt=""
                            src="/images/synchronise-icon.svg" />
                        <div class="text14px medium" style="color: #60CF82;">
                            Synchronisé
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Integration @closeIntegration="closeIntegration" v-if="showIntegration" :integration="currentIntegration" />
</template>

<style scoped>
.selected .text14px.grey400 {
    color: var(--main);
}
</style>
