<template>
    <div data-v-28573630="" data-v-74ca2ebb="" class="pagination-block pagination-offset">
        <ul data-v-28573630="" class="pagination">
            <li v-for="(item, key) in arr" :key="key"
                data-v-28573630="">
                <a data-v-28573630=""
                   :href="setUrl(item)"
                   aria-current="page"
                   :class="{'active': item == current_page ? true : false}"
                    @click.prevent="clickLink(item)">
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

        if (this.current_page == this.last_page) {
            start = this.current_page - 4 > 0 ? this.current_page - 4 : this.current_page - 1;

            end = this.last_page
        }

        for (let i = start; i <= end; i++) {
            this.arr.push(i)
        }
        console.log(end)
        this.current = this.current_page;
    },
    methods: {
        setUrl(item) {
            let href = window.location.href;

            return  (href.indexOf('&page=') > 0) ?
                href.substring(0, href.indexOf('&page=')) + '&page=' + item :
                href + '&page=' + item;
        },
        clickLink(item) {
            let searchParams = (new URL(window.location.href)).searchParams;
            let query = {};

            searchParams.forEach((val, key) => {
                query[key] = val;
            })

            query.page = item;

            this.$router.push({name: 'filteredTires', query: query });
        }
    }
}
</script>

<style scoped>

</style>
