<template>
    <div data-v-74ca2ebb="">
        <breadcrumbs />
        <div data-v-74ca2ebb="" class="container">
            <div data-v-74ca2ebb="" class="row grid-4 gap-20">
                <div data-v-74ca2ebb="" class="col-1">
                    <filter-section
                        :filtered-data="filter.filteredData"
                        @send-filtered-key="getFilteredItems($event)">
                        Найти шины
                    </filter-section>
                </div>
                <div data-v-74ca2ebb="" class="col-3 relative">
                    <div data-v-74ca2ebb="" class="filter-sort">
                        <div data-v-6ebc2317="" data-v-74ca2ebb="" data-name="sort" class="custom-select selected sort without-highlight">
                            <!----> <input data-v-6ebc2317="" type="text" id="select-52" readonly="readonly" class="" value="По умолчанию"> <!---->
                            <div data-v-6ebc2317="" class="custom-select-options">
                                <!---->
                                <div data-v-6ebc2317="" class="custom-select-item active">
                                    По умолчанию
                                </div>
                                <div data-v-6ebc2317="" class="custom-select-item">
                                    Сначала дешевые
                                </div>
                                <div data-v-6ebc2317="" class="custom-select-item">
                                    Сначала дорогие
                                </div>
                                <div data-v-6ebc2317="" class="custom-select-item">
                                    По порядку
                                </div>
                                <div data-v-6ebc2317="" class="custom-select-item">
                                    В обратном порядке
                                </div>
                            </div>
                        </div>
                        <div data-v-314f74b7="" data-v-74ca2ebb="" class="list-type" value="list">
                            <button data-v-314f74b7="" class="list-type-list active">
                                <svg data-v-314f74b7="" width="16" height="12" viewBox="0 0 16 12" xmlns="http://www.w3.org/2000/svg">
                                    <rect data-v-314f74b7="" width="16" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" y="5" width="16" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" y="10" width="16" height="2" rx="1"></rect>
                                </svg>
                            </button>
                            <button data-v-314f74b7="" class="list-type-grid">
                                <svg data-v-314f74b7="" width="18" height="12" viewBox="0 0 18 12" xmlns="http://www.w3.org/2000/svg">
                                    <rect data-v-314f74b7="" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="7" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="14" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" y="5" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="7" y="5" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="14" y="5" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" y="10" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="7" y="10" width="4" height="2" rx="1"></rect>
                                    <rect data-v-314f74b7="" x="14" y="10" width="4" height="2" rx="1"></rect>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div data-v-74ca2ebb="" class="products products-list">
                        <item-horizontal-element v-for="item in filter.filteredItems"
                            :item="item">
                        </item-horizontal-element>
                    </div>
                    <pagination />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import breadcrumbs from "@/views/layouts/Catalog/FilterCategory/breadcrumbs";
import FilterSection from "@/components/FilterSection";
import itemHorizontalElement from "@/views/layouts/Catalog/FilterCategory/itemHorizontalElement";
import pagination from "@/views/layouts/Catalog/FilterCategory/pagination";
import axios from 'axios'

export default {
    components: {
        breadcrumbs,
        FilterSection,
        itemHorizontalElement,
        pagination
    },
    data() {
        return {
            apiUrl: {
                getFilteredData: 'http://lasar/api/catalog/filter-keys',
                getFilteredTires: 'http://lasar/api/catalog/filtered-tires',
            },
            filter: {
                filteredData: {},
                filteredItems: []
            },
            paginator: {
                page: 1,
            },
        }
    },
    mounted() {
        this.getFilteredData()
    },
    methods: {
        async getFilteredData() {
            let response = await fetch(this.apiUrl.getFilteredData);

            if (response.ok) {
                this.filter.filteredData = await response.json();
            }
        },
        getFilteredItems(data) {
            axios.post(this.apiUrl.getFilteredTires, {
                filteredData: data,
                page: this.paginator.page
            })
            .then(response => {
                console.log(data)
                console.log(response.data)
                this.filter.filteredItems = response.data.items
            })
        }
    }
}
</script>

<style scoped>

</style>
