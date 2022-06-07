let mix = require('laravel-mix');

if( !mix.inProduction() ) {
    mix
        .webpackConfig( {
            devtool: 'source-map'
        } )
        .sourceMaps();
}

mix
    .disableNotifications()
    .options( {
        processCssUrls: false
    } )
    .sass('source/css/style.scss', 'css')
    .setPublicPath('webroot');
