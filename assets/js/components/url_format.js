const UrlFormat = array => {
    let defaults = [
        "megbizas-tipusa", "ingatlan-tipusa","meret", "szobak", "ar", "kereses"
    ];

    let url = window.location.href.toString().replace( window.location.protocol + "//", "" ).replace( window.location.hostname + "/", "" ).replace( "ingatlanok/", "" ).replace( "/", "" );
    let page = url.indexOf( '/' ) === -1 ? 1 : url.substr( url.indexOf( '/' ), url.length-1 ).replace( '/', '' );

    let filterArray = {};
    url.split( "+" ).forEach( item => {
        filterArray[item.split("=")[0]] = item.split("=")[1];
    });

    for ( let item in filterArray )
    {
        if ( !defaults.includes( item ) & array.item === undefined )
        {
            filterArray[item] = undefined;
        }
    }

    for ( let item in array )
    {
        filterArray[item] = array[item];
    }

    let completeUrl = "";
    for ( let item in filterArray )
    {
        if ( filterArray[item] !== undefined )
        {
            completeUrl += item + "=" + filterArray[item] + "+";
        }
    }
    completeUrl = completeUrl.substring( 0, completeUrl.length -1 );

    let finalUrl = page === 1 ? window.location.protocol + "//" + window.location.hostname + "/ingatlanok/" +completeUrl : window.location.protocol + "//" + window.location.hostname + "/ingatlanok/" +completeUrl + "/" + page;

    return finalUrl;
};