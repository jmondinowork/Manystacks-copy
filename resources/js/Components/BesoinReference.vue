<script setup>
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
</script>

<template>
    <a :href="`mailto:${props.env.manystack_contact}?subject=Besoin%20d'une%20référence%20ou%20d'une%20configuration%20précise`"
        class="w_container vertical gap16px white round padding20px aligncenter clickable justify-center" style="min-height: 300px;">
        <div class="w_container vertical _100 gap4px align-center">
            <div class="text16px medium text-center">
                Besoin d'une référence ou d'une configuration précise ?
            </div>
            <div class="w_container aligncenter gap4px">
                <div class="text14px medium purple">Contactez-nous</div>
                <img src="https://assets-global.website-files.com/65474f5b3ac46dc4b33db7b7/6569a3a4071ce91d020f5319_Vectors-Wrapper.svg"
                    loading="lazy" width="16" height="16" alt="" class="image16x16px">
            </div>
        </div>
    </a>
</template>
