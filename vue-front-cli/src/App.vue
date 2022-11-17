<template>
    <div>
        <div>
            <header-layoute />
            <div>
                <router-view />
            </div>
            <footer-layoute />
        </div>
    </div>
</template>
<script>
import headerLayoute from "@/views/layouts/headerLayoute";
import footerLayoute from "@/views/layouts/footerLayoute";
export default {
    components: {
        headerLayoute,
        footerLayoute
    }
}
</script>
<style>

</style>
