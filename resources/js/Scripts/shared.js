export default {
    data() {
        return {
            selected: [],
            selectedItem: false,
            options: ['Pending', 'Complete', 'Cancelled', 'Custom'],
        }
    },
    methods: {

        selectItem: function(item) {
            this.selectedItem = item
        },
        unSelectItem: function (item) {
            this.selectedItem = false
        },

        getStatus: function (){
            let urlParams = new URLSearchParams(window.location.search)
            return urlParams.get('status');
        },

        changeStatusColor: function (status) {
            if (status == 'Pending') {
                this.statusColor = 'text-blue-500'
            } else if (status == 'Complete') {
                this.statusColor = 'text-green-500'
            } else if (status == 'Cancelled') {
                this.statusColor = 'text-red-500'
            } else if (status == 'Custom') {
                this.statusColor = 'text-yellow-500'
            }
            return this.statusColor;
        }
    }
}
