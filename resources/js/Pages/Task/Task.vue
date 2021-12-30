<template>
    <app-layout>
        <template #header>

                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        <span v-show="route().current('tasks.index')">My Tasks</span>
                        <span v-show="route().current('tasks.show')">{{ data.name }}</span>
                    </h2>
                </div>

                <div class="space-x-5" align="right">
                    <div class="inline-block">
                         <button @click="openModal(true)" class="py-2 px-4 bg-green-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                                <span v-show="route().current('tasks.index')">Add Task</span>
                                <span v-show="route().current('tasks.show')">Add Sub Task</span>
                        </button>
                    </div>

                    <div class="inline-block">
                        <select @change="reorderTasks($event)" id="order" class="mt-1 block w-full text-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                            <option selected disabled>Filter by status</option>
                            <option>All</option>
                            <option v-for="value in options" :key="value" :value="value" :selected="value == getStatus() ">
                                {{value}}
                            </option>
                        </select>
                    </div>

                    <div v-show="route().current('tasks.index')" class="inline-block">
                        <button @click="exportTasks('xlsx')" class="py-2 px-4 bg-blue-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                                Export(.xlsx)
                        </button>
                    </div>

                    <div v-show="route().current('tasks.index')" class="inline-block">
                        <button @click="exportTasks('csv')" class="py-2 px-4 bg-blue-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                                Export(.csv)
                        </button>
                    </div>

                    <div v-show="route().current('tasks.index')" class="inline-block">
                        <button @click="exportTasks('json')" class="py-2 px-4 bg-blue-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
                                Export(.json)
                        </button>
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
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <draggable  v-model="tasks" tag="tbody" item-key="order" @end="sortTasks(tasks)" >
                            <template #item="{element}">
                                <tr class="grab">
                                    <td class="border px-4 py-2">{{ element.name }}</td>
                                    <td class="border px-4 py-2">{{ element.description }}</td>
                                    <td class="border px-4 py-2">
                                        <span :class="changeStatusColor(element.status)">
                                            {{ element.status != 'Custom' ? element.status : element.custom_status}}
                                        </span>
                                    </td>
                                    <td class="border px-4 py-2 space-x-1">
                                        <button @click="show(element.id)" class="bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">View</button>
                                        <button @click="edit(element, true)" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">Edit</button>
                                        <button :disabled="disabled" @click="deleteRow(element.id)" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </draggable>

                    </table>
​
                    <dialog-modal :show="isOpen" @close="openModal(false)" >
                        <template #title>
                            Add new task
                        </template>

                        <template #content>
                        <!-- Name -->
                            <div class="mb-4">
                                <jet-label for="name" value="Name" />
                                <jet-input id="name" ref="taskname" type="text" class="mt-1 block w-full" @keyup.enter="save" v-model="form.name"/>

                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <jet-label for="description" value="Description" @keyup.enter="save" />
                                <jet-input id="description" type="text" class="mt-1 block w-full" v-model="form.description" />
                            </div>

                            <!-- Status -->
                            <div class="mb-4" v-show="editMode">
                                <jet-label for="status" value="Status" />
                                <select ref="status" id="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                    v-model="form.status">
                                    <option v-for="value in options" :key="value" :value="value">{{value}}</option>
                                </select>
                            </div>

                            <!-- Custom Status -->
                            <div class="mb-4" v-show="form.status == 'Custom'">
                                <jet-label for="customstatus" value="" />
                                <jet-input id="customstatus" type="text" class="mt-1 block w-full" v-model="form.custom_status" />
                            </div>
                        </template>

                        <template #footer>

                            <jet-secondary-button @click="openModal(false)" >
                                Cancel
                            </jet-secondary-button>

                            <jet-button class="ml-2"
                                    v-show="!editMode" @click="save(form)" :class="{ 'opacity-25': disabled}" :disabled="disabled">
                                Save
                            </jet-button>


                            <jet-button class="ml-2"  :class="{ 'opacity-25': disabled}" :disabled="disabled"
                                        v-show="editMode" @click="update(form)">
                                Update
                            </jet-button>
                        </template>
                    </dialog-modal>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetInput from '@/Jetstream/Input'
    import DialogModal from '@/Jetstream/DialogModal'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from '@/Jetstream/Button'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import draggable from 'vuedraggable'
    import shared from '@/Scripts/shared'


     export default {
        components: {
            AppLayout,
            JetInput,
            JetButton,
            JetSecondaryButton,
            JetInputError,
            JetLabel,
            DialogModal,
            draggable
        },


       extends: shared,

        props: ['data'],

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    description: null,
                    status: 'Pending',
                    custom_status: null,
                    parent: route().current('tasks.index') ? null : this.data.id
                }),
                disabled: null,
                editMode: false,
                isOpen: false,
                tasks: route().current('tasks.index') ? this.data : this.data.subtasks
            }
        },

        methods: {
            disabledClick: function(s){
                this.disabled = s;
            },
            openModal: function (status) {

                if(status == true){
                    this.isOpen = true;
                    this.$refs.taskname.focus();
                }else if(status == false){
                    this.form = {};
                    this.isOpen = false;
                    this.editMode = false;
                }

                return this.isOpen;
            },

            show: function (id) {
                 this.$inertia.visit('/tasks/' + id)

            },

            save: function (data) {
               this.$inertia.visit('/tasks', {
                    method: 'post',
                    data: data,
                    onBefore: () => {
                        this.disabledClick(true)
                    },
                    onSuccess: () => {
                        this.disabledClick(false),
                        this.openModal(false),
                        this.form = {}
                    }
                    // preserveScroll: true,
                    // preserveState: true

                })
            },

            edit: function (data, status) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal(status);
            },

            update: function (data) {
                  this.$inertia.visit('/tasks/' + data.id, {
                    method: 'put',
                    data: data,
                    onBefore: () => {
                        this.disabledClick(true)
                    },
                     onSuccess: () => {
                        this.disabledClick(false),
                        this.openModal(false)
                    },
                    onFinish: () => this.form = {},
                    preserveScroll: true
                })
            },

            deleteRow: function (id) {
                this.$inertia.visit('/tasks/' + id, {
                    method: 'delete',
                    preserveScroll: true,
                    onBefore: () => {
                        this.disabledClick(true)
                    },
                    onSuccess: () => {
                        this.disabledClick(false)
                    }
                })
            },

            exportTasks: function (type) {
                  const url = '/export/tasks?type=' + type;
                  window.location.href = url;
            },

            reorderTasks: function(event){
                let val = event.target.value
                if(route().current('tasks.index')){
                    if(val == 'All'){
                        this.$inertia.get('/tasks')
                    }else{
                        this.$inertia.get('/tasks?status=' + val)
                    }
                }else{
                    if(val == 'All'){
                        this.$inertia.get(window.location.pathname)
                    }else{
                        this.$inertia.get(window.location.pathname +  '?status=' + val)
                    }
                }
            },

            sortTasks: function(data) {

                data.map(function(item, index) {
                    axios.post('/tasks/sort', {
                        item: item.id,
                        order: index
                    })
                })
            },

        }

    }
</script>
<style>
.grab{
    cursor: move;
    cursor: grab;
    cursor: -moz-grab;
    cursor: -webkit-grab;
}

.grab:active {
    cursor: grabbing;
    cursor: -moz-grabbing;
    cursor: -webkit-grabbing;
}
</style>
