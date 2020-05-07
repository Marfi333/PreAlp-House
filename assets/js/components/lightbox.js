class Lightbox {
    constructor( inputElement )
    {
        /*this.imageArray = inputElement.value;
            inputElement.remove();*/
        this.url = 'https://cdn.prealphouse.hu/image/';
        this.viewButton = document.querySelector( 'p.lightbox_view_button' );
        this.leftButton = document.querySelector( 'svg.lightbox_left_button' );
        this.rightButton = document.querySelector( 'svg.lightbox_right_button' );
        this.dotMenu = document.querySelector( 'div.lightbox_dots_class' );
    }
}