<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Склады</h5>
        </div>
    </div>
    <!-- /basic card -->
    <div class="card">
        <div class="card-body">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Название</th>
                            <th>Поставщик</th>
                            <th>Уровень</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in storages" :key="item.id">
                            <td><strong>{{ item.name }}</strong></td>
                            <td>{{ item.sallerName }}</td>
                            <td><input v-model="item.value" type="text"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    <div class="btn btn-primary" @click="updateStorage">
                        Сохранить <i class="icon-download4 ml-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import axios from "axios";

export default {
    data() {
        return {
            storages: [],
        }
    },
    mounted() {
        this.index();
    },
    methods: {
        index() {
            axios.get('http://lasar/api/catalog/storages')
            .then(response => {
                this.storages = response.data.storages;
            })
            .catch(err => {
                console.log(err)
            })
        },
        updateStorage() {
            let data = []

            this.storages.forEach(item => {
                data.push({
                    id: item.id,
                    value: item.value
                })
            })

            axios.post('http://lasar/api/catalog/storages', data)
            .then(response => {
                this.index();
                console.log(response.data)
            })
        }
    }
}

</script>

<style scoped>

</style>
