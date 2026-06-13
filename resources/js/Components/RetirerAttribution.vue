<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentProduct: {
        type: Object,
        default: []
    },
    currentUser: {
        type: Object,
        default: []
    }
});
const emit = defineEmits(['closeRetirer', 'updateAttribution']);

const close = () => {
    emit('closeRetirer');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const title = ref('');
watch(() => data.currentProduct, () => {
    title.value = data.currentProduct.categorie === "licences" ? 'la licence' : "l'équipement";
});

const updateAttribution = () => {
    let action = 'retirer';
    emit('updateAttribution', data.currentProduct.id, action);
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <!-- <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Retirer {{ title }}
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div> -->

            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="text16px">
                        {{ currentProduct.name }} ne sera plus attribué à {{ currentUser.name }}
                    </div>
                    <div class="w_container gap8px">
                        <div @click="updateAttribution" class="bigbutton purple">
                            <div class="text14px white">Confirmer</div>
                        </div>

                        <div class="bigbutton" @click="close">
                            <div class="text14px">Annuler</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
