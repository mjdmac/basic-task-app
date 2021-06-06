<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Restore deleted tasks
                    </h2>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">

                    <table class="table-fixed w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">Task Name</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Deleted at</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="task in data" :key="task.id">
                                <td class="border px-4 py-2">{{ task.name }}</td>
                                <td class="border px-4 py-2">
                                    <span :class="changeStatusColor(task.status)">
                                        {{ task.status != 'Custom' ? task.status : task.custom_status}}
                                    </span>
                                </td>
                                <td class="border px-4 py-2">{{ task.deleted_at_date }}</td>
                                <td class="border px-4 py-2 space-x-1">
                                    <button @click="restoreTask(task.id)" class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">Restore</button>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import shared from '@/Scripts/shared'


     export default {
        components: {
            AppLayout
        },

        extends: shared,

        props: ['data'],

        data() {
            return {
            }
        },

        methods: {
            restoreTask: function (id) {
                this.$inertia.visit('/restore/' + id, {
                    method: 'post',
                    preserveScroll: true,
                    preserveState: true,
                })
            },

        }

    }
</script>
