import './components/navbar.js';
import './components/footer.js';
import './components/dropdown.js';
import './components/search_bar.js';

/* number regex */
global.numberRegex = e => {
    let reg = /^\d+$/;

    if ( !reg.test( e.value ) )
    {
        e.value = e.value.substring(0, e.value.length -1);
    }
};

window.onload = () => {
    let content = document.getElementsByClassName( "_homepage_content" )[0].children.length;
    let next = document.getElementsByClassName( "next" )[0];

    if ( (content-1) % 4 === 0 )
    {
        next.style.height = "100px";
    }
};