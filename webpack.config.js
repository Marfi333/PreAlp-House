var Encore = require( '@symfony/webpack-encore' );
var fs     = require( 'fs' );
var config = JSON.parse( fs.readFileSync( __dirname + '/assets.config.json' ).toString(), true );

Encore
    .setOutputPath( config.outputPath )
    .setPublicPath( config.publicPath )
    .splitEntryChunks()
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps( !Encore.isProduction() )
    .enableVersioning( Encore.isProduction() )
    .configureBabel( () => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })
    .enableSassLoader()
    .enablePostCssLoader()
    .copyFiles({
        from:   './assets/images',
        to:     'images/[path][name].[hash:8].[ext]'
    });

for ( let asset of Object.entries( config.assets ) )
{
    for ( let element of asset[1] )
    {
        switch ( asset[0] )
        {
            case 'scss':
                Encore.addStyleEntry( element.name, config.assetsPath + asset[0] + "/" + element.path );
                break;

            case 'js':
                Encore.addEntry( element.name, config.assetsPath + asset[0] + "/" + element.path );
                break;
        }
    }
}

module.exports = Encore.getWebpackConfig();