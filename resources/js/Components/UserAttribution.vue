<script setup>
import { Link } from "@inertiajs/vue3";
import { onMounted, onUnmounted, ref, watch } from "vue";
import { slugify } from "@/functions";

const data = defineProps({
    show: {
        type: Boolean,
    },
    title: {
        type: String,
    },
    equipement_available: {
        type: Array,
    }
});
const emit = defineEmits(['closeAttribution', 'updateAttribution']);

const close = () => {
    emit('closeAttribution');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const filtedProducts = ref(data.equipement_available);
watch(() => data.equipement_available, () => {
    filtedProducts.value = data.equipement_available;
    searchQuery.value = '';
});

const searchQuery = ref('');
const searchProducts = () => {
    if (searchQuery.value.trim() === '') {
        filtedProducts.value = data.equipement_available;
        return;
    }
    filtedProducts.value = data.equipement_available.filter(a => {
        const query = searchQuery.value.toLowerCase();
        return a.name.toLowerCase().includes(query) ||
            a.numero_unique.toString().startsWith(searchQuery.value);
    });
}

const updateAttribution = (productId) => {
    let action = 'attribuer';
    emit('updateAttribution', productId, action);
}
</script>

<template>
    <div class="darkmodalbackground" :class="{ 'show': data.show }">
        <div class="modalcontainer" style="max-width: 800px;">
            <div class="componentcontainer">
                <div class="w_container _100 justifyspacebetween aligncenter">
                    <div class="text20px unbounded">Attribuer des {{ data.title }}</div>
                    <img @click="close"
                        src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65b8bbf4430c3c231a740166_Vectors-Wrapper.svg"
                        loading="lazy" width="24" height="24" alt="" class="image28x28px cursor-pointer">
                </div>
            </div>
            <div class="componentcontainer">
                <div class="w_container vertical gap20px">
                    <div class="w_container vertical gap12px">
                        <div class="searchbar" ref="searchBarContainer w-full">
                            <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg"
                                loading="lazy" width="20" height="20" alt="" class="image20x20px">
                            <input type="text" class="text14px grey900 show w-full p-0" ref="searchbar" id="searchbar"
                                v-model="searchQuery" @input="searchProducts" placeholder="Rechercher"
                                autocomplete="off">
                        </div>
                        <div class="separatorhorizontal"></div>
                        <div class="w_container vertical gap12px overflow-auto min-h-96" style="max-height: 60vh;">
                            <div v-if="filtedProducts" class="w_container flex-col gap12px">
                                <div v-for="product in filtedProducts" :key="product.id"
                                    class="w_container _100 justifyspacebetween white round padding12px thenvertical aligncenter">
                                    <div class="w_container aligncenter gap16px">
                                        <div class="w_container _80x80 grey">
                                            <div class="productimagecontainer"
                                                :style="{ 'background-image': 'url(' + product.image_principale + ')' }">
                                            </div>
                                        </div>
                                        <div class="w_container vertical gap2px">
                                            <div class="text16px medium">{{ product.name }}
                                            </div>
                                            <div v-if="product.sous_categorie != 'licences'"
                                                class="w_container vertical nogap">
                                                <div class="text12px gray">{{ product.numero_unique }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tagblock w-fit" v-if="product.sous_categorie != 'licences'"
                                        :class="slugify(product.status)">
                                        <div class="texttag"><span class="text-span-9">•&nbsp;</span> {{
                                            product.status }}
                                        </div>
                                    </div>
                                    <div @click="updateAttribution(product.id)" class="lightbutton">
                                        <div class="text14px medium purple nowrap">Attribuer</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="frame-34">
                                    <div class="text14px aligncenter text-pretty">Vous ne disposez actuellement d'aucun
                                        produit à
                                        attribuer. <br> Vous pouvez enrichir votre sélection en explorant notre
                                        catalogue et en passant commande.</div>
                                    <Link :href="route('catalogue')" class="w_container aligncenter gap4px">
                                    <div class="text14px medium purple">Accédez au catalogue</div>
                                    <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                                        loading="lazy" width="16" height="16" alt="" class="image16x16px">
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w_container vertical gap8px">
                        <div class="bigbutton" @click="close">
                            <div class="text14px">Fermer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.stackimage {
    max-width: 300px;
    padding-bottom: 0;
}
</style>
