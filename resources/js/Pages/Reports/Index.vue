<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Reportes</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reporte de Tickets -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Tickets</h2>
                    <p class="text-sm text-gray-500 mb-4">Genera un reporte detallado de todos los tickets con filtros por estado, prioridad y fecha.</p>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('reports.tickets')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                        >
                            Ver Reporte
                        </Link>
                        <Link
                            :href="route('reports.tickets-pdf')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
                        >
                            Exportar PDF
                        </Link>
                    </div>
                </div>

                <!-- Reporte de Visitas -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Reporte de Visitas</h2>
                    <p class="text-sm text-gray-500 mb-4">Genera un reporte detallado de todas las visitas con filtros por estado, tipo y fecha.</p>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('reports.visits')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                        >
                            Ver Reporte
                        </Link>
                        <Link
                            :href="route('reports.visits-pdf')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm"
                        >
                            Exportar PDF
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
