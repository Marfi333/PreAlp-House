import './components/redirect.js';
import Swal from 'sweetalert2';

document.getElementsByClassName( "removeEstateOverview" ).forEach( node => {
  node.addEventListener( "click", element => {
    Swal.fire({
        title: 'Biztosan törölni szeretnéd?',
        text: "Ezt a lépést nem lehet visszavonni!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Igen, törlöm',
        cancelButtonText: 'Mégse'
      }).then((result) => {
        if (result.value) 
        {
          fetch( 'https://dashboard.prealphouse.hu/ingatlanok/torles/'+element.target.dataset.id, {
              method: 'POST',
              headers: {
                  "Content-Type": "application/json"
              },
              body: ""
          }).then( response => response.json() )
          .then( response => {
            if ( response.success )
            {
              Swal.fire(
                'Törölve!',
                'Az ingatlan törölve lett.',
                'success'
              ).then(() => {
                location.reload();
              });
            }
            else
            {
              Swal.fire(
                'Hiba',
                response.message,
                'error'
              );
            }
          });
        }
      });
});
});

document.getElementById( "searchByPublicId" ).addEventListener( "click", () => {
    let input = document.getElementById( "publicIdSearchInput" ).value;

    redirect( 'https://dashboard.prealphouse.hu/ingatlanok/attekintes?q=' + input );
});

document.querySelectorAll( ".private_msg" ).forEach( item => {
  item.addEventListener( "click", element => {
    let comment = element.target.dataset.comment;
    if ( comment == "" )
    {
      Swal.fire(
        '',
        'Ehhez az ingatlanhoz nem tartozik megjegyzés!',
        'info'
      );
    }
    else
    {
      Swal.fire({
        title: "Ingatlan megjegyzése",
        text: comment
      });
    }
  });
});