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
    .sass('css-source/style.scss', 'css')
    .setPublicPath('webroot');
