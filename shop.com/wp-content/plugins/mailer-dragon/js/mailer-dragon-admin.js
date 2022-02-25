/*!
 Mailer Dragon Admin v1.0.0
 Adds appropriate scripts to admin settings
 (c) 2017 Norbert Dreszer - http://implecode.com
 */

jQuery( document ).ready( function ( ) {
    jQuery( '.ic_chosen' ).on( 'chosen:ready', function () {
        jQuery( 'table.email-filters' ).show();
    } );
    jQuery( ".ic_chosen" ).chosen( { width: "160px" } ).change( function ( ) {
        var email_contents = { };
        jQuery( "#ic_mailer_groups .email-post-types" ).each( function () {
            var post_type = jQuery( this ).data( "type" );
            email_contents[post_type] = ic_dropdown_value( jQuery( this ) );
        } );

        var data = {
            'action': 'ic_mailer_receivers',
            'ic_mailer_roles': ic_dropdown_value( jQuery( "select[name='ic_mailer[roles][]']" ) ),
            'ic_mailer_users': ic_dropdown_value( jQuery( "select[name='ic_mailer[users][]']" ) ),
            'ic_mailer_contents': email_contents,
            'ic_mailer_custom': ic_dropdown_value( jQuery( "select[name='ic_mailer[custom][]']" ) ),
            'security': ic_mailer_ajax.nonce
        };
        jQuery.post( ajaxurl, data, function ( response ) {
            jQuery( ".receivers-info strong" ).text( response );
        } );

        data['action'] = 'ic_mailer_delayed_receivers';
        jQuery.post( ajaxurl, data, function ( response ) {
            jQuery( ".delayed-info strong" ).text( response );
        } );
    } );

    jQuery( "span.ic_tip" ).tooltip( {
        position: {
            my: "left-48 top+37",
            at: "right+48 bottom-37",
            collision: "flip"
        },
        track: true,
    } );
} );

function ic_dropdown_value( object ) {
    var value = object.val( );
    var selected_value = object.find( ":selected" );
    if ( value !== undefined && selected_value.length > 0 ) {
        return value;
    } else {
        return '';
    }
}