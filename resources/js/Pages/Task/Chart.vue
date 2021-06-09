<template>
    <app-layout>
        <template #header>
            <div class="grid grid-cols-2">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Completed tasks by users
                    </h2>
                </div>

            </div>
        </template>


        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div align="center" class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
                   <div v-show="data.data <= 0">
                       <span class="text-gray-500">Complete tasks to show chart</span>
                   </div>

                    <!-- Chart -->

                    <div style="height:350px;width: 350px;">
                            <vue3-chart-js
                                :id="pieChart.id"
                                :type="pieChart.type"
                                :data="pieChart.data"
                                @before-render="beforeRenderLogic"
                            ></vue3-chart-js>
                        </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
    import Vue3ChartJs from '@j-t-mcc/vue3-chartjs'
    import AppLayout from '@/Layouts/AppLayout'

export default {
    components: {
        Vue3ChartJs,
        AppLayout
    },

    props: ['data'],

    data() {
        return {
            chartlabels: [],
            chartdata: [],
            // newdata: this.data
        }
    },

    mounted() {

    },

    setup (tasks) {
        // console.log(tasks.data)
        const pieChart = {
        id: 'pieChart',
        type: 'pie',
        data: {
            labels: tasks.data.labels,
            datasets: [
                {
                    labels: 'Completed Task(s)',
                    data: tasks.data.data,
                    backgroundColor: tasks.data.colors,
                    borderColor: 'black',
                    borderWidth: 1
                },
            ]
        }
        }

        const beforeRenderLogic = (event) => {
        //...
        //if(a === b) {
        //  event.preventDefault()
        //}
        }



        return {
        pieChart,
        beforeRenderLogic
        }
    },
}
</script>
