<template>
    <div data-v-74ca2ebb="" id="filter">
        <div id="filter-tabs" class="tabs">
            <div class="tabs-content">
                <div id="filter-params" class="tab-content active">
                    <div class="form">
                        <div data-v-14a2e3ec="">
                            <filter-input />

                            <div data-v-14a2e3ec="" class="form-item">
                                <div data-v-14a2e3ec="" class="toggle-list ">
                                    <div data-v-14a2e3ec="" class="toggle-list-content">
                                        <div data-v-14a2e3ec="" class="price-fields">
                                            <div data-v-14a2e3ec="" class="inputs"><input data-v-14a2e3ec="" type="text" id="price-min" value="1 700 ₽">
                                                <input data-v-14a2e3ec="" type="text" id="price-max" value="186 060 ₽">
                                            </div>
                                            <div data-v-14a2e3ec="" class="labels">
                                                <label data-v-14a2e3ec="" for="price-min">От</label>
                                                <label data-v-14a2e3ec="" for="price-max">До</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div data-v-14a2e3ec="" class="form-item">
                                <button data-v-14a2e3ec="" type="button" class="submit-form">Найти шины</button>
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
    }

}
</script>

<style scoped>

</style>
