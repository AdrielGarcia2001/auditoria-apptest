<script setup>
import { computed, ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import NotificationBell from '@/Components/NotificationBell.vue';

const page = usePage();
const open = ref(false);

const auth = computed(() => page.props.auth?.user ?? null);
const flash = computed(() => page.props.flash ?? {});

const isAdmin = computed(() => auth.value?.role === 'admin');
const isSupervisor = computed(() => auth.value?.role === 'supervisor');

const canManage = computed(() => isAdmin.value || isSupervisor.value);

const navItems = computed(() => {
    const items = [
        { name: 'Dashboard', route: 'dashboard' },
        { name: 'Tickets', route: 'tickets.index' },
        { name: 'Visitas', route: 'visits.index' },
        { name: 'Reportes', route: 'reports.index' },
    ];

    if (isAdmin.value) {
        items.push({ name: 'Administración', route: 'admin.index' });
    }

    return items;
});

const isActive = (routeName) => route().current(routeName);

const roleLabels = {
    admin: 'Administrador',
    supervisor: 'Supervisor',
    technician: 'Técnico',
};

const logout = () => router.post(route('logout'));
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="text-lg font-bold text-gray-800">
                            Auditoría
                        </Link>

                        <nav class="hidden items-center gap-1 sm:flex">
                            <Link
                                v-for="item in navItems"
                                :key="item.route"
                                :href="route(item.route)"
                                class="rounded-md px-3 py-2 text-sm font-medium transition"
                                :class="isActive(item.route)
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                            >
                                {{ item.name }}
                            </Link>
                        </nav>
                    </div>

                    <div class="flex items-center gap-4">
                        <NotificationBell />

                        <div v-if="auth" class="flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-medium text-gray-800">{{ auth.name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ roleLabels[auth.role] ?? auth.role }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                                @click="logout"
                            >
                                Cerrar sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <nav class="border-b border-gray-200 bg-white sm:hidden">
            <div class="flex flex-wrap gap-1 px-4 py-2">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    class="rounded-md px-3 py-2 text-sm font-medium"
                    :class="isActive(item.route) ? 'bg-gray-900 text-white' : 'text-gray-600'"
                >
                    {{ item.name }}
                </Link>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div
                v-if="flash.success"
                class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            >
                {{ flash.success }}
            </div>

            <slot />
        </main>
    </div>
</template>
