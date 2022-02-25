<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages Discounts gateaway includes folder
 *
 * Here includes files are defined and managed.
 *
 * @version		1.0.0
 * @package		2ckeckout-gateway/includes
 * @author 		Norbert Dreszer
 */
require_once(MAILER_DRAGON_BASE_PATH . '/includes/sanitize-class.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/settings-functions.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/mailer-settings.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/email-selectors.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/register-mailer.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/mailer-meta.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/admin-ajax.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/email-sender.php');
require_once(MAILER_DRAGON_BASE_PATH . '/includes/subscription-widget.php');

