<script setup>
import { userInitials } from '@/functions';
import { computed } from 'vue';

const data = defineProps({
    attribution: {
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

const licencesLength = computed(() => {
    return data.attribution.commande_products.filter(product => product.categorie === "licences").length;
});
const equipementsLength = computed(() => {
    return data.attribution.commande_products.filter(product => product.categorie !== "licences").length;
});
</script>

<template>
    <template v-if="attribution.type == 'Salle'">
        <div class="stacklicencecontainer" v-if="display == 'grid'">
            <div class="image_container big">
                <img v-if="attribution.profile_img" :src="attribution.profile_img" loading="lazy" alt="">
                <div v-else class="avatarcircle big">
                    <div class="text-6xl text-white">{{ userInitials(attribution.name) }}</div>
                </div>
            </div>
            <div class="w_container vertical gap24px">
                <div class="w_container vertical gap8px">
                    <div class="w_container vertical alignleft">
                        <div class="w_container_2 flex justify-between w-full items-center">
                            <div class="text20px unbounded">{{ attribution.name }}</div>
                            <input type="checkbox" :checked="isSelected" v-if="isSelectable"
                                class="checkboxunselected" />
                        </div>
                        <div class="w_container gap12px items-center">
                            <div v-for="tag in attribution.tags.slice(0, 2)" :key="tag.id"
                                class="tagblock w-fit cursor-pointer"
                                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                <div class="texttag">
                                    {{ tag.name }}
                                </div>
                            </div>
                        </div>
                        <div class="divider_gray_horizontal"></div>
                    </div>
                    <div class="flex gap-2" v-if="attribution.commande_products.length">
                        <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2bca03dab88c100affc70_monitor.png"
                            loading="lazy" alt="" class="image22x22px">
                        <div class="text14px gray">{{ attribution.commande_products.length }} équipements</div>
                    </div>
                    <div class="flex gap-2" v-else>
                        <img src="/images/tag-icon.svg" loading="lazy" alt="" class="image20x20px">
                        <div class="text14px gray">Aucun équipement</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stacklicencerow" v-else>
            <div class="image_container small">
                <img v-if="attribution.profile_img" :src="attribution.profile_img" loading="lazy" alt="">
                <div v-else class="avatarcircle big">
                    <div class="text-6xl text-white">{{ userInitials(attribution.name) }}</div>
                </div>
            </div>
            <div class="w_container vertical gap-6">
                <div class="w_container vertical">
                    <div class="d_container-row cols-3">
                        <div class="description_licence_container">
                            <div class="text20px unbounded">{{ attribution.name }}</div>
                        </div>
                        <div class="description_licence_container">
                            <div v-if="attribution.tags.length" v-for="tag in attribution.tags.slice(0, 2)"
                                :key="tag.id" class="tagblock w-fit cursor-pointer"
                                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                <div class="texttag">
                                    {{ tag.name }}
                                </div>
                            </div>
                        </div>
                        <div class="description_licence_container justify-end">
                            <div class="text14px black bold-text">{{ attribution.commande_products.length }}
                            </div>
                            <div class="text14px black">équipement(s)</div>
                            <input type="checkbox" :checked="isSelected" v-if="isSelectable"
                                class="checkboxunselected" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template v-else-if="attribution.type == 'Personne'">
        <div class="stacklicencecontainer position-relative" v-if="display == 'grid'">
            <div class="image_container big">
                <img v-if="attribution.profile_img" :src="attribution.profile_img" loading="lazy" alt=""
                    class="rounded-full">
                <div v-else class="avatarcircle big">
                    <div class="text-6xl text-white">{{ userInitials(attribution.name) }}</div>
                </div>
            </div>
            <div class="w_container vertical gap24px">
                <div class="w_container vertical gap8px">
                    <div class="w_container vertical alignleft">
                        <div class="w_container_2 flex justify-between w-full items-center">
                            <div class="w_container_2">
                                <div class="text20px unbounded">{{ attribution.name }}</div>
                                <div class="text14px">{{ attribution.email }}</div>
                            </div>
                            <input type="checkbox" :checked="isSelected" v-if="isSelectable"
                                class="checkboxunselected" />
                        </div>
                        <div class="w_container gap12px items-center">
                            <div v-for="tag in attribution.tags.slice(0, 2)" :key="tag.id"
                                class="tagblock w-fit cursor-pointer"
                                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                <div class="texttag">
                                    {{ tag.name }}
                                </div>
                            </div>
                        </div>
                        <div class="divider_gray_horizontal"></div>
                    </div>
                    <div class="d_container_equipe-_check">
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center" v-if="equipementsLength">
                                <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2bca03dab88c100affc70_monitor.png"
                                    loading="lazy" alt="" class="image22x22px">
                                <div class="text14px gray">{{ equipementsLength }} équipement(s)</div>
                            </div>
                            <div class="flex gap-2 items-center" v-else>
                                <img src="/images/tag-icon.svg" loading="lazy" alt="" class="image20x20px">
                                <div class="text14px gray">Aucun équipement</div>
                            </div>
                            <div class="flex gap-2 items-center" v-if="licencesLength">
                                <img src="/images/licence-icon.svg" loading="lazy" alt="" class="image20x20px">
                                <div class="text14px gray">{{ licencesLength }} licence(s)</div>
                            </div>
                            <div class="flex gap-2 items-center" v-else>
                                <img src="/images/tag-icon.svg" loading="lazy" alt="" class="image20x20px">
                                <div class="text14px gray">Aucune licence</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img v-if="attribution.tenant_name === 'microsoft'" src="/images/Microsoft_logo.svg" loading="lazy" alt=""
                class="image20x20px position-absolute right-3 bottom-3">
            <img v-else-if="attribution.tenant_name === 'google'" src="/images/google-logo.png" loading="lazy" alt=""
                class="image20x20px position-absolute right-3 bottom-3">
            <img v-else-if="attribution.tenant_name === 'waiting'" src="/images/tenant_waiting.png"
                alt="En attente de synchronisation" class="image24x24px position-absolute right-3 bottom-3">
            <img v-else src="/images/unsynchronise-icon.svg" loading="lazy" alt=""
                class="image20x20px position-absolute right-3 bottom-3">
        </div>

        <div class="stacklicencerow" v-else>
            <div class="image_container small">
                <img v-if="attribution.profile_img" :src="attribution.profile_img" loading="lazy" alt=""
                    class="rounded-full">
                <div v-else class="avatarcircle big">
                    <div class="text-6xl text-white">{{ userInitials(attribution.name) }}</div>
                </div>
            </div>
            <div class="w_container vertical gap-6">
                <div class="w_container vertical">
                    <div class="d_container-row cols-4">
                        <div class="description_licence_container flex-col items-start">
                            <div class="text20px unbounded">{{ attribution.name }}</div>
                            <div class="text12px gray">{{ attribution.email }}</div>
                        </div>
                        <div class="description_licence_container">
                            <div v-for="tag in attribution.tags.slice(0, 2)" :key="tag.id"
                                class="tagblock w-fit cursor-pointer"
                                :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                                <div class="texttag">
                                    {{ tag.name }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="flex gap-2 items-center" v-if="equipementsLength">
                                <div class="text14px black">{{ equipementsLength }} équipement(s)</div>
                            </div>
                            <div class="flex gap-2 items-center" v-else>
                                <div class="text14px black">Aucun équipement</div>
                            </div>
                            <div class="flex gap-2 items-center" v-if="licencesLength">
                                <div class="text14px black">{{ licencesLength }} licence(s)</div>
                            </div>
                            <div class="flex gap-2 items-center" v-else>
                                <div class="text14px black">Aucune licence</div>
                            </div>
                        </div>
                        <div class="description_licence_container justify-end">
                            <img v-if="attribution.tenant_name === 'microsoft'" src="/images/Microsoft_logo.svg"
                                loading="lazy" alt="" class="image20x20px">
                            <img v-else-if="attribution.tenant_name === 'google'" src="/images/google-logo.png"
                                loading="lazy" alt="" class="image20x20px">
                                <img v-else-if="attribution.tenant_name === 'waiting'" src="/images/tenant_waiting.png"
                                alt="En attente de synchronisation" class="image24x24px">
                            <img v-else src="/images/unsynchronise-icon.svg" loading="lazy" alt="" class="image20x20px">
                            <input type="checkbox" :checked="isSelected" v-if="isSelectable"
                                class="checkboxunselected" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</template>
