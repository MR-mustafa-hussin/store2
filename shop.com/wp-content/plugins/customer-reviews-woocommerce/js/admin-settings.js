jQuery(document).ready(function() {
	// Select all/none
	jQuery( '.ivole-new-settings' ).on( 'click', '.select_all', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).prop( 'selected', true );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	jQuery( '.ivole-new-settings' ).on( 'click', '.select_none', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).prop( 'selected', false );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	jQuery( '#cr_check_duplicate_site_url' ).on( 'click', function() {
		let button = jQuery( this );
		button.next( 'span' ).addClass( 'is-active' );
		button.prop( 'disabled', true );
		jQuery.ajax(
			{
			url: ajaxurl,
			data: {
				action: 'cr_check_duplicate_site_url',
				security: button.attr( 'data-nonce' )
			}
			}
		).done( function( response ) {
			button.next( 'span' ).removeClass( 'is-active' );
			button.prop( 'disabled', false );
			button.prev( 'span' ).text( response.result );
			if( response.is_duplicate === false ) {
				button.remove();
			}
		} ).fail( function( response ) {
				button.next( 'span' ).removeClass( 'is-active' );
				button.prop( 'disabled', false );
		} );
	} );

	jQuery(".cr-trustbadgea").each(function() {
		let badge = jQuery(this).find(".cr-badge").eq(0);
		let scale = jQuery(this).width() / badge.outerWidth();
		if( 1 > scale ) {
			badge.css("transform", "scale(" + scale + ")");
		}
		badge.css("visibility", "visible");
	});
} );
