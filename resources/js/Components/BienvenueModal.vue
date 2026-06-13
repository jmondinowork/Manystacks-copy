<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import Cookies from 'js-cookie';

const { props } = usePage();
const form = useForm({});

if (Cookies.get('tuto-step') == undefined) {
    props.userAuth.role == 'collaborateur' ? Cookies.set('tuto-step', 'catalogue') : Cookies.set('tuto-step', 'dashboard');
    Cookies.set('tuto-step-nb', 1);
}
const maxStep = props.userAuth.role == 'collaborateur' ? 6 : 10;
const currentStep = ref(Cookies.get('tuto-step-nb'));
const step = ref(Cookies.get('tuto-step'));
const next = (nextStep) => {
    Cookies.set('tuto-step', nextStep);
    Cookies.set('tuto-step-nb', parseInt(currentStep.value) + 1);
    window.location.href = `/${nextStep}`;
}

const submit = () => {
    form.processing = true;
    form.post(route('bienvenue'), {
        onFinish: () => {
            Cookies.remove('tuto-step');
            Cookies.remove('tuto-step-nb');
            window.location.href = '/dashboard';
        }
    });
}
</script>

<template>
    <div class="tuto">
        <div class="absolute" style="top: 52px;left: 84px;" v-if="step == 'dashboard'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Bienvenue sur votre dashboard !</h2>
                <p class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Dashboard</span>, vous voyez en un coup d'œil les
                    informations les plus importantes concernant votre compte.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('catalogue')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 106px;left: 84px;" v-if="step == 'catalogue'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Voici notre catalogue !</h2>
                <p v-if="props.userAuth.role != 'collaborateur'" class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Catalogue</span>, vous pouvez consulter tous les produits
                    que nous proposons, les ajouter à votre panier et les commander.
                </p>
                <p v-else class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Catalogue</span>, vous pouvez consulter tous les produits
                    que nous proposons et les ajouter à votre panier puis les envoyer à votre administrateur.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mes-equipements')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 160px;left: 84px;" v-if="step == 'mes-equipements'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos équipements ici !</h2>
                <p v-if="props.userAuth.role != 'collaborateur'" class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Equipements</span>, vous pouvez retrouver tous vos
                    équipements Manystacks ainsi que ce que vous avez importé.
                </p>
                <p v-else class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Equipements</span>, vous pouvez retrouver tous les
                    équipements de votre entreprise.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mes-licences')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 218px;left: 84px;" v-if="step == 'mes-licences'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos licences ici !</h2>
                <p class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Licences</span>, vous pouvez retrouver toutes vos
                    licences, les manager et les attribuer.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mon-equipe')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 274px;left: 84px;" v-if="step == 'mon-equipe'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos collaborateurs ici !</h2>
                <p v-if="props.userAuth.role != 'collaborateur'" class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Équipe</span>, vous pouvez retrouver tous vos
                    collaborateurs et les manager, leur attribuer des équipements, des licences et plus.
                </p>
                <p v-else class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Équipe</span>, vous pouvez retrouver tous les membres de
                    votre équipe.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mes-salles')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 330px;left: 84px;" v-if="step == 'mes-salles'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos salles ici !</h2>
                <p v-if="props.userAuth.role != 'collaborateur'" class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Salles</span>, vous pouvez retrouver toutes vos salles et
                    les manager, leur attribuer des équipements et plus.
                </p>
                <p v-else class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Salles</span>, vous pouvez retrouver toutes les salles de
                    votre entreprise.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mes-commandes')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 386px;left: 84px;" v-if="step == 'mes-commandes'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos commandes ici !</h2>
                <p class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Commandes</span>, vous pouvez retrouver et suivre en temps
                    réel toutes vos commandes.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('mes-contrats')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 442px;left: 84px;" v-if="step == 'mes-contrats'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos contrats ici !</h2>
                <p class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Contrats</span>, vous pouvez retrouver tous vos contrats.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('supports')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="top: 498px;left: 84px;" v-if="step == 'supports'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 top-10 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos tickets de support ici !</h2>
                <p class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Support</span>, vous pouvez retrouver tous vos tickets de
                    support, les suivres et en créer.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="next('profile/mon-compte')"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer">
                        Continuer
                    </div>
                    <div @click="submit" class="text-indigo-500 hover:underline cursor-pointer">Passer le tutoriel</div>
                </div>
            </div>
        </div>

        <div class="absolute" style="bottom: 8px;left: 84px;" v-if="step == 'profile/mon-compte'">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto relative">
                <div
                    class="absolute -left-4 bottom-6 w-10 h-8 border-t-8 border-b-8 border-r-8 border-transparent border-r-white bg-white rotate-45">
                </div>
                <div class="absolute top-2 right-2 text-gray-500 text-sm">{{ currentStep + '/' + maxStep }}</div>
                <h2 class="text-xl font-bold mb-4">Retrouvez vos paramètres de compte !</h2>
                <p v-if="props.userAuth.role != 'collaborateur'" class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Profil</span>, vous pouvez retrouver les informations de
                    votre entreprise, vos adresses de livraisons et vos intégrations API.
                </p>
                <p v-else class="text-gray-600 mb-6">
                    Sur la partie <span class="font-semibold">Profil</span>, vous pouvez retrouver les informations de
                    votre compte.
                </p>
                <div class="flex justify-between items-center">
                    <div @click="submit"
                        class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer z-10">
                        J'ai compris, merci !
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.tuto {
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
}
</style>
