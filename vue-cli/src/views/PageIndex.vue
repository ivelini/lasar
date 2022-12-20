<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Все страницы</h5>
        </div>

        <div class="card-body">
            <div class="col-lg-12">
                <div class="text-right">
                    <router-link :to="{ name: 'pageAdd'}" class="nav-link">
                        <div class="btn btn-primary">
                            Новая страница <i class="icon-download4 ml-3"></i>
                        </div>
                    </router-link>

                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Название</th>
                            <th>Ярлык</th>
                            <th>Категория</th>
                            <th>Действие</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in pages" :key="item.id">
                            <td><router-link :to="{ name: 'pageEdit', params: { page_id: item.id }}"
                                        >{{ item.name }}</router-link></td>
                            <td>{{ item.label }}</td>
                            <td>{{ item.categoryName }}</td>
                            <td>
                                <a
                                   @click="delPage(item.id)"
                                   class="list-icons-item text-danger"
                                   title="Удалить ссылку"
                                   style="cursor: pointer;">
                                    <i class="icon-trash" style="font-size: 1.5rem; padding-left: 20px;"></i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /basic card -->

</template>
<script>

import axios from "axios";

export default {
    data() {
        return {
            pages: []
        }
    },
    mounted() {
        this.index();
    },
    methods: {
        index() {
            axios.get('http://lasar/api/page/index')
                .then(response => {
                    this.pages = response.data.pages;
                })
                .catch(err => console.log(err))
        },
        async delPage(id) {
            let response = await fetch('http://lasar/api/page/' + id, {
                method: 'delete'
            })

            if (response.ok) this.index();
        }
    },
}

</script>

<style scoped>

</style>
