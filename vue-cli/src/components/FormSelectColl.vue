<template>
    <div class="row">
        <div class="col-lg-4">
            <div class="form-group">
                <div class="col-12">
                    <h6>Порядок столбцов для выгрузки</h6>
                    <div id="columns-import" class="ui-sortable list-group">
                        <div v-for="importKey in importKeys" :key="importKey.id"
                             :data-column="importKey.col"
                             class="p-2 bg-light border rounded ui-sortable-handle"
                             :style="{ color: importKey.isRequired == true ? 'green' : 'gray' }">
                            {{ importKey.name }}
                            <div v-if="importKey.isMany"
                                 style="float: right; cursor: pointer"
                                @click="addInput($event.target)">
                                <span>+</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="form-group">
                <two-list-draggable
                    :keys-header="keysHeader"
                    @selected-list-reload="onSelectedListReload($event)"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="text-right">
                    <div id="selectList"
                         @click="sendSelectedList"
                         class="btn btn-primary"
                         style = "pointer-events: none; opacity: 0.4">
                        Сопостаить столбцы <i class="icon-download4 ml-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TwoListDraggable from '@/components/two-list-draggable';

export default {
    emits: ['responseSelectColl'],
    components: {
        TwoListDraggable
    },
    props: {
        importKeys: {
            type: Array,
            required: true
        },
        keysHeader: {
            type: Object,
            required: true
        },
        apiUrl: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            selectedList: []
        }
    },
    watch: {
        selectedList: {
            handler(val) {
               let cntRequire = 0
                this.importKeys.forEach(key => {
                    if (key.isRequired == true) cntRequire++
                })

                if (val.length >= cntRequire) {
                    document.querySelector('#selectList').style = '';
                } else {
                    document.querySelector('#selectList').style = 'pointer-events: none; opacity: 0.4';
                }
            },
            deep: true
        }
    },
    methods: {
        onSelectedListReload(e) {
            this.selectedList = e;
        },
        addInput(target) {
            let targetDiv = target.parentNode.parentNode;
            let div = targetDiv.cloneNode(true);
            let span = div.lastChild.firstChild;
            span.innerText = '-';
            span.addEventListener('click', () => {
                div.remove()
            })
           targetDiv.after(div)
        },
        sendSelectedList() {
            let sendList = {};

            let columnsDiv = Array.from(document.querySelector('#columns-import').children);
            let columnsImport = [];

            for (let el of columnsDiv) {
                columnsImport.push(el.dataset.column)
            }

            let arrKeys = [];
            this.importKeys.forEach(el => {
                if (el.isMany) arrKeys.push(el.col);
            })

            this.selectedList.forEach((el, index) => {
                let selectArrKey = false;
                if (arrKeys.length > 0) {
                    arrKeys.forEach(key => {
                        if (key == columnsImport[index]) {
                            if (!Object.prototype.hasOwnProperty.call(sendList, key)) {
                                sendList[key] = [];
                            }
                            if (columnsImport.length > index) sendList[key].push(el.colIndex)
                            selectArrKey = true;
                        }
                    })
                }

                if (columnsImport.length > index && selectArrKey == false) sendList[columnsImport[index]] =  el.colIndex;
            })

            console.log(JSON.stringify(sendList))

            let inputs = [
                { name: 'selectedKeys', value: sendList }
            ]

            let result = this.send(this.apiUrl, inputs)

            result.then(value => {
                this.$emit('responseSelectColl', value)
            })
        },
        async send(api_url, inputs = [], method = 'POST') {
            let oData = new FormData();

            inputs.forEach(input => {
                input.value = JSON.stringify(input.value)
                oData.append(input.name, input.value);
            })

            let response = await fetch(api_url, {
                method: method,
                body: oData
            });

            let result = await response.json();

            return result;
        },
    }
}
</script>

<style scoped>

</style>
