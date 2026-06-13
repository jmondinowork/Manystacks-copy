<script setup>
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { vOnClickOutside } from '@vueuse/components';
import axios from 'axios';

const { props } = usePage();

const entreprisesShow = ref(false);
const toggleEntreprises = () => {
    entreprisesShow.value = !entreprisesShow.value;
}
const closeEntreprises = () => {
    entreprisesShow.value = false;
}

const switchEntreprise = (entrepriseId) => {
    axios.post(route('switchEntreprise', { entreprise_id: entrepriseId }))
        .then((response) => {
            window.location.reload();
        })
        .catch((error) => {
            // console.log(error);
        });
}
</script>

<template>
    <div class="position-relative" v-on-click-outside="closeEntreprises" v-if="props.entreprise.siblings.length">
        <div class="p-4 flex justify-between items-center h-11 bg-white rounded-lg cursor-pointer "
            @click="toggleEntreprises">
            <div class="text14px">
                Votre entreprise :&nbsp;
            </div>
            <div class="text14px bold-text">
                {{ props.entreprise.raison_sociale }}
            </div>
            <img src="/images/dropdown-arrow.svg"
                loading="lazy" alt="" class="image24x24px" />
        </div>

        <div v-if="entreprisesShow"
            class="flex flex-col justify-center items-start gap-3 position-absolute top-14 left-0 shadow-lg bg-white rounded-lg w-full">
            <div @click="switchEntreprise(entreprise.id)" v-for="entreprise in props.entreprise.siblings"
                class="flex p-4 justify-between items-center cursor-pointer w-full rounded-lg hover:bg-purple-50">
                <div class="flex flex-col gap-1">
                    <div class="black text14px">{{ entreprise.raison_sociale }}</div>
                    <div class="text12px gray">SIRET : {{ entreprise.siret }}</div>
                </div>
                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/67335656e78806f61b78dd9c_log-in.png"
                    loading="lazy" alt="" class="image24x24px" />
            </div>
        </div>
    </div>


</template>

<style scoped></style>
