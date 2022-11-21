<template>
    <div v-if="key !== 'prices'" data-v-14a2e3ec="" class="input-field form-item">
        <div data-v-6ebc2317="" data-v-14a2e3ec="" data-name="test" class="custom-select" ref="divCustomSelect">
            <div data-v-6ebc2317="" class="custom-select-label">{{ key }}</div>
            <input data-v-6ebc2317=""
                   type="text"
                   readonly="readonly"
                   @click="inputClick($event.target)">
            <div data-v-6ebc2317=""
                 class="custom-select-options">
                <div data-v-6ebc2317=""
                     @click="resetSelect(key)"
                     class="custom-select-item">
                    Любая
                </div>
                <div v-for="item in data[key]"
                     @click="itemClick($event.target, item, key)"
                     data-v-6ebc2317=""
                     class="custom-select-item">
                    {{ item.name ? item.name : item }}
                </div>
            </div>
        </div>
    </div>

    <div v-else-if="key === 'prices'" data-v-14a2e3ec="" class="form-item">
        <div data-v-14a2e3ec="" class="toggle-list ">
            <div data-v-14a2e3ec="" class="toggle-list-content">
                <div data-v-14a2e3ec="" class="price-fields">
                    <div data-v-14a2e3ec="" class="inputs">
                        <input data-v-14a2e3ec="" type="text" id="price-min" :value="data[key].min" @blur="setPrice($event.target, 'min')">
                        <input data-v-14a2e3ec="" type="text" id="price-max" :value="data[key].max" @blur="setPrice($event.target, 'max')">
                    </div>
                    <div data-v-14a2e3ec="" class="labels">
                        <label data-v-14a2e3ec="" for="price-min">От</label>
                        <label data-v-14a2e3ec="" for="price-max">До</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    emits:['setSelectedInput', 'removeKey'],
    props: {
        data: {
            type: Object,
            required: true,
        }
    },
    data() {
        return {
            key: '',
            elInput: {},
            divCustomSelectOptions: {}
        }
    },
    mounted() {
        this.key = Object.keys(this.data)[0];
    },
    methods: {
        inputClick(el) {
            this.elInput = el;
            this.divCustomSelectOptions = el.nextSibling
            this.addRemoveClass(this.divCustomSelectOptions, 'active')
        },
        itemClick(el, item, key) {
            this.elInput.value = item.name ? item.name : item;
            this.addRemoveClass(this.$refs.divCustomSelect, 'selected', 'add')
            this.addRemoveClass(this.divCustomSelectOptions, 'active')

            let arr = []
            arr['key'] = key
            arr['value'] = item.name ? item.id : item
            this.$emit('setSelectedInput', arr )
        },
        resetSelect(key) {
            this.elInput.value = '';
            this.addRemoveClass(this.divCustomSelectOptions, 'active')
            this.addRemoveClass(this.$refs.divCustomSelect, 'selected')
            this.$emit('removeKey', key )
        },
        addRemoveClass(element, className, action = null) {
            let classElement = element.classList

            classElement.contains(className) == true &&  action != 'add'?
                    classElement.remove(className) :
                    classElement.add(className);
        },
        setPrice(el, key) {
            if(el.value != ''){
                let arr = []
                arr['key'] = 'price.' + key
                arr['value'] =  el.value
                this.$emit('setSelectedInput', arr )
            } else {
                this.$emit('removeKey', 'price.' + key )
            }
        }
    }
}
</script>

<style scoped>

</style>
