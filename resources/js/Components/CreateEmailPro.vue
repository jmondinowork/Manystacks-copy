<script setup>
import { onMounted, onUnmounted } from "vue";
import { usePage, useForm } from "@inertiajs/vue3";
import { useStore } from "vuex";
import { computed } from "vue";
import { ref } from "vue";

const { props } = usePage();
const store = useStore();
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

const hasGoogleWorkspace = computed(() => props.licence_available.some(licence => licence.reference_id.includes('GoogleWorkspace')));

const patterns = computed(() => {
    let firstName = props.attribution.name.split(' ')[0].toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '');
    let lastName = props.attribution.name.split(' ')[1].toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\s+/g, '');

    return [
        firstName + lastName,
        firstName + '.' + lastName,
        firstName + '_' + lastName,
        firstName + '-' + lastName,
        firstName[0] + lastName,
        firstName[0] + '.' + lastName,
        firstName[0] + '_' + lastName,
        firstName[0] + '-' + lastName,
        lastName + firstName,
        lastName + '.' + firstName,
        lastName + '_' + firstName,
        lastName + '-' + firstName,
        lastName + firstName[0],
        lastName + '.' + firstName[0],
        lastName + '_' + firstName[0],
        lastName + '-' + firstName[0],
    ];
});

const currentDomainsList = ref(props.domains[props.currentDomain.tenant]);
const selectDomain = (tenant) => {
    form.tenant = tenant;
    form.domain = props.domains[tenant][0];
    currentDomainsList.value = props.domains[tenant];
}

const form = useForm({
    pattern: patterns.value[0],
    email_perso: props.attribution.email_perso,
    domain: props.currentDomain.domain,
    tenant: props.currentDomain.tenant,
    processing: false,
});
const errors = ref({
    pattern: false,
    email_perso: false,
});

const isValidEmail = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
const validateField = async (value, fieldName) => {
    if (fieldName == 'email_perso' && !isValidEmail(value)) {
        errors.value[fieldName] = true;
        return false;
    }
    if (fieldName == 'pattern') {
        let response = await axios.post(route('checkUserEmailTenant'), { email: form.pattern + '@' + form.domain, tenant: form.tenant });

        if (response.data.exists) {
            errors.value[fieldName] = true;
            return false;
        }
    }
    errors.value[fieldName] = false;
    return true;
}
const handleChange = (input) => validateField(form[input.target.id], input.target.id);
const submit = async () => {
    let hasError = 0;

    form.processing = true;

    if (form.tenant === 'google' && !hasGoogleWorkspace.value) {
        store.dispatch('updateErrorAnnounce', 'Vous devez avoir une licence Google Workspace disponible pour synchroniser un compte Google.');
        form.processing = false;
        return;
    }

    for (const fieldName in errors.value) {
        if (!await validateField(form[fieldName], fieldName)) {
            hasError = 1;
        }
    }

    if (!hasError) {
        try {
            form.userid = props.attribution.id;
            form.email = form.pattern + '@' + form.domain;

            const response = await axios.post(route('createTenantAccount', form));

            store.dispatch('updateAnnounce', 'Le compte a été synchronisé avec succès.');
            props.attribution = response.data.user;

            form.reset();
            close();
        } catch (error) {
            store.dispatch('updateErrorAnnounce', 'Une erreur est survenue lors de la synchronisation du compte.');

            form.processing = false;
        }
    }
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer" style="max-width: 800px;">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Synchroniser un collaborateur
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <form @submit.prevent="submit" class="componentcontainer flex-col">
                <div class="w_container vertical">
                    <div class="w_container aligncenter gap-2">
                        <div class="toggle" v-for="(domain, key) in props.domains" :key="key"
                            :class="{ 'selected': form.tenant == key }" @click="selectDomain(key)">
                            <img :src="'/images/' + key + '-logo.png'" loading="lazy" alt="" class="image20x20px">
                            <div class="text14px capitalize">{{ key }}</div>
                        </div>
                    </div>
                </div>

                <div class="w_container flex items-center p-2 gap-2 rounded-lg error mt-4"
                    style="background-color: var(--yellow);" v-if="form.tenant === 'google'">
                    <img class="image16x16px" loading="lazy" width="auto" height="auto" alt=""
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65741c8ac4e2ecd7e67084ef_alert-triangle.svg" />
                    <div class="text14px white">
                        Attention, pour créer une nouvelle adresse email Google, vous devrez obligatoirement lui
                        attribuer une licence Google Workspace avant.
                    </div>
                </div>

                <div class="text16px bold-text mt-4">L'adresse email pro de votre collaborateur sera :</div>

                <div class="flex flex-col mt-4">
                    <div class="w_container gap-2 items-center">
                        <div class="w_container vertical">
                            <div class="text14px">
                                Pattern
                                <span class="red">*</span>
                            </div>
                            <select v-model="form.pattern" id="pattern" :class="{ 'error': errors.pattern }">
                                <option v-for="pattern in patterns" :value="pattern">
                                    {{ pattern }}
                                </option>
                            </select>
                        </div>
                        <div class="text16px bold-text mt-5">@</div>
                        <div class="w_container vertical">
                            <div class="text14px">
                                Domaine
                                <span class="red">*</span>
                            </div>
                            <!-- <input type="text" v-model="form.domain" readonly> -->
                            <select v-model="form.domain" id="domain">
                                <option v-for="domain in currentDomainsList" :value="domain">
                                    {{ domain }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div v-if="errors.pattern"
                        class="flex items-center space-x-2 p-2 bg-red-100 text-red-600 rounded-md mt-2 w-fit">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text12px">L'adresse email n'est pas disponible. Essayez un autre pattern</span>
                    </div>
                    <div v-else class="flex items-center space-x-2 p-2 bg-green-100 text-green-600 rounded-md w-fit">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2beba3dab88c100b1f006_Vector.png"
                            style="width: 16px;height: 16px;" alt="">
                        <span class="text12px">L'adresse email est disponible !</span>
                    </div>

                    <div class="text16px bold-text mt-6 mb-6">Les instructions de première connexion du
                        collaborateur seront
                        envoyées à :</div>
                    <div class="w_container gap-2 items-center">
                        <div class="w_container vertical">
                            <div class="text14px">
                                Adresse email personnelle
                                <span class="red">*</span>
                            </div>
                            <input style="width: 50%;" type="email" id="email_perso" v-model="form.email_perso"
                                :class="{ 'error': errors.email_perso }" @input="handleChange">
                        </div>
                    </div>
                </div>

                <div class="w_container vertical gap8px mt-10">
                    <button type="submit" :disabled="form.processing"
                        :class="['button gap-5', form.processing ? 'gray' : '']">
                        <div class="text14px white">
                            Synchroniser
                        </div>
                        <span v-if="form.processing" class="loader small"></span>
                    </button>
                    <div class="bigbutton" @click="close">
                        <div class="text14px">
                            Annuler
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
select {
    background-color: white;
}

input.error {
    border: 1px solid red;
}

input,
select {
    background-color: #FFF;
    display: block;
    width: 100%;
    height: 38px;
    padding: 8px 12px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 1.42857143;
    vertical-align: middle;
    border: 1px solid #fff;
}

input.error,
select.error {
    border: 1px solid var(--red);
}

input.error:focus,
select.error:focus {
    border: 1px solid var(--red) !important;
}
</style>
