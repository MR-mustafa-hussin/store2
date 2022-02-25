/*!
 impleCode Admin Product Reviews Scripts v1.0.0

 (c) 2015 Norbert Dreszer - https://implecode.com
 */

var reviewsBox;

jQuery( document ).ready( function ( $ ) {

    ic_revs_show_where_div();
    jQuery( ".show-reviews" ).click( function () {
        ic_revs_show_where_div();
    } );

    reviewsBox = {
        st: 0,
        get: function ( total, num ) {

            var st = this.st, data;
            if ( !num )
                num = 20;

            this.st += num;
            this.total = total;
            $( '#commentsdiv .spinner' ).addClass( 'is-active' );

            data = {
                'action': 'get_reviews',
                'mode': 'single',
                '_ajax_nonce': $( '#add_comment_nonce' ).val(),
                'p': $( '#post_ID' ).val(),
                'start': st,
                'number': num
            };

            $.post( ajaxurl, data,
                function ( r ) {

                    r = wpAjax.parseAjaxResponse( r );
                    $( '#commentsdiv .widefat' ).show();
                    $( '#commentsdiv .spinner' ).removeClass( 'is-active' );

                    if ( 'object' == typeof r && r.responses[0] ) {
                        $( '#the-comment-list' ).append( r.responses[0].data );

                        theList = theExtraList = null;
                        $( 'a[className*=\':\']' ).unbind();

                        if ( commentsBox.st > commentsBox.total )
                            $( '#show-comments' ).hide();
                        else
                            $( '#show-comments' ).show().children( 'a' ).html( reviews_object.showcomm );

                        return;
                    } else if ( 1 == r ) {
                        $( '#show-comments' ).html( reviews_object.endcomm );
                        return;
                    }

                    $( '#the-comment-list' ).append( '<tr><td colspan="2">' + wpAjax.broken + '</td></tr>' );
                }
            );

            return false;
        }
    };

    $( ".row-actions a" ).click( function () {
        setTimeout( function () {
            ic_update_pending( -1 );
        }, 1000 );
    } );
    jQuery( ".ic-revs-translate .dashicons-no" ).click( function () {
        var data = {
            'action': 'hide_ic_revs_translate_notice'
        };
        jQuery.post( ajaxurl, data, function ( response ) {
            jQuery( ".ic-revs-translate" ).hide( "slow" );
        } );
    } );
} );

( function ( $ ) {

    // we create a copy of the WP inline edit post function
    var $wp_inline_edit = commentReply.open;

    // and then we overwrite the function with our own code
    commentReply.open = function ( $comment_id ) {

        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $wp_inline_edit.apply( this, arguments );

        // now we take care of our business

        if ( $comment_id > 0 ) {
            // define the edit row
            var $edit_row = $( '#replyrow' );
            var $post_row = $( '#comment-' + $comment_id );

            // get the data
            var $review_title = $( '.column-rating', $post_row ).find( ".review-title" ).html();
            var $review_rating = $( '.column-rating', $post_row ).find( ".review-rating" ).data( "current_rating" );
            // populate the data
            $( ':input[name="ic_review_title"]', $edit_row ).val( $review_title );
            ic_apply_rating( $review_rating, jQuery( "#replyrow .review-rating.allow-edit > span.rate-1" ) );
            //$( ':input[name="ic_review_rating"]', $edit_row ).val( $review_rating );
        }
    };

} )( jQuery );

function ic_update_pending( diff ) {
    jQuery( '#adminmenu span.pending-count, #wp-admin-bar-comments span.pending-count' ).each( function () {
        var a = jQuery( this ), n = ic_getCount( a ) + diff;
        if ( n < 1 )
            n = 0;
        a.closest( '.awaiting-mod' )[ 0 === n ? 'addClass' : 'removeClass' ]( 'count-0' );
        ic_updateCount( a, n );
    } );
}
;

function ic_getCount( el ) {
    var n = parseInt( el.html().replace( /[^0-9]+/g, '' ), 10 );
    if ( isNaN( n ) )
        return 0;
    return n;
}
;

function ic_updateCount( el, n ) {
    var n1 = '';
    if ( isNaN( n ) )
        return;
    n = n < 1 ? '0' : n.toString();
    if ( n.length > 3 ) {
        while ( n.length > 3 ) {
            n1 = thousandsSeparator + n.substr( n.length - 3 ) + n1;
            n = n.substr( 0, n.length - 3 );
        }
        n = n + n1;
    }
    el.html( n );
}

function ic_revs_show_where_div() {
    jQuery( ".show-reviews" ).each( function () {
        if ( jQuery( this ).is( ':checked' ) ) {
            jQuery( this ).nextAll( "div.enabled-where:first" ).show();
        } else {
            jQuery( this ).nextAll( "div.enabled-where:first" ).hide();
        }
    } );
}