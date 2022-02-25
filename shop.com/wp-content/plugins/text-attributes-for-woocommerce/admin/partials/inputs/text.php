<?php 

/**
 * Text input on the edit product page. 
 * 
 * @since             1.0.0
 * @package           Zobnin_Text_Attributes_For_WooCommerce
 * @subpackage 				Zobnin_Text_Attributes_For_WooCommerce/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<input type="text" class="short" name="attribute_values[<?php echo esc_attr( $i ); ?>]"  value="<?php echo $this->get_attribute_value( $attribute );?>" />