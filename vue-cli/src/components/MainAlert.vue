<template>
    <div class="card" v-show="alertStatus">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12">
                    <div>
                        <span class="font-weight-semibold"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {

    computed: {
        ...mapGetters('alert', ['alertStatus', 'alertBody'])
    },
    watch: {
        alertStatus(val) {
            if (val == true) this.showAlert();
        }
    },
    methods: {
        ...mapActions('alert', ['alertClear']),
        showAlert() {
            document.querySelector('.col-lg-12 > div').className = 'alert alert-' + this.alertBody.level + ' alert-styled-left alert-dismissible';
            document.querySelector('.col-lg-12 > div > span').innerHTML = this.alertBody.text;

            setTimeout(() => {
                this.alertClear();
            }, 5000)
        }
    }
}
</script>

<style scoped>

</style>
