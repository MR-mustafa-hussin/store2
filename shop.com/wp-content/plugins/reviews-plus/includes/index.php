<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages product reviews includes folder
 *
 * @version		1.0.0
 * @package		reviews-plus/includes
 * @author 		Norbert Dreszer
 */
if ( is_admin() ) {
	require_once(AL_REVIEWS_BASE_PATH . '/includes/admin-class.php');
	require_once(AL_REVIEWS_BASE_PATH . '/includes/review-meta.php');
}
require_once(AL_REVIEWS_BASE_PATH . '/includes/includes.php');
require_once(AL_REVIEWS_BASE_PATH . '/includes/admin-review-list.php');
