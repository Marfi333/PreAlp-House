import './components/redirect.js';
import Swal from 'sweetalert2';
import { ImageAutosave } from './components/image.js';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

let Editor;
ClassicEditor
    .create( document.querySelector( '#editor' ) )
    .then( editor => {
        document.querySelector( ".ck-reset" ).classList.add( 'max_width' );
        document.querySelector( ".ck-reset" ).style.borderRadius = "5px !important";
        Editor = editor;
    })
    .catch(error => console.log(error));

const upload = file => {
    fetch( 'https://dashboard.prealphouse.hu/ingatlanok/kep/feltoltes', {
        method: 'POST',
        headers: {
            "Content-Type": "multipart/form-data"
        },
        body: file
    }).then( response => response.json() )
    .then(console.log("success"));
};

const handleImageUpload = event => {
    const files = event.target.files;
    const formData = new FormData();
    formData.append( 'image', files[0] );

    fetch( 'https://dashboard.prealphouse.hu/ingatlanok/kep/feltoltes', {
        method: 'POST',
        body: formData
    })
    .then( response => response.json() )
    .then( data => {
        if ( data.success === true )
        {
            let container = document.querySelector( ".uploaded_image_container" );
            container.insertBefore( ImageAutosave.createElement( ImageAutosave.getFullLink( data.path ) ), container.childNodes[0] );
            ImageAutosave.addStorage( ImageAutosave.getFullLink( data.path ) );
        }
        else
        {
            console.log("err")
        }
    })
    .catch( error => console.log(error) );
};

document.getElementById( "imageInputField" ).addEventListener( "change", element => {
    handleImageUpload(element);
});

window.onload = () => {
    if ( ImageAutosave.getStorage() !== null )
    {
        ImageAutosave.getStorage().forEach( element => {
            let container = document.querySelector( ".uploaded_image_container" );
            container.insertBefore( ImageAutosave.createElement( element ), container.childNodes[0] );
        });
    }
};
/*
window.onbeforeunload = event => {
    return "Biztos el akarod hagyni az oldalt?";
};*/

document.getElementById( "createEstateButton" ).addEventListener( "click", () => {
    let values = {};
    let errors = 0;

    let firstImageError = 0;
    let imageContainer = document.getElementById( "imageFieldContainer" ).children;
    console.log(imageContainer);
    if ( imageContainer.length > 1 )
    {
        document.getElementById( "imageErrorTextfield" ).innerText = "";
    }
    else
    {
        document.getElementById( "imageErrorTextfield" ).innerText = "Legalább 1 képet fel kell tölteni!";
        errors++;
        firstImageError++;
    }

    let imagesArray = [];
    let selected = 0;
    for( let node of imageContainer )
    {
        if ( !node.classList.contains( "add_new_image" ) )
        {
            if ( node.classList.contains( "selectedMainPicture" ) )
            {
                imagesArray.push({src: node.children[0].src, main: true});
                selected++;
            }
            else
            {
                imagesArray.push({src: node.children[0].src, main: false});
            }
        }
    }
    if ( firstImageError == 0 )
    {
        if ( selected > 0 )
        {
            document.getElementById( "imageErrorTextfield" ).innerText = "";
            values.images = imagesArray;
        }
        else
        {
            document.getElementById( "imageErrorTextfield" ).innerText = "Ki kell választani az elsődleges képet!";
            errors++;
        }
    }

    /* CIM */
    if ( document.getElementById( "titleInputField" ).value.replace(/\s/g, "").length > 0 )
    {
        document.getElementById( "titleInputError" ).innerText = "";
        values.title = document.getElementById( "titleInputField" ).value.trim();
    }
    else
    {
        document.getElementById( "titleInputError" ).innerText = "Kötelező kitölteni!";
        errors++;
    }

    /* MEGBIZAS TIPUSA */
    if ( document.getElementById( "megbizasTipusaField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "megbizasTipusaError" ).innerText = "";
        values.megbizasTipusa = document.getElementById( "megbizasTipusaField" ).value;
    }
    else
    {
        document.getElementById( "megbizasTipusaError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* INGATLAN TIPUSA */
    if ( document.getElementById( "ingatlanTipusaField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "ingatlanTipusaError" ).innerText = "";
        values.ingatlanTipusa = document.getElementById( "ingatlanTipusaField" ).value;
    }
    else
    {
        document.getElementById( "ingatlanTipusaError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* SZERKEZET */
    if ( document.getElementById( "szerkezetField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "szerkezetError" ).innerText = "";
        values.szerkezet = document.getElementById( "szerkezetField" ).value;
    }
    else
    {
        document.getElementById( "szerkezetError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* FUTES */
    if ( document.getElementById( "futesField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "futesError" ).innerText = "";
        values.futes = document.getElementById( "futesField" ).value;
    }
    else
    {
        document.getElementById( "futesError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* ERKELY */
    if ( document.getElementById( "erkelyField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "erkelyError" ).innerText = "";
        values.erkely = document.getElementById( "erkelyField" ).value;
    }
    else
    {
        //document.getElementById( "erkelyError" ).innerText = "Kötelező választani!";
        values.erkely = "-";
    }

    /* ÉPITES IDEJE */
    if ( document.getElementById( "buildField" ).value !== "" )
    {
        values.built = document.getElementById( "buildField" ).value;
    }
    else
    {
        //document.getElementById( "erkelyError" ).innerText = "Kötelező választani!";
        values.build = "-";
    }

    /* KILATAS */
    if ( document.getElementById( "kilatasField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "kilatasError" ).innerText = "";
        values.kilatas = document.getElementById( "kilatasField" ).value;
    }
    else
    {
        document.getElementById( "kilatasError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* LIFT */
    if ( document.getElementById( "liftField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "liftError" ).innerText = "";
        values.lift = document.getElementById( "liftField" ).value;
    }
    else
    {
        //document.getElementById( "liftError" ).innerText = "Kötelező választani!";
        values.lift = "-";
    }

    /* ALLAPOT*/
    if ( document.getElementById( "allapotField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "allapotError" ).innerText = "";
        values.allapot = document.getElementById( "allapotField" ).value;
    }
    else
    {
        document.getElementById( "allapotError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* EMELET */
    if ( document.getElementById( "emeletField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "emeletError" ).innerText = "";
        values.emelet = document.getElementById( "emeletField" ).value;
    }
    else
    {
        document.getElementById( "emeletError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* AKADALYMENTESITETT */
    if ( document.getElementById( "akadalymentesitettField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "akadalymentesitettError" ).innerText = "";
        values.akadalymentesitett = document.getElementById( "akadalymentesitettField" ).value;
    }
    else
    {
        //document.getElementById( "akadalymentesitettError" ).innerText = "Kötelező választani!";
        values.akadalymentesitett = "-";
    }

    /* SZOBAK */
    if ( document.getElementById( "szobakField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "szobakError" ).innerText = "";
        values.szobak = document.getElementById( "szobakField" ).value;
    }
    else
    {
        document.getElementById( "szobakError" ).innerText = "Kötelező választani!";
        errors++;
    }

    /* MERET */
    if ( document.getElementById( "meretField" ).value.replace(/\s/g, "").length > 0 )
    {
        document.getElementById( "meretError" ).innerText = "";
        values.meret = document.getElementById( "meretField" ).value;
    }
    else
    {
        document.getElementById( "meretError" ).innerText = "Kötelező megadni!";
        errors++;
    }

    /* AR */
    if ( document.getElementById( "arField" ).value.replace(/\s/g, "").length > 0 )
    {
        document.getElementById( "arError" ).innerText = "";
        values.ar = document.getElementById( "arField" ).value;
    }
    else
    {
        document.getElementById( "arError" ).innerText = "Kötelező megadni!";
        errors++;
    }

    /* MEGYE */
    if ( document.getElementById( "megyeField" ).value !== "-- Válassz --" )
    {
        document.getElementById( "megyeError" ).innerText = "";
        values.megye = document.getElementById( "megyeField" ).value;
    }
    else
    {
        document.getElementById( "megyeError" ).innerText = "Kötelező megadni!";
        errors++;
    }

    /* VAROS */
    if ( document.getElementById( "varosField" ).value.replace(/\s/g, "").length > 0 )
    {
        document.getElementById( "varosError" ).innerText = "";
        values.varos = document.getElementById( "varosField" ).value;
    }
    else
    {
        document.getElementById( "varosError" ).innerText = "Kötelező megadni!";
        errors++;
    }

    /* Address */
    if ( document.getElementById( "cimField" ).value.replace(/\s/g, "").length > 0 )
    {
        document.getElementById( "cimError" ).innerText = "";
        values.address = document.getElementById( "cimField" ).value;
    }
    else
    {
        document.getElementById( "cimError" ).innerText = "Kötelező megadni!";
        errors++;
    }

    /* CHECKBOX */
    values.publikusCim = document.getElementById( "publikusField" ).checked;

    if ( Editor.getData() == "" )
    {
        document.getElementById( "contentError" ).innerText = "Kötelező kitölteni!";
        errors++;
    }
    else
    {
        document.getElementById( "contentError" ).innerText = "";
        values.content = Editor.getData();
    }

    if ( document.getElementById( "megjegyzesField" ).value.replace(/\s/g, "").length > 0 )
    {
        values.megjegyzes = document.getElementById( "megjegyzesField" ).value;
    }
    

    if ( errors > 0 )
    {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    else
    {
        fetch( 'https://dashboard.prealphouse.hu/ingatlanok/feltoltes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(values)
        }).then( response => response.json() )
        .then(response => {
            if (response.success )
            {
                Swal.fire(
                    'Sikeres feltöltés',
                    'Az ingatlan megjelent az oldalon.',
                    'success'
                ).then(() => {
                    ImageAutosave.setStorage([]);
                    redirect("https://dashboard.prealphouse.hu/ingatlanok/attekintes");
                });
            }
            else
                Swal.fire(
                    'Sikertelen feltöltés',
                    response.message,
                    'error'
                );
        })
        .catch(error => {});
        }
});