<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="componentcontainer">
        <div class="w_container vertical gap16px">
            <div class="text20px unbounded">
                La nouveauté du moment
            </div>
            <div class="newproductcontainer">
                <img class="newproductimage" loading="lazy" width="1384" height="300"
                    src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/65684aef0fdf820e88eb9480_image%2017.png" />
                <div class="newproductinfo">
                    <div class="w_container vertical">
                        <div class="text40px unbounded">
                            Iphone 15
                        </div>
                        <div class="text40px">
                            Titanium ou Newphoria
                        </div>
                    </div>
                    <Link :href="'/catalogue/appareils/telephones/apple-iphone-15'" class="button black gap8px padding20px clickable">
                        <div class="text20px white">
                            Découvrir
                        </div>
                        <img class="image24x24px" loading="lazy" width="24" height="24"
                            src="https://uploads-ssl.webflow.com/65474f5b3ac46dc4b33db7b7/656843e415da7e91f293c87c_Vectors-Wrapper.svg" />
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
