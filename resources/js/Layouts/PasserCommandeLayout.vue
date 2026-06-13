<script setup>
import AppLayout from './AppLayout.vue';

const currentPage = window.location.pathname.split('/').pop();
const data = defineProps({
    full_licence: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <AppLayout>
        <div class="componentcontainer" v-if="data.full_licence">
            <div class="w_container _100 justifycenter">
                <div class="allstepcontainer hide" style="width: unset;">
                    <div class="stepcontainer">
                        <div class="circle green">
                            <div class="text14px medium white">1</div>
                        </div>
                        <div class="text16px stepbar">Licences</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="componentcontainer" :class="currentPage" v-else>
            <div class="w_container _100 justifycenter">
                <div class="allstepcontainer hide">
                    <div class="stepcontainer">
                        <div class="circle">
                            <div class="text14px medium white">1</div>
                        </div>
                        <div class="text16px stepbar">Entreprise</div>
                    </div>
                    <div class="stepcontainer">
                        <div class="circle">
                            <div class="text14px medium white">2</div>
                        </div>
                        <div class="text16px stepbar">Livraison</div>
                    </div>
                    <div class="stepcontainer">
                        <div class="circle">
                            <div class="text14px medium white">3</div>
                        </div>
                        <div class="text16px stepbar">Documents</div>
                    </div>
                    <div class="vectors-wrapper vector vector1"></div>
                    <div class="vectors-wrapper-copy vector"></div>
                </div>
            </div>
        </div>

        <slot></slot>
    </AppLayout>
</template>

<style scoped>
.entreprise .stepcontainer:nth-child(1) .circle,
.livraison .stepcontainer:nth-child(-n+2) .circle,
.signataire .stepcontainer .circle {
    background-color: var(--green);
}

.vector {
    border-top: 2px dashed var(--grey-400);
}

.livraison .vector1,
.signataire .vector {
    border-top: 2px solid var(--green);
}
</style>
