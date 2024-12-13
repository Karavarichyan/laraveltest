<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';

const blogs = ref([]);
const form = reactive({
    id: null,
    title: '',
    content: '',
    image: null,
});

const fetchBlogs = async () => {
    try {
        const response = await axios.get('/api/blogs');
        blogs.value = response.data || [];
    } catch (error) {
        console.error('Error fetching blogs:', error);
    }
};

const saveBlog = async () => {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('content', form.content);
    if (form.image) formData.append('image', form.image);

    try {
        if (form.id) {
            await axios.post(`/api/blogs/${form.id}?_method=PUT`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        } else {
            await axios.post('/api/blogs', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
        }
        resetForm();
        fetchBlogs();
    } catch (error) {
        console.error('Error saving blog:', error.response?.data || error);
        if (error.response && error.response.data.errors) {
            alert('Ошибка: ' + JSON.stringify(error.response.data.errors));
        }
    }
};

const deleteBlog = async (id) => {
    try {
        await axios.delete(`/api/blogs/${id}`);
        fetchBlogs();
    } catch (error) {
        console.error('Error deleting blog:', error);
    }
};

const editBlog = (blog) => {
    form.id = blog.id;
    form.title = blog.title;
    form.content = blog.content;
};

const resetForm = () => {
    form.id = null;
    form.title = '';
    form.content = '';
    form.image = null;
};

onMounted(() => {
    fetchBlogs();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        You're logged in!
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        {{ form.id ? 'Edit Blog' : 'Create Blog' }}
                    </h3>
                    <form @submit.prevent="saveBlog" class="mt-4 space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input
                                v-model="form.title"
                                type="text"
                                id="title"
                                class="block w-full p-2 border rounded dark:bg-gray-700 dark:text-white"
                                required
                            />
                        </div>
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                            <textarea
                                v-model="form.content"
                                id="content"
                                rows="4"
                                class="block w-full p-2 border rounded dark:bg-gray-700 dark:text-white"
                                required
                            ></textarea>
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                            <input
                                type="file"
                                id="image"
                                @change="(e) => (form.image = e.target.files[0])"
                                class="block w-full p-2 border rounded dark:bg-gray-700 dark:text-white"
                            />
                        </div>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600"
                        >
                            {{ form.id ? 'Update Blog' : 'Create Blog' }}
                        </button>
                        <button
                            v-if="form.id"
                            type="button"
                            @click="resetForm"
                            class="ml-2 px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600"
                        >
                            Cancel
                        </button>
                    </form>
                </div>

                <div class="mt-12">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        Your Blogs
                    </h3>
                    <div v-if="blogs.length > 0" class="mt-4 space-y-4">
                        <div
                            v-for="blog in blogs"
                            :key="blog.id"
                            class="p-4 bg-white shadow rounded dark:bg-gray-700"
                        >
                            <h4 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ blog.title }}
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300">
                                {{ blog.content }}
                            </p>
                            <div v-if="blog.image" class="mt-4">
                                <img
                                    :src="blog.image"
                                    alt="Blog Image"
                                    class="max-w-full h-auto rounded"
                                />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Created by User ID: {{ blog.user_id }}
                            </p>
                            <div class="mt-4 flex space-x-2">
                                <button
                                    class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600"
                                    @click="editBlog(blog)"
                                >
                                    Edit
                                </button>
                                <button
                                    class="px-4 py-2 bg-red-500 text-white rounded shadow hover:bg-red-600"
                                    @click="deleteBlog(blog.id)"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 dark:text-gray-400">
                        No blogs found.
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
