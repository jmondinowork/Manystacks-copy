<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import InputError from '@/Components/vendor/InputError.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
    processing: false,
});

const dataUser = ref(null);

const rotatingMessages = ref([]);
const currentMessageIndex = ref(0);
let intervalId = null;

onMounted(() => {
    intervalId = setInterval(() => {
        currentMessageIndex.value = (currentMessageIndex.value + 1) % rotatingMessages.value.length;
    }, 5000);
});

onUnmounted(() => {
    clearInterval(intervalId);
});

const submit = async () => {
    form.processing = true;

    try {
        const response = await axios.post('/getDataLoginUser', { email: form.email, password: form.password });
        dataUser.value = response.data;

        const messages = [];

        messages.push(`Bonjour ${dataUser.value.name}, nous préparons votre espace...`);

        if (dataUser.value.ion_id) {
            messages.push("Synchronisation de vos licences...");
        }
        if (dataUser.value.microsoft) {
            messages.push("Synchronisation de votre espace Microsoft...");
        }
        if (dataUser.value.google) {
            messages.push("Synchronisation de votre espace Google...");
        }
        if (dataUser.value.sirh) {
            messages.push("Synchronisation de vos utilisateurs SIRH...");
        }

        const defaultMessages = [
            "Veuillez patienter...",
            "Un instant...",
            "Presque terminé, merci de patienter...",
            "Finalisation en cours..."
        ];

        let defaultIndex = 0;
        while (messages.length < 5) {
            messages.push(defaultMessages[defaultIndex % defaultMessages.length]);
            defaultIndex++;
        }

        rotatingMessages.value = messages;

        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    }
    catch (error) {
        form.processing = false;
        form.errors.email = true;
    }
};
</script>

<template>
    <div class="page-wrapper">
        <header class="section_home_hero-header">
            <div class="w-layout-grid home_hero-header_component">
                <div class="right_container se-connecter">
                    <div class="container_inscription">
                        <div class="creer_compte-logo-text">
                            <div>
                                <ApplicationLogo class="small_logo" />
                            </div>
                            <h1 class="heading-24 title_heading_home">Se connecter</h1>
                        </div>
                        <div class="margin-bottom margin-small">
                            <div class="home_contact_content">
                                <div class="home_contact_form-block w-form">
                                    <form @submit.prevent="submit" class="home_contact_form">
                                        <div class="form_field-wrapper">
                                            <label for="Contact-9-Email-2" class="form_field-label">Email</label>
                                            <input class="form_input w-full" id="email" type="email"
                                                autocomplete="username" autofocus v-model="form.email" required>
                                            <InputError class="mt-2" v-if="form.errors.email"
                                                :message="'Ces identifiants ne correspondent pas à nos enregistrements.'" />
                                        </div>
                                        <div class="form_field-wrapper">
                                            <label for="Contact-9-Email-2" class="form_field-label">Mot de passe</label>
                                            <input class="form_input w-full" id="password" type="password"
                                                v-model="form.password" required autocomplete="current-password">
                                            <Link v-if="canResetPassword" :href="route('password.request')"
                                                class="mot_de_passe_oubli">
                                            <h1 class="heading-24 action_mot_oubli"><a href="#" class="se_connecter">Mot
                                                    de passe oublié ?</a></h1>
                                            </Link>
                                        </div>
                                        <button type="submit" :disabled="form.processing"
                                            :class="['button gap-5', form.processing ? 'gray' : '']">
                                            <div class="text14px white">
                                                Se connecter
                                            </div>
                                            <span v-if="form.processing" class="loader small"></span>
                                        </button>

                                        <div v-if="form.processing" class="mt-4 text-center">
                                            <transition name="fade" mode="out-in">
                                                <p :key="currentMessageIndex" class="text-blue-500 text-sm">
                                                    {{ rotatingMessages[currentMessageIndex] }}
                                                </p>
                                            </transition>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="creer_compte-link-seconnecter">
                            <h1 class="heading-24 link_seconnecter">Pas de compte ?
                                <a href="https://www.manystacks.co/contact" class="se_connecter">
                                    Contactez-nous
                                </a>
                                maintenant.
                            </h1>
                        </div>
                        <div>
                            <a class="underline text-blue-500" href="https://www.manystacks.co/legal/politique-de-confidentialite">Politique de confidentialité</a>
                        </div>
                    </div>
                </div>
                <div class="divider"></div>
                <div class="left-photo-container_csc">
                    <div class="photo_wrapper_left">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b970d355270c4c5b83a_Achat.png"
                            loading="lazy" width="230" alt="" class="image_csc mobile_none">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b970d355270c4c5b8dc_Designer.png"
                            loading="lazy" width="230" alt="" class="image_csc mobile_none">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b960d355270c4c5b7c5_Sales.png"
                            loading="lazy" width="230" alt="" class="image_csc mobile_none">
                    </div>
                    <div class="photo_wrapper_right">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b970d355270c4c5b86f_HR.png"
                            loading="lazy" width="230" alt="" class="image_csc mobile_none">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b960d355270c4c5af22_CEO.png"
                            loading="lazy" width="230" alt="" class="image_csc mobile_none">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66d74b960d355270c4c5aeef_Group%2097.png"
                            loading="lazy" width="230" alt="" class="image_csc">
                    </div>
                </div>
            </div>
        </header>
    </div>

</template>

<style scoped>
.small_logo {
    width: 4.5rem;
    height: 4.5rem;
    object-fit: contain;
}

.page-wrapper {
    border-style: none;
    border-width: 1px;
    border-top-color: var(--main);
    border-right-color: var(--main);
    border-bottom-color: var(--main);
    border-left-color: var(--main);
}

.section_home_hero-header {
    display: block;
}

.home_hero-header_component {
    display: flex;
    height: 100vh;
    padding-top: 5rem;
    padding-bottom: 5rem;
    justify-content: center;
    align-items: center;
    grid-auto-columns: 1fr;
    gap: 16px 0vh;
    grid-template-columns: 1fr 1fr 1fr;
    grid-template-rows: auto;
    border-style: solid;
    border-width: 1px;
    border-color: rgb(112, 112, 255);
}

.right_container {
    margin-right: 5rem;
    margin-left: 5rem;
    padding-right: 0px;
}

.container_inscription {
    display: flex;
    margin-top: 0rem;
    margin-bottom: 0rem;
    margin-left: 0vh;
    padding-top: 2rem;
    padding-bottom: 2rem;
    flex-flow: column;
    justify-content: center;
    align-items: center;
}

.creer_compte-logo-text {
    display: flex;
    margin-bottom: 32px;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
}

.margin-bottom.margin-small {
    display: flex;
    width: 100%;
    height: 100%;
    max-height: 35rem;
    max-width: 27rem;
    margin-top: 0rem;
    margin-right: 0rem;
    margin-left: 0rem;
    padding: 3.5rem 2rem;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
    gap: 13px;
    border-radius: 12px;
    box-shadow: rgba(0, 0, 0, 0.05) -8px -8px 12px 0px, rgba(0, 0, 0, 0.15) 8px 8px 12px 0px;
}

.creer_compte-link-seconnecter {
    margin-top: 1rem;
    font-size: 3rem;
    font-weight: 400;
}

.heading-24.title_heading_home {
    width: auto;
    margin-top: 0px;
    margin-bottom: 0px;
    font-size: 1.5rem;
    text-align: center;
    font-weight: bold;
}

.heading-24.link_seconnecter {
    font-size: 16px;
    font-weight: 400;
}

.home_contact_form-block {
    margin-bottom: 0px;
}

.home_contact_form {
    display: grid;
    width: 20rem;
    grid-auto-columns: 1fr;
    gap: 1.5rem;
    grid-template-columns: 1fr;
    grid-template-rows: auto auto;
}

.form_field-label {
    margin-bottom: 0.5rem;
    font-weight: 400;
}

.form_input {
    height: auto;
    max-width: 60rem;
    min-height: 2.75rem;
    margin-bottom: 0px;
    padding: 0.5rem 0.75rem;
    border-style: solid;
    border-width: 1px;
    border-color: rgb(112, 112, 255);
    border-radius: 8px;
    background-color: white;
    color: rgb(23, 30, 41);
    font-size: 1rem;
    line-height: 1.6;
}

.form_input:focus {
    border-color: rgb(112, 112, 255) !important;
}

.heading-24.action_mot_oubli {
    margin-top: 0px;
    margin-bottom: 0px;
    font-size: 12px;
    font-weight: 400;
}

.se_connecter {
    color: rgb(23, 30, 41);
    text-decoration: underline;
}

.divider {
    width: 1px;
    height: 90vh;
    background-color: var(--main);
}

.left-photo-container_csc {
    display: flex;
    overflow: visible;
    height: 100vh;
    padding-top: 0rem;
    padding-right: 5rem;
    padding-bottom: 0rem;
    gap: 32px;
    padding-left: 5rem;
    justify-content: flex-start;
    align-items: center;
}

.photo_wrapper_left {
    display: flex;
    margin-top: 24px;
    padding-top: 0rem;
    padding-bottom: 0rem;
    flex-flow: column;
    justify-content: flex-start;
    align-items: center;
    gap: 24px;
}

.photo_wrapper_right {
    display: flex;
    margin-top: 112px;
    margin-bottom: 0px;
    padding-top: 0rem;
    padding-bottom: 0rem;
    flex-flow: column;
    gap: 24px;
}

.image_csc {
    border-radius: 12px;
    box-shadow: rgba(0, 0, 0, 0.15) 8px 8px 12px 0px;
}

img {
    max-width: 100%;
    vertical-align: middle;
    display: inline-block;
}

/* Classes de transition pour l'effet fade */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
