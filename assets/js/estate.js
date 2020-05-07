import './components/navbar.js';
import './components/footer.js';
import Swal from 'sweetalert2';
//import './components/lightbox.js';

class Lightbox {
    constructor( inputElement )
    {
        this.imageContainer = document.querySelector( 'div.estate_image_box' );
        this.currentImage = 0;
        this.pageImage = 0;
        this.imageArray = JSON.parse(inputElement.value);
            inputElement.remove();
        this.url = 'https://cdn.prealphouse.hu/image/';
        let viewButton = document.querySelector( 'p.lightbox_view_button' );
            viewButton.addEventListener( "click", this.viewButtonListener.bind(this) );
        let leftButton = document.querySelector( 'svg.lightbox_left_button' );
            leftButton.addEventListener( "click", this.leftButtonListener.bind(this) );
        let rightButton = document.querySelector( 'svg.lightbox_right_button' );
            rightButton.addEventListener( "click", this.rightButtonListener.bind(this) );
        this.dotMenu = document.querySelector( 'div.lightbox_dots_class' );

        for ( let i = 0; i < this.imageArray.length; i++ )
        {
            let dotElement = document.createElement( "DIV" );
                dotElement.classList.add("dot");
                dotElement.dataset.elementNumber = i;
                dotElement.addEventListener( "click", this.dotListener.bind(this) );

                if ( i == 0 )
                {
                    let activeElement = document.createElement("DIV");
                        activeElement.classList.add("active");

                    dotElement.appendChild( activeElement );
                }

            this.dotMenu.appendChild( dotElement );
        }
    }

    dotListener( element ) 
    {
        this.currentImage = parseInt( element.target.dataset.elementNumber );
        this.resetImage();
    }

    leftButtonListener()
    {
        if ( this.currentImage !== 0 )
        {
            this.currentImage -= 1;
            this.resetImage();
        }
    }

    rightButtonListener()
    {
        if ( this.currentImage !== this.imageArray.length-1 )
        {
            this.currentImage += 1;
            this.resetImage();
        }
    }

    viewButtonListener()
    {
        this.launchLightbox();
        this.pageImage = this.currentImage;
    }

    launchLightbox()
    {
        let lightbox = document.querySelector( 'div.lightbox' );
        let body = document.querySelector( 'body' );
        let image = document.querySelector( 'img.lightbox_image_tag' );
            image.src = this.url+this.imageArray[this.currentImage];
        let counter = document.querySelector( 'span.lightbox_image_counter' );
            counter.innerText = this.currentImage+1;

        let left = document.querySelector( 'svg.lightbox_main_left_button' );
        let right = document.querySelector( 'svg.lightbox_main_right_button' );

        body.style.overflow = "hidden";
        lightbox.style.display = "flex";
        setTimeout(() => {
            lightbox.style.opacity = "1";
        }, 200);

        lightbox.addEventListener( "click", event => {
            if ( event.target.classList.value === "lightbox" )
            {
                this.hideLightbox();
                lightbox.removeEventListener( "click", () => {} );
            }
        });

        left.addEventListener( "click", this.mainLeftClick.bind( this ) );
        right.addEventListener( "click", this.mainRightClick.bind( this ) );
    }

    mainLeftClick()
    {
        let image = document.querySelector( 'img.lightbox_image_tag' );
        let counter = document.querySelector( 'span.lightbox_image_counter' );

        if ( this.currentImage !== 0 )
        {
            this.currentImage -= 1;
            image.src = this.url+this.imageArray[this.currentImage];
            counter.innerText = this.currentImage+1;
        }
    }

    mainRightClick()
    {
        let image = document.querySelector( 'img.lightbox_image_tag' );
        let counter = document.querySelector( 'span.lightbox_image_counter' );

        if ( this.currentImage !== this.imageArray.length -1 )
        {
            this.currentImage += 1;
            image.src = this.url+this.imageArray[this.currentImage];
            counter.innerText = this.currentImage+1;
        }
    }

    hideLightbox()
    {
      
        let lightbox = document.querySelector( 'div.lightbox' );
        let body = document.querySelector( 'body' );
        let left = document.querySelector( 'svg.lightbox_main_left_button' );
        let right = document.querySelector( 'svg.lightbox_main_right_button' );

        left.parentNode.replaceChild( left.cloneNode( true ), left );
        right.parentNode.replaceChild( right.cloneNode( true ), right );

        body.style.overflow = "auto";
        lightbox.style.opacity = "0";
        setTimeout(() => {
            lightbox.style.display = "none";
        }, 200);
        this.currentImage = this.pageImage;
    }

    resetImage()
    {
        document.querySelector( 'div.estate_image_box' ).style.backgroundImage = "url('"+this.url+this.imageArray[this.currentImage]+"')";
        document.querySelector( 'div.estate_image_box' ).dataset.imageNumber = this.currentImage;

        this.dotMenu.innerHTML = "";
        for ( let i = 0; i < this.imageArray.length; i++ )
        {
            let dotElement = document.createElement( "DIV" );
                dotElement.classList.add("dot");
                dotElement.dataset.elementNumber = i;
                dotElement.addEventListener( "click", this.dotListener.bind(this) );

                if ( i == this.currentImage )
                {
                    let activeElement = document.createElement("DIV");
                        activeElement.classList.add("active");

                    dotElement.appendChild( activeElement );
                }

            this.dotMenu.appendChild( dotElement );
        }
    }
}

let lightbox = new Lightbox( document.querySelector( 'input.imagesArrayInput' ) );

document.querySelector( 'div.checkbox_container' ).addEventListener( "click", element => {
    document.querySelector( 'div.checkbox' ).classList.toggle( "active" );
    //document.querySelector( 'div.checkbox' ).innerText="x";
    //font-weight: 500; font-size: 12px; padding-left: 4px; padding-top: 0px; padding-bottom: 4px; padding-right: 1px
});

document.querySelector( "button.estate_message_submit" ).addEventListener( "click", () => {
    let name = document.querySelector( "input.estate_message_name" ).value;
    let email = document.querySelector( "input.estate_message_email" ).value;
    let phone = document.querySelector( "input.estate_message_phone" ).value;
    let message = document.querySelector( "textarea.estate_message_message" ).value;
    let checkbox = document.querySelector( "div.estate_message_checkbox" );

    let specialFormat = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
    let emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let phoneFormat = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;

    let errors = 0;

    if ( name.length === 0 )
    {
        document.querySelector( "p.error_name" ).innerText = "Kötelező kitölteni!";
        errors++;
    }
    else if ( specialFormat.test( name ) )
    {
        document.querySelector( "p.error_name" ).innerText = "A mező nem tartalmazhat speciális karaktereket!";
        errors++;
    }
    else
    {
        document.querySelector( "p.error_name" ).innerText = "";
    }

    if ( email.length === 0 )
    {
        document.querySelector( "p.error_email" ).innerText = "Kötelező kitölteni!";
        errors++;
    }
    else if ( !emailFormat.test( email ) )
    {
        document.querySelector( "p.error_email" ).innerText = "Létező email címet kell megadni!";
        errors++;
    }
    else
    {
        document.querySelector( "p.error_email" ).innerText = "";
    }

    if ( phone.length !== 0 && !phoneFormat.test( phone ) )
    {
        document.querySelector( "p.error_phone" ).innerText = "Létező telefonszámot kell megadni!";
        errors++;
    }
    else
    {
        document.querySelector( "p.error_phone" ).innerText = "";
    }

    if ( message.length === 0 )
    {
        document.querySelector( "p.error_message" ).innerText = "Kötelező kitölteni!";
        errors++;
    }
    else if ( message.length > 249 )
    {
        document.querySelector( "p.error_message" ).innerText = "Az üzenet hossza maximum 250 karakter lehet!";
        errors++;
    }
    else
    {
        document.querySelector( "p.error_message" ).innerText = "";
    }

    if ( !checkbox.classList.contains( "active" ) )
    {
        document.querySelector( "p.error_checkbox" ).innerText = "Kötelező elfogadni a feltételeket!";
        errors++;
    }
    else
    {
        document.querySelector( "p.error_checkbox" ).innerText = "";
    }

    if ( errors === 0 )
    {
        let http = new XMLHttpRequest();
        let url = "https://prealphouse.hu/uzenet";
        let params = "name="+name+"&phone="+phone+"&email="+email+"&message="+message;

        http.open( "POST", url, true );
        http.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );

        http.onreadystatechange = () => {
            if ( http.readyState == 4 && http.status == 200 )
            {
                Swal.fire( "Üzenet sikeresen elküldve!", "", "success" );

                document.querySelector( "input.estate_message_name" ).value = "";
                document.querySelector( "input.estate_message_email" ).value = "";
                document.querySelector( "input.estate_message_phone" ).value = "";
                document.querySelector( "textarea.estate_message_message" ).value = "";
                document.querySelector( "div.estate_message_checkbox" ).classList.remove( "active" );
            }
            else
            {
                Swal.fire({
                    title: "Hiba történt az üzenet elküldése során!",
                    text: "Kérjük próbálja újra később.",
                    icon: "error",
                    width: 600
                });
            }
        };

        http.send( params );
    }
});