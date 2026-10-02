<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, default: false },
    canRegister: { type: Boolean, default: false },
    laravelVersion: { type: String, default: '' },
    phpVersion: { type: String, default: '' },
});
</script>

<template>
    <Head title="Sistema de Auditoría" />

    <div class="flex min-h-screen flex-col items-center justify-center bg-gray-100 px-6">
        <div class="w-full max-w-3xl rounded-lg bg-white p-10 text-center shadow-md">
            <h1 class="text-3xl font-bold text-gray-800">
                Sistema de Auditoría y Cuadrillas Técnicas
            </h1>

            <p class="mt-4 text-gray-600">
                Gestión de tickets, visitas preventivas y correctivas, listas de verificación
                y reportes.
            </p>

            <div class="mt-8 flex items-center justify-center gap-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="rounded-md bg-gray-800 px-6 py-3 text-white hover:bg-gray-700"
                >
                    Iniciar sesión
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="rounded-md border border-gray-300 px-6 py-3 text-gray-700 hover:bg-gray-50"
                >
                    Registrarse
                </Link>
            </div>

            <p class="mt-8 text-xs text-gray-400">
                Laravel {{ laravelVersion }} · PHP {{ phpVersion }}
            </p>
        </div>
    </div>
</template>
