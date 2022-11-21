<template>
    <div data-v-62d65988="" data-v-74ca2ebb="" class="product-horizontal">
        <div data-v-62d65988="" class="image-block">
            <div data-v-62d65988="" class="image">
                <a data-v-62d65988="" href="#" class="">
                    <img data-v-62d65988=""
                         data-src="#" alt="" class="lazyLoad">
                </a>
            </div>
            <div data-v-62d65988="" class="seasonality">
                <div v-show="item.isSpikes" data-v-62d65988="" class="seasonality-item">
                    <div data-v-62d65988="" class="season season-spikes"></div>

                </div>
                <!---->
            </div>
        </div>
        <div data-v-62d65988="" class="product-info">
            <h3 data-v-62d65988="" class="title">
                <a data-v-62d65988="" href="#" class="">
                    {{ item.title }}
                    <!---->
                </a>
            </h3>
            <div data-v-62d65988="" class="article">
                Код товара: {{ item.num }}
            </div>


            <!---->
        </div>
        <div data-v-62d65988="" class="price-stocks">
            <div data-v-62d65988="" class="price"><span data-v-62d65988="">{{ item.price }} ₽</span></div>
            <div data-v-62d65988="" class="stock">
                В наличии {{ item.count }} шт.
                <!---->
            </div>
            <div data-v-62d65988="" class="dates">
                <div data-v-62d65988="" class="payment-info">Оплата при получении</div>
                <div data-v-62d65988="" class="date date-pickup">
                    Самовывоз сейчас
                </div>
                <div data-v-62d65988="" class="city">Челябинск</div>
            </div>
        </div>
        <div data-v-62d65988="" class="buttons">
            <div data-v-22490b82="" data-v-62d65988="" class="product-add-cart-block is-product-list">
                <div data-v-f8a82ede="" data-v-22490b82="" class="quantity-field quantity-big">
                    <button data-v-f8a82ede="" type="button" class="minus">
                        <svg data-v-f8a82ede="" width="13" height="1" viewBox="0 0 13 1" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect data-v-f8a82ede="" x="13" width="1" height="13" rx="0.5" transform="rotate(90 13 0)" fill="#343434"></rect>
                        </svg>
                    </button>
                    <input data-v-f8a82ede="" type="number" :value="item.count >= 4 ? 4 : item.count">
                    <button data-v-f8a82ede="" type="button" class="plus">
                        <svg data-v-f8a82ede="" width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect data-v-f8a82ede="" x="6" width="1" height="13" rx="0.5" fill="#343434"></rect>
                            <rect data-v-f8a82ede="" x="13" y="6" width="1" height="13" rx="0.5" transform="rotate(90 13 6)" fill="#343434"></rect>
                        </svg>
                    </button>
                </div>
                <button data-v-22490b82="" class="button-add">
                    Купить
                </button> <!---->
            </div>

        </div>
    </div>
</template>

<script>
export default {
    props: {
        item: {
            required: true
        },
    }
}
</script>

<style scoped>

</style>
