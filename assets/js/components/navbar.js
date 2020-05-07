Array.prototype.forEach.call( document.getElementsByClassName( 'menu_item' ), element => {
    element.addEventListener('mouseout', () => {
        window.innerWidth >= 1000 ? document.getElementsByClassName(element.dataset.line)[0].style.opacity = 0 : null;
    });

    element.addEventListener('mouseover', () => {
        window.innerWidth >= 1000 ? document.getElementsByClassName(element.dataset.line)[0].style.opacity = 1 : null;
    });
});

global.redirect = url => {
    window.location.href = url;
};