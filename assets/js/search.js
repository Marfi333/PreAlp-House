import './components/navbar.js';
import './components/footer.js';
import './components/dropdown.js';
import './components/search_bar.js';

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

let filter = {
    convert: arr => {
        let returnString = "";

        for( let i = 0; i < arr.length; i++ )
        {
            returnString += arr[i];

            if ( i !== arr.length-1 )
            {
                returnString += ";";
            }
        }

        return returnString;
    },
    stringify: arr => {
        return JSON.stringify( arr );
    },
    toArray: string => {
        let arr = [];
        if ( string.length !== 0 ) arr = string.split( ";" );

        return arr;
    },
    toUrl: array => {

    },
    add: ( list, item ) => {
        let arr = [];
        if ( list.length !== 0 ) arr = list.split( ";" );

        arr.push( item );

        return filter.convert( arr );
    },
    remove: ( list, item ) => {
        let arr = [];
        if ( list.length !== 0 ) arr = list.split( ";" );

        let index = arr.indexOf( item );

        if ( index > -1 )
        {
            arr.splice( index, 1 );
        }

        return filter.convert( arr );
    }
};

let filterEvent = element => {
    let filterArray = {};

    if ( document.querySelector( "button.sorting_menu" ).dataset.value != "" )
    {
        filterArray["rendezes"] = document.querySelector( "button.sorting_menu" ).dataset.value;
    }

    Array.prototype.forEach.call( document.querySelectorAll( "div.menu.filter_element" ), element => {
        if ( element.dataset.filterValue.length > 0 )
        {
            filterArray[element.dataset.filterLabel] =  element.dataset.filterValue ;
        }
    });

    window.history.replaceState( '', '', UrlFormat( filterArray ));
};

Array.prototype.forEach.call( document.querySelectorAll( "button.filter" ), element => {
    element.addEventListener( "click", () => {
        element.parentNode.children[1].classList.toggle("show");
    });
});

Array.prototype.forEach.call( document.querySelectorAll( "div.filteritem" ), element => {
    element.addEventListener( "click", () => {
        // TODO: automata refresh
        element.children[0].classList.toggle("selected");

        if ( element.children[0].classList.contains( "selected" ) )
        {
            if ( element.classList.contains( "custom_select" ) )
            {
                element.parentNode.dataset.filterValue = filter.add( element.parentNode.dataset.filterValue, element.children[1].children[0].value + ". emelet" );
            }
            else
            {
                element.parentNode.dataset.filterValue = filter.add( element.parentNode.dataset.filterValue, element.dataset.value );
            }
        }
        else
        {
            if ( element.classList.contains( "custom_select" ) )
            {
                element.parentNode.dataset.filterValue = filter.remove(element.parentNode.dataset.filterValue, element.children[1].children[0].value + ". emelet" );
            }
            else
            {
                element.parentNode.dataset.filterValue = filter.remove(element.parentNode.dataset.filterValue, element.dataset.value );
            }
        }
        
        filterEvent( element );
    });
});

Array.prototype.forEach.call( document.querySelectorAll( "a.sorting_menu_item" ), element => {
    element.addEventListener( "click", () => {
        // TODO: automata refresh
        setTimeout(() => {
            filterEvent( element );
        }, 20);
    });
});