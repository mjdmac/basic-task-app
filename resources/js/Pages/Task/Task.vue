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
                         <button @click="openModal()" class="py-2 px-4 bg-green-500 text-white text-sm font-semibold rounded-md shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-opacity-75">
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
                                        <button @click="edit(element)" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">Edit</button>
                                        <button :disabled="disabled" @click="deleteRow(element.id)" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded-lg text-sm font-semibold">Delete</button>
                                    </td>
                                </tr>
                            </template>
                        </draggable>

                    </table>

                    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
                      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                        <div class="fixed inset-0 transition-opacity">
                          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>

                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                          <form>
                          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                               <!-- Name -->
                                <div class="mb-4">
                                    <jet-label for="name" value="Name" />
                                    <jet-input id="name" ref="name" type="text" class="mt-1 block w-full" v-model="form.name"/>
                                    <jet-input-error :message="$page.props.errors.name" class="mt-2" />
                                </div>

                                <!-- Description -->
                                <div class="mb-4">
                                    <jet-label for="description" value="Description" />
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


                          </div>
                          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button :disabled="disabled" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                      v-show="!editMode" @click="save(form)">
                                Save
                              </button>
                            </span>
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                              <button :disabled="disabled" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5"
                                        v-show="editMode" @click="update(form)">
                                Update
                              </button>
                            </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">

                              <button @click="closeModal()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                Cancel
                              </button>
                            </span>
                          </div>
                          </form>

                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import draggable from 'vuedraggable'
    import shared from '@/Scripts/shared'


     export default {
        components: {
            AppLayout,
            JetInput,
            JetInputError,
            JetLabel,
            draggable
        },

        extends: shared,

        props: ['data'],

        data() {
            return {
                form: {
                    name: null,
                    description: null,
                    status: 'Pending',
                    custom_status: null,
                    parent: route().current('tasks.index') ? null : this.data.id
                },
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
            openModal: function () {
                this.isOpen = true;
            },

            resetForm: function () {
                this.form = {
                    name: null,
                    description: null,
                    status: 'Pending',
                    custom_status: null,
                }
            },

            closeModal: function () {
                this.isOpen = false;
                this.editMode=false;
                this.resetForm();
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
                        this.disabledClick(false)
                    }
                })
            },

            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },

            update: function (data) {
                  this.$inertia.visit('/tasks/' + data.id, {
                    method: 'put',
                    data: data,
                    preserveScroll: true,
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
