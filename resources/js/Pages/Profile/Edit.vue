<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    mustVerifyEmail: { type: Boolean, default: false },
    status: { type: String, default: '' },
});

const page = usePage();
const user = page.props.auth.user;

const profileForm = useForm({
    name: user.name,
    email: user.email,
});

const submitProfile = () => {
    profileForm.patch(route('profile.update'));
};

const deleteForm = useForm({
    password: '',
});

const deleteAccount = () => {
    deleteForm.delete(route('profile.destroy'), {
        onFinish: () => deleteForm.reset('password'),
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Perfil" />

        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Perfil</h1>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-medium text-gray-800">
                    Información de perfil
                </h2>

                <form @submit.prevent="submitProfile" class="space-y-4">
                    <div>
                        <InputLabel for="name" label="Nombre" />

                        <TextInput
                            id="name"
                            v-model="profileForm.name"
                            type="text"
                            name="name"
                            autocomplete="name"
                            class="mt-1 block w-full"
                        />

                        <InputError :message="profileForm.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" label="Correo electrónico" />

                        <TextInput
                            id="email"
                            v-model="profileForm.email"
                            type="email"
                            name="email"
                            autocomplete="username"
                            class="mt-1 block w-full"
                        />

                        <InputError :message="profileForm.errors.email" class="mt-2" />
                    </div>

                    <p v-if="props.mustVerifyEmail && !user.email_verified_at" class="text-sm text-yellow-600">
                        Tu correo electrónico no está verificado.
                    </p>

                    <div class="flex items-center gap-4">
                        <button
                            type="submit"
                            class="rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 disabled:opacity-50"
                            :disabled="profileForm.processing"
                        >
                            Guardar
                        </button>

                        <span v-if="profileForm.recentlySuccessful" class="text-sm text-gray-600">
                            Guardado.
                        </span>
                    </div>
                </form>
            </section>

            <section class="rounded-lg bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-medium text-red-600">
                    Eliminar cuenta
                </h2>

                <p class="mb-4 text-sm text-gray-600">
                    Una vez eliminada, la cuenta y todos sus datos se pierden de forma permanente.
                </p>

                <form @submit.prevent="deleteAccount" class="space-y-4">
                    <div>
                        <InputLabel for="password" label="Contraseña" />

                        <TextInput
                            id="password"
                            v-model="deleteForm.password"
                            type="password"
                            name="password"
                            autocomplete="current-password"
                            class="mt-1 block w-full"
                        />

                        <InputError :message="deleteForm.errors.password" class="mt-2" />
                    </div>

                    <button
                        type="submit"
                        class="rounded-md bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-red-500 disabled:opacity-50"
                        :disabled="deleteForm.processing"
                    >
                        Eliminar cuenta
                    </button>
                </form>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
