<template>
    <!-- Basic card -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Обновление цен</h5>
        </div>

        <div class="card-body">
            <p class="mb-3">
                Загрузите цены в фомате xlsx.
            </p>
        </div>
    </div>
    <!-- /basic card -->
    <div class="card">
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Импорт цен</h6>
        </div>

        <div class="wizard-form steps-basic wizard clearfix">
            <div class="steps clearfix">
                <ul role="tablist">
                    <li role="tab" :class="classStep1">
                        <a href="#">
                            <span class="number">1</span> Загрузка
                        </a>
                    </li>
                    <li role="tab" :class="classStep2">
                        <a href="#">
                            <span class="number">2</span> Сопоставление столбцов
                        </a>
                    </li>
                    <li role="tab" :class="classStep3">
                        <a href="#">
                            <span class="number">3</span> Импорт данных
                        </a>
                    </li>
                </ul>
            </div>
            <div class="content clearfix">
                <fieldset v-show="!isUploadDataFile" class="body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <form-input-file
                                    api-url="http://lasar/api/catalog/import/file"
                                    type-for-update-catalog-table="tires_price"
                                    :application-type="[
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                                        ]"
                                    text-alert="'Принимаются только <b>xls и xlsx</b> файлы'"
                                    @response-input-file="responseApi($event)"
                                />
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset v-show="isUploadDataFile == true && isJobUpdateCreated == false" class="body">
                    <form-select-coll
                        api-url="http://lasar/api/catalog/import/price"
                        :import-keys="importKeys"
                        :keys-header="keysHeader"
                        @response-select-coll="responseApi($event)"
                    />
                </fieldset>

                <fieldset v-show="isJobUpdateCreated" class="body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="alert alert-success border-0 alert-dismissible">
                                    <span class="font-weight-semibold">Осуществляется импорт каталога. Обновите страницу позднее. Примерное время импорта 30-60 минут.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</template>

<script>

import FormInputFile from "@/components/FormInputFile";
import FormSelectColl from "@/components/FormSelectColl";

export default {
    components: {
        FormInputFile,
        FormSelectColl
    },
    data() {
        return {
            keysHeader: {},
            importKeys: importKeyNameList(),
            isUploadDataFile: false,
            isJobUpdateCreated: false,
            uploadStatus: false,
        }
    },
    computed: {
        classStep1() {
            let className = '';
            if (this.isUploadDataFile == false) {
                className = 'current';
            } else {
                className = 'done';
            }
            return className;
        },
        classStep2() {
            let className = '';
            if (this.isJobUpdateCreated == true) {
                className = 'done';
            } else if (this.isUploadDataFile == true) {
                className = 'current';
            } else {
                className = 'disabled'
            }
            return className;
        },
        classStep3() {
            let className = '';
            if (this.isUploadDataFile == true && this.isJobUpdateCreated == true) {
                className = 'current';
            } else {
                className = 'disabled';
            }
            return className;
        },
    },
    mounted() {
        let result = this.getCurrentStatusUploadCatalog()

        result.then(value => {
            console.log(value)
            if (value.upload_status == false || value.upload_status == 'uploaded' || value.upload_status == 'error') {
                this.isUploadDataFile = false;
                this.isJobUpdateCreated = false;
            }

            if (value.upload_status == 'loading') {
                this.isUploadDataFile = true;
                this.isJobUpdateCreated = true;
            }
        })

    },
    methods: {
        getCurrentStatusUploadCatalog() {
            let inputs = [
                { name: 'type', value: 'tires_price' }
            ]

            let result = this.send('http://lasar/api/catalog/import/status', inputs)

            return result;

        },
        async send(api_url, inputs = [], method = 'POST') {
            let oData = new FormData();

            inputs.forEach(input => {
                oData.append(input.name, input.value);
            })

            let response = await fetch(api_url, {
                method: method,
                body: oData
            });

            let result = await response.json();

            return result;
        },
        responseApi(e) {
            if (e.is_upload_data_file !== 'undefined' && e.is_upload_data_file === true) {
                this.isUploadDataFile = true;
                this.keysHeader = e.keysHeader
            }
            if (e.is_job_update_created != 'undefined' && e.is_job_update_created == true) {
                this.isJobUpdateCreated = true;
            }
            console.log(e)
        }
    }
}

function importKeyNameList() {
    return [
        { id: 0, name: 'Номер в учетной системе', col: 'num', isRequired: true },
        { id: 1, name: 'Код производителя', col: 'code', isRequired: true },
        { id: 2, name: 'Склад', col: 'storage', isRequired: true },
        { id: 3, name: 'Остаток', col: 'count', isRequired: true },
        { id: 4, name: 'Цена', col: 'price', isRequired: true },
    ]
}
</script>

<style scoped>

</style>
