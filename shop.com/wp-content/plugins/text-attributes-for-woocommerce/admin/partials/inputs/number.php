<?php 

/**
 * Number input on the edit product page. 
 * 
 * @since             1.0.0
 * @package           Zobnin_Text_Attributes_For_WooCommerce
 * @subpackage 				Zobnin_Text_Attributes_For_WooCommerce/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<input type="number" class="short attribute_value_fix" name="attribute_values[<?php echo esc_attr( $i ); ?>]" value="<?php echo floatval( $this->get_attribute_value( $attribute ) );?>" />