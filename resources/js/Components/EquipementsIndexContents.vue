<script setup>
import ApplicationLogo from './vendor/ApplicationLogo.vue';

import { slugify, userInitials } from '@/functions';

const data = defineProps({
    equipement: {
        type: Object,
        default: {},
    },
    isSelectable: {
        type: Boolean,
        default: false,
    },
    isSelected: {
        type: Boolean,
        default: false,
    },
    display: {
        type: String,
        default: 'grid',
    },
});
</script>

<template>
    <div class="stacklicencecontainer" v-if="data.display == 'grid'">
        <div class="image_container big">
            <img :src="equipement.image_principale" loading="lazy" alt="" class="contain">
        </div>
        <div class="w_container vertical gap24px">
            <div class="w_container vertical gap8px">
                <div class="w_container vertical alignleft">
                    <div class="w_container_2">
                        <div class="text20px unbounded">{{ equipement.name }}</div>
                    </div>
                    <div class="tagblock" :class="slugify(equipement.status)">
                        <div class="dot"></div>
                        <div>{{ equipement.status }}</div>
                    </div>
                    <div class="divider_gray_horizontal"></div>
                </div>
                <div v-if="equipement.user_attributed_id" class="flex gap-2 items-center">
                    <div class="image_container tiny">
                        <img v-if="equipement.user_attributed.type == 'Salle'"
                            src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2ca6b777543b95c54bcc_Vectors-Wrapper.svg"
                            loading="lazy" width="20" height="20" alt="" class="image24x24px">
                        <img v-else-if="equipement.user_attributed.profile_img" class="rounded-full"
                            :src="equipement.user_attributed.profile_img">
                        <div v-else class="avatarcircle">
                            <div class="text40px white">{{ userInitials(equipement.user_attributed.name) }}
                            </div>
                        </div>
                    </div>
                    <div class="text14px gray">{{ equipement.user_attributed.name }}</div>
                </div>
                <div v-else class="flex gap-2 items-center h-10">
                    <img src="/images/tag-icon.svg" loading="lazy" width="20" height="20" alt="" class="image20x20px">
                    <div class="text14px gray">Non attribué</div>
                </div>
            </div>
            <input type="checkbox" :checked="isSelected" v-if="isSelectable" class="checkboxunselected" />
        </div>
    </div>

    <div class="stacklicencerow" v-else>
        <div class="image_container small">
            <img :src="equipement.image_principale" loading="lazy" alt="" class="contain">
        </div>
        <div class="w_container vertical gap-6">
            <div class="w_container vertical">
                <div class="w_container vertical items-start" style="gap: 4px;">

                </div>
                <div class="d_container-row cols-3">
                    <div class="description_licence_container flex-col items-start">
                        <div class="text20px unbounded">{{ equipement.name }}</div>
                        <div class="text12px gray">{{ equipement.numero_unique }}</div>
                    </div>
                    <div class="description_licence_container justify-end">
                        <div class="tagblock" :class="slugify(equipement.status)">
                            <div class="dot"></div>
                            <div>{{ equipement.status }}</div>
                        </div>
                    </div>
                    <div class="description_licence_container justify-end">
                        <div v-if="equipement.user_attributed_id" class="flex gap-2 items-center">
                            <div class="image_container tiny">
                                <img v-if="equipement.user_attributed.type == 'Salle'"
                                    src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/65ae2ca6b777543b95c54bcc_Vectors-Wrapper.svg"
                                    loading="lazy" width="20" height="20" alt="" class="image24x24px">
                                <img v-else-if="equipement.user_attributed.profile_img" class="rounded-full"
                                    :src="equipement.user_attributed.profile_img">
                                <div v-else class="avatarcircle list">
                                    <div class="text40px white">{{ userInitials(equipement.user_attributed.name) }}
                                    </div>
                                </div>
                            </div>
                            <div class="text14px gray">{{ equipement.user_attributed.name }}</div>
                        </div>
                        <div v-else class="flex gap-2 items-center h-10">
                            <img src="/images/tag-icon.svg" loading="lazy" width="20" height="20" alt=""
                                class="image20x20px">
                            <div class="text14px gray">Non attribué</div>
                        </div>
                        <input type="checkbox" :checked="isSelected" v-if="isSelectable" class="checkboxunselected" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ApplicationLogo v-if="equipement.ref_fournisseur" class="postit"></ApplicationLogo>
</template>

<style scoped>
.postit {
    position: absolute;
    right: 20px;
    bottom: 20px;
    width: 14px;
}

.avatarcircle,
.avatarcircle_img,
.circle40px {
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
}

.avatarcircle .text40px {
    font-size: 14px;
}
</style>
