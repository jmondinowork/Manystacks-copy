<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['close']);

const close = () => {
    emit('close');
}

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const processing = ref(false);
const offboard = () => {
    processing.value = true;

    try {
        axios
            .post(route('offboardCollaborateur'), { user_id: props.attribution.id })
            .then(() => {
                localStorage.setItem('offboarding', 'Collaborateur offboardé avec succès');
                window.location.reload();
            });
    } catch (error) {
        store.dispatch(
            'updateErrorAnnounce',
            "Une erreur est survenue lors de l'offboarding du collaborateur"
        );
        processing.value = false;
    }
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Offboarding du collaborateur
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer">
                <div class="w_container flex-col gap-6 w-full">
                    <div class="text18px bold-text">
                        Vous êtes sur le point d'offboarder votre collaborateur.
                    </div>

                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text16px">
                            Tous ses équipements seront mis en réserve et lui seront retirés.
                        </div>
                    </div>

                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text16px">
                            Toutes ses licences lui seront retirées.
                        </div>
                    </div>

                    <div class="flex gap-2 items-center">
                        <div class="dot"></div>
                        <div class="text16px">
                            Son e-mail pro et son compte Manystacks seront conservés.
                        </div>
                    </div>

                    <div class="w_container gap12px">
                        <button type="submit" :disabled="processing" @click="offboard"
                            :class="['button gap-5 w-full', processing ? 'gray' : '']">
                            <div class="text14px white">
                                Confirmer
                            </div>
                            <span v-if="processing" class="loader small"></span>
                        </button>

                        <div class="bigbutton" @click="close()" style="background-color: var(--grey-100);">
                            <div class="text14px">
                                Annuler
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
