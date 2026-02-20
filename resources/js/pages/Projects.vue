<script setup lang="ts">
import { Head, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed } from 'vue';

interface Props {
    projects: any[];
}

const props = defineProps<Props>();
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isCustomer = computed(() => user.value?.role === 'customer');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Projects', href: '/projects' },
];

const deleteProject = (projectId: number) => {
    if (confirm('Are you sure you want to delete this project? This will permanently delete the project and all its tasks.')) {
        router.delete(`/projects/${projectId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Success message will be shown by the backend
            },
        });
    }
};
</script>

<template>
    <Head title="Projects" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 bg-[#F9FAFB]">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-[#1E293B]">Projects</h1>
                    <p class="text-[#1E293B] mt-1">{{ isCustomer ? 'Projects where you have created tasks' : 'Your assigned projects' }}</p>
                </div>
                <div v-if="isCustomer">
                    <a :href="'/projects/create'" class="flex items-center gap-2 px-6 py-3 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create New Project
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl p-6 border border-[#CBD5E1] shadow-sm">
                    <p class="text-sm text-[#1E293B] font-medium">Your Projects</p>
                    <p class="text-3xl font-bold text-[#5B21B6] mt-2">{{ props.projects.length }}</p>
                </div>
                <div class="bg-white rounded-xl p-6 border border-[#CBD5E1] shadow-sm">
                    <p class="text-sm text-[#1E293B] font-medium">Your Tasks</p>
                    <p class="text-3xl font-bold text-[#F97316] mt-2">
                        {{ isCustomer 
                            ? props.projects.reduce((sum, p) => sum + (p.customer_tasks_count || 0), 0)
                            : props.projects.reduce((sum, p) => sum + (p.assigned_tasks_count || 0), 0) 
                        }}
                    </p>
                </div>
            </div>

            <!-- Projects Table -->
            <div class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#CBD5E1]">
                    <h2 class="text-xl font-bold text-[#1E293B]">Your Projects</h2>
                </div>
                <div v-if="props.projects.length > 0" class="overflow-x-auto">
                    <!-- Customer View -->
                    <table v-if="isCustomer" class="w-full">
                        <thead class="bg-[#F9FAFB] border-b border-[#CBD5E1]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Your Tasks</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD5E1]">
                            <tr v-for="project in props.projects" :key="project.id" @click="router.visit(`/projects/${project.id}`)" class="hover:bg-[#F9FAFB] transition-colors cursor-pointer">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-[#5B21B6]">{{ project.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-[#1E293B]">
                                    {{ project.description || 'No description' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-[#5B21B6]/10 text-[#5B21B6]">
                                        {{ project.customer_tasks_count || 0 }} tasks
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[#1E293B]">{{ new Date(project.created_at).toLocaleDateString() }}</td>
                                <td class="px-6 py-4 whitespace-nowrap" @click.stop>
                                    <button 
                                        @click="deleteProject(project.id)" 
                                        class="p-2 text-[#EF4444] hover:bg-[#EF4444]/10 rounded-lg transition-colors"
                                        title="Delete project"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Developer View -->
                    <table v-else class="w-full">
                        <thead class="bg-[#F9FAFB] border-b border-[#CBD5E1]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Project Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Your Tasks</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-[#1E293B] uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#CBD5E1]">
                            <tr v-for="project in props.projects" :key="project.id" @click="router.visit(`/projects/${project.id}`)" class="hover:bg-[#F9FAFB] transition-colors cursor-pointer">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-[#5B21B6]">{{ project.name }}</div>
                                </td>
                                <td class="px-6 py-4 text-[#1E293B]">
                                    {{ project.description || 'No description' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[#1E293B]">
                                    {{ project.customer?.name || 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-[#5B21B6]/10 text-[#5B21B6]">
                                        {{ project.assigned_tasks_count || 0 }} tasks
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[#1E293B]">{{ new Date(project.created_at).toLocaleDateString() }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-[#CBD5E1] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                    </svg>
                    <p class="text-[#1E293B]/60 mb-4">{{ isCustomer ? 'No projects yet. Create your first task to get started!' : 'No projects assigned to you' }}</p>
                    <a v-if="isCustomer" :href="'/tasks/create'" class="inline-block px-6 py-2 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold">
                        Create Task
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
