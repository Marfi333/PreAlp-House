export class ImageAutosave
{
    static getFullLink( img )
    {
        return "https://cdn.prealphouse.hu/uploads/" + img;
    }

    static setStorage( arr )
    {
        sessionStorage.setItem( 'image_autosave', JSON.stringify( arr ));
            return true;
    }

    static getStorage()
    {
        if ( sessionStorage.getItem( 'image_autosave' ) === null )
            return null;

        return JSON.parse( sessionStorage.getItem( 'image_autosave' ));
    }

    static addStorage( item )
    {
        if ( sessionStorage.getItem( 'image_autosave' ) === null )
        {
            let array = [ item ];
            sessionStorage.setItem( 'image_autosave', JSON.stringify( array ) );
                return true;
        }

        let array = JSON.parse( sessionStorage.getItem( 'image_autosave' ) );
        array.push( item );

        sessionStorage.setItem( 'image_autosave', JSON.stringify( array ));
            return true;
    }

    static removeStorage( item )
    {
        if ( sessionStorage.getItem( 'image_autosave' ) === null )
            return false;

        let array = JSON.parse( sessionStorage.getItem( 'image_autosave' ) );
        let index = array.indexOf( item );

        if ( index > -1 )
        {
            array.splice( index, 1 );
            sessionStorage.setItem( 'image_autosave', JSON.stringify( array ) );
                return true;
        }

        return false;
    }

    static has( key )
    {   
        if ( sessionStorage.getItem( 'image_autosave' ) === null )
            return false;

        let array = JSON.parse( sessionStorage.getItem( 'image_autosave' ));

        if ( array.includes( key ) )
            return true;

        return false;
    }

    static createElement( src )
    {
        let imageSrc = document.createElement( "DIV" );
            imageSrc.classList.add( "image_element" );
            imageSrc.classList.add( "imageElementSelectorClass" );

        let img = document.createElement( "IMG" );
            img.src = src;
            img.classList.add("uploadedPhoto");
            img.addEventListener( "click", ImageAutosave.mainImageListener );

        let svg = document.createElementNS( 'http://www.w3.org/2000/svg', "svg" );
            svg.setAttribute( 'aria-hidden', 'true' );
            svg.setAttribute( 'focusable', 'false' );
            svg.setAttribute( 'role', 'img' );
            svg.setAttribute( 'xmlns', 'http://www.w3.org/2000/svg' );
            svg.setAttribute( 'viewBox', '0 0 320 512' );
            svg.classList.add( 'times' );
            svg.innerHTML = '<path fill="currentColor" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" class=""></path>';
            svg.addEventListener( "click", ImageAutosave.removeImageListener );

        imageSrc.appendChild( img );
        imageSrc.appendChild( svg );

        return imageSrc;
    }

    static mainImageListener( element )
    {
        let images = document.querySelectorAll( '.imageElementSelectorClass' );
        images.forEach( item => {
            item.classList.remove( "selectedMainPicture" );
        });

        element.target.parentNode.classList.add( "selectedMainPicture" );
    }

    static removeImageListener( element )
    {
        console.log(element);
        if ( element.target.nodeName == "svg" )
        {
            var removableElement = element.target;
        }
        else
        {
            var removableElement = element.target.parentNode;
        }
    
        var imageLink = removableElement.parentNode.childNodes[0].src;
        var pNode = removableElement.parentNode.parentNode;
        var rChild =  removableElement.parentNode;
        ImageAutosave.removeStorage( imageLink );

        pNode.removeChild( rChild );

        //removableElement.parentNode.parentNode.removeChild( removableElement.parentNode );

    }
}