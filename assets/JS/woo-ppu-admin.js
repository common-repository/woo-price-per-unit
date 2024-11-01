/*
*   Javacripts for Product page
*/
//Hides or shows custom field on product page if select "Recalculated price additional text" is set to Custom
"use strict";
//Warning for no weight set
jQuery(document).ready(function($){
    var inputWeight = $( '#shipping_product_data input#_weight' );
    ShowNoWeightWarn();
    inputWeight.on( "change",function(){ShowNoWeightWarn()});
    function ShowNoWeightWarn() {
        if ( inputWeight.val() == "" ) {
            $( "#mcmp_ppu_options .admin-warn-no-weight" ).show();
        } else {
            $( "#mcmp_ppu_options .admin-warn-no-weight" ).hide();
        }
    }
});
/*
*   Javacripts for General settings page
*/
//Hides or shows "Preposition for weight" on general settings if select "Recalculated price additional text" is set to Automatic
jQuery(document).ready(function($){
    var selectRecalcText = $( 'select#_mcmp_ppu_recalc_text' );
    var inputRecalcPreposition = $( 'input#_mcmp_ppu_recalc_text_automatic_preposition' ).closest('tr');
    ShowPreposition();
    selectRecalcText.on( "change",function(){ShowPreposition(300)});
    function ShowPreposition(speed=0) {
        if ( selectRecalcText.val()=='-automatic-' ) {
            inputRecalcPreposition.show(speed);
        } else {
            inputRecalcPreposition.hide(speed);
        }
    }
});