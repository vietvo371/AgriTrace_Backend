const mix = require('laravel-mix');
mix.js([
        'resources/js/app.js',
    ], 'public/assets_admin/js/app.js')

    .js([
        'resources/js/app.js',
    ], 'public/assets_client/js/app.js')

    .version();
