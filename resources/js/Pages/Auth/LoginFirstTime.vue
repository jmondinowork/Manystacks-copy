<script setup>
import ApplicationLogo from '@/Components/vendor/ApplicationLogo.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { props } = usePage();
const form = useForm({
    password: '',
    email: props.user.email,
    rgpd: false,
    token: props.user.remember_token,
    processing: false,
});

const conditions = ref({
    lowercase: false,
    uppercase: false,
    number: false,
    specialChar: false,
    length: false,
    allConditions: false,
});
const checkPassword = () => {
    const password = form.password;

    conditions.value.lowercase = /[a-z]/.test(password);
    conditions.value.uppercase = /[A-Z]/.test(password);
    conditions.value.number = /[0-9]/.test(password);
    conditions.value.specialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(password);
    conditions.value.length = password.length >= 10;
    conditions.value.allConditions = conditions.value.lowercase && conditions.value.uppercase && conditions.value.number && conditions.value.specialChar && conditions.value.length;
}

const checkSubmitPassword = computed(() => {
    if (conditions.value.allConditions && form.rgpd) {
        return true;
    }
    return false;
});

const createPassword = () => {
    if (checkSubmitPassword.value) {
        form.processing = true;

        form.post(route('storeFirstTime'));
    }
};
</script>

<template>
    <div class="componentcc-s1">
        <div class="containertitre">
            <ApplicationLogo class="image57x34" />

            <div class="contenttext">
                <div class="text24px unbounded bold-text">Créer votre compte</div>
                <div class="text14px">Créez votre mot de passe pour continuer sur Manystacks.</div>
            </div>
        </div>
        <div class="containermdp">
            <form @submit.prevent="submit" class="containermdp-form">
                <div class="w-form">
                    <div method="get" name="email-form" class="vertical">
                        <div class="telephone">
                            <input class="emailfield" readonly type="text" v-model="form.email">
                        </div>
                        <div class="mdp">
                            <div class="text14px">Mot de passe</div>
                            <input class="emailfield" placeholder="*****" v-model="form.password" type="password"
                                @input="checkPassword">
                        </div>
                        <div class="mdp-requirements">
                            <div class="text14px bold-text">Votre mot de passe doit contenir :
                            </div>
                            <ul role="list" class="list w-list-unstyled">
                                <li>
                                    <div :class="{ 'text14px': true, 'success': conditions.length }">Au moins <span
                                            class="text-span-26">10</span> caractères</div>
                                </li>
                                <li>
                                    <div :class="{ 'text14px': true, 'success': conditions.allConditions }">Ainsi que
                                        les
                                        éléments suivants :</div>
                                </li>
                                <li>
                                    <ul role="list" class="list-2">
                                        <li>
                                            <div :class="{ 'text14px': true, 'success': conditions.lowercase }">
                                                Minuscules
                                                (a-z)</div>
                                        </li>
                                        <li>
                                            <div :class="{ 'text14px': true, 'success': conditions.uppercase }">
                                                Majuscules
                                                (A-Z)</div>
                                        </li>
                                        <li>
                                            <div :class="{ 'text14px': true, 'success': conditions.number }">Chiffres
                                                (0-9)</div>
                                        </li>
                                        <li>
                                            <div :class="{ 'text14px': true, 'success': conditions.specialChar }">
                                                Caractères spéciaux (!&amp;%$#)</div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div></div>
                        <label class="w-checkbox checkbox-field">
                            <input class="w-checkbox-input" type="checkbox" v-model="form.rgpd">
                            <span class="checkbox-label" for="checkbox">J’ai lu et accepte les <a href="#"
                                    class="text-span-27">Conditions générales</a></span>
                        </label>
                    </div>
                </div>
                <div class="containercontinuer">
                    <input type="submit" :disabled="form.processing" class="button"
                        :class="checkSubmitPassword ? 'purple' : 'gray'" value="Continuer" @click="createPassword()">
                </div>
            </form>
        </div>
    </div>
<div>

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

.containermdp {
    display: flex;
    width: 562px;
    max-width: 100%;
    padding: 40px;
    flex-flow: column;
    justify-content: center;
    align-items: center;
    gap: 24px;
    border-radius: 12px;
    box-shadow: rgba(0, 0, 0, 0.05) -8px -8px 12px 0px, rgba(0, 0, 0, 0.15) 8px 8px 12px 0px;
}

.containermdp-form {
    display: flex;
    width: 100%;
    height: 100%;
    flex-flow: column;
}

.containercontinuer {
    display: flex;
    width: 100%;
    height: auto;
    margin-top: 14px;
    margin-bottom: 14px;
    justify-content: center;
    align-items: center;
    gap: 12px;
}

.vertical {
    display: flex;
    flex-flow: column;
    gap: 8px;
}

.mdp-requirements {
    display: flex;
    padding: 24px;
    flex-flow: column;
    justify-content: flex-start;
    align-items: flex-start;
    gap: 12px;
    border-radius: 4px;
    background-color: var(--grey-100);
}

.checkbox-field {
    display: flex;
    margin-top: 24px;
    margin-bottom: 12px;
    justify-content: center;
    align-items: flex-start;
    gap: 12px;
    text-align: center;
    cursor: pointer;
}

.w-checkbox-input {
    float: left;
    margin: 4px 0px 0px -20px;
    line-height: normal;
    cursor: pointer;
}

.checkbox-label {
    font-size: 14px;
    display: inline-block;
    cursor: pointer;
    font-weight: normal;
    margin-bottom: 0px;
}

.text-span-27 {
    text-decoration: underline;
}

.emailfield {
    width: 100%;
    height: 60px;
    border-style: solid;
    border-width: 1px;
    border-top-color: var(--main);
    border-right-color: var(--main);
    border-bottom-color: var(--main);
    border-left-color: var(--main);
    border-radius: 4px;
    background-color: rgb(247, 248, 249);
    margin-bottom: 10px;
}

.emailfield:focus {
    border-color: var(--main) !important;
}

.image20x20px.eye {
    position: absolute;
    right: 10px;
    top: 50%;
}

.mdp {
    position: relative;
    display: flex;
    width: auto;
    flex-flow: column;
    justify-content: flex-start;
    align-items: flex-start;
    gap: 4px;
}

.list {
    display: flex;
    flex-flow: column;
    gap: 8px;
    padding-left: 0;
    list-style: none;
}

ul {
    margin-top: 0px;
    margin-bottom: 10px;
    list-style-type: circle;
    margin-block-start: 0px;
    margin-block-end: 0px;
    padding-left: 40px;
}

li {
    display: list-item;
    text-align: -webkit-match-parent;
    unicode-bidi: isolate;
}

.text-span-26 {
    font-weight: 600;
}

.list-2 {
    display: flex;
    flex-flow: column;
    gap: 4px;
}

.success {
    color: var(--green);
    font-weight: 600;
}
</style>
