<template>
    <div id="formAction">
        <div class="form-group row" v-show="!activeProgressUpload">
            <label class="col-form-label col-lg-2">Файл</label>
            <div class="col-lg-10">
                <div class="custom-file">
                    <input type="file"
                           id="priceFile"
                           class="custom-file-input"
                           @input="changeInput"
                    >
                    <label class="custom-file-label" for="priceFile">Выбрать</label>
                </div>
            </div>
        </div>
        <div class="row" v-show="activeProgressUpload">
            <div class="col-lg-12" style="text-align: center;">
                <div class="pace-demo" style="margin-bottom: 20px">
                    <div class="theme_xbox">
                        <div class="pace_progress" data-progress-text="60%" data-progress="60"></div>
                        <div class="pace_activity"></div>
                    </div>
                </div>
                <div class="alert alert-primary border-0 alert-dismissible">
                    <span class="font-weight-semibold"><b>Загрузка файла на сервер.</b> Подождите.</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <div class="btn btn-primary" @click="sendForm" style="pointer-events: none; opacity: 0.4">Загрузить <i class="icon-download4 ml-3"></i></div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
    emits: ['responseInputFile'],
    props: {
        apiUrl: {
            type: String,
            required: true
        },
        applicationType: {
            type: Array,
            required: true
        },
        textAlert: {
            type: String,
            required: true
        },
        typeForUpdateCatalogTable: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            file: {},
            isReady: false,
            activeProgressUpload: false
        }
    },
    watch: {
        isReady(val) {
            if (val == true) document.querySelector('#formAction .btn').style = '';
            if (val == false) document.querySelector('#formAction .btn').style = 'pointer-events: none; opacity: 0.4';
        }
    },
    methods: {
        ...mapActions('alert', ['alertGenerate']),
        changeInput(e) {
            let file = e.target.files[0];

            let isTypeOk = false;

            this.applicationType.forEach(type => {
                if (file.type == type) isTypeOk = true
            })

            if (isTypeOk) {
                this.file = e.target.files[0];
                document.querySelector('#formAction .custom-file label').innerHTML = this.file.name;
                this.isReady = true;
            } else {
                this.alertGenerate({ text: this.textAlert } )
            }
        },
        sendForm() {
            let inputs = [
                { name: 'fileData', value: this.file },
                { name: 'type', value: this.typeForUpdateCatalogTable }
            ];

            let result = this.send(this.apiUrl, inputs)

            result.then(value => {
                this.activeProgressUpload = false;
                this.$emit('responseInputFile', value)
            })
            document.querySelector('#formAction .custom-file label').innerHTML = 'Выбрать';
            this.isReady = false;
            this.activeProgressUpload = true;
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
        }
    }
}
</script>

<style scoped>

</style>
