<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'technician',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrarse" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nombre" label="Nombre" />

                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    name="name"
                    autocomplete="name"
                    class="mt-1 block w-full"
                    autofocus
                />

                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo electrónico" label="Correo electrónico" />

                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    name="email"
                    autocomplete="username"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" label="Contraseña" />

                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar contraseña"
                    label="Confirmar contraseña"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.password_confirmation" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="role" value="Rol" label="Rol" />

                <div class="mt-2 flex gap-6">
                    <label class="flex items-center gap-2">
                        <input
                            id="role"
                            v-model="form.role"
                            type="radio"
                            name="role"
                            value="supervisor"
                            class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <span class="text-sm text-gray-700">Supervisor</span>
                    </label>

                    <label class="flex items-center gap-2">
                        <input
                            v-model="form.role"
                            type="radio"
                            name="role"
                            value="technician"
                            class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <span class="text-sm text-gray-700">Técnico</span>
                    </label>
                </div>

                <InputError :message="form.errors.role" class="mt-2" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900"
                >
                    ¿Ya estás registrado?
                </Link>

                <PrimaryButton class="ms-4" :disabled="form.processing">
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
