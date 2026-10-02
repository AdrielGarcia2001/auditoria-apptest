<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const props = defineProps({
    status: { type: String, default: '' },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <GuestLayout>
        <Head title="Verificación de correo" />

        <p class="mb-4 text-sm text-gray-600">
            Gracias por registrarte. Por favor confirma tu correo electrónico haciendo clic
            en el enlace que te enviamos. Si no lo recibiste, te enviaremos otro.
        </p>

        <div v-if="verificationLinkSent" class="mt-4 text-sm font-medium text-green-600">
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>

        <form @submit.prevent="submit" class="mt-4 flex items-center justify-end">
            <PrimaryButton :disabled="form.processing">
                Reenviar enlace de verificación
            </PrimaryButton>

            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="ms-4 text-sm text-gray-600 underline hover:text-gray-900"
            >
                Cerrar sesión
            </Link>
        </form>
    </GuestLayout>
</template>
