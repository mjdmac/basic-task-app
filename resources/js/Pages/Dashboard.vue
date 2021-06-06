<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        <span v-show="route().current('dashboard')">All Tasks</span>
                        <span v-show="route().current('dashboard.show')">{{ data.name }}</span>
                    </h2>
                </div>
                <!-- <div class="space-x-4" align="right">
                    <div class="inline-block">
                        <select @change="reorderTasks($event)" id="order" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option selected disabled>Select</option>
                            <option>All</option>
                            <option v-for="value in options" :key="value" :value="value" :selected="value == getStatus() ">
                                {{value}}
                            </option>
                        </select>
                    </div>
                </div> -->
            </div>
        </template>

        <div class="py-6">
            <div class="p-10 grid grid-cols-1 sm:grid-cols-1 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-5">

                <div v-for="task in tasks" :key="task.id">
                <!--Card -->
                <div class="rounded overflow-hidden shadow-lg">
                <div class="px-6 py-4">
                    <div class="font-bold text-xl mb-2 text-green-500">{{ task.name }}</div>
                    <p class="text-gray-700 text-base">
                     {{ task.description }}
                    </p>

                    <p class="py-4 text-black text-sm">
                        Owner: {{ task.owner.name }}
                    </p>
                </div>

                <div class="px-6 pt-4 pb-2" align="right">

                    <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold mr-2 mb-2">
                        <span :class="changeStatusColor(task.status)">{{ task.status != 'Custom' ? task.status : task.custom_status }}</span>
                    </span>

                    <button @click="showTaskCard(task.id)" align="left" class="inline-block bg-green-500 rounded-full px-3 py-1 text-sm font-semibold text-white mr-2 mb-2">
                        View sub tasks
                    </button>
                </div>

                </div>
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
            AppLayout,
        },

        extends: shared,

        props: ['data'],

        data() {
            return {
                tasks: route().current('dashboard') ? this.data : this.data.subtasks
            }
        },

        methods: {
            showTaskCard: function (id) {
                 this.$inertia.visit('/dashboard/' + id)
            },
        }

    }
</script>
