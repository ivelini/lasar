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
                <a @click="update()" class="list-icons-item text-primary" title="Обновить остатки" style="cursor: pointer">
                    <i class="icon-database-refresh" style="font-size: 1.5rem;padding-left: 20px"></i>
                </a>

                <a href="#" class="list-icons-item text-teal" title="Ненайденные остатки">
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
   },
    methods: {
        del() {
            this.$emit('deleteUrl', this.urlId);
        },
        update() {
            this.$emit('updatePrice', this.urlId);
        }
    }
}
</script>

<style scoped>

</style>
