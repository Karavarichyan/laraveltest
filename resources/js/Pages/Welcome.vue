<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-200 min-h-screen flex flex-col items-center justify-center">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md dark:bg-gray-800 p-6">
            <h1 class="text-2xl font-bold text-center mb-4">Welcome </h1>
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                Please log in to your account or register            </p>

            <!-- Логин и Регистрация -->
            <div class="flex flex-col space-y-4">
                <Link
                    v-if="canLogin"
                    :href="route('login')"
                    class="w-full py-2 px-4 bg-indigo-600 text-white text-center rounded-lg shadow-md hover:bg-indigo-700 transition"
                >
                    Log in
                </Link>

                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="w-full py-2 px-4 bg-green-500 text-white text-center rounded-lg shadow-md hover:bg-green-600 transition"
                >
                    Register
                </Link>
            </div>

            <!-- Версия Laravel и PHP -->
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Laravel v{{ laravelVersion }} / PHP v{{ phpVersion }}
            </div>
        </div>
    </div>
</template>
