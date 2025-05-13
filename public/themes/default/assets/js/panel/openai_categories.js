//Admin

function categorySave( item_id ) {
    "use strict";

    document.getElementById( "custom_template_button" ).disabled = true;
    document.getElementById( "custom_template_button" ).innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append( 'item_id', item_id );
    formData.append( 'name', $( "#category_name" ).val() );


    $.ajax( {
        type: "post",
        url: "/dashboard/admin/openai/categories/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function ( data ) {
            toastr.success(magicai_localize?.template_saved ||'Template Saved Succesfully')
            location.href = '/dashboard/admin/openai/categories';
            document.getElementById( "custom_template_button" ).disabled = false;
            document.getElementById( "custom_template_button" ).innerHTML = "Save";
        },
        error: function ( data ) {
            var err = data.responseJSON.errors;
            $.each( err, function ( index, value ) {
                toastr.error( value );
            } );
            document.getElementById( "custom_template_button" ).disabled = false;
            document.getElementById( "custom_template_button" ).innerHTML = "Save";
        }
    } );
    return false;
}
