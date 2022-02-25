jQuery( function( $ ) {
  $( '.save_attributes' ).on( 'click', function() {
    $( '.woocommerce_attribute:hidden .attribute_value_fix' ).each( function( attribute ) {
      this.value = '';
    });
  });
});