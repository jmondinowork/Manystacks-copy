<script setup>
import { ref, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';

const props = usePage().props;
const stacksPublic = ref(props.stacksPublic);
const activeIndex = ref(0);
const isVisible = ref(true);
let intervalRef = ref(null);

const selectStack = (index) => {
    activeIndex.value = index;
    isVisible.value = !isVisible.value;
    clearInterval(intervalRef.value);
};

const rotateStack = () => {
    activeIndex.value = (activeIndex.value + 1) % stacksPublic.value.length;
    isVisible.value = !isVisible.value;
};

onMounted(() => {
    intervalRef.value = setInterval(rotateStack, 3000);
});

const filtedProducts = (products) => {
    return products.slice(0, 4);
};
</script>

<template>
    <div class="componentcontainer directionvertical">
        <transition @after-leave="isVisible = true">
            <div class="w_container horizontalthenvertical gap12px" v-if="isVisible" :key="activeIndex">
                <div class="stackcontainer" :style="{ 'background-color': stacksPublic[activeIndex].color }">
                    <img class="stackavatar" loading="lazy" alt="" :src="stacksPublic[activeIndex].img" />

                    <div class="w_container vertical alignend justifyspacebetween margin240pxleft height100">
                        <div class="containerslider">
                            <div class="sliderstack">
                                <div v-for="(stack, index) in stacksPublic" :key="index" class="sliderunit"
                                    :class="{ active: index === activeIndex }" @click="selectStack(index)"></div>
                            </div>
                            <div class="text40px left">
                                La stack pour vos
                                <br />
                                <span class="text-span">{{ stacksPublic[activeIndex].stack_name }}</span>
                            </div>
                        </div>
                        <Link class="button white gap8px padding20px clickable" :href="`/mes-stacks/${stacksPublic[activeIndex].slug}`">
                        <div class="text20px">Découvrir</div>
                        <img class="image24x24px" loading="lazy" width="24" height="24"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65531b7efef5c60da90988c2_Vectors-Wrapper.svg" />
                        </Link>
                    </div>

                </div>
                <Link :href="`/mes-stacks/${stacksPublic[activeIndex].slug}`" class="stackproducts">
                    <div v-for="product in filtedProducts(stacksPublic[activeIndex].products)" :key="product.id"
                        class="w_container vertical center gap4px padding12px white round _170x170 clickable">
                        <div class="w_container vertical center gap4px padding12px white round _170x170 clickable"
                            id="product-id">
                            <div class="productimagecontainer"
                                :style="{ 'background-image': 'url(' + product.image_principale + ')' }"></div>
                            <div class="div-block-8">
                                <div class="text14px medium nowrap">{{ product.name }}</div>
                            </div>
                        </div>
                    </div>
                </Link>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.button {
    display: flex;
    background-color: #fff !important;
}
.smooth-fade-enter-active,
.smooth-fade-leave-active {
    transition: opacity 0.4s ease;
}

.smooth-fade-enter-from,
.smooth-fade-leave-to {
    opacity: 0;
}

.smooth-fade-enter-to,
.smooth-fade-leave-from {
    opacity: 1;
}
</style>
