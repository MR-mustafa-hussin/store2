<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Product reviews form template
 *
 * @version        1.0.0
 * @package        reviews-plus/includes
 * @author        Norbert Dreszer
 */
if ( post_password_required() || !comments_open() ) {
	return;
}
$labels = ic_get_reviews_labels();
?>

<div id="product_reviews" class="reviews-area">
	<?php if ( is_ic_reviews_heading_active() ) { ?>
		<h3 class="catalog-header"><?php
			$count = '';
			echo $labels[ 'reviews' ] . $count;
			?></h3>
		<?php
	}

	if ( have_comments() ) {
		do_action( 'ic_before_reviews' );
		ic_reviews_navigation();
		ic_reviews_list();
		ic_reviews_navigation( 'below' );
	}

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( !comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
		?>
		<p class="no-comments"><?php echo $labels[ 'reviews_closed' ] ?></p>
		<?php
	}
	ic_reviews_form();
	?>

</div>