<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * The template to display product header on product page
 *
 * Copy it to your theme implecode folder to edit the output: your-theme-folder-name/implecode/product-header.php
 *
 * @version		1.1.2
 * @package		mailer-dragon/templates/template-parts
 * @author 		Norbert Dreszer
 */
?>
<style>.mailer-form input {display:inline-block;width: auto;}</style>
<form method="post" class="mailer-form" id="mailer_form" action="#mailer_form">
	<?php do_action( 'ic_mailer_dragon_action_fields' ) ?>
	<input type="text" class="subscriber-name" name="subscriber_name" placeholder="<?php _e( 'First Name', 'mailer-dragon' ) ?>">
	<input type="email" class="subscriber-email" name="subscriber_email" placeholder="<?php _e( 'Email Address', 'mailer-dragon' ) ?>*">
	<?php do_action( 'ic_mailer_dragon_before_button' ) ?>
	<input type="submit" name="<?php echo ic_mail_form_name() ?>" class="button subscribe-button" value="<?php _e( 'Subscribe', 'mailer-dragon' ) ?>">
</form>

<?php
