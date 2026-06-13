<script setup>
import { ref } from 'vue';

const explanation = ref(false);
const oobe = ref(false);

const emit = defineEmits(['updateTab']);

const changeTab = (tabName) => {
    emit('updateTab', tabName);
}
</script>

<template>
    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="explanation = !explanation">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/help-circle-icon.svg" alt="">
                </div>
                <h2 class="text-lg font-bold text-gray-800">
                    Qu'est-ce que Windows Autopilot&nbsp;?
                </h2>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': explanation }" alt="">
        </div>

        <div v-if="explanation">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <p class="text-gray-700">
                Windows Autopilot est une solution de déploiement de Microsoft qui permet de préparer,
                configurer
                et personnaliser le PC de vos collaborateurs, directement au démarrage de l'ordinateur, selon vos
                configurations.
            </p>
            <br />
            <p class="text-gray-700">
                A partir du moment ou votre collaborateur allume son PC, ou qu'un ordinateur est réinitialisé, et qu'il
                entre ses identifiants Microsoft
                365,
                Windows Autopilot prends le relais se charge d'installer les <span @click="changeTab('applications')"
                    class="selected">applications</span> et de pousser les <span @click="changeTab('securite')"
                    class="selected">politiques de
                    sécurité</span> que vous avez définies.
            </p>
            <br />
            <p class="text-gray-700">
                Vous trouverez ci-dessous, dans la section OOBE, la liste détaillée des paramètres d'Autopilot.
            </p>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="oobe = !oobe">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/box-icon.svg" alt="">
                </div>
                <h2 class="text-lg font-bold text-gray-800">
                    Out of Box Experience (OOBE)
                </h2>
            </div>
            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': oobe }" alt="">
        </div>

        <div v-if="oobe">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="px-2 flex flex-col gap-6">
                <!-- 1. Deployment mode -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Deployment mode :</span>
                        </div>
                        <span class="bold-text-2">User-driven</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Déploie l’appareil en impliquant l’utilisateur pour la configuration initiale.
                    </div>
                </div>

                <!-- 2. Join to Microsoft Entra ID as -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Join to Microsoft Entra ID as :</span>
                        </div>
                        <span class="bold-text-2">Microsoft Entra joined</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Associe l’appareil au tenant Microsoft Entra ID (anciennement Azure AD) pour la gestion et la
                        sécurité.
                    </div>
                </div>

                <!-- 3. Language (Region) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Language (Region) :</span>
                        </div>
                        <span class="bold-text-2">Operating system default</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Utilise la langue définie par défaut sur le système d’exploitation.
                    </div>
                </div>

                <!-- 4. Automatically configure keyboard -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Automatically configure keyboard :</span>
                        </div>
                        <span class="bold-text-2">Yes</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Configure automatiquement la disposition du clavier selon la langue sélectionnée.
                    </div>
                </div>

                <!-- 5. Microsoft Software Licence Terms -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Microsoft Software Licence Terms :</span>
                        </div>
                        <span class="bold-text-2">Hide</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Masque l’affichage des termes de licence lors de la configuration initiale.
                    </div>
                </div>

                <!-- 6. Privacy Settings -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Privacy Settings :</span>
                        </div>
                        <span class="bold-text-2">Hide</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Masque les options de confidentialité pendant le processus d’installation.
                    </div>
                </div>

                <!-- 7. Hide Change account options -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Hide Change account options :</span>
                        </div>
                        <span class="bold-text-2">Hide</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Empêche l’utilisateur de modifier son compte durant l’installation.
                    </div>
                </div>

                <!-- 8. User account type -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">User account type :</span>
                        </div>
                        <span class="bold-text-2">Standard</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Affecte les nouveaux utilisateurs en tant que comptes standard, avec des droits limités.
                    </div>
                </div>

                <!-- 9. Allow Pre-provisioned deployment -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Pre-provisioned deployment :</span>
                        </div>
                        <span class="bold-text-2">No</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Désactive la possibilité pour l’administrateur de préparer et de configurer un appareil avant de
                        le donner à l’utilisateur.
                    </div>
                </div>

                <!-- 10. Apply device name template -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Apply device name template :</span>
                        </div>
                        <span class="bold-text-2">No</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’applique pas de modèle de nom d’appareil personnalisé pendant l’out-of-box experience (OOBE).
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.selected {
    color: var(--main);
    text-decoration: underline;
    cursor: pointer;
}
</style>
