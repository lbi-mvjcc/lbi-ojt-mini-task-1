<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ref, onMounted, onUnmounted } from 'vue';

interface Props {
    projects: any[];
    selectedProjectId?: number | string | null;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tasks', href: '/tasks' },
    { title: 'Create Task', href: '/tasks/create' },
];

interface Task {
    id: number;
    title: string;
    description: string;
    category: string;
    deadline?: string;
}

const addedTasks = ref<Task[]>([]);
const currentTaskId = ref(1);

const form = useForm({
    project_id: props.selectedProjectId ? String(props.selectedProjectId) : '',
    title: '',
    description: '',
    category: '',
    deadline: '',
});

// Load tasks from sessionStorage on mount
onMounted(() => {
    const pendingData = sessionStorage.getItem('pendingTasks');
    if (pendingData) {
        const data = JSON.parse(pendingData);
        form.project_id = data.project_id;
        addedTasks.value = data.tasks;
        // Set currentTaskId to be higher than existing IDs
        if (data.tasks.length > 0) {
            currentTaskId.value = Math.max(...data.tasks.map((t: Task) => t.id)) + 1;
        }
    }
    
    // Check if there's a task being edited
    const editingData = sessionStorage.getItem('editingTask');
    if (editingData) {
        const task = JSON.parse(editingData);
        form.title = task.title;
        form.description = task.description;
        form.category = task.category;
        sessionStorage.removeItem('editingTask');
    }
});

const addTask = () => {
    if (!form.project_id || !form.title || !form.category) {
        // Let browser validation handle this
        return;
    }

    // Add task to the beginning of the list (newest first)
    addedTasks.value.unshift({
        id: currentTaskId.value++,
        title: form.title,
        description: form.description,
        category: form.category,
        deadline: form.deadline,
    });

    // Save to sessionStorage
    sessionStorage.setItem('pendingTasks', JSON.stringify({
        project_id: form.project_id,
        tasks: addedTasks.value
    }));

    // Reset form for next task
    form.title = '';
    form.description = '';
    form.category = '';
    form.deadline = '';
};

const removeTask = (id: number) => {
    addedTasks.value = addedTasks.value.filter(task => task.id !== id);
    
    // Update sessionStorage
    if (addedTasks.value.length > 0) {
        sessionStorage.setItem('pendingTasks', JSON.stringify({
            project_id: form.project_id,
            tasks: addedTasks.value
        }));
    } else {
        sessionStorage.removeItem('pendingTasks');
    }
};

const editTask = (id: number) => {
    const task = addedTasks.value.find(t => t.id === id);
    if (task) {
        // Populate form with task data
        form.title = task.title;
        form.description = task.description;
        form.category = task.category;
        
        // Remove the task from the list
        removeTask(id);
        
        // Scroll to form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

const viewAllTasks = () => {
    // Store tasks in session storage and navigate to review page
    sessionStorage.setItem('pendingTasks', JSON.stringify({
        project_id: form.project_id,
        tasks: addedTasks.value
    }));
    router.visit('/tasks/review');
};

const cancelTasks = () => {
    // Clear sessionStorage and go back to tasks page
    sessionStorage.removeItem('pendingTasks');
    router.visit('/tasks');
};

// Searchable dropdown state
const showProjectDropdown = ref(false);
const projectSearch = ref('');
const filteredProjects = ref(props.projects);

const toggleProjectDropdown = () => {
    if (addedTasks.value.length === 0) {
        showProjectDropdown.value = !showProjectDropdown.value;
        if (showProjectDropdown.value) {
            projectSearch.value = '';
            filteredProjects.value = props.projects;
        }
    }
};

const filterProjects = () => {
    const search = projectSearch.value.toLowerCase();
    filteredProjects.value = props.projects.filter(project => 
        project.name.toLowerCase().includes(search)
    );
};

const selectProject = (projectId: number, projectName: string) => {
    form.project_id = String(projectId);
    showProjectDropdown.value = false;
};

const getSelectedProjectName = () => {
    const project = props.projects.find(p => p.id == form.project_id);
    return project?.name || 'Select a project';
};

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.project-dropdown-container')) {
        showProjectDropdown.value = false;
    }
};

onMounted(() => {
    const pendingData = sessionStorage.getItem('pendingTasks');
    if (pendingData) {
        const data = JSON.parse(pendingData);
        form.project_id = data.project_id;
        addedTasks.value = data.tasks;
        // Set currentTaskId to be higher than existing IDs
        if (data.tasks.length > 0) {
            currentTaskId.value = Math.max(...data.tasks.map((t: Task) => t.id)) + 1;
        }
    }
    
    // Check if there's a task being edited
    const editingData = sessionStorage.getItem('editingTask');
    if (editingData) {
        const task = JSON.parse(editingData);
        form.title = task.title;
        form.description = task.description;
        form.category = task.category;
        sessionStorage.removeItem('editingTask');
    }
    
    // Add click outside listener
    document.addEventListener('click', handleClickOutside);
});

// Clean up event listener
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Create Task" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 bg-[#F9FAFB]">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-bold text-[#1E293B]">Create New Tasks</h1>
                <p class="text-[#1E293B] mt-1">Add tasks one by one, then review and submit all at once</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Task Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-8">
                        <form @submit.prevent="addTask" class="space-y-6">
                            <!-- Project Selection -->
                            <div class="relative project-dropdown-container">
                                <label for="project" class="block text-sm font-semibold text-[#1E293B] mb-2">
                                    Project <span class="text-[#EF4444]">*</span>
                                </label>
                                <div 
                                    @click="toggleProjectDropdown"
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white cursor-pointer flex items-center justify-between"
                                    :class="{ 'bg-gray-100 cursor-not-allowed': addedTasks.length > 0 }"
                                >
                                    <span :class="{ 'text-[#94A3B8]': !form.project_id }">{{ getSelectedProjectName() }}</span>
                                    <svg class="w-5 h-5 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                                
                                <!-- Dropdown Menu -->
                                <div v-if="showProjectDropdown" class="absolute z-10 w-full mt-1 bg-white border border-[#CBD5E1] rounded-lg shadow-lg max-h-80 overflow-hidden">
                                    <!-- Search Bar -->
                                    <div class="p-3 border-b border-[#E2E8F0]">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                            <input
                                                v-model="projectSearch"
                                                @input="filterProjects"
                                                type="text"
                                                placeholder="Search projects..."
                                                class="w-full pl-10 pr-4 py-2 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-sm text-[#1E293B] placeholder:text-[#94A3B8]"
                                            />
                                        </div>
                                    </div>
                                    
                                    <!-- Project List -->
                                    <div class="max-h-60 overflow-y-auto">
                                        <div v-if="filteredProjects.length === 0" class="px-4 py-3 text-sm text-[#94A3B8] text-center">
                                            No projects found
                                        </div>
                                        <div
                                            v-for="project in filteredProjects"
                                            :key="project.id"
                                            @click="selectProject(project.id, project.name)"
                                            class="px-4 py-3 hover:bg-[#F9FAFB] cursor-pointer transition-colors"
                                            :class="{ 'bg-[#5B21B6]/10': form.project_id == project.id }"
                                        >
                                            <p class="text-sm font-medium text-[#1E293B]">{{ project.name }}</p>
                                            <p v-if="project.description" class="text-xs text-[#64748B] mt-0.5 truncate">{{ project.description }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <p v-if="addedTasks.length > 0" class="mt-1 text-xs text-[#1E293B]/60">Project locked after adding first task</p>
                            </div>

                            <!-- Task -->
                            <div>
                                <label for="title" class="block text-sm font-semibold text-[#1E293B] mb-2">
                                    Task <span class="text-[#EF4444]">*</span>
                                </label>
                                <input
                                    id="title"
                                    v-model="form.title"
                                    type="text"
                                    required
                                    placeholder="Enter task"
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white placeholder:text-[#94A3B8]"
                                />
                            </div>

                            <!-- Task Description -->
                            <div>
                                <label for="description" class="block text-sm font-semibold text-[#1E293B] mb-2">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="Enter task description (optional)"
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent resize-none text-[#1E293B] bg-white placeholder:text-[#94A3B8]"
                                ></textarea>
                            </div>

                            <!-- Deadline -->
                            <div>
                                <label class="block text-sm font-semibold text-[#1E293B] mb-2">
                                    Deadline (Optional)
                                </label>
                                <input
                                    type="date"
                                    v-model="form.deadline"
                                    :min="new Date().toISOString().split('T')[0]"
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white"
                                />
                            </div>

                            <!-- Category Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-[#1E293B] mb-3">
                                    Category <span class="text-[#EF4444]">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-4">
                                    <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="form.category === 'frontend' ? 'border-[#5B21B6] bg-[#5B21B6]/5' : 'border-[#CBD5E1] hover:border-[#5B21B6]/50'">
                                        <input
                                            type="radio"
                                            v-model="form.category"
                                            value="frontend"
                                            class="sr-only"
                                            required
                                        />
                                        <div class="text-center">
                                            <div class="w-12 h-12 mx-auto mb-2 rounded-lg bg-[#5B21B6]/10 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-[#5B21B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-[#1E293B]">Frontend</span>
                                        </div>
                                    </label>

                                    <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="form.category === 'backend' ? 'border-[#06B6D4] bg-[#06B6D4]/5' : 'border-[#CBD5E1] hover:border-[#06B6D4]/50'">
                                        <input
                                            type="radio"
                                            v-model="form.category"
                                            value="backend"
                                            class="sr-only"
                                            required
                                        />
                                        <div class="text-center">
                                            <div class="w-12 h-12 mx-auto mb-2 rounded-lg bg-[#06B6D4]/10 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-[#06B6D4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-[#1E293B]">Backend</span>
                                        </div>
                                    </label>

                                    <label class="relative flex items-center justify-center p-4 border-2 rounded-lg cursor-pointer transition-all"
                                        :class="form.category === 'server' ? 'border-[#F97316] bg-[#F97316]/5' : 'border-[#CBD5E1] hover:border-[#F97316]/50'">
                                        <input
                                            type="radio"
                                            v-model="form.category"
                                            value="server"
                                            class="sr-only"
                                            required
                                        />
                                        <div class="text-center">
                                            <div class="w-12 h-12 mx-auto mb-2 rounded-lg bg-[#F97316]/10 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-[#F97316]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-[#1E293B]">Server</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Add Task Button -->
                            <button
                                type="submit"
                                class="w-full px-6 py-3 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold"
                            >
                                + Add Task
                            </button>

                            <!-- Action Buttons -->
                            <div class="flex gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="cancelTasks"
                                    class="flex-1 px-6 py-3 border border-[#CBD5E1] text-[#1E293B] rounded-lg hover:bg-[#F9FAFB] transition-colors font-semibold text-center"
                                >
                                    Cancel
                                </button>
                                <button
                                    v-if="addedTasks.length > 0"
                                    type="button"
                                    @click="viewAllTasks"
                                    class="flex-1 px-6 py-3 bg-[#1E293B] text-white rounded-lg hover:bg-[#334155] transition-colors font-semibold"
                                >
                                    Create Tasks
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Added Tasks List -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-6 sticky top-6">
                        <h2 class="text-lg font-bold text-[#1E293B] mb-4">Added Tasks ({{ addedTasks.length }})</h2>
                        
                        <div v-if="addedTasks.length === 0" class="text-center py-8 text-[#94A3B8]">
                            <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm">No tasks added yet</p>
                        </div>

                        <div v-else class="space-y-2 mb-4 max-h-96 overflow-y-auto">
                            <div v-for="task in addedTasks" :key="task.id" 
                                class="flex items-start justify-between p-3 bg-[#F9FAFB] rounded-lg border border-[#E2E8F0] hover:border-[#5B21B6]/30 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[#1E293B] truncate">{{ task.title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded-full"
                                            :class="{
                                                'bg-[#5B21B6]/10 text-[#5B21B6]': task.category === 'frontend',
                                                'bg-[#06B6D4]/10 text-[#06B6D4]': task.category === 'backend',
                                                'bg-[#F97316]/10 text-[#F97316]': task.category === 'server'
                                            }">
                                            {{ task.category }}
                                        </span>
                                        <span v-if="task.deadline" class="text-xs text-[#64748B]">
                                            📅 {{ new Date(task.deadline).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 ml-2">
                                    <button
                                        @click="editTask(task.id)"
                                        class="text-[#5B21B6] hover:text-[#6D28D9] transition-colors"
                                        title="Edit task"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="removeTask(task.id)"
                                        class="text-[#EF4444] hover:text-[#DC2626] transition-colors"
                                        title="Remove task"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            v-if="addedTasks.length > 0"
                            @click="viewAllTasks"
                            class="w-full px-6 py-3 bg-[#1E293B] text-white rounded-lg hover:bg-[#334155] transition-colors font-semibold"
                        >
                            View All Tasks →
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
