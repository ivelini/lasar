<template>
    <tr >
        <td><p>{{ saller }}</p><span class="badge badge-primary">Обработка: {{ labelTitle }}</span></td>
        <td>
            <a :href="url" target="_blank">
                {{ url }}
            </a>
        </td>
        <td>
            {{ storage }}
        </td>
        <td>
            <div class="list-icons">
                <div class="loading-spinner"></div>
                <a @click="update()" class="list-icons-item text-primary" title="Обновить остатки" style="cursor: pointer">
                    <i class="icon-database-refresh" style="font-size: 1.5rem;padding-left: 20px"></i>
                </a>

                <a @click="download()" class="list-icons-item text-teal" title="Ненайденные остатки" style="cursor: pointer">
                    <i class="icon-file-minus2" style="font-size: 1.5rem;padding-left: 20px"></i>
                </a>
                <a @click="del()" class="list-icons-item text-danger" title="Удалить ссылку" style="cursor: pointer">
                    <i class="icon-trash" style="font-size: 1.5rem;padding-left: 20px"></i>
                </a>
            </div>
        </td>
    </tr>
</template>

<script>
import axios from "axios";

export default {
    emits: ['deleteUrl', 'updatePrice'],
   props: {
       urlId: {
           type: Number,
           required: true
       },
       saller: {
           type: String,
           required: true
       },
       url: {
           type: String,
           required: true
       },
       storage: {
           type: String,
           required: false
       },
       labelTitle: {
           type: String,
           required: true
       },
       labelName: {
           type: String,
           required: true
       },
   },
    methods: {
        del() {
            this.$emit('deleteUrl', this.urlId);
        },
        update() {
            this.$emit('updatePrice', this.urlId);
        },
        download() {
            let fileName = '' + this.labelName + '' + this.storage + '.csv';

            axios.get('http://lasar/api/catalog/import-price-xml/get-file?fileName=' + fileName, { responseType: 'blob' })
                .then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', fileName); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(err => console.log(err))
        }
    }
}
</script>

<style scoped>

</style>
