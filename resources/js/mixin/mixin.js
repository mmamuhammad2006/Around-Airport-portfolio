// mixin.js file
export default {
    methods: {
        /**
         * Set URL Parameter
         * @param { String } key
         * @param { String } value
         * @param { String } url
         */
        setUrlParameter(key, value, url = window.location.href) {
            let reg = new RegExp("([?&])" + key + "=.*?(&|$)", "i")
            let separator = url.indexOf('?') !== -1 ? "&" : "?"

            if (url.match(reg)) {
                window.history.pushState(null, null, url.replace(reg, '$1' + key + "=" + value + '$2'))
            } else {
                window.history.pushState(null, null, url + separator + key + "=" + value)
            }
        },
        /**
         * Get URL Parameter
         * @param  { String } name
         * @param  { String } url
         * @return { String }
         */
        getUrlParameter(name, url = window.location.href) {
            name = name.replace(/[\[\]]/g, "\\$&")
            let reg = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
                results = reg.exec(url)

            if (!results) return null
            if (!results[2]) return ''

            return decodeURIComponent(results[2].replace(/\+/g, " "))
        }
    }
}
