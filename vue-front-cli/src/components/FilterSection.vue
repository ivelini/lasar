<template>
    <div data-v-74ca2ebb="" id="filter">
        <div id="filter-tabs" class="tabs">
            <div class="tabs-content">
                <div id="filter-params" class="tab-content active">
                    <div class="form">
                        <div data-v-14a2e3ec="">
                            <filter-input v-for="(data, key) in filteredData" :key="key"
                                :data="formatInputData(key,data)"
                                :translite-name-inpyts="transliteNameInputs()"
                                @set-selected-input="setDataFilter($event)"
                                @remove-key="removeKey($event)">
                            </filter-input>

                            <div data-v-14a2e3ec="" class="form-item">
                                <button data-v-14a2e3ec=""
                                        type="button"
                                        class="submit-form"
                                        @click="sendKey">
                                    <slot></slot>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FilterInput from "@/components/FilterInput";

export default {
    components: {
        FilterInput
    },
    props: {
        filteredData: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            selectedData: {}
        }
    },
    methods: {
        formatInputData(key, data) {
            let obj = {};
            obj[key] = data;
            return obj;
        },
        setDataFilter(arr) {
            this.selectedData[arr['key']] = arr['value']
        },
        removeKey(key){
            if (key in this.selectedData) delete this.selectedData[key]
        },
        sendKey() {
            if (Object.keys(this.selectedData).length > 0) {
                this.$router.push({name: 'filteredTires', query: this.selectedData});
            }

        },
        transliteNameInputs() {
            return {
                width: 'Ширина',
                height: 'Профиль',
                diameter: 'Диаметр',
                seasonId: 'Сезон',
                isSpikes: 'Наличие шипов',
                vendorId: 'Производитель',
                prices: 'Цена'
            }
        }
    }
}
</script>

<style scoped>

</style>
