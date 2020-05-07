class CookieConsent
{
    constructor()
    {
        if ( this.getCookie( "__consent" ) !== "dismiss" )
        {
            document.getElementsByTagName( "body" )[0].appendChild( this.createElement( "/dokumentumok/sutik" ) );
        }
    }

    setCookie(cname, cvalue, exdays)
    {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    getCookie(cname)
    {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
        }
        return "";
    }

    createElement( _link ) {
        let consent = document.createElement("div");
            consent.classList.add( "cookie_consent" );
        
        let container = document.createElement( "div" );
            container.classList.add("consent_container");

        let text = document.createElement("p");
            text.innerText = "A weboldalunkon sütiket használunk a legjobb felhasználói élmény érdekében. Az oldal használatával hozzájárulsz ezeknek a használatához.";

        let link = document.createElement("a");
            link.innerText = "Részletek";
            link.href = "/dokumentumok/sutik-kezelese";

        let button = document.createElement("button");
            button.innerText = "Értem";
            button.addEventListener( "click", () => {
                this.setCookie( "__consent", "dismiss", 600 );
                consent.remove();
            });

        container.appendChild(text);
        container.appendChild(link);
        container.appendChild(button);

        consent.appendChild(container);

        return consent;
    }
}

new CookieConsent();