<script setup>
import { userInitials } from '@/functions';

const data = defineProps({
    user: {
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
    }
});
</script>

<template>
    <div class="stacklicencecontainer">
        <div class="image_container">
            <img v-if="user.profile_img" :src="user.profile_img" loading="lazy" alt="" class="imageutilisateur">
            <div v-else class="avatarcontainer">
                <div class="text16px medium white">{{
                    userInitials(currentUser.name) }}</div>
            </div>
        </div>
        <div class="w_container vertical gap24px">
            <div class="w_container vertical gap8px">
                <div class="w_container vertical alignleft">
                    <div class="w_container_2">
                        <div class="text20px unbounded">{{ user.name }}</div>
                        <div class="text14px">{{ user.email }}</div>
                    </div>
                    <div v-for="tag in user.tags.slice(0, 2)" :key="tag.id" class="tagblock text12px"
                        :style="{ 'color': `var(--${tag.color})`, 'backgroundColor': `var(--${tag.color}-light)` }">
                        <div class="texttag">
                            {{ tag.name }}
                        </div>
                    </div>
                    <div class="divider_gray_horizontal"></div>
                </div>
                <div lass="equipement" v-if="user.commandeProducts.length">
                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2bca03dab88c100affc70_monitor.png"
                        loading="lazy" alt="" class="image24x24px">
                    <div class="text14px gray">{{ user.commandeProducts.length }} équipement(s)</div>
                </div>

                <div class="d_container_equipe" v-else>
                    <img src="https://cdn.prod.website-files.com/65474f5b3ac46dc4b33db7b7/66f2b9f22f4902e1a5593145_tag.png"
                        loading="lazy" alt="" class="image24x24px">
                    <div class="text14px gray">Aucun appareil</div>
                </div>
            </div>
        </div>
    </div>

</template>

<style scoped>
.avatarcontainer {
    width: 170px;
    height: 170px;
}

.avatarcircle,
.avatarcircle_img,
.circle40px {
    width: 50px;
    height: 50px;
    min-width: 50px;
    min-height: 50px;
}

.avatarcircle .text40px {
    font-size: 20px;
}
</style>
