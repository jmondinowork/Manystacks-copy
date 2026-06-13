<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { onMounted, onUnmounted, computed } from "vue";

const props = usePage().props;
const data = defineProps({
    show: {
        type: Boolean,
        default: true,
    }
});
const emit = defineEmits(['close']);

const close = () => {
    emit('close');
}
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && data.show) {
        close();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));


const getCategoryLink = computed(() => {
    if (props.categories) {
        const urlSegments = window.location.pathname.split('/');
        const basePath = urlSegments.length > 2 ? `/${urlSegments[1]}/${urlSegments[2]}` : props.souscategorieplucked ? `/${page}/${props.currentcategorie}` : urlSegments[1];

        return props.categories.map(categorie => ({
            categorie, link: `${basePath}/${categorie}`
        }));
    }
    return [];
});
</script>

<template>
    <div class="menuburgercontainer" :class="{ 'show': data.show }">
        <div class="w_container alignright" @click="close">
            <img class="image28x28px clickable" loading="lazy" width="30" height="30"
                src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/6566010d4acd6bf0221f3980_icon.svg" />
        </div>
        <div class="text14px medium">
            Interfaces
        </div>
        <Link :href="route('catalogue')" class="pageselector showalways">
        <img class="image20x20px" loading="lazy" width="20" height="20"
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/656603dbe11bf865ead9de43_shopping-bag.svg" />
        <div class="text14px icontitle showalways">
            Le catalogue
        </div>
        </Link>
        <Link :href="route('mes-commandes')" class="pageselector showalways">
        <img class="image20x20px" loading="lazy" width="20" height="20"
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65660461278a251b44574f5a_package.svg" />
        <div class="text14px icontitle showalways">
            Mes commandes
        </div>
        </Link>
        <div class="separatorhorizontal"></div>
        <div class="text14px medium">
            Favoris
        </div>
        <Link :href="route('mes-stacks')" class="pageselector showalways">
        <img class="image20x20px" loading="lazy" width="20" height="20"
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753db4436aab32b0c2c39_Vectors-Wrapper.svg" />
        <div class="text14px icontitle showalways">
            Mes stacks
        </div>
        </Link>
        <!-- <Link :href="route('mon-catalogue')" class="pageselector showalways">
        <img class="image20x20px" loading="lazy" width="20" height="20"
            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753db4436aab32b0c2c39_Vectors-Wrapper.svg" />
        <div class="text14px icontitle showalways">
            Mon catalogue
        </div>
        </Link> -->
        <div class="separatorhorizontal" v-if="props.categories && props.categories.length"></div>
        <div class="text14px medium" v-if="props.categories && props.categories.length">
            Catégories
        </div>

        <Link :href="link.link" class="pageselector showalways" v-for="link in getCategoryLink" :key="link.categorie">
        <div class="text14px icontitle showalways capitalize">{{ link.categorie }}</div>
        </Link>
    </div>
</template>

<style scoped>
.menuburgercontainer.show {
    left: 0;
}

.menuburgercontainer {
    transition: left 0.5s ease;
    left: -100%;
}
</style>
