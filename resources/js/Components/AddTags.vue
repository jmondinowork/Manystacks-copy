<script setup>
import { usePage } from '@inertiajs/vue3';
import { vOnClickOutside } from '@vueuse/components';
import { computed, ref } from 'vue';

import CreateTags from '@/Components/CreateTags.vue';

const emit = defineEmits(['updateTagsForm']);
const updateTagsForm = (tags) => {
    emit('updateTagsForm', tags);
}

const { props } = usePage();
const data = defineProps({
    userTags: {
        type: Array,
        default: [],
    }
});

const showCreateTag = ref(false);
const closeCreateTag = () => showCreateTag.value = false;
const openCreateTag = () => showCreateTag.value = true;

const dropdownOpen = ref(false);
const openDropdown = () => dropdownOpen.value = true;
const hideDropDown = () => dropdownOpen.value = false;

const allTags = ref([...props.tags]);
const userTags = ref(data.userTags);
const selectTag = (id, event) => {
    event.stopPropagation();

    userTags.value.push(allTags.value.find(tag => tag.id === id));
    allTags.value = allTags.value.filter(tag => tag.id !== id);

    updateTagsForm(userTags.value);
}
const undoTag = (id) => {
    allTags.value.push(userTags.value.find(tag => tag.id === id));
    userTags.value = userTags.value.filter(tag => tag.id !== id);

    updateTagsForm(userTags.value);
}
const updateCreatedTags = (tags) => {
    userTags.value.push(tags);
    props.tags.push(tags);

    updateTagsForm(userTags.value);
}

const searchTerm = ref('');
const filteredTags = computed(() => allTags.value.filter(
    tag => tag.name.toLowerCase().includes(
        searchTerm.value.toLowerCase()
    ))
);
</script>

<template>
    <div class="w_container vertical" v-bind="$attrs">
        <label for="tags" class="text14px title">Tags</label>
        <div class="w_container gap-2 flex-wrap">
            <div v-for="tag in userTags" :key="tag.id" class="tagblock w-fit cursor-pointer"
                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                <div class="texttag">
                    {{ tag.name }}
                </div>
                <span @click="undoTag(tag.id)">&#x2715;</span>
            </div>
        </div>
        <div class="w_container justify-center items-center">
            <div @click="openDropdown" class="searchbar grey w-full" ref="searchBarContainer">
                <img class="image20x20px" loading="lazy" width="20" height="20"
                    src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/654753ddce41bf0bcdd2e0ce_magnifying-glass.svg" />
                <input type="text" class="text14px grey600 light w-full p-0" ref="searchbar" id="searchbar"
                    placeholder="Rechercher" v-model="searchTerm" autocomplete="off">

                <div @click="openCreateTag" class="bigbutton purple w-fit text-nowrap h-fit p-1 rounded">
                    <div class="text14px white"> Créer un tag </div>
                </div>

                <div v-on-click-outside="hideDropDown" v-if="dropdownOpen" class="select-items p-4">
                    <div class="w_container gap-2 flex-wrap">
                        <div v-for="tag in filteredTags" :key="tag.id" @click="selectTag(tag.id, $event)"
                            class="tagblock w-fit cursor-pointer"
                            :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                            <div class="texttag">
                                {{ tag.name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <CreateTags :show="showCreateTag" @closeTag="closeCreateTag" @updateTags="updateCreatedTags"></CreateTags>
</template>

<style scoped>
.dark .title {
    font-weight: 500;
}
.dark .searchbar,
.dark .searchbar input {
    background-color: var(--grey-50);
    border-radius: 8px;
}
.searchbar {
    border-radius: 0px;
}
.select-items {
    position: absolute;
    top: 40px;
    right: 0;
    display: flex;
    flex-direction: column;
    cursor: default;
    z-index: 99;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    padding: 6px;
}
</style>
