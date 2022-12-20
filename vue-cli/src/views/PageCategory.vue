<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Все категории</h5>
        </div>
    </div>
    <!-- /basic card -->
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <input :value="add.name"
                                   @input="add.name = $event.target.value"
                                type="text"
                                class="form-control"
                                placeholder="Название категории">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <input v-model="add.label"
                                   type="text"
                                   class="form-control"
                                   placeholder="Ярлык категории">
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="btn btn-primary" @click="addCategory">
                            Добавить <i class="icon-download4 ml-3"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Ярлык</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="category in categories" :key="category.id">
                                <td>{{ category.name }}</td>
                                <td>{{ category.label }}</td>
                                <td>
                                    <a v-show="category.id != 1"
                                       @click="delCategory(category.id)"
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
    </div>
</template>
<script>


import axios from "axios";

export default {

    data() {
        return {
            categories: [],
            add: {
                name: null,
                label: null
            }
        }
    },
    mounted() {
        this.index();
    },
    methods: {
        index() {
            axios.get('http://lasar/api/page/category/index')
                .then(response => {
                    this.categories = response.data.categories;
                })
                .catch(err => console.log(err))
        },
        addCategory() {
            let data = {
                name: this.add.name,
                label: this.add.label,
            }
            axios.post('http://lasar/api/page/category/add', data)
            .then(() => {
                this.index();
                this.add.name = null;
                this.add.label = null;
            })
            .catch(err => console.log(err));
        },
        async delCategory(id) {
            let response = await fetch('http://lasar/api/page/category/' + id, {
                method: 'delete'
            })

            if (response.ok) this.index();
        }
    }
}

</script>

<style scoped>

</style>
