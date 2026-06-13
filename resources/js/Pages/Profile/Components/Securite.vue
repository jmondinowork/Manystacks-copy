<script setup>
import { ref } from 'vue';

const antivirus = ref(false);
const diskEncryption = ref(false);
const firewall = ref(false);
const accountProtection = ref(false);
const atackSurface = ref(false);
</script>

<template>
    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="antivirus = !antivirus">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/shield-purple-icon.svg" alt="">
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-gray-800">
                        Manystacks Basic Antivirus
                    </h2>
                    <p class="text-gray-500">
                        Microsoft Defender Antivirus
                    </p>
                </div>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': antivirus }" alt="">
        </div>

        <div v-if="antivirus">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="flex flex-col gap-6">
                <!-- 1. Allow Archive Scanning -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Archive Scanning :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Permet de scanner les fichiers archivés (ex. zip) pour y déceler des menaces.
                    </div>
                </div>

                <!-- 2. Allow Behavior Monitoring -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Behavior Monitoring :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Surveille en temps réel le comportement des applications pour repérer toute activité suspecte.
                    </div>
                </div>

                <!-- 3. Allow Cloud Protection -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Cloud Protection :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise le recours au cloud pour obtenir des informations de sécurité plus récentes.
                    </div>
                </div>

                <!-- 4. Allow Email Scanning -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Email Scanning :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Analyse les courriels afin de bloquer les pièces jointes ou liens malveillants.
                    </div>
                </div>

                <!-- 5. Allow Full Scan On Mapped Network Drives -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Full Scan On Mapped Network Drives :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Permet d’analyser les lecteurs réseau mappés lors d’une analyse complète.
                    </div>
                </div>

                <!-- 6. Allow Full Scan Removable Drive Scanning -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Full Scan Removable Drive Scanning :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Analyse les supports amovibles (clés USB, disques externes) lors d’une analyse complète.
                    </div>
                </div>

                <!-- 7. Allow scanning of all downloaded files and attachments -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow scanning of all downloaded files and attachments :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Analyse automatiquement tous les fichiers téléchargés ou reçus en pièces jointes.
                    </div>
                </div>

                <!-- 8. Allow Realtime Monitoring -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Realtime Monitoring :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active une surveillance permanente qui vérifie les menaces au fur et à mesure de leur
                        apparition.
                    </div>
                </div>

                <!-- 9. Allow Scanning Network Files -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Scanning Network Files :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Permet de scanner les fichiers qui se trouvent sur le réseau pour détecter d’éventuelles
                        menaces.
                    </div>
                </div>

                <!-- 10. Allow Script Scanning -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Script Scanning :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Analyse les scripts (ex. .ps1, .vbs) pour détecter du code malveillant.
                    </div>
                </div>

                <!-- 11. Allow User UI Access -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow User UI Access :</span>
                        </div>
                        <span class="bold-text-2">Not allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque l’accès de l’utilisateur à l’interface du logiciel de sécurité.
                    </div>
                </div>

                <!-- 12. Check For Signatures Before Running Scan -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Check For Signatures Before Running Scan :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Vérifie que les définitions de virus sont à jour avant de lancer une analyse.
                    </div>
                </div>

                <!-- 13. Cloud Block Level -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Cloud Block Level :</span>
                        </div>
                        <span class="bold-text-2">High</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Définit l’intensité du filtrage via le cloud sur un niveau élevé.
                    </div>
                </div>

                <!-- 14. Enable Low CPU Priority -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Low CPU Priority :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Diminue la charge du processeur pendant l’analyse pour réduire l’impact sur les performances.
                    </div>
                </div>

                <!-- 15. Enable Network Protection -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Network Protection :</span>
                        </div>
                        <span class="bold-text-2">Enabled (block mode)</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque les menaces en ligne avant qu’elles n’atteignent votre appareil.
                    </div>
                </div>

                <!-- 16. PUA Protection -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">PUA Protection :</span>
                        </div>
                        <span class="bold-text-2">PUA Protection on</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Détecte et bloque les logiciels potentiellement indésirables pour maintenir un système propre.
                    </div>
                </div>

                <!-- 17. Real Time Scan Direction -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Real Time Scan Direction :</span>
                        </div>
                        <span class="bold-text-2">Monitor all files (bi-directional)</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Surveille en temps réel tous les fichiers, qu’ils soient ouverts ou modifiés.
                    </div>
                </div>

                <!-- 18. Scan Parameter -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Scan Parameter :</span>
                        </div>
                        <span class="bold-text-2">Quick scan</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Lance une analyse rapide qui vérifie les zones critiques du système.
                    </div>
                </div>

                <!-- 19. Schedule Scan Day -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Schedule Scan Day :</span>
                        </div>
                        <span class="bold-text-2">Sunday</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Programme une analyse automatique chaque semaine le jour sélectionné.
                    </div>
                </div>

                <!-- 20. Submit Samples Consent -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Submit Samples Consent :</span>
                        </div>
                        <span class="bold-text-2">Send safe samples automatically.</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Envoie automatiquement des échantillons jugés sûrs pour améliorer la protection.
                    </div>
                </div>

                <!-- 21. Remediation action for Severe threats -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Remediation action for Severe threats :</span>
                        </div>
                        <span class="bold-text-2">Quarantine</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Met en quarantaine les menaces jugées très dangereuses pour protéger le système.
                    </div>
                </div>

                <!-- 22. Remediation action for Low severity threats -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Remediation action for Low severity threats :</span>
                        </div>
                        <span class="bold-text-2">Quarantine</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Met en quarantaine les menaces de faible gravité pour éviter tout risque ultérieur.
                    </div>
                </div>

                <!-- 23. Remediation action for Moderate severity threats -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Remediation action for Moderate severity threats :</span>
                        </div>
                        <span class="bold-text-2">Quarantine</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Met en quarantaine les menaces moyennement graves pour sécuriser votre appareil.
                    </div>
                </div>

                <!-- 24. Remediation action for High severity threats -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Remediation action for High severity threats :</span>
                        </div>
                        <span class="bold-text-2">Quarantine</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Met en quarantaine les menaces à haut risque pour empêcher leur propagation.
                    </div>
                </div>

                <!-- 25. Allow Network Protection Down Level -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Network Protection Down Level :</span>
                        </div>
                        <span class="bold-text-2">Network protection will be enabled downlevel.</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active la protection réseau même sur les versions plus anciennes du système.
                    </div>
                </div>

                <!-- 26. Scheduler Randomization Time -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Scheduler Randomization Time :</span>
                        </div>
                        <span class="bold-text-2">4</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Ajoute un décalage (en heures) pour éviter que toutes les analyses ne commencent en même temps.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="diskEncryption = !diskEncryption">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/disk-icon.svg" alt="">
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-gray-800">
                        Manystacks Basic Disk Encryption
                    </h2>
                    <p class="text-gray-500">
                        BitLocker
                    </p>
                </div>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': diskEncryption }" alt="">
        </div>

        <div v-if="diskEncryption" class="px-6">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="flex flex-col gap-6">
                <!-- Section Title: Windows Components > BitLocker Drive Encryption -->
                <h2 class="text-xl font-semibold">BitLocker Drive Encryption</h2>

                <!-- 1. Choose drive encryption method and cipher strength (Windows 10 [Version 1511] and later) -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Choose drive encryption method and cipher strength (Windows 10
                                [Version 1511] and later) :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise le paramétrage du type et du niveau de chiffrement pour BitLocker.
                    </div>
                </div>

                <!-- 2. Select the encryption method for removable data drives -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Select the encryption method for removable data drives :</span>
                        </div>
                        <span class="bold-text-2">XTS-AES 256-bit</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Applique un chiffrement XTS-AES 256 bits pour les lecteurs de stockage amovibles.
                    </div>
                </div>

                <!-- 3. Select the encryption method for operating system drives -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Select the encryption method for operating system drives
                                :</span>
                        </div>
                        <span class="bold-text-2">XTS-AES 256-bit</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Applique un chiffrement XTS-AES 256 bits pour le lecteur du système d’exploitation.
                    </div>
                </div>

                <!-- 4. Select the encryption method for fixed data drives -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Select the encryption method for fixed data drives :</span>
                        </div>
                        <span class="bold-text-2">XTS-AES 256-bit</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Applique un chiffrement XTS-AES 256 bits pour les disques fixes.
                    </div>
                </div>

                <!-- Section Title: Windows Components > BitLocker Drive Encryption > Operating System Drives -->
                <h2 class="text-xl font-semibold">Operating System Drives</h2>

                <!-- 5. Enforce drive encryption type on operating system drives -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enforce drive encryption type on operating system drives
                                :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Impose le chiffrement BitLocker sur le lecteur du système d’exploitation.
                    </div>
                </div>

                <!-- 6. Select the encryption type: (Device) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Select the encryption type: (Device) :</span>
                        </div>
                        <span class="bold-text-2">Full encryption</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Utilise le chiffrement complet (Full) plutôt que partiel (Used Space Only).
                    </div>
                </div>

                <!-- 7. Configure minimum PIN length for startup -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Configure minimum PIN length for startup :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise la définition d’une longueur minimale de code PIN lors du démarrage.
                    </div>
                </div>

                <!-- 8. Minimum characters -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Minimum characters :</span>
                        </div>
                        <span class="bold-text-2">8</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Exige que le code PIN comporte au moins 8 caractères.
                    </div>
                </div>

                <!-- 9. Allow enhanced PINs for startup -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow enhanced PINs for startup :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise l’utilisation de caractères étendus (majuscules, symboles, etc.) dans le PIN.
                    </div>
                </div>

                <!-- 10. Disallow standard users from changing the PIN or password -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disallow standard users from changing the PIN or password
                                :</span>
                        </div>
                        <span class="bold-text-2">Disabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Les utilisateurs standard peuvent tout de même modifier le PIN ou le mot de passe.
                    </div>
                </div>

                <!-- 11. Allow devices compliant with InstantGo or HSTI to opt out of pre-boot PIN. -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow devices compliant with InstantGo or HSTI to opt out of
                                pre-boot PIN. :</span>
                        </div>
                        <span class="bold-text-2">Disabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Empêche les appareils InstantGo/HSTI de contourner la saisie du PIN avant le démarrage.
                    </div>
                </div>

                <!-- Section Title: Windows Components > BitLocker Drive Encryption > Fixed Data Drives -->
                <h2 class="text-xl font-semibold">Fixed Data Drives</h2>

                <!-- 12. Enforce drive encryption type on fixed data drives -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enforce drive encryption type on fixed data drives :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Impose le chiffrement BitLocker sur les lecteurs de données fixes (non système).
                    </div>
                </div>

                <!-- 13. Select the encryption type: (Device) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Select the encryption type: (Device) :</span>
                        </div>
                        <span class="bold-text-2">Full encryption</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Utilise le chiffrement complet (Full) plutôt que partiel (Used Space Only) sur ces lecteurs.
                    </div>
                </div>

                <!-- 14. Deny write access to fixed drives not protected by BitLocker -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Deny write access to fixed drives not protected by BitLocker
                                :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque l’écriture sur les lecteurs fixes qui ne sont pas chiffrés par BitLocker.
                    </div>
                </div>

                <!-- Section Title: Windows Components > BitLocker Drive Encryption > Removable Data Drives -->
                <h2 class="text-xl font-semibold">Removable Data Drives</h2>

                <!-- 15. Control use of BitLocker on removable drives -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Control use of BitLocker on removable drives :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active le contrôle BitLocker sur les supports de stockage amovibles.
                    </div>
                </div>

                <!-- 16. Allow users to apply BitLocker protection on removable data drives (Device) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow users to apply BitLocker protection on removable data
                                drives (Device) :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Empêche les utilisateurs d’activer BitLocker sur les supports de stockage amovibles.
                    </div>
                </div>

                <!-- 17. Allow users to suspend and decrypt BitLocker protection on removable data drives (Device) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow users to suspend and decrypt BitLocker protection on
                                removable data drives (Device) :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Empêche les utilisateurs de suspendre ou de désactiver BitLocker sur les lecteurs amovibles.
                    </div>
                </div>

                <!-- 18. Deny write access to removable drives not protected by BitLocker -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Deny write access to removable drives not protected by BitLocker
                                :</span>
                        </div>
                        <span class="bold-text-2">Enabled</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque l’écriture sur les supports de stockage amovibles qui ne sont pas chiffrés par BitLocker.
                    </div>
                </div>

                <!-- 19. Do not allow write access to devices configured in another organization -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Do not allow write access to devices configured in another
                                organization :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise tout de même l’écriture sur des périphériques configurés dans une autre organisation.
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="firewall = !firewall">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/firewall-icon.svg" alt="">
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-gray-800">
                        Manystacks Basic FireWall
                    </h2>
                    <p class="text-gray-500">
                        Windows Defender Firewall
                    </p>
                </div>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': firewall }" alt="">
        </div>

        <div v-if="firewall" class="px-6">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="flex flex-col gap-6">
                <!-- 1. Certificate revocation list verification -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Certificate revocation list verification :</span>
                        </div>
                        <span class="bold-text-2">Attempt</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Le système essaie de vérifier la validité des certificats avant de les accepter.
                    </div>
                </div>

                <!-- 2. Enable Domain Network Firewall -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Domain Network Firewall :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active le pare-feu pour les connexions sur le réseau de domaine.
                    </div>
                </div>

                <!-- 3. Allow Local Ipsec Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Ipsec Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies IPsec locales avec d’autres stratégies.
                    </div>
                </div>

                <!-- 4. Disable Stealth Mode -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Le mode furtif reste activé pour mieux dissimuler les réponses du système sur le réseau.
                    </div>
                </div>

                <!-- 5. Auth Apps Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Auth Apps Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas l’utilisateur à fusionner ses préférences pour les applications approuvées.
                    </div>
                </div>

                <!-- 6. Enable Log Dropped Packets -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Dropped Packets :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Dropped Packets</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des paquets bloqués par le pare-feu.
                    </div>
                </div>

                <!-- 7. Shielded -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Shielded :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        La fonctionnalité de protection avancée (Shielded) est désactivée.
                    </div>
                </div>

                <!-- 8. Default Outbound Action -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Outbound Action :</span>
                        </div>
                        <span class="bold-text-2">Allow</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise par défaut le trafic sortant de l’appareil.
                    </div>
                </div>

                <!-- 9. Disable Inbound Notifications -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Inbound Notifications :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Les notifications pour les connexions entrantes restent activées.
                    </div>
                </div>

                <!-- 10. Disable Unicast Responses To Multicast Broadcast -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Unicast Responses To Multicast Broadcast :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Désactive l’envoi de réponses individuelles (unicast) aux requêtes de type multicast.
                    </div>
                </div>

                <!-- 11. Enable Log Ignored Rules -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Ignored Rules :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Ignored Rules</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des règles de pare-feu qui ont été ignorées.
                    </div>
                </div>

                <!-- 12. Global Ports Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Global Ports Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des préférences de ports globaux configurées par l’utilisateur.
                    </div>
                </div>

                <!-- 13. Default Inbound Action for Domain Profile -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Inbound Action for Domain Profile :</span>
                        </div>
                        <span class="bold-text-2">Allow</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise par défaut le trafic entrant sur le profil de domaine.
                    </div>
                </div>

                <!-- 14. Disable Stealth Mode Ipsec Secured Packet Exemption -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode Ipsec Secured Packet Exemption :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Ne désactive pas l’exemption en mode furtif pour les paquets sécurisés par IPsec.
                    </div>
                </div>

                <!-- 15. Allow Local Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies de pare-feu locales avec d’autres stratégies.
                    </div>
                </div>

                <!-- 16. Enable Log Success Connections -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Success Connections :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Successful Connections</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des connexions autorisées par le pare-feu.
                    </div>
                </div>

                <!-- 17. Enable Private Network Firewall -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Private Network Firewall :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active le pare-feu pour les réseaux privés (par exemple, votre réseau domestique).
                    </div>
                </div>

                <!-- 18. Enable Log Ignored Rules -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Ignored Rules :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Ignored Rules</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des règles de pare-feu qui ont été ignorées.
                    </div>
                </div>

                <!-- 19. Default Inbound Action for Private Profile -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Inbound Action for Private Profile :</span>
                        </div>
                        <span class="bold-text-2">Block</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque par défaut le trafic entrant sur le profil de réseau privé.
                    </div>
                </div>

                <!-- 20. Disable Unicast Responses To Multicast Broadcast -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Unicast Responses To Multicast Broadcast :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Désactive l’envoi de réponses individuelles (unicast) aux requêtes de type multicast.
                    </div>
                </div>

                <!-- 21. Disable Stealth Mode -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Le mode furtif reste activé pour mieux dissimuler les réponses du système sur le réseau.
                    </div>
                </div>

                <!-- 22. Enable Log Success Connections -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Success Connections :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Successful Connections</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des connexions autorisées par le pare-feu.
                    </div>
                </div>

                <!-- 23. Global Ports Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Global Ports Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des préférences de ports globaux configurées par l’utilisateur.
                    </div>
                </div>

                <!-- 24. Enable Log Dropped Packets -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Dropped Packets :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Dropped Packets</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des paquets bloqués par le pare-feu.
                    </div>
                </div>

                <!-- 25. Allow Local Ipsec Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Ipsec Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies IPsec locales avec d’autres stratégies.
                    </div>
                </div>

                <!-- 26. Disable Stealth Mode Ipsec Secured Packet Exemption -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode Ipsec Secured Packet Exemption :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Ne désactive pas l’exemption en mode furtif pour les paquets sécurisés par IPsec.
                    </div>
                </div>

                <!-- 27. Disable Inbound Notifications -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Inbound Notifications :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Les notifications pour les connexions entrantes restent activées.
                    </div>
                </div>

                <!-- 28. Shielded -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Shielded :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        La fonctionnalité de protection avancée (Shielded) est désactivée.
                    </div>
                </div>

                <!-- 29. Allow Local Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies de pare-feu locales avec d’autres stratégies.
                    </div>
                </div>

                <!-- 30. Default Outbound Action -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Outbound Action :</span>
                        </div>
                        <span class="bold-text-2">Allow</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise par défaut le trafic sortant de l’appareil.
                    </div>
                </div>

                <!-- 31. Auth Apps Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Auth Apps Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas l’utilisateur à fusionner ses préférences pour les applications approuvées.
                    </div>
                </div>

                <!-- 32. Enable Public Network Firewall -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Public Network Firewall :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active le pare-feu pour les réseaux publics (par exemple, les réseaux Wi-Fi publics).
                    </div>
                </div>

                <!-- 33. Enable Log Dropped Packets -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Dropped Packets :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Dropped Packets</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des paquets bloqués par le pare-feu.
                    </div>
                </div>

                <!-- 34. Enable Log Ignored Rules -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Ignored Rules :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Ignored Rules</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des règles de pare-feu qui ont été ignorées.
                    </div>
                </div>

                <!-- 35. Disable Stealth Mode -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Le mode furtif reste activé pour mieux dissimuler les réponses du système sur le réseau.
                    </div>
                </div>

                <!-- 36. Default Outbound Action -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Outbound Action :</span>
                        </div>
                        <span class="bold-text-2">Allow</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise par défaut le trafic sortant de l’appareil.
                    </div>
                </div>

                <!-- 37. Disable Inbound Notifications -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Inbound Notifications :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Les notifications pour les connexions entrantes restent activées.
                    </div>
                </div>

                <!-- 38. Disable Stealth Mode Ipsec Secured Packet Exemption -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Stealth Mode Ipsec Secured Packet Exemption :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Ne désactive pas l’exemption en mode furtif pour les paquets sécurisés par IPsec.
                    </div>
                </div>

                <!-- 39. Shielded -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Shielded :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        La fonctionnalité de protection avancée (Shielded) est désactivée.
                    </div>
                </div>

                <!-- 40. Allow Local Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies de pare-feu locales avec d’autres stratégies.
                    </div>
                </div>

                <!-- 41. Auth Apps Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Auth Apps Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas l’utilisateur à fusionner ses préférences pour les applications approuvées.
                    </div>
                </div>

                <!-- 42. Default Inbound Action for Public Profile -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Default Inbound Action for Public Profile :</span>
                        </div>
                        <span class="bold-text-2">Block</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Bloque par défaut le trafic entrant pour le profil réseau public.
                    </div>
                </div>

                <!-- 43. Disable Unicast Responses To Multicast Broadcast -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disable Unicast Responses To Multicast Broadcast :</span>
                        </div>
                        <span class="bold-text-2">True</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Désactive l’envoi de réponses individuelles (unicast) aux requêtes de type multicast.
                    </div>
                </div>

                <!-- 44. Global Ports Allow User Pref Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Global Ports Allow User Pref Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des préférences de ports globaux configurées par l’utilisateur.
                    </div>
                </div>

                <!-- 45. Enable Log Success Connections -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Log Success Connections :</span>
                        </div>
                        <span class="bold-text-2">Enable Logging Of Successful Connections</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active l’enregistrement des connexions autorisées par le pare-feu.
                    </div>
                </div>

                <!-- 46. Allow Local Ipsec Policy Merge -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Allow Local Ipsec Policy Merge :</span>
                        </div>
                        <span class="bold-text-2">False</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        N’autorise pas la fusion des stratégies IPsec locales avec d’autres stratégies.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="accountProtection = !accountProtection">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/lock-icon.svg" alt="">
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-gray-800">
                        Manystacks Basic Account Protection
                    </h2>
                    <p class="text-gray-500">
                        Windows Hello
                    </p>
                </div>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': accountProtection }"
                alt="">
        </div>

        <div v-if="accountProtection" class="px-6">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="flex flex-col gap-6">
                <!-- 1. Facial Features Use Enhanced Anti Spoofing -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Facial Features Use Enhanced Anti Spoofing :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Améliore la reconnaissance faciale pour détecter plus facilement les tentatives de tromperie.
                    </div>
                </div>

                <!-- 2. PIN History -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">PIN History :</span>
                        </div>
                        <span class="bold-text-2">5</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Conserve l’historique de 5 codes PIN précédents pour empêcher leur réutilisation.
                    </div>
                </div>

                <!-- 3. Require Security Device -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Require Security Device :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Nécessite un élément matériel sécurisé (par ex. TPM) pour stocker les informations
                        d’authentification.
                    </div>
                </div>

                <!-- 4. Special Characters -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Special Characters :</span>
                        </div>
                        <span class="bold-text-2">Allows the use of special characters in PIN.</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Permet d’inclure des caractères spéciaux dans le code PIN.
                    </div>
                </div>

                <!-- 5. Use Passport For Work -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Use Passport For Work :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active Windows Hello for Business pour l’authentification au travail.
                    </div>
                </div>

                <!-- 6. Expiration -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Expiration :</span>
                        </div>
                        <span class="bold-text-2">90</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Exige le renouvellement du code PIN tous les 90 jours.
                    </div>
                </div>

                <!-- 7. Minimum PIN Length -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Minimum PIN Length :</span>
                        </div>
                        <span class="bold-text-2">8</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Fixe la longueur minimale du code PIN à 8 caractères.
                    </div>
                </div>

                <!-- 8. Uppercase Letters -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Uppercase Letters :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise l’utilisation de lettres majuscules dans le code PIN.
                    </div>
                </div>

                <!-- 9. Enable Pin Recovery -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Pin Recovery :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active la fonctionnalité de récupération du code PIN en cas d’oubli.
                    </div>
                </div>

                <!-- 10. Lowercase Letters -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Lowercase Letters :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise l’utilisation de lettres minuscules dans le code PIN.
                    </div>
                </div>

                <!-- 11. Special Characters (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Special Characters (User) :</span>
                        </div>
                        <span class="bold-text-2">Allows the use of special characters in PIN.</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Permet à l’utilisateur d’inclure des caractères spéciaux dans le code PIN.
                    </div>
                </div>

                <!-- 12. PIN History (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">PIN History (User) :</span>
                        </div>
                        <span class="bold-text-2">5</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Conserve l’historique de 5 codes PIN précédents pour l’utilisateur.
                    </div>
                </div>

                <!-- 13. Minimum PIN Length (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Minimum PIN Length (User) :</span>
                        </div>
                        <span class="bold-text-2">8</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Fixe la longueur minimale du code PIN à 8 caractères pour l’utilisateur.
                    </div>
                </div>

                <!-- 14. Expiration (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Expiration (User) :</span>
                        </div>
                        <span class="bold-text-2">90</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Exige le renouvellement du code PIN tous les 90 jours pour l’utilisateur.
                    </div>
                </div>

                <!-- 15. Enable Pin Recovery (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Enable Pin Recovery (User) :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active la récupération du code PIN pour l’utilisateur.
                    </div>
                </div>

                <!-- 16. Require Security Device (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Require Security Device (User) :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Nécessite un élément matériel sécurisé pour l’utilisateur (ex. TPM).
                    </div>
                </div>

                <!-- 17. Uppercase Letters (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Uppercase Letters (User) :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise l’utilisation de lettres majuscules dans le code PIN côté utilisateur.
                    </div>
                </div>

                <!-- 18. Lowercase Letters (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Lowercase Letters (User) :</span>
                        </div>
                        <span class="bold-text-2">Allowed</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Autorise l’utilisation de lettres minuscules dans le code PIN côté utilisateur.
                    </div>
                </div>

                <!-- 19. Use Passport For Work (User) -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Use Passport For Work (User) :</span>
                        </div>
                        <span class="bold-text-2">true</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active Windows Hello for Business pour l’authentification au travail côté utilisateur.
                    </div>
                </div>

                <!-- 1. Credential Guard -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Credential Guard :</span>
                        </div>
                        <span class="bold-text-2">(Enabled with UEFI lock)</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active Credential Guard avec verrouillage UEFI pour mieux protéger les informations
                        d’identification.
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8">
        <div class="flex justify-between items-center cursor-pointer" @click="atackSurface = !atackSurface">
            <div class="flex items-center space-x-2">
                <div class="box-container">
                    <img src="/images/crosshair-icon.svg" alt="">
                </div>
                <div class="flex flex-col">
                    <h2 class="text-lg font-bold text-gray-800">
                        Manystacks Basic Attack Surface Reduction
                    </h2>
                    <p class="text-gray-500">
                        Windows Defender Attack Surface Reduction
                    </p>
                </div>
            </div>

            <img src="/images/dropdown-arrow.svg" class="image28x28px" :class="{ 'rotate-180': atackSurface }" alt="">
        </div>

        <div v-if="atackSurface" class="px-6">
            <div class="separatorhorizontal mb-4 mt-8"></div>

            <div class="flex flex-col gap-6">
                <!-- Section Title: Exploit Guard -->
                <h2 class="text-xl font-semibold">Exploit Guard</h2>

                <!-- 1. Exploit Protection Settings -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Exploit Protection Settings :</span>
                        </div>
                        <span class="bold-text-2">
                            &lt;WindowsDefenderExploitGuard&gt; &lt;SystemConfig&gt; &lt;DEP Enable="true"
                            EmulateAtlThunks="false" EnableAtlThunkEmulation="true" /&gt; &lt;ASLR
                            ForceRelocateImages="true" RequireRelocateImages="false" BottomUp="true" HighEntropy="true"
                            /&gt; &lt;CFG Enable="true" /&gt; &lt;SEHOP Enable="true" /&gt; &lt;/SystemConfig&gt;
                            &lt;AppConfig Executable="*"&gt; &lt;DEP Enable="true" /&gt; &lt;ASLR
                            ForceRelocateImages="true" BottomUp="true" HighEntropy="true" /&gt; &lt;CFG Enable="true"
                            /&gt; &lt;SEHOP Enable="true" /&gt; &lt;/AppConfig&gt; &lt;/WindowsDefenderExploitGuard&gt;
                        </span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Paramétrage avancé d’Exploit Protection pour renforcer la sécurité des applications et du
                        système.
                    </div>
                </div>

                <!-- Section Title: Windows Defender Security Center -->
                <h2 class="text-xl font-semibold">Windows Defender Security Center</h2>

                <!-- 2. Disallow Exploit Protection Override -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Disallow Exploit Protection Override :</span>
                        </div>
                        <span class="bold-text-2">Enable</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Local users ne peuvent pas modifier les réglages Exploit Protection dans la console de sécurité.
                    </div>
                </div>

                <!-- Section Title: Microsoft Defender Application Control -->
                <h2 class="text-xl font-semibold">Microsoft Defender Application Control</h2>

                <!-- 3. App locker application control -->
                <div class="flex flex-col border p-4 rounded-lg">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">App locker application control :</span>
                        </div>
                        <span class="bold-text-2">Audit Components, Store Apps, and Smartlocker</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Définit les règles d’audit pour contrôler les applications, y compris les applications du Store
                        et Smartlocker.
                    </div>
                </div>

                <!-- 4. Block users from ignoring SmartScreen warnings -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Block users from ignoring SmartScreen warnings :</span>
                        </div>
                        <span class="bold-text-2">Yes</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Empêche l’utilisateur de passer outre les avertissements SmartScreen lors du lancement
                        d’applications suspectes.
                    </div>
                </div>

                <!-- 5. Turn on Windows SmartScreen -->
                <div class="flex flex-col border p-4 rounded-lg mt-2">
                    <div class="flex justify-between">
                        <div class="flex gap-2 items-center">
                            <div class="dot"></div>
                            <span class="text-gray-700">Turn on Windows SmartScreen :</span>
                        </div>
                        <span class="bold-text-2">Yes</span>
                    </div>
                    <div class="separatorhorizontal my-2"></div>
                    <div class="gray">
                        Active SmartScreen pour protéger l’appareil en vérifiant la réputation des fichiers et des
                        applications.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
