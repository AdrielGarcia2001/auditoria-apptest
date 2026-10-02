<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    users: Array,
    stats: Object,
    tickets: Object,
    statuses: Array,
    types: Array,
});

const roleLabels = {
    admin: 'Administrador',
    supervisor: 'Supervisor',
    technician: 'Técnico',
};

const roleColors = {
    admin: 'bg-purple-100 text-purple-800',
    supervisor: 'bg-blue-100 text-blue-800',
    technician: 'bg-green-100 text-green-800',
};

const statusColors = {
    pending: 'bg-yellow-100 text-yellow-800',
    assigned: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-purple-100 text-purple-800',
    completed: 'bg-green-100 text-green-800',
    verified: 'bg-emerald-100 text-emerald-800',
    reopened: 'bg-orange-100 text-orange-800',
    cancelled: 'bg-gray-100 text-gray-800',
};

const useCountUp = (read) => {
    const display = ref(0);
    let frame = null;

    const animate = (from, to) => {
        if (frame) {
            cancelAnimationFrame(frame);
        }

        const start = performance.now();
        const duration = 500;

        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            display.value = Math.round(from + (to - from) * eased);

            if (progress < 1) {
                frame = requestAnimationFrame(tick);
            }
        };

        frame = requestAnimationFrame(tick);
    };

    onMounted(() => animate(0, read()));
    watch(read, (value, previous) => animate(previous ?? 0, value));

    return display;
};

const totalCount = useCountUp(() => props.stats.total);
const pendingCount = useCountUp(() => props.stats.pending);
const adminCount = useCountUp(() => props.stats.byRole.admin);
const supervisorCount = useCountUp(() => props.stats.byRole.supervisor);
const technicianCount = useCountUp(() => props.stats.byRole.technician);

const userFilter = ref('pending');

const filteredUsers = computed(() => {
    if (userFilter.value === 'pending') {
        return props.users.filter((user) => !user.approved_at);
    }

    return props.users;
});

const selectedRoles = ref({});

watch(
    () => props.users,
    (users) => {
        (users ?? []).forEach((user) => {
            selectedRoles.value[user.id] = selectedRoles.value[user.id] ?? user.role;
        });
    },
    { immediate: true },
);

const approving = ref(null);

const approve = (user) => {
    approving.value = user.id;

    router.patch(
        route('admin.users.approve', user.id),
        { role: selectedRoles.value[user.id] ?? user.role },
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => (approving.value = null),
        },
    );
};

const ticketEdits = ref({});

watch(
    () => props.tickets?.data,
    (tickets) => {
        (tickets ?? []).forEach((ticket) => {
            ticketEdits.value[ticket.id] = {
                type: ticket.type,
                status: ticket.status,
            };
        });
    },
    { immediate: true },
);

const savingTicket = ref(null);

const isDirty = (ticket) => {
    const edit = ticketEdits.value[ticket.id];

    return edit && (edit.type !== ticket.type || edit.status !== ticket.status);
};

const saveTicket = (ticket) => {
    savingTicket.value = ticket.id;

    router.patch(route('admin.tickets.update', ticket.id), ticketEdits.value[ticket.id], {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => (savingTicket.value = null),
    });
};

const statusLabels = computed(() =>
    Object.fromEntries(props.statuses.map((status) => [status.value, status.label])),
);

const formatDate = (date) =>
    new Date(date).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
</script>

<template>
    <AuthenticatedLayout>
        <div>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Administración</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Aprueba registros de usuarios y gestiona el tipo y estado de los tickets.
                </p>
            </div>

            <div
                v-if="pendingCount > 0"
                class="mb-6 flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3"
            >
                <span class="relative flex h-3 w-3">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex h-3 w-3 rounded-full bg-amber-500"></span>
                </span>
                <p class="text-sm font-medium text-amber-800">
                    Hay <strong>{{ pendingCount }}</strong>
                    solicitud(es) de registro esperando aprobación.
                </p>
            </div>

            <!-- Estadísticas -->
            <div class="mb-8 grid grid-cols-2 gap-4 md:grid-cols-5">
                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Registrados</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ totalCount }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow ring-2 ring-amber-400">
                    <p class="text-xs font-medium uppercase tracking-wide text-amber-600">Pendientes</p>
                    <p class="mt-1 text-3xl font-bold text-amber-600">{{ pendingCount }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Administradores</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ adminCount }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Supervisores</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ supervisorCount }}</p>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Técnicos</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ technicianCount }}</p>
                </div>
            </div>

            <!-- Registros de usuarios -->
            <div class="mb-8 overflow-hidden rounded-lg bg-white shadow">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Registros de usuarios</h2>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-sm font-medium transition"
                            :class="userFilter === 'pending'
                                ? 'bg-amber-100 text-amber-800'
                                : 'text-gray-600 hover:bg-gray-100'"
                            @click="userFilter = 'pending'"
                        >
                            Pendientes ({{ pendingCount }})
                        </button>
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-sm font-medium transition"
                            :class="userFilter === 'all'
                                ? 'bg-gray-900 text-white'
                                : 'text-gray-600 hover:bg-gray-100'"
                            @click="userFilter = 'all'"
                        >
                            Todos ({{ totalCount }})
                        </button>
                    </div>
                </div>

                <table v-if="filteredUsers.length > 0" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registrado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ user.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ user.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="roleColors[user.role]"
                                >
                                    {{ roleLabels[user.role] ?? user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    v-if="!user.approved_at"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800"
                                >
                                    Pendiente
                                </span>
                                <span
                                    v-else
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                >
                                    Aprobado
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(user.created_at) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div v-if="!user.approved_at" class="flex items-center gap-2">
                                    <select
                                        v-model="selectedRoles[user.id]"
                                        class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="supervisor">Supervisor</option>
                                        <option value="technician">Técnico</option>
                                    </select>
                                    <button
                                        type="button"
                                        class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-700 disabled:opacity-50"
                                        :disabled="approving === user.id"
                                        @click="approve(user)"
                                    >
                                        {{ approving === user.id ? 'Aprobando…' : 'Aprobar' }}
                                    </button>
                                </div>
                                <span v-else class="text-gray-400">—</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div v-else class="px-6 py-8 text-center text-sm text-gray-500">
                    {{ userFilter === 'pending' ? 'No hay solicitudes pendientes.' : 'No hay usuarios registrados.' }}
                </div>
            </div>

            <!-- Gestión de tickets -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Gestión de tickets</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Modifica el tipo (preventivo o correctivo) y el estado, o consulta los detalles del ticket.
                    </p>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ ticket.ticket_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ ticket.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select
                                    v-model="ticketEdits[ticket.id].type"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="type in types" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select
                                    v-model="ticketEdits[ticket.id].status"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option v-for="status in statuses" :key="status.value" :value="status.value">
                                        {{ status.label }}
                                    </option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    type="button"
                                    class="mr-3 rounded-md bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    :disabled="!isDirty(ticket) || savingTicket === ticket.id"
                                    @click="saveTicket(ticket)"
                                >
                                    {{ savingTicket === ticket.id ? 'Guardando…' : 'Guardar' }}
                                </button>
                                <span
                                    v-if="ticketEdits[ticket.id].status"
                                    class="mr-3 px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="statusColors[ticketEdits[ticket.id].status]"
                                >
                                    {{ statusLabels[ticketEdits[ticket.id].status] }}
                                </span>
                                <Link
                                    :href="route('tickets.show', ticket.id)"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    Ver detalles
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="flex justify-center gap-2 px-6 py-4">
                    <Link
                        v-for="link in tickets.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        class="px-3 py-1 rounded border"
                        :class="link.active
                            ? 'bg-blue-600 text-white'
                            : 'bg-white text-gray-700 hover:bg-gray-50'"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
