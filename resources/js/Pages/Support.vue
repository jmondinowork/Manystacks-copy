<script setup>
import SupportNavbar from '@/Components/SupportNavbar.vue';
import SimpleLayout from '@/Layouts/SimpleLayout.vue';
import EmptyPage from '@/Components/EmptyPage.vue';

import { usePage, Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref, nextTick } from 'vue';
import { useStore } from "vuex";

const store = useStore();
const { props } = usePage();

const superadmin = window.location.pathname.includes('Admin');
const messageContainer = ref('');
const currentTicket = ref(props.supports.find(support => support.id === props.currentSupportId));
const updateCurrentTicket = async (ticket) => {
    currentTicket.value = ticket;
    await nextTick();
    messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
}

onMounted(() => {
    if (currentTicket.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
});

const form = useForm({
    message: "",
    id: null,
    from: superadmin ? 'superadmin' : 'user'
});
const submit = async () => {
    if (!form.message) {
        return;
    }

    try {
        form.id = currentTicket.value.id;
        const response = await axios.post('/api/sendMessage', form);
        props.supports = response.data;
        currentTicket.value = props.supports.find(support => support.id === currentTicket.value.id);
        form.message = "";
        await nextTick();
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
        store.dispatch('updateAnnounce', "Votre message a bien été envoyé");
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de l'envoi de votre message");
    }
}

const clotureTicket = async () => {
    try {
        const response = await axios.post('/api/clotureTicket', { id: currentTicket.value.id });
        props.supports = props.supports.filter(support => support.id !== currentTicket.value.id);
        updateCurrentTicket(props.supports[0]);
        store.dispatch('updateAnnounce', "Le ticket a bien été clôturé");
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la clôture du ticket");
    }
}
</script>

<template>
    <Head>
        <title>Support</title>
        <meta name="description" content="Bienvenue dans votre panier">
    </Head>

    <SimpleLayout>
        <div v-if="props.supports.length" class="maincontainer h-full">
            <div class="commandecontainer">
                <SupportNavbar :currentTicket="currentTicket" @updateCurrentTicket="updateCurrentTicket">
                </SupportNavbar>

                <div ref="messageContainer"
                    class="componentcontainer height100 alignstretch overflowhide flex-col justify-between overflow-auto"
                    style="gap: 40px;">
                    <div @click="clotureTicket" v-if="currentTicket.status != 'Résolu' && superadmin"
                        class="position-absolute top-2 right-2 bigbutton purple w-fit z-50">
                        <div class="text14px white nowrap">Cloturer le ticket</div>
                    </div>
                    <template v-if="currentTicket">
                        <div class="flex flex-col gap-6 w-full">
                            <div class="w_container" v-for="message in currentTicket.messages" :key="message.id"
                                :class="((message.from == 'user' && superadmin) || (message.from == 'superadmin' && !superadmin)) ? 'justify-start' : 'justify-end'">
                                <div class="message" :class="{ 'admin': message.from == 'superadmin' }">
                                    <div>{{ message.message }}</div>
                                </div>
                            </div>
                        </div>

                        <div v-if="currentTicket.status != 'Résolu'">
                            <form @submit.prevent="submit" class="w_container items-center gap-4">
                                <textarea name="" id="" v-model="form.message" placeholder="Votre message"></textarea>
                                <button type="submit">
                                    <img src="/images/envoyer-icon.png" style="height: 20px;" alt="">
                                </button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <EmptyPage v-else :section="'support'" @updateCurrentTicket="updateCurrentTicket"></EmptyPage>
    </SimpleLayout>
</template>

<style scoped>
.commandecontainer {
    width: 100%;
    height: 100%;
    max-height: 100%;
    min-height: 100%;
    grid-column-gap: 8px;
    grid-row-gap: 8px;
    grid-template-rows: auto;
    grid-template-columns: 1fr minmax(70%, 30%);
    grid-auto-columns: 1fr;
    justify-content: center;
    align-items: stretch;
    display: grid;
    overflow: hidden;
}

.message {
    background-color: var(--orange-light);
    color: var(--orange);
    font-weight: 500;
    padding: 20px;
    border-radius: 8px;
    max-width: 70%;
}

.message.admin {
    background-color: var(--main-light);
    color: var(--main);
}

textarea {
    background-color: #FFF;
    border-radius: 8px;
    width: 100%;
}
</style>
