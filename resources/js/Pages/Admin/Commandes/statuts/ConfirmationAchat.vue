<script setup>
import { usePage, useForm } from '@inertiajs/vue3';

const { props } = usePage();

const form = useForm({
    id: props.commande.id
});

const submit = () => {
    form.processing = true;
    form.post(route('commande.confirmationAchat'), {
        onFinish: () => window.location.reload(true),
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="w_container vertical gap20px">
            <div class="w_container vertical gap8px">
                <button type="submit" :disabled="form.processing"
                    :class="['bigbutton', form.processing ? 'gray' : 'purple']">
                    <div class="text14px white">
                        Confirmer l'achat
                    </div>
                </button>
            </div>
        </div>
    </form>
</template>
