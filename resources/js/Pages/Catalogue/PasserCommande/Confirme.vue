<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useStore } from 'vuex';
import { usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const { props } = usePage();
const store = useStore();

onMounted(() => {
    store.dispatch('updatePanierLength', 0)
})
</script>

<template>
    <AppLayout>
        <div class="componentcontainer height100">
            <div class="w_container _100 height100 justifycenter aligncenter vertical gap24px"><img
                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6579da913b466da17412d5ae_check_green_icon.svg"
                    loading="lazy" alt="" class="image28x28px">
                <div class="w_container aligncenter vertical widthmax600px">
                    <div class="text24px unbounded aligncenter">Votre commande est confirmée !</div>
                    <div class="text14px aligncenter">
                        Votre commande a bien été reçue et nous sommes en train de préparer votre contrat de location. Vous
                        serez informé(e) par mail des prochaines étapes.
                        <br> <br>
                        Suivez l'avancement de votre commande à tout moment dans l'onglet "Commandes" ou en cliquant sur
                        le bouton ci-dessous.
                    </div>
                </div>
                <div class="w_container aligncenter vertical widthmax400px">
                    <Link :href="`/mes-commandes/${props.reference_commande}`" class="bigbutton purple">
                    <div class="text14px white">Suivre votre commande</div>
                    </Link>
                </div>
                <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                <div class="text14px medium purple">Retourner au catalogue</div>
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
