<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import { useStore } from "vuex";
import { colors } from "@/config";

const store = useStore();
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['closeTag', 'updateTags']);

const close = () => {
    emit('closeTag');
}
const updateTags = (tags) => {
    emit('updateTags', tags);
    close();
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));


const tagTitle = ref('');
const dropdownOpen = ref(false);
const selectedColor = ref(colors[0]);
const selectColor = (color, event) => {
    event.stopPropagation();
    selectedColor.value = color;
    dropdownOpen.value = false;
}
const createTag = async () => {
    if (tagTitle.value.trim() === '') {
        alert('Veuillez entrer un nom pour votre tag.');
        return;
    }

    const tagData = {
        name: tagTitle.value,
        color: selectedColor.value.val,
    };

    try {
        const response = await axios.post('/api/createTag', tagData);
        updateTags(response.data);
        store.dispatch('updateAnnounce', "Le tag a été créé avec succès");
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la création du tag");
    }
};
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Créer un tag
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Titre
                        </div>
                        <div class="textinput">
                            <input class="text14px w-full" v-model="tagTitle" type="text">
                        </div>
                    </div>
                    <div class="w_container vertical">
                        <div class="text14px medium">
                            Couleur
                        </div>
                        <div class="textinput cursor-pointer position-relative" @click="dropdownOpen = !dropdownOpen">
                            <div class="w_container aligncenter gap8px">
                                <div class="div-block-11" :style="{ 'background-color': selectedColor.hex }"></div>
                                <div class="text14px">
                                    {{ selectedColor.title }}
                                </div>
                            </div>

                            <div v-if="dropdownOpen" class="select-items">
                                <div v-for="color in colors" :key="color.title">
                                    <div class="w_container aligncenter gap8px select-item cursor-pointer"
                                        @click="selectColor(color, $event)">
                                        <div class="div-block-11" :style="{ 'background-color': color.hex }">
                                        </div>
                                        <div class="text14px">
                                            {{ color.title }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <img src="/images/dropdown-arrow.svg" style="width: 10px;height: 7px;" loading="lazy" alt=""
                                class="vectors-wrapper-5">
                        </div>
                    </div>
                    <div class="bigbutton purple" @click="createTag">
                        <div class="text14px white">
                            Créer
                        </div>
                    </div>
                    <div class="bigbutton" @click="close">
                        <div class="text14px">
                            Fermer
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.select-item:hover {
    background-color: #F7F8F9;
}

.select-item {
    padding: 12px;
}

.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
}
</style>
