/* Global dropdowns */
Array.prototype.forEach.call( document.querySelectorAll("[-data-dropdown]"), button => {
    if ( button.nodeName === "BUTTON" )
    {
        button.addEventListener( "click", () => {
            Array.prototype.forEach.call( document.querySelectorAll("[-data-dropdown="+button.attributes["-data-dropdown"].nodeValue+"]"), dropdown => {
                if ( dropdown.nodeName === "DIV" )
                {
                    dropdown.classList.toggle("show");

                    dropdown.children.forEach(child => {
                        child.addEventListener("click", child => {
                            child.target.parentNode.parentNode.children[0].innerText = child.target.innerText;
                            child.target.parentNode.parentNode.children[0].dataset.value = child.target.innerText;

                            child.target.parentNode.parentNode.children[1].classList.remove("show");
                        });
                    });
                }
            });
        })
    }
});

/* Global dropdown cleaner */
window.onclick = event => {
    /* Csak egy dropdown */
    if( event.target.matches('.global_dropbtn') )
    {
        Array.prototype.forEach.call( document.querySelectorAll("[-data-dropdown]"), element => {
            if ( element.nodeName === "DIV" )
            {
                if ( event.target.attributes["-data-dropdown"].value !== element.attributes["-data-dropdown"].value )
                {
                    element.classList.remove("show");
                }
            }
        });
    }

    /* Becsukja az összes dropdownt */
    if ( event.target.nodeName !== "A" && !event.target.matches('.global_dropbtn'))
    {
        console.log(event);
        event.preventDefault();

        if ( event.target.parentNode.attributes["-data-dropdown"] !== undefined )
        {
            Array.prototype.forEach.call( document.querySelectorAll("[-data-dropdown="+event.target.parentNode.attributes["-data-dropdown"].value+"]"), element => {
                if ( element.nodeName === "BUTTON" )
                {
                    element.innerText = event.target.innerText;
                    element.dataset.value = event.target.innerText;
                }
            });
        }

        Array.prototype.forEach.call( document.getElementsByClassName('global_dropdown_content'), dropdown => {
            if ( dropdown.classList.contains( "show" ) )
            {
                dropdown.classList.remove( "show" );
            }
        });
    }

    if ( !event.target.matches('#pi') && !event.target.matches('.modified') )
    {
        let pricetag = "";

        if ( document.getElementById( "priceMin" ) !== null )
        {
            if ( ( document.getElementById( "priceMin" ).value === "" || document.getElementById( "priceMin" ).value === "0" ) && ( document.getElementById( "priceMax" ).value === "" || document.getElementById( "priceMax" ).value === "0" ) )
            {
                pricetag = "";
            }
            else
            {
                let min = document.getElementById( "priceMin" ).value === "" ? 0 : document.getElementById( "priceMin" ).value;
                let max = document.getElementById( "priceMax" ).value === "" ? 0 : document.getElementById( "priceMax" ).value;

                pricetag = min +"-"+ max;
            }

            document.getElementById( "ar" ).innerHTML = '<input id="pi" onfocus="changeInput(\'p\', this);" class="default" type="text" placeholder="ár" value="'+pricetag+'">';

            document.getElementById( "pi" ).innerText = pricetag;
        }
    }

    if ( !event.target.matches('#mi') && !event.target.matches('.modified') )
    {
        let pricetag = "";

        if ( document.getElementById( "sizeMin" ) !== null )
        {
            if ( ( document.getElementById( "sizeMin" ).value === "" || document.getElementById( "sizeMin" ).value === "0" ) && ( document.getElementById( "sizeMax" ).value === "" || document.getElementById( "sizeMax" ).value === "0" ) )
            {
                pricetag = "";
            }
            else
            {
                let min = document.getElementById( "sizeMin" ).value === "" ? 0 : document.getElementById( "sizeMin" ).value;
                let max = document.getElementById( "sizeMax" ).value === "" ? 0 : document.getElementById( "sizeMax" ).value;

                pricetag = min +"-"+ max;
            }

            document.getElementById( "mr" ).innerHTML = '<input id="mi" onfocus="changeInput(\'m\', this);" class="default" type="text" placeholder="méret" value="'+pricetag+'">';

            document.getElementById( "mi" ).innerText = pricetag;
        }
    }
}

/* Room dropdown reset */
document.getElementById("szm").addEventListener("click", e => {
    setTimeout(() => {
        document.getElementById("szmr").innerHTML = '<span style="color: rgba(0,0,0,.4)">szobák</span>';
    }, 10);
});

/* Price currency setter */
Array.prototype.forEach.call( document.getElementsByClassName( 'ch' ), element => {
    element.addEventListener( 'click', e => {
        document.getElementById( "ar" ).dataset.currency = element.dataset.currency;
    });
});