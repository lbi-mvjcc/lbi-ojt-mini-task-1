<script setup lang="ts">
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { computed, ref, onMounted } from 'vue';

interface Props {
    task: any;
}

const props = defineProps<Props>();
const page = usePage();
const user = computed(() => page.props.auth?.user);
const isCustomer = computed(() => user.value?.role === 'customer');
const isDeveloper = computed(() => user.value?.role !== 'customer');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tasks', href: '/tasks' },
    { title: props.task.title, href: `/tasks/${props.task.id}` },
];

const showUploadModal = ref(false);
const uploadType = ref('file');
const commentTextarea = ref<HTMLTextAreaElement | null>(null);
const showEditModal = ref(false);

const commentForm = useForm({
    comment: '',
});

const editForm = useForm({
    title: props.task.title,
    description: props.task.description || '',
    deadline: props.task.deadline || '',
});

const attachmentForm = useForm({
    type: 'file',
    link_url: '',
    file: null as File | null,
});

const autoResizeTextarea = (event: Event) => {
    const textarea = event.target as HTMLTextAreaElement;
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
};

const submitComment = () => {
    commentForm.post(`/tasks/${props.task.id}/comments`, {
        preserveScroll: true,
        onSuccess: () => {
            commentForm.reset();
            if (commentTextarea.value) {
                commentTextarea.value.style.height = 'auto';
            }
        },
    });
};

const submitEdit = () => {
    editForm.put(`/tasks/${props.task.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
};

// Scroll to comment if hash is present
onMounted(() => {
    const hash = window.location.hash;
    if (hash) {
        setTimeout(() => {
            const element = document.querySelector(hash);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Add highlight effect
                element.classList.add('ring-2', 'ring-[#5B21B6]', 'ring-offset-2');
                setTimeout(() => {
                    element.classList.remove('ring-2', 'ring-[#5B21B6]', 'ring-offset-2');
                }, 2000);
            }
        }, 100);
    }
});

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        const maxSize = 50 * 1024 * 1024; // 50MB in bytes
        
        if (file.size > maxSize) {
            alert(`File size (${(file.size / 1024 / 1024).toFixed(2)} MB) exceeds the maximum allowed size of 50 MB. Please choose a smaller file.`);
            target.value = ''; // Clear the file input
            attachmentForm.file = null;
            return;
        }
        
        attachmentForm.file = file;
    }
};

const submitAttachment = () => {
    if (uploadType.value === 'link') {
        if (!attachmentForm.link_url) {
            alert('Please enter a URL');
            return;
        }
        
        router.post(`/tasks/${props.task.id}/attachments`, {
            type: 'link',
            link_url: attachmentForm.link_url,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                attachmentForm.reset();
                showUploadModal.value = false;
                uploadType.value = 'file';
            },
        });
    } else {
        if (!attachmentForm.file) {
            alert('Please select a file');
            return;
        }
        
        const formData = new FormData();
        formData.append('type', uploadType.value);
        formData.append('file', attachmentForm.file);
        
        router.post(`/tasks/${props.task.id}/attachments`, formData, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                attachmentForm.reset();
                attachmentForm.file = null;
                showUploadModal.value = false;
                uploadType.value = 'file';
            },
            onError: (errors) => {
                console.error('Upload error:', errors);
            },
        });
    }
};

const deleteAttachment = (attachmentId: number) => {
    if (confirm('Are you sure you want to delete this attachment?')) {
        router.delete(`/tasks/${props.task.id}/attachments/${attachmentId}`, {
            preserveScroll: true,
        });
    }
};

const getFileIcon = (type: string) => {
    switch (type) {
        case 'link':
            return 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1';
        case 'photo':
            return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
        case 'video':
            return 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
        default:
            return 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
    }
};

const formatFileSize = (bytes: number) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const updateStatus = (status: string) => {
    router.put(`/tasks/${props.task.id}`, { status }, {
        preserveScroll: true,
    });
};

const deleteTask = () => {
    if (confirm('Are you sure you want to delete this task?')) {
        router.delete(`/tasks/${props.task.id}`);
    }
};

const statusColor = computed(() => {
    switch (props.task.status) {
        case 'completed':
            return 'bg-[#22C55E]/10 text-[#22C55E]';
        case 'in_progress':
            return 'bg-[#F97316]/10 text-[#F97316]';
        default:
            return 'bg-[#EF4444]/10 text-[#EF4444]';
    }
});

const categoryColor = computed(() => {
    switch (props.task.category) {
        case 'frontend':
            return 'bg-[#5B21B6]/10 text-[#5B21B6]';
        case 'backend':
            return 'bg-[#06B6D4]/10 text-[#06B6D4]';
        default:
            return 'bg-[#F97316]/10 text-[#F97316]';
    }
});
</script>

<template>
    <Head :title="task.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6 bg-[#F9FAFB]">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-[#1E293B]">{{ task.title }}</h1>
                    <span :class="categoryColor" class="px-3 py-1 rounded-full text-sm font-semibold capitalize">
                        {{ task.category }}
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span :class="statusColor" class="px-4 py-2 rounded-full text-sm font-semibold capitalize">
                        {{ task.status.replace('_', ' ') }}
                    </span>
                    <button
                        v-if="isCustomer"
                        @click="showEditModal = true"
                        class="px-4 py-2 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold text-sm flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Task
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Description -->
                <div class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#1E293B] mb-4">Description</h2>
                    <p class="text-[#1E293B] whitespace-pre-wrap">{{ task.description || 'No description provided' }}</p>
                </div>

                <!-- Work Submission Section (for developers) -->
                <div v-if="!isCustomer" class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-[#1E293B]">Work Submission</h2>
                        <button
                            @click="showUploadModal = true"
                            class="px-4 py-2 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold text-sm flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Upload
                        </button>
                    </div>

                    <!-- Attachments List -->
                    <div v-if="task.attachments && task.attachments.length > 0" class="space-y-3">
                        <div v-for="attachment in task.attachments" :key="attachment.id" class="border border-[#E2E8F0] rounded-lg p-4 hover:border-[#5B21B6]/30 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-10 h-10 bg-[#5B21B6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-[#5B21B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getFileIcon(attachment.type)"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <a 
                                            v-if="attachment.type === 'link'"
                                            :href="attachment.url"
                                            target="_blank"
                                            class="font-semibold text-[#5B21B6] hover:text-[#6D28D9] truncate block"
                                        >
                                            {{ attachment.name }}
                                        </a>
                                        <a 
                                            v-else
                                            :href="`/storage/${attachment.url}`"
                                            target="_blank"
                                            class="font-semibold text-[#1E293B] hover:text-[#5B21B6] truncate block"
                                        >
                                            {{ attachment.name }}
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-[#64748B] capitalize">{{ attachment.type }}</span>
                                            <span v-if="attachment.file_size" class="text-xs text-[#64748B]">• {{ formatFileSize(attachment.file_size) }}</span>
                                            <span class="text-xs text-[#64748B]">• {{ new Date(attachment.created_at).toLocaleDateString() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    v-if="attachment.user_id === user?.id"
                                    @click="deleteAttachment(attachment.id)"
                                    class="ml-2 p-2 text-[#EF4444] hover:bg-[#EF4444]/10 rounded-lg transition-colors"
                                    title="Delete attachment"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div v-else class="border-2 border-dashed border-[#CBD5E1] rounded-lg p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-[#CBD5E1] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <p class="text-[#1E293B]/60 mb-2">No work submitted yet</p>
                        <p class="text-sm text-[#1E293B]/40">Click Upload to add files, photos, videos, or links</p>
                    </div>
                    
                    <!-- Status Update for Developers -->
                    <div class="mt-6">
                        <h3 class="text-lg font-bold text-[#1E293B] mb-3">Update Status</h3>
                        <div class="flex gap-3">
                            <button
                                v-for="status in ['pending', 'in_progress', 'completed']"
                                :key="status"
                                @click="updateStatus(status)"
                                :class="[
                                    'flex-1 px-4 py-2 rounded-lg font-semibold text-sm transition-all',
                                    task.status === status
                                        ? status === 'completed' ? 'bg-[#22C55E] text-white'
                                        : status === 'in_progress' ? 'bg-[#F97316] text-white'
                                        : 'bg-[#EF4444] text-white'
                                        : 'bg-[#F9FAFB] text-[#1E293B] hover:bg-[#E2E8F0]'
                                ]"
                            >
                                {{ status.replace('_', ' ').toUpperCase() }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Submitted Work Section (for customers) -->
                <div v-if="isCustomer" class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#1E293B] mb-4">Submitted Work</h2>
                    
                    <!-- Attachments List for Customers -->
                    <div v-if="task.attachments && task.attachments.length > 0" class="space-y-3">
                        <div v-for="attachment in task.attachments" :key="attachment.id" class="border border-[#E2E8F0] rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#5B21B6]/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-[#5B21B6]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getFileIcon(attachment.type)"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <a 
                                        v-if="attachment.type === 'link'"
                                        :href="attachment.url"
                                        target="_blank"
                                        class="font-semibold text-[#5B21B6] hover:text-[#6D28D9] truncate block"
                                    >
                                        {{ attachment.name }}
                                    </a>
                                    <a 
                                        v-else
                                        :href="`/storage/${attachment.url}`"
                                        target="_blank"
                                        class="font-semibold text-[#1E293B] hover:text-[#5B21B6] truncate block"
                                    >
                                        {{ attachment.name }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-[#64748B] capitalize">{{ attachment.type }}</span>
                                        <span v-if="attachment.file_size" class="text-xs text-[#64748B]">• {{ formatFileSize(attachment.file_size) }}</span>
                                        <span class="text-xs text-[#64748B]">• {{ new Date(attachment.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="border border-[#CBD5E1] rounded-lg p-8 text-center">
                        <svg class="w-12 h-12 mx-auto text-[#CBD5E1] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-[#1E293B]/60">No work submitted yet</p>
                        <p class="text-sm text-[#1E293B]/40 mt-1">The developer will upload their work here when completed</p>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-xl border border-[#CBD5E1] shadow-sm p-6">
                    <h2 class="text-xl font-bold text-[#1E293B] mb-4">Comments</h2>
                    
                    <!-- Comment Form -->
                    <form @submit.prevent="submitComment" class="mb-6">
                        <div class="relative">
                            <textarea
                                ref="commentTextarea"
                                v-model="commentForm.comment"
                                @input="autoResizeTextarea"
                                @keydown.enter.exact.prevent="submitComment"
                                rows="1"
                                placeholder="Write a comment..."
                                class="w-full px-4 py-3 pr-24 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent resize-none text-[#1E293B] bg-white placeholder:text-[#94A3B8] overflow-hidden"
                                required
                            ></textarea>
                            <button
                                type="submit"
                                :disabled="commentForm.processing || !commentForm.comment.trim()"
                                class="absolute right-2 top-1/2 -translate-y-1/2 px-4 py-2 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ commentForm.processing ? 'Posting...' : 'Comment' }}
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div v-if="task.comments && task.comments.length > 0" class="space-y-4">
                        <div v-for="comment in task.comments" :key="comment.id" :id="`comment-${comment.id}`" class="border border-[#E2E8F0] rounded-lg p-4 scroll-mt-24">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#5B21B6]/10 flex items-center justify-center">
                                        <span class="text-[#5B21B6] font-semibold text-sm">
                                            {{ comment.user.name.charAt(0).toUpperCase() }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[#1E293B]">{{ comment.user.name }}</p>
                                        <p class="text-xs text-[#64748B]">{{ new Date(comment.created_at).toLocaleString() }}</p>
                                    </div>
                                </div>
                                <span 
                                    :class="{
                                        'bg-[#06B6D4]/10 text-[#06B6D4]': comment.user.role === 'customer',
                                        'bg-[#5B21B6]/10 text-[#5B21B6]': comment.user.role !== 'customer'
                                    }"
                                    class="px-2 py-1 rounded-full text-xs font-medium capitalize"
                                >
                                    {{ comment.user.role === 'customer' ? 'Customer' : 'Developer' }}
                                </span>
                            </div>
                            <p class="text-[#1E293B] ml-13 break-words">{{ comment.comment }}</p>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-[#94A3B8]">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-sm">No comments yet. Be the first to comment!</p>
                    </div>
                </div>

                <!-- Upload Modal -->
                <div v-if="showUploadModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click="showUploadModal = false">
                    <div @click.stop class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
                        <div class="p-6 border-b border-[#E2E8F0]">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-[#1E293B]">Upload Work</h3>
                                <button @click="showUploadModal = false" class="text-[#64748B] hover:text-[#1E293B]">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <form @submit.prevent="submitAttachment" class="p-6 space-y-4">
                            <!-- Error Messages -->
                            <div v-if="attachmentForm.errors && Object.keys(attachmentForm.errors).length > 0" class="bg-[#EF4444]/10 border border-[#EF4444] rounded-lg p-3">
                                <p v-for="(error, key) in attachmentForm.errors" :key="key" class="text-sm text-[#EF4444]">{{ error }}</p>
                            </div>

                            <!-- Upload Type Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-[#1E293B] mb-3">Upload Type</label>
                                <div class="grid grid-cols-4 gap-3">
                                    <button
                                        type="button"
                                        v-for="type in ['link', 'file', 'photo', 'video']"
                                        :key="type"
                                        @click="uploadType = type"
                                        :class="[
                                            'p-3 border-2 rounded-lg transition-all text-center',
                                            uploadType === type
                                                ? 'border-[#5B21B6] bg-[#5B21B6]/5'
                                                : 'border-[#CBD5E1] hover:border-[#5B21B6]/50'
                                        ]"
                                    >
                                        <svg class="w-6 h-6 mx-auto mb-1" :class="uploadType === type ? 'text-[#5B21B6]' : 'text-[#64748B]'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getFileIcon(type)"></path>
                                        </svg>
                                        <span class="text-xs font-medium capitalize" :class="uploadType === type ? 'text-[#5B21B6]' : 'text-[#64748B]'">{{ type }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Link Input -->
                            <div v-if="uploadType === 'link'">
                                <label class="block text-sm font-semibold text-[#1E293B] mb-2">Link URL</label>
                                <input
                                    v-model="attachmentForm.link_url"
                                    type="url"
                                    placeholder="https://example.com"
                                    required
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white"
                                />
                            </div>

                            <!-- File Input -->
                            <div v-else>
                                <label class="block text-sm font-semibold text-[#1E293B] mb-2">Select File</label>
                                <input
                                    type="file"
                                    @change="handleFileSelect"
                                    :accept="uploadType === 'photo' ? 'image/*' : uploadType === 'video' ? 'video/*' : '*'"
                                    required
                                    class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#5B21B6] file:text-white hover:file:bg-[#6D28D9]"
                                />
                                <div class="flex items-start gap-2 mt-2 p-3 bg-[#FEF3C7] border border-[#F59E0B] rounded-lg">
                                    <svg class="w-5 h-5 text-[#F59E0B] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-semibold text-[#92400E]">File Size Limit</p>
                                        <p class="text-xs text-[#92400E] mt-1">Maximum file size: 50 MB. Files larger than this will be rejected.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex gap-3 pt-4">
                                <button
                                    type="submit"
                                    :disabled="attachmentForm.processing"
                                    class="flex-1 px-6 py-3 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {{ attachmentForm.processing ? 'Uploading...' : 'Upload' }}
                                </button>
                                <button
                                    type="button"
                                    @click="showUploadModal = false"
                                    class="px-6 py-3 border border-[#CBD5E1] text-[#1E293B] rounded-lg hover:bg-[#F9FAFB] transition-colors font-semibold"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="flex gap-3 justify-end">
                <a :href="'/tasks'" class="px-8 py-3 border border-[#CBD5E1] text-[#1E293B] rounded-lg hover:bg-[#F9FAFB] transition-colors font-semibold">
                    Back to Tasks
                </a>
                <button v-if="isCustomer" @click="deleteTask" class="px-8 py-3 bg-[#EF4444] text-white rounded-lg hover:bg-[#DC2626] transition-colors font-semibold">
                    Delete Task
                </button>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="showEditModal = false">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-[#CBD5E1]">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-[#1E293B]">Edit Task</h3>
                        <button @click="showEditModal = false" class="text-[#64748B] hover:text-[#1E293B]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <!-- Task Title -->
                    <div>
                        <label class="block text-sm font-semibold text-[#1E293B] mb-2">
                            Task Title <span class="text-[#EF4444]">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="editForm.title"
                            required
                            class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white placeholder:text-[#94A3B8]"
                            placeholder="Enter task title"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-semibold text-[#1E293B] mb-2">
                            Description (Optional)
                        </label>
                        <textarea
                            v-model="editForm.description"
                            rows="4"
                            placeholder="Enter task description"
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
                            v-model="editForm.deadline"
                            :min="new Date().toISOString().split('T')[0]"
                            class="w-full px-4 py-3 border border-[#CBD5E1] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5B21B6] focus:border-transparent text-[#1E293B] bg-white"
                        />
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="flex-1 px-6 py-3 border border-[#CBD5E1] text-[#1E293B] rounded-lg hover:bg-[#F9FAFB] transition-colors font-semibold"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="flex-1 px-6 py-3 bg-[#5B21B6] text-white rounded-lg hover:bg-[#6D28D9] transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>