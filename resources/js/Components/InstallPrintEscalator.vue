<script setup>
import { onMounted, onUnmounted } from 'vue';

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closePrint']);

const close = () => emit('closePrint')
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const detectOS = () => {
    const userAgent = window.navigator.userAgent;
    if (userAgent.indexOf('Windows') !== -1) {
        return 'windows';
    } else if (userAgent.indexOf('Mac') !== -1) {
        return 'mac';
    }
    return 'unknown';
};
const downloadScript = () => {
    const os = detectOS();
    let url = '';
    if (os === 'windows') {
        url = 'https://manystacks.s3.eu-west-3.amazonaws.com/Scripts/AddPrinterEscalatorWindows.zip';
    } else if (os === 'mac') {
        url = 'https://manystacks.s3.eu-west-3.amazonaws.com/Scripts/AddPrinterEscalatorMacos.zip';
    }
    const a = document.createElement('a');
    a.href = url;
    a.download = url.split('/').pop();
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <form @submit.prevent="submit" class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Installer l'imprimante
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="text16px font-semibold">
                        0. Assurez-vous que vous êtes bien connecté au réseau WIFI LESCALATOR.
                    </div>
                    <div class="text14px mt-4">
                        1. <span class="font-semibold">Téléchargez</span> le script d'installation en <span
                            class="underline cursor-pointer text16px text-blue-500 font-semibold"
                            @click="downloadScript()">cliquant juste ici</span>.
                    </div>
                    <div v-if="detectOS() === 'windows'" class="w_container vertical gap20px">
                        <div class="text14px">
                            2. Rendez-vous dans le <span class="font-semibold">dossier téléchargement</span> et <span
                                class="font-semibold">Décompressez</span> le dossier AddPrinterEscalatorWindows avec
                            clique droit > "extraire tout..."
                        </div>
                        <div class="text14px">
                            3. Allez dans le dossier <span class="font-semibold">décompressé</span> et <span
                                class="font-semibold">double-cliquez</span> sur le fichier <span
                                class="font-semibold">"AddPrinterEscalator"</span>
                        </div>
                        <div class="text14px">
                            4. Cette fenêtre s'ouvre, cliquez alors sur <span class="font-semibold">"Informations
                                complémentaire"</span> puis <span class="font-semibold">"Exécuter quand même"</span>
                            <div class="w_container gap20px position-relative">
                                <img src="/images/script-wind-1.png" style="width: calc(50% - 10px);margin: 0 auto;"
                                    alt="">
                                <img src="/images/script-wind-2.png" style="width: calc(50% - 10px);margin: 0 auto;"
                                    alt="">
                            </div>
                        </div>
                        <div class="text14px">
                            5. <span class="font-semibold">Autorisez</span> l'application à apporter des modifications à votre appareil
                        </div>
                        <div class="text14px">
                            6. La fenêtre d'installation s'ouvre, <span class="font-semibold">laissez s'exécuter</span> le script jusqu'à ce qu'on vous demande d'appuyer sur une touche.
                        </div>
                        <div class="text14px">
                            6. Appuyez sur une touche pour terminer l'installation.
                        </div>
                        <div class="text14px">
                            7. C'est terminé ! Vous pouvez maintenant imprimer vos documents.
                        </div>
                    </div>
                    <div v-else class="w_container vertical gap20px">
                        <div class="text14px">
                            2. <span class="font-semibold">Décompressez</span> le fichier téléchargé en double-cliquant
                            dessus.
                        </div>
                        <div class="text14px">
                            3. <span class="font-semibold">Exécutez</span> le script sur votre ordinateur en
                            double-cliquant dessus.
                        </div>
                        <div class="text14px">
                            4. Cette fenêtre s'ouvre, cliquez alors sur <span class="font-semibold">"Annuler"</span>
                            <img src="/images/script-macos-1.png" style="width: 300px;margin: 0 auto;" alt="">
                        </div>
                        <div class="text14px">
                            5. Allez dans <span class="font-semibold">"Réglages Système"</span> puis <span
                                class="font-semibold">"Sécurité et confidentialité"</span> descendez et cliquez sur
                            <span class="font-semibold">"Ouvrir quand même"</span>
                            <img src="/images/script-macos-2.png" style="width: 600px;height:auto;margin: 0 auto;"
                                alt="">
                        </div>
                        <div class="text14px">
                            6. Votre mot de passe ou touchID vous sera demandé, puis cliquez sur <span
                                class="font-semibold">"Ouvrir"</span>
                            <img src="/images/script-macos-3.png" style="width: 300px;margin: 0 auto;" alt="">
                        </div>
                        <div class="text14px">
                            7. C'est terminé ! Vous pouvez maintenant imprimer vos documents.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
