/*!
 impleCode Product Reviews Scripts v1.0.0

 (c) 2015 Norbert Dreszer - https://implecode.com
 */

jQuery( document ).ready( function () {
    jQuery( document ).on( "ic_tabs_initialized", function () {
        if ( window.location.hash.indexOf( "comment" ) >= 0 || window.location.hash.indexOf( "respond" ) >= 0 || window.location.hash.indexOf( "review" ) >= 0 ) {
            jQuery( ".boxed .after-product-details .ic_tabs > h3[data-tab_id='ic_revs']" ).trigger( "click" );
        }
    } );
    jQuery( ".review-rating.allow-edit > span" ).click( function () {
        var rating = jQuery( this ).data( "rating" );
        ic_apply_rating( rating, jQuery( this ) );
        jQuery( this ).parent( 'p.review-rating' ).find( 'input[name="ic_review_rating"]' ).val( rating );
    } );
    jQuery( ".review-rating.allow-edit > span" ).hover( function () {
        var rating = jQuery( this ).data( "rating" );
        ic_apply_rating( rating, jQuery( this ) );
    }, function () {
        var rating = jQuery( this ).parent( 'p.review-rating' ).find( 'input[name="ic_review_rating"]' ).val();
        ic_apply_rating( rating, jQuery( this ) );
    } );
} );

function ic_apply_rating( rating, obj ) {
    obj.parent( 'p.review-rating.allow-edit' ).find( "span" ).removeClass( 'rating-on' );
    for ( i = 1; i <= rating; i++ ) {
        obj.parent( 'p.review-rating.allow-edit' ).find( "span.rate-" + i ).addClass( 'rating-on' );
    }
    var off_rating = 5 - rating;
    for ( i = 1; i <= off_rating; i++ ) {
        var a = i + rating;
        obj.parent( 'p.review-rating.allow-edit' ).find( "span.rate-" + a ).addClass( 'rating-off' );
    }
}