<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-body">
            <p class="mb-3">
                <b>{{ item.name }}</b>
            </p>
        </div>
    </div>
    <!-- /basic card -->
    <div class="card">
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="item.name"
                        type="text" class="form-control" placeholder="Название страницы">
                </div>
            </div>
            <editor
                v-model="item.content"
                api-key="9ggh2ttq9kb0izc3xigjp7o81p1y94cvephj4ejpi3i6656b"
                :init="{
              menubar: false,
              plugins: 'lists link image emoticons',
              toolbar: 'styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image emoticons'
            }"
            />
        </div>

        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Настройки</h6>
        </div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-12">
                    <select v-model="item.categoryId"
                        class="form-control">
                        <template v-for="item in categories" :key="item.id">
                            <option :value="item.id">
                                {{ item.name }}
                            </option>
                        </template>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="item.title"
                        type="text" class="form-control" placeholder="Title страницы">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="item.description"
                        type="text" class="form-control" placeholder="Description страницы">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="item.label"
                        type="text" class="form-control" placeholder="Ярлык">
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-primary" @click="update">
                    Сохранить
                    <i class="icon-download4 ml-3"></i>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
import Editor from '@tinymce/tinymce-vue'
import axios from "axios";

export default {
    components: {
        Editor
    },
    data() {
        return {
            item: [],
            categories: []
        }
    },
    mounted() {
        this.getEdit();
    },
    methods: {
        getEdit() {
            let id = this.$route.params.page_id;

            axios.get('http://lasar/api/page/' + id)
            .then(response => {
                this.item = response.data.page;
                this.categories = response.data.categories
            })
        },
        update() {
            let id = this.$route.params.page_id;
            axios.post('http://lasar/api/page/' + id, this.item)
            .then(response => {
                console.log(response.data)
            })
        }
    }
}

</script>

<style scoped>

</style>
