const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/frontend/js')
    .js('resources/js/admin/app.js', 'public/assets/admin/js/app.js')
    .vue()
    .sass('resources/sass/app.scss', 'public/frontend/css')
    .sass('resources/sass/admin/sb-admin-2.scss', 'public/assets/admin/css/sb-admin-2.min.css')
    .options({
        processCssUrls: false,
        postCss: [
            require('postcss-import'),
            require('cssnano')({ preset: 'default' })

        ]
}).version();
