<template>
    <main-navbar />

    <div class="page-content">

        <main-sidebar />

        <div class="content-wrapper">
            <div class="content-inner">
                <div class="content">

                    <main-alert />

                    <router-view />

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import MainNavbar from "@/components/MainNavbar";
import MainSidebar from "@/components/MainSidebar";
import MainAlert from "@/components/MainAlert";

export default {
    components: {
        MainNavbar,
        MainSidebar,
        MainAlert
    }
}
</script>

<style>

</style>
