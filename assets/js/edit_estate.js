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
            //ImageAutosave.addStorage( ImageAutosave.getFullLink( data.path ) );
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
    document.querySelectorAll(".times").forEach(times => {
        times.addEventListener("click", ImageAutosave.removeImageListener);
    });

    document.querySelectorAll(".uploadedPhoto").forEach(img => {
        img.addEventListener( "click", ImageAutosave.mainImageListener );
    });
};

document.getElementById( "updateEstateButton" ).addEventListener( "click", () => {
    let values = {};
    let errors = 0;

    let id = document.querySelector('input[name="edit_estate_id"]');
    if ( id === null )
    {
        errors++;
        Swal.fire(
            'Sikertelen felt??lt??s',
            'Hiba t??rt??nt a k??pek feldolgoz??sa sor??n. Pr??b??ld meg ??jra k??s??bb.',
            'error'
        );
        return;
    }
    values.id = id.value;

    let firstImageError = 0;
    let imageContainer = document.getElementById( "imageFieldContainer" ).children;
    
    if ( imageContainer.length > 1 )
    {
        document.getElementById( "imageErrorTextfield" ).innerText = "";
    }
    else
    {
        document.getElementById( "imageErrorTextfield" ).innerText = "Legal??bb 1 k??pet fel kell t??lteni!";
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
            document.getElementById( "imageErrorTextfield" ).innerText = "Ki kell v??lasztani az els??dleges k??pet!";
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
        document.getElementById( "titleInputError" ).innerText = "K??telez?? kit??lteni!";
        errors++;
    }

    /* MEGBIZAS TIPUSA */
    if ( document.getElementById( "megbizasTipusaField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "megbizasTipusaError" ).innerText = "";
        values.megbizasTipusa = document.getElementById( "megbizasTipusaField" ).value;
    }
    else
    {
        document.getElementById( "megbizasTipusaError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* INGATLAN TIPUSA */
    if ( document.getElementById( "ingatlanTipusaField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "ingatlanTipusaError" ).innerText = "";
        values.ingatlanTipusa = document.getElementById( "ingatlanTipusaField" ).value;
    }
    else
    {
        document.getElementById( "ingatlanTipusaError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* SZERKEZET */
    if ( document.getElementById( "szerkezetField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "szerkezetError" ).innerText = "";
        values.szerkezet = document.getElementById( "szerkezetField" ).value;
    }
    else
    {
        document.getElementById( "szerkezetError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* FUTES */
    if ( document.getElementById( "futesField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "futesError" ).innerText = "";
        values.futes = document.getElementById( "futesField" ).value;
    }
    else
    {
        document.getElementById( "futesError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* ERKELY */
    if ( document.getElementById( "erkelyField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "erkelyError" ).innerText = "";
        values.erkely = document.getElementById( "erkelyField" ).value;
    }
    else
    {
        //document.getElementById( "erkelyError" ).innerText = "K??telez?? v??lasztani!";
        values.erkely = "-";
    }

    /* ??PITES IDEJE */
    if ( document.getElementById( "buildField" ).value !== "" )
    {
        values.built = document.getElementById( "buildField" ).value;
    }
    else
    {
        //document.getElementById( "erkelyError" ).innerText = "K??telez?? v??lasztani!";
        values.build = "-";
    }

    /* KILATAS */
    if ( document.getElementById( "kilatasField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "kilatasError" ).innerText = "";
        values.kilatas = document.getElementById( "kilatasField" ).value;
    }
    else
    {
        document.getElementById( "kilatasError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* LIFT */
    if ( document.getElementById( "liftField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "liftError" ).innerText = "";
        values.lift = document.getElementById( "liftField" ).value;
    }
    else
    {
        //document.getElementById( "liftError" ).innerText = "K??telez?? v??lasztani!";
        values.lift = "-";
    }

    /* ALLAPOT*/
    if ( document.getElementById( "allapotField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "allapotError" ).innerText = "";
        values.allapot = document.getElementById( "allapotField" ).value;
    }
    else
    {
        document.getElementById( "allapotError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* EMELET */
    if ( document.getElementById( "emeletField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "emeletError" ).innerText = "";
        values.emelet = document.getElementById( "emeletField" ).value;
    }
    else
    {
        document.getElementById( "emeletError" ).innerText = "K??telez?? v??lasztani!";
        errors++;
    }

    /* AKADALYMENTESITETT */
    if ( document.getElementById( "akadalymentesitettField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "akadalymentesitettError" ).innerText = "";
        values.akadalymentesitett = document.getElementById( "akadalymentesitettField" ).value;
    }
    else
    {
        //document.getElementById( "akadalymentesitettError" ).innerText = "K??telez?? v??lasztani!";
        values.akadalymentesitett = "-";
    }

    /* SZOBAK */
    if ( document.getElementById( "szobakField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "szobakError" ).innerText = "";
        values.szobak = document.getElementById( "szobakField" ).value;
    }
    else
    {
        document.getElementById( "szobakError" ).innerText = "K??telez?? v??lasztani!";
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
        document.getElementById( "meretError" ).innerText = "K??telez?? megadni!";
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
        document.getElementById( "arError" ).innerText = "K??telez?? megadni!";
        errors++;
    }

    /* MEGYE */
    if ( document.getElementById( "megyeField" ).value !== "-- V??lassz --" )
    {
        document.getElementById( "megyeError" ).innerText = "";
        values.megye = document.getElementById( "megyeField" ).value;
    }
    else
    {
        document.getElementById( "megyeError" ).innerText = "K??telez?? megadni!";
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
        document.getElementById( "varosError" ).innerText = "K??telez?? megadni!";
        errors++;
    }

    /* CHECKBOX */
    values.publikusCim = document.getElementById( "publikusField" ).checked;

    if ( Editor.getData() == "" )
    {
        document.getElementById( "contentError" ).innerText = "K??telez?? kit??lteni!";
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
        fetch( `https://dashboard.prealphouse.hu/ingatlanok/szerkesztes/${values.id}`, {
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
                    'Sikeres m??dos??t??s',
                    'Az ingatlan megjelent az oldalon.',
                    'success'
                ).then(() => {
                    ImageAutosave.setStorage([]);
                    redirect("https://dashboard.prealphouse.hu/ingatlanok/attekintes");
                });
            }
            else
                Swal.fire(
                    'Sikertelen m??dos??t??s',
                    response.message,
                    'error'
                );
        })
        .catch(error => {});
    }
});