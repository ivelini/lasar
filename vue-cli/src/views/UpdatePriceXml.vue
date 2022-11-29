<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Импорт цен</h5>
        </div>

        <div class="card-body">
            <p class="mb-3">
                Привяжите к каждому поставщику ссылку на файл загрузки остатков в формате XML.
            </p>
        </div>
    </div>
    <!-- /basic card -->
    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Обновление цен от поставщиков</h6>
        </div>
        <div class="card-body">
            <fieldset class="mb-3">
                <form @submit.prevent="addUrl">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div class="row" id="add-saller">
                                <div class="col-lg-2">
                                    <select class="form-control"
                                            :value="form.sallerId"
                                            @change="event => form.sallerId = event.target.value">
                                        <option>Поставщик</option>
                                        <option v-for="item in index.sallersForSelectUrl" :key="item.id"
                                            :value="item.id">
                                            {{ item.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <select class="form-control"
                                            :value="form.labelId"
                                            @change="event => form.labelId = event.target.value">
                                        <option>Обработка прайса</option>
                                        <option v-for="item in index.labels" :key="item.id"
                                                :value="item.id">
                                            {{ item.title }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <input v-model="form.url"
                                        class="form-control"
                                        type="text"
                                        placeholder="Ссылка на XML файл">
                                </div>
                                <div class="col-lg-2">
                                    <input :value="form.storage"
                                           @input="event => form.storage = event.target.value"
                                           class="form-control"
                                           placeholder="Склад"
                                            >
                                </div>
                                <div class="col-lg-2">
                                    <button type="submit" class="btn btn-primary">
                                        Добавить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                </fieldset>
            <fieldset class="mb-3">
                <legend class="text-uppercase font-size-sm font-weight-bold">Выбранные поставщики</legend>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="15%"><b>Поставщик</b></th>
                            <th width="50%"><b>Ссылка на XML файл</b></th>
                            <th width="20%"><b>Склад</b></th>
                            <th width="15%"><b>Опции</b></th>
                        </tr>
                        </thead>
                        <tbody>
                            <row-component v-for="(item, key)  in index.selectedUrls" :key="key"
                             :urlId="item.urlId"
                            :saller="item.sallerName"
                            :url="item.url"
                            :storage="item.storage"
                            :label-title="item.labelTitle"
                            @delete-url="delUrl($event)"
                            @update-price="updPrice($event)"/>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { mapActions } from 'vuex'
import RowComponent from "@/components/updatePriceXml/RowComponent";

export default {
    components: {
        RowComponent
    },
    data() {
        return {
            form: {
                url: '',
                storage: '',
                sallerId: 0,
                labelId: 0,
            },
            index: {
                sallersForSelectUrl: [],
                labels: [],
                selectedUrls: [
                    // {
                    //     sallerId: 0,
                    //     sallerName: 'Бринекс',
                    //     url: 'http://...',
                    //     storage: ''
                    // }
                ]
            },
        }
    },
    mounted() {
        this.getIndex()
    },
    methods: {
        ...mapActions('alert', ['alertGenerate']),
        getIndex() {
            axios.get('http://lasar/api/catalog/import-price-xml')
            .then(response => {
                this.index.sallersForSelectUrl = response.data.sallersForSelectUrl;
                this.index.labels = response.data.labels;
                this.index.selectedUrls = response.data.selectedUrls;
            })
            .catch(err => {
                console.log(err)
            })
        },
        addUrl() {
            if(this.form.sallerId == 0) {
                this.alertGenerate({text: 'Выберете поставщика'})
            } else if(this.form.labelId == 0) {
                this.alertGenerate({text: 'Выберете обработку'})
            } else if(this.form.url.length == 0) {
                this.alertGenerate({text: 'Укажите ссылку на XML файл в формате <b>https://ссылка</b>'})
            } else {
                this.form.method = 'add';
                this.send(this.form)

                this.form.url = null
                this.form.storage = null
            }
        },
        delUrl(id) {
            let data = {
                method: 'del',
                urlId: id
            }
            this.send(data)
        },
        updPrice(urlId) {
            let data = {
                method: 'updPrice',
                urlId: urlId
            }
            this.send(data)
        },
        send(data) {
            axios.post('http://lasar/api/catalog/import-price-xml', data)
                .then((response) => {
                    console.log(response.data)
                    this.getIndex()
                })
                .catch(err => console.log(err))
        }
    }
}
</script>

<style scoped>

</style>
