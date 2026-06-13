<script setup>
import { colors } from "@/config";

import { usePage, useForm } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { useStore } from "vuex";

const store = useStore();
const props = usePage().props;
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    title: {
        type: String,
        default: 'Créez une nouvelle stack'
    },
    action: {
        type: String,
        default: "Créer"
    },
    stack_name: {
        type: String,
        default: ''
    },
    stack_color: {
        type: String,
        default: '#fc850a'
    },
    stack_id: {
        type: Number,
        default: 0
    }
});
const emit = defineEmits(['closeCreate']);

const close = () => {
    emit('closeCreate');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));


const stackTitle = ref(data.stack_name);
const dropdownOpen = ref(false);

const selectedColor = ref(colors.find(color => color.hex === data.stack_color));
const selectColor = (color, event) => {
    event.stopPropagation();
    selectedColor.value = color;
    dropdownOpen.value = false;
}
const createStack = async () => {
    if (stackTitle.value.trim() === '') {
        alert('Veuillez entrer un nom pour votre stack.');
        return;
    }

    const stackData = {
        id: data.stack_id,
        stack_name: stackTitle.value,
        color: selectedColor.value.hex,
    };

    try {
        const response = await axios.post('/api/createStack', stackData);
        if (data.stack_id) {
            const foundStack = response.data.find(stack => stack.id === data.stack_id);
            Inertia.visit(`/mes-stacks/${foundStack.slug}`);
        }
        else {
            props.mes_stacks = response.data;
            store.dispatch('updateAnnounce', "La stack a été créée avec succès");
            close();
        }
    } catch (error) {
        store.dispatch('updateErrorAnnounce', "Une erreur s'est produite lors de la création de la stack");
    }
};

const deleteStackConfirm = ref(0);
const deleteStack = () => {
    let form = useForm({
        stack_id: data.stack_id
    });
    form.post(route('deleteStack'));
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    {{ data.title }}
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
                            <input class="text14px w-full" v-model="stackTitle" type="text">
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
                    <div class="bigbutton purple" @click="createStack">
                        <div class="text14px white">
                            {{ data.action }}
                        </div>
                    </div>
                    <div class="bigbutton" @click="close">
                        <div class="text14px">
                            Annuler
                        </div>
                    </div>
                    <template v-if="data.action == 'Modifier'">
                        <div class="separatorhorizontal"></div>

                        <template v-if="!deleteStackConfirm">
                            <div @click="deleteStackConfirm = true"
                                class="w_container _100 justifycenter aligncenter gap8px clickable"><img
                                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65942678713325f5f99d078b_trash%20red.svg"
                                    loading="lazy" alt="" class="image20x20px">
                                <div class="text14px red medium">Je souhaite supprimer cette stack</div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="w_container gap12px">
                                <div class="bigbutton" style="background-color: var(--red);" @click="deleteStack">
                                    <div class="text14px" style="color: #fff;">
                                        Supprimer cette stack
                                    </div>
                                </div>
                                <div class="bigbutton" @click="deleteStackConfirm = false">
                                    <div class="text14px">
                                        Annuler
                                    </div>
                                </div>
                            </div>
                        </template>

                    </template>
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
