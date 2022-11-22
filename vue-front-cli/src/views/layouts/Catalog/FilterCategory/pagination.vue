<template>
    <div data-v-28573630="" data-v-74ca2ebb="" class="pagination-block pagination-offset">
        <ul data-v-28573630="" class="pagination">
            <li v-for="(item, key) in arr" :key="key"
                data-v-28573630="">
                <a data-v-28573630=""
                   :href="'?page=' + item"
                   aria-current="page"
                   :class="{'active': item == current_page ? true : false}"
                   @click.prevent="clickPage(item)"
                >
                    {{ item }}
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    props: ['current_page', 'last_page'],
    data() {
        return {
            arr: [],
            current: 0
        }
    },
    mounted() {
        let start = 1;
        let end = 0;
        if(this.current_page == 1 && this.last_page >= 5) {
            end = 5
        } else if(this.current_page == 1 && this.last_page <= 5) {
            end = this.last_page
        }

        if (this.current_page != 1 && this.current_page < this.last_page) {
            start = this.current_page - 2 > 0 ? this.current_page - 2 : this.current_page - 1;

            end = this.current_page + 2 <= this.last_page ? this.current_page + 2 : this.current_page + 1;
            end = this.current_page + 3 == 5 ? this.current_page + 3 : end;

        }

        for (let i = start; i <= end; i++) {
            this.arr.push(i)
        }
        this.current = this.current_page;
    },
    methods: {
        clickPage(item) {
            this.$emit('clickPage' , item)
        }
    }
}
</script>

<style scoped>

</style>
