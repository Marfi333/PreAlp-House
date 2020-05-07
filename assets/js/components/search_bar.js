const UrlFormatBar = array => {
    let defaults = [
        "megbizas-tipusa", "ingatlan-tipusa","meret", "szobak", "ar", "kereses"
    ];

    let url = "";
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

    let finalUrl = page === 1 ? completeUrl :  +completeUrl + "/" + page;

    return finalUrl;
};

/* Search button event */
document.querySelector( "button.search" ).addEventListener( "click", () => {
    let evObj = document.createEvent( 'Events' );
    evObj.initEvent('click', true, false);
    document.getElementsByTagName( "body" )[0].dispatchEvent(evObj);
    

    let i = 0;
    let search_string = "https://prealphouse.hu/ingatlanok/";
        if ( document.querySelector("[data-label=megbizas-tipusa]") !== "" ) 
        {
            search_string += "megbizas-tipusa=" + document.querySelector("[data-label=megbizas-tipusa]").dataset.value; i++;
        }

        if ( document.querySelector("[data-label=ingatlan-tipusa]") !== "" ) 
        {
            i !== 0 ? search_string += "+" : null;
            search_string += "ingatlan-tipusa=" + document.querySelector("[data-label=ingatlan-tipusa]").dataset.value; i++;
        }

        if ( document.querySelector("input#mi").value !== "" ) 
        {
            i !== 0 ? search_string += "+" : null;
            search_string += "meret=" + document.querySelector("input#mi").value; i++;
        }

        if ( document.querySelector("[data-label=szobak]").dataset.value !== "" && document.querySelector("[data-label=szobak]").dataset.value !== "szobák" ) 
        {
            i !== 0 ? search_string += "+" : null;
            search_string += "szobak=" + document.querySelector("[data-label=szobak]").dataset.value; i++;
        }

        if ( document.querySelector("input#pi").value !== "" ) 
        {
            i !== 0 ? search_string += "+" : null;
            let c = document.querySelector("#ar[data-currency]").dataset.currency.includes( 'ezer' ) ? 'e' : 'm';
            search_string += "ar=" + document.querySelector("input#pi").value + c; i++;
        }

        if ( document.querySelector("[data-label=kifejezes]").value !== "" ) 
        {
            i !== 0 ? search_string += "+" : null;
            search_string += "kereses=" + document.querySelector("[data-label=kifejezes]").value; i++;
        }

        if ( document.querySelector( "button.sorting_menu" ) !== null && document.querySelector( "button.sorting_menu" ).dataset.value !== "" )
        {
            i !== 0 ? search_string += "+" : null;
            search_string += "rendezes=" + document.querySelector( "button.sorting_menu" ) .dataset.value;
        }

        //window.location.href = search_string.normalize("NFKD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
        let filterArray = {};

        Array.prototype.forEach.call( document.querySelectorAll( "div.menu.filter_element" ), element => {
            if ( element.dataset.filterValue.length > 0 )
            {
                filterArray[element.dataset.filterLabel] =  element.dataset.filterValue ;
            }
        });

        //window.location.href = search_string + "+" + UrlFormatBar( filterArray );
        if ( UrlFormatBar( filterArray ) === "" )
        {
            window.location.href = search_string;
        }
        else
        {
            window.location.href = search_string + "+" + UrlFormatBar( filterArray );
        }
});

/* Price and size input manager */
global.changeInput = ( type, element ) => {
    let focused;

    if ( type == "m" )
    {
        if ( element.value === "0-0" )
        {
            focused = '<input id="sizeMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min" value="">' +
                        '<div class="separator">-</div>' +
                        '<input id="sizeMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max" value="">';
        }
        if ( element.value.includes( "-" ) )
        {
            focused = '<input id="sizeMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min" value="'+element.value.split('-')[0]+'">' +
                        '<div class="separator">-</div>' +
                        '<input id="sizeMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max" value="'+element.value.split('-')[1]+'">';
        }
        else
        {
            focused = '<input id="sizeMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min">' +
                        '<div class="separator">-</div>' +
                        '<input id="sizeMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max">';
        }

        let normal = '<input id="mi" onfocus="changeInput(this, "m");" class="default" type="text" placeholder="ár">';

        element.parentNode.innerHTML = focused;
        document.getElementById("sizeMin").focus();
    }
    else if ( type == "p" )
    {
        if ( element.value === "0-0" )
        {
            focused = '<input id="priceMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min" value="">' +
                        '<div class="separator">-</div>' +
                        '<input id="priceMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max" value="">';
        }
        if ( element.value.includes( "-" ) )
        {
            focused = '<input id="priceMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min" value="'+element.value.split('-')[0]+'">' +
                        '<div class="separator">-</div>' +
                        '<input id="priceMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max" value="'+element.value.split('-')[1]+'">';
        }
        else
        {
            focused = '<input id="priceMin" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="min">' +
                        '<div class="separator">-</div>' +
                        '<input id="priceMax" type="text" class="modified" onkeyup="numberRegex(this)" placeholder="max">';
        }

        let normal = '<input id="pi" onfocus="changeInput(this, "p");" class="default" type="text" placeholder="ár">';

        element.parentNode.innerHTML = focused;
        document.getElementById("priceMin").focus();
    }
};