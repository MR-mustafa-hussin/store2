<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nonce = wp_create_nonce( "cr_qna_" . $cr_post_id );
$nonce_ans = wp_create_nonce( "cr_qna_a_" . $cr_post_id );
$nonce_showmore = wp_create_nonce( "cr_qna_sm_" . $cr_post_id );

$current_user = wp_get_current_user();
$user_name = '';
$user_email = '';
if( $current_user instanceof WP_User ) {
	$user_email = $current_user->user_email;
	$user_name = $current_user->user_firstname . ' ' . $current_user->user_lastname;
	if ( empty( trim( $user_name ) ) ) $user_name = '';
}
if( $attributes ) {
	$json_attributes = wc_esc_json( wp_json_encode( $attributes ) );
} else {
	$json_attributes = '';
}
?>
<div id="cr_qna" class="cr-qna-block" data-attributes="<?php echo $json_attributes; ?>">
	<h2><?php _e( 'Q & A', 'customer-reviews-woocommerce' ); ?></h2>
	<div class="cr-qna-search-block">
		<div class="cr-ajax-qna-search">
			<svg width='1em' height='1em' viewBox='0 0 16 16' class='cr-qna-search-icon' fill='#18B394' xmlns='http://www.w3.org/2000/svg'>
				<path fill-rule='evenodd' d='M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z'/><path fill-rule='evenodd' d='M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z'/>
			</svg>
			<input class="cr-input-text" type="text" placeholder="<?php echo __( 'Search answers', 'customer-reviews-woocommerce' ); ?>">
			<span class="cr-clear-input">
				<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle-fill" fill="#18B394" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
				</svg>
			</span>
		</div>
		<button type="button" class="cr-qna-ask-button"><?php _e( 'Ask a question', 'customer-reviews-woocommerce' ); ?></button>
	</div>
	<div class="cr-qna-list-block">
		<?php
		if( isset( $qna ) && is_array( $qna ) && 0 < count( $qna ) ) :
			?><div class="cr-qna-list-block-inner"><?php
			echo CR_Qna::display_qna_list( $qna );
			?></div><?php
			?>
			<button id="cr-show-more-q-id" type="button" data-nonce="<?php echo $nonce_showmore; ?>" data-product="<?php echo $cr_post_id; ?>" data-page="0"<?php if( count( $qna ) >= $total_qna ) echo ' style="display:none"'; ?>><?php echo __( 'Show more', 'customer-reviews-woocommerce' ); ?></button>
			<span id="cr-show-more-q-spinner" style="display:none;"></span>
			<p class="cr-search-no-qna" style="display:none"><?php esc_html_e( 'Sorry, no questions were found', 'customer-reviews-woocommerce' );?></p>
			<?php
		else:
		?>
		<div class="cr-qna-list-empty"><?php _e( 'There are no questions yet', 'customer-reviews-woocommerce' ); ?></div>
		<?php
		endif;
		?>
	</div>
	<div class="cr-qna-new-q-overlay">
		<div class="cr-qna-new-q-form">
			<button class="cr-qna-new-q-form-close">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path class="cr-no-icon" d="M12.12 10l3.53 3.53-2.12 2.12L10 12.12l-3.54 3.54-2.12-2.12L7.88 10 4.34 6.46l2.12-2.12L10 7.88l3.54-3.53 2.12 2.12z"/></g></svg>
			</button>
			<div class="cr-qna-new-q-form-input">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Ask a question', 'customer-reviews-woocommerce' ); ?></p>
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your question will be answered by a store representative or other customers.', 'customer-reviews-woocommerce' ); ?></p>
				<textarea name="question" class="cr-qna-new-q-form-q" rows="3" placeholder="<?php _e( 'Start your question with \'What\', \'How\', \'Why\', etc.', 'customer-reviews-woocommerce' ); ?>"></textarea>
				<input type="text" name="name" class="cr-qna-new-q-form-name" placeholder="<?php _e( 'Your name', 'customer-reviews-woocommerce' ); ?>" value="<?php echo $user_name;?>">
				<input type="email" name="email" class="cr-qna-new-q-form-email" placeholder="<?php _e( 'Your email', 'customer-reviews-woocommerce' ); ?>" value="<?php echo $user_email;?>">
				<div class="cr-qna-new-q-form-s">
					<?php
					if( 0 < strlen( $cr_recaptcha ) ) {
						echo '<p>' . sprintf( __( 'This site is protected by reCAPTCHA and the Google %1$sPrivacy Policy%2$s and %3$sTerms of Service%4$s apply.', 'customer-reviews-woocommerce' ), '<a href="https://policies.google.com/privacy" rel="noopener noreferrer nofollow" target="_blank">', '</a>', '<a href="https://policies.google.com/terms" rel="noopener noreferrer nofollow" target="_blank">', '</a>' ) . '</p>';
					}
					?>
					<button type="button" data-nonce="<?php echo $nonce; ?>" data-product="<?php echo $cr_post_id; ?>" data-crcptcha="<?php echo $cr_recaptcha; ?>" class="cr-qna-new-q-form-s-b"><?php _e( 'Submit', 'customer-reviews-woocommerce' ); ?></button>
					<button type="button" class="cr-qna-new-q-form-s-b cr-qna-new-q-form-s-p"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/spinner-dots.svg'; ?>" alt="Loading" /></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-ok">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Thank you for the question!', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/mail.svg'; ?>" alt="Mail" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your question has been received and will be answered soon. Please do not submit the same question again.', 'customer-reviews-woocommerce' ); ?></p>
				<div class="cr-qna-new-q-form-s">
					<button type="button" class="cr-qna-new-q-form-s-b"><?php _e( 'OK', 'customer-reviews-woocommerce' ); ?></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-error">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Error', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/warning.svg'; ?>" alt="Warning" height="32px" />
				<p class="cr-qna-new-q-form-text">
					<?php _e( 'An error occurred when saving your question. Please report it to the website administrator. Additional information:', 'customer-reviews-woocommerce' ); ?>
					<span class="cr-qna-new-q-form-text-additional"></span>
				</p>
			</div>
		</div>
		<div class="cr-qna-new-q-form cr-qna-new-a-form">
			<button class="cr-qna-new-q-form-close">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path class="cr-no-icon" d="M12.12 10l3.53 3.53-2.12 2.12L10 12.12l-3.54 3.54-2.12-2.12L7.88 10 4.34 6.46l2.12-2.12L10 7.88l3.54-3.53 2.12 2.12z"/></g></svg>
			</button>
			<div class="cr-qna-new-q-form-input">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Add an answer', 'customer-reviews-woocommerce' ); ?></p>
				<p class="cr-qna-new-q-form-text"></p>
				<textarea name="question" class="cr-qna-new-q-form-q" rows="3" placeholder="<?php _e( 'Write your answer', 'customer-reviews-woocommerce' ); ?>"></textarea>
				<input type="text" name="name" class="cr-qna-new-q-form-name" placeholder="<?php _e( 'Your name', 'customer-reviews-woocommerce' ); ?>" value="<?php echo $user_name;?>">
				<input type="email" name="email" class="cr-qna-new-q-form-email" placeholder="<?php _e( 'Your email', 'customer-reviews-woocommerce' ); ?>" value="<?php echo $user_email;?>">
				<div class="cr-qna-new-q-form-s">
					<?php
					if( 0 < strlen( $cr_recaptcha ) ) {
						echo '<p>' . sprintf( __( 'This site is protected by reCAPTCHA and the Google %1$sPrivacy Policy%2$s and %3$sTerms of Service%4$s apply.', 'customer-reviews-woocommerce' ), '<a href="https://policies.google.com/privacy" rel="noopener noreferrer nofollow" target="_blank">', '</a>', '<a href="https://policies.google.com/terms" rel="noopener noreferrer nofollow" target="_blank">', '</a>' ) . '</p>';
					}
					?>
					<button type="button" data-nonce="<?php echo $nonce_ans; ?>" data-product="<?php echo $cr_post_id; ?>" data-crcptcha="<?php echo $cr_recaptcha; ?>" class="cr-qna-new-q-form-s-b"><?php _e( 'Submit', 'customer-reviews-woocommerce' ); ?></button>
					<button type="button" class="cr-qna-new-q-form-s-b cr-qna-new-q-form-s-p"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/spinner-dots.svg'; ?>" alt="Loading" /></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-ok">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Thank you for the answer!', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/mail.svg'; ?>" alt="Mail" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your answer has been received and will be published soon. Please do not submit the same answer again.', 'customer-reviews-woocommerce' ); ?></p>
				<div class="cr-qna-new-q-form-s">
					<button type="button" class="cr-qna-new-q-form-s-b"><?php _e( 'OK', 'customer-reviews-woocommerce' ); ?></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-error">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Error', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/warning.svg'; ?>" alt="Warning" height="32px" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'An error occurred when saving your question. Please report it to the website administrator. Additional information:', 'customer-reviews-woocommerce' ); ?></p>
			</div>
		</div>
	</div>
</div>
