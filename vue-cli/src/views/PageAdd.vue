<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Новая страница</h5>
        </div>

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
                    <input v-model="seo.title"
                        type="text" class="form-control" placeholder="Title страницы">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="seo.description"
                        type="text" class="form-control" placeholder="Description страницы">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <input v-model="seo.label"
                        type="text" class="form-control" placeholder="Ярлык">
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-primary" @click="action">
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
            categories: null,
            item: {
                id: null,
                name: null,
                categoryId: null,
                content: null,
            },
            seo: {
                title: null,
                description: null,
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
        action() {
            if (this.item.id == null) {
                this.addPage();
            } else {
                this.updatePage();
            }
        },
        addPage() {
            let data = {
                ...this.item,
                ...this.seo
            }
            axios.post('http://lasar/api/page/add', data)
                .then(() => {
                    this.$router.push({name: 'pageIndex'});
                })
                .catch(err => console.log(err))
        },
        updatePage() {
            console.log(this.item.id)
        }
    }
}

</script>

<style scoped>

</style>
