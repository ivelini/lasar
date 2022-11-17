<template>
    <div class="row">
        <div class="col-6">
            <h6>Столбцы на импорт</h6>
            <draggable
                class="ui-sortable list-group"
                :list="selectedList"
                group="keys"
                itemKey="name">
                <template #item="{ element }">
                    <div class="p-2 bg-light border rounded cursor-move ui-sortable-handle">{{ element.name }}</div>
                </template>
            </draggable>
        </div>

        <div class="col-6">
            <h6>Столбцы из файла импорта</h6>
            <draggable
                class="ui-sortable list-group"
                :list="rawList"
                group="keys"
                itemKey="name">
                <template #item="{ element }">
                    <div class="p-2 bg-light border rounded cursor-move ui-sortable-handle">{{ element.name }}</div>
                </template>
            </draggable>
        </div>
    </div>
</template>
<script>
import draggable from "vuedraggable";

export default {
    emits: ['selectedListReload'],
    props: {
        keysHeader: Object
    },
    components: {
        draggable
    },
    data() {
        return {
            selectedList: [],
            rawList: []
        };
    },
    watch: {
        keysHeader: {
            handler(val) {
                for (let key in val) {
                    this.rawList.push({'name': key, 'colIndex': val[key] })
                }
            },
            deep: true
        },
        selectedList: {
            handler(val) {
                this.$emit('selectedListReload', val)
            },
            deep:true
        }
    }
};
</script>
