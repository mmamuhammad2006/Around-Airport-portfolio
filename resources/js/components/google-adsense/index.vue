<template>
    <div class="container">
        <Adsense
            :data-ad-client="dataAdClient"
            :data-ad-slot="dataAdSlot"/>
    </div>
</template>

<script>
window.Vue = require('vue').default;
import Ads from 'vue-google-adsense'
Vue.use(require('vue-script2'))

Vue.use(Ads.Adsense);
export default {
    props: {
        dataAdClient: {
            type: String,
            required: true
        },
        dataAdSlot: {
            type: String,
            default: ''
        }
    }
}
</script>
