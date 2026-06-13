<script setup>
import CreateStack from "./CreateStack.vue";
import { usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, computed, ref } from "vue";
import { useStore } from "vuex";

const store = useStore();
const props = usePage().props;

const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    },
    currentProduct: {
        type: Object,
        default: []
    }
});
const emit = defineEmits(['closeAdd']);

const close = () => {
    emit('closeAdd');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const searchTerm = ref('');
const filteredStacks = computed(() => props.mes_stacks.filter(
    stack => stack.stack_name.toLowerCase().includes(
        searchTerm.value.toLowerCase()
    ))
);

const addToStack = async (stack_id) => {
    try {
        const response = await axios.post('/api/addToStack', {
            stack_id: stack_id,
            product_id: data.currentProduct.id,
        });
        props.mes_stacks = response.data

        store.dispatch('updateAnnounce', data.currentProduct.name + 'a bien été ajouté à la stack')
        close();
    } catch (error) {
        store.dispatch('updateErrorAnnounce', data.currentProduct.name + " est déjà présent dans cette stack")
    }
}

const showCreate = ref(false);
const closeCreate = () => showCreate.value = false;
const openCreate = () => showCreate.value = true;
</script>

<template>
    <!-- Add to stack -->
    <div class="darkmodalbackground" :class="{ 'show': data.show && !showCreate }">
        <div class="modalcontainer">
            <div class="componentcontainer justify-between">
                <div class="text20px unbounded">
                    Ajoutez cet article à l’une de vos stacks
                </div>
                <div class="w_container alignright cursor-pointer" @click="close">
                    <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                        src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
                </div>
            </div>

            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical gap12px">
                        <div class="searchbar" ref="searchBarContainer">
                            <img class="image20x20px" loading="lazy" width="20" height="20"
                                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                            <input type="text" class="text14px grey600 light w-full p-0" ref="searchbar" id="searchbar"
                                placeholder="Rechercher" v-model="searchTerm" autocomplete="off">
                        </div>

                        <div class="separatorhorizontal"></div>
                        <div v-if="props.mes_stacks.length" class="w_container vertical gap12px _356px">
                            <!-- Repeat this block for each stack -->
                            <div v-for="stack in filteredStacks" :key="stack.id" @click="addToStack(stack.id)"
                                class="w_container padding12px white clickable">
                                <div class="verticalstackcontainer" :style="{ 'border-color': stack.color }">
                                    <div class="text14px medium">
                                        {{ stack.stack_name }}
                                    </div>
                                    <div class="verticalstackimages">
                                        <!-- Repeat for each stack image -->
                                        <div v-for="product in stack.products" :key="product.id"
                                            class="smallstackverticalimagecontainer">
                                            <div :style="{ 'background-image': 'url(' + product.image_principale + ')' }"
                                                class="productimagecontainer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End of repeated block -->
                        </div>
                        <div v-else class="w_container vertical gap12px _356px">
                            <div class="text14px aligncenter">Votre collection de stacks est vide.</div>
                        </div>
                    </div>
                    <div class="w_container vertical gap8px">
                        <div class="bigbutton purple" @click="openCreate">
                            <div class="text14px white">
                                Créer un nouvelle stack
                            </div>
                        </div>
                        <div class="bigbutton" @click="close">
                            <div class="text14px">
                                Annuler
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create stack -->
    <CreateStack :show="showCreate" @closeCreate="closeCreate"></CreateStack>
</template>

<style scoped>
.searchbar input::placeholder {
    font-weight: 300;
    color: var(--grey-400);
}
</style>
