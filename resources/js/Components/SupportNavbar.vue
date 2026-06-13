<script setup>
import CreateSupport from "@/Components/CreateSupport.vue";
import { slugify } from "@/functions";

import { usePage } from '@inertiajs/vue3';
import { ref, watch } from "vue";

const { props } = usePage();
const data = defineProps({
    currentTicket: {
        type: Object,
        default: {},
    }
});
const currentTicket = ref(data.currentTicket);
const superadmin = window.location.pathname.includes('Admin');

const emit = defineEmits(['updateCurrentTicket']);
const updateCurrentTicket = (ticket) => {
    currentTicket.value = ticket;
    emit('updateCurrentTicket', ticket);
}

const updateCurrentTicketCreated = (ticket) => updateCurrentTicket(ticket);

const showCreateSupport = ref(false);
const closeCreateSupport = () => showCreateSupport.value = false;
const openCreateSupport = () => showCreateSupport.value = true;

const selectedTicket = (support_id) => {
    if (!currentTicket.value) return;

    return currentTicket.value.id === support_id;
}

watch(() => props.supports, (newValue, oldValue) => {
    if (newValue) {
        currentTicket.value = props.supports[0];
    }
});
</script>

<template>
    <div class="w_container vertical" id="supportnavbar">
        <div class="componentcontainer height100 overflow-scroll">
            <div class="w_container vertical height100 gap12px">
                <div class="w_container vertical pb-1">
                    <div v-for="support in props.supports" :key="support.id" :href="'/supports/' + support.id"
                        @click="updateCurrentTicket(support)" class="selectpage"
                        :class="{ 'active': selectedTicket(support.id) }">
                        <div class="text12px grey justify-end">
                            {{ support.numero_support }}
                        </div>
                        <div class="text14px medium" :class="{ 'purple': selectedTicket(support.id) }">
                            {{ support.user.name }}
                        </div>
                        <div class="text14px medium" :class="{ 'purple': selectedTicket(support.id) }">
                            {{ support.object }}
                        </div>
                        <div class="text14px medium" :class="{ 'purple': selectedTicket(support.id) }"
                            v-if="support.commande">N°{{ support.commande.reference_commande }}
                        </div>
                        <div class="text14px medium" :class="{ 'purple': selectedTicket(support.id) }"
                            v-if="support.equipement">{{ support.equipement.name }}</div>
                        <div class=" flex gap-4 items-center">
                            <div class="tagblock" :class="slugify(support.status)">
                                <div class="dot"></div>
                                <div>{{ support.status }}</div>
                            </div>
                            <div class="separatorvertical"></div>
                            <div class="flex gap-2 items-center">
                                <div>{{ support.messages.length }}</div>
                                <img src="/images/message-icon.png" style="height: 14px;" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!superadmin" class="bigbutton purple" @click="openCreateSupport">
            <div class="w_container aligncenter gap8px">
                <div class="text14px white">Créer un ticket</div>
            </div>
        </div>
    </div>

    <CreateSupport :show="showCreateSupport" @closeCreateSupport="closeCreateSupport"
        @updateCurrentTicketCreated="updateCurrentTicketCreated"></CreateSupport>
</template>

<style scoped>
#supportnavbar {
    max-height: calc(100vh - 114px);
}

.selectpage.active {
    background-color: #FFF;
}

.selectpage {
    flex-direction: column;
    padding: 12px 20px;
    align-items: start;
    height: unset;
}
</style>
