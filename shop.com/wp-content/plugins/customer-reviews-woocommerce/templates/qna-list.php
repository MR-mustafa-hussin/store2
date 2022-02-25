<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ($qna as $q) {
	?>
	<div class="cr-qna-list-q-cont">
		<div class="cr-qna-list-q-q">
			<div class="cr-qna-list-q-q-l">
				<img class="cr-qna-list-q-icon" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/question.svg'; ?>" alt="Question" />
			</div>
			<div class="cr-qna-list-q-q-r">
				<span class="cr-qna-list-question"><?php echo $q['question']; ?></span>
				<span class="cr-qna-list-q-author"><?php echo sprintf( __( '%s asked on %s', 'customer-reviews-woocommerce' ), '<span class="cr-qna-list-q-author-b">' . esc_html( $q['author'] ) . '</span>', date_i18n( $date_format, strtotime( $q['date'] ) ) ); ?></span>
			</div>
		</div>
		<?php
		if( isset( $q['answers'] ) && is_array( $q['answers'] ) && 0 < count( $q['answers'] ) ) :
		?>
		<div class="cr-qna-list-q-a">
			<div class="cr-qna-list-q-a-l">
				<img class="cr-qna-list-q-icon" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/answer.svg'; ?>" alt="Answer" />
			</div>
			<div class="cr-qna-list-q-a-r">
				<?php
				$cr_i = 0;
				$cr_len = count( $q['answers'] );
				foreach ($q['answers'] as $a) {
					if( $cr_i === $cr_len-1 ) {
						$cr_class_qna_list_answer = 'cr-qna-list-answer cr-qna-list-last';
					} else {
						$cr_class_qna_list_answer = 'cr-qna-list-answer';
					}
					?>
					<div class="<?php echo $cr_class_qna_list_answer; ?>">
						<span class="cr-qna-list-answer-s"><?php echo $a['answer']; ?></span>
						<span class="cr-qna-list-q-author"><?php echo sprintf( __( '%s answered on %s', 'customer-reviews-woocommerce' ), '<span class="cr-qna-list-q-author-b">' . esc_html( $a['author'] ) . '</span>', date_i18n( $date_format, strtotime( $a['date'] ) ) ); ?></span>
						<?php
							if( 1 === $a['author_type'] ) {
								$store_manager = apply_filters( 'cr_qna_store_manager', 'store manager' );
								echo '<span class="cr-qna-list-q-author-verified">';
								echo '<img class="cr-qna-list-v-icon" src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'img/store-manager.svg' . '" alt="' . esc_attr__( 'store manager', 'customer-reviews-woocommerce' ) . '" />';
								echo esc_html__( $store_manager, 'customer-reviews-woocommerce' ) . '</span>';
							} elseif( 2 === $a['author_type'] ) {
								echo '<span class="cr-qna-list-q-author-verified">';
								echo '<img class="cr-qna-list-v-icon" src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'img/verified.svg' . '" alt="' . $cr_verified_label . '" />';
								echo $cr_verified_label . '</span>';
							}
						?>
					</div>
					<?php
					$cr_i++;
				}
				?>
			</div>
		</div>
		<?php
		endif;
		?>
		<div class="cr-qna-list-q-b">
			<div class="cr-qna-list-q-b-l"></div>
			<div class="cr-qna-list-q-b-r">
				<button type="button" data-question="<?php echo $q['id']; ?>" data-post="<?php echo $q['post']; ?>" class="cr-qna-ans-button"><?php _e( 'Answer the question', 'customer-reviews-woocommerce' ); ?></button>
				<div class="cr-qna-q-voting cr-qna-q-voting-<?php echo $q['id']; ?>" data-vquestion="<?php echo $q['id']; ?>">
					<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/upvote.svg'; ?>" alt="Upvote" title="<?php _e( 'Upvote the question', 'customer-reviews-woocommerce' ); ?>" data-upvote="1" />
					<span class="cr-qna-q-voting-upvote">(<?php
						if( isset( $q['votes'] ) && isset( $q['votes']['upvotes'] ) ) {
							echo intval( $q['votes']['upvotes'] );
						} else {
							echo '0';
						} ?>)</span>
					<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/downvote.svg'; ?>" alt="Downvote" title="<?php _e( 'Downvote the question', 'customer-reviews-woocommerce' ); ?>" data-upvote="0"  />
					<span class="cr-qna-q-voting-downvote">(<?php
						if( isset( $q['votes'] ) && isset( $q['votes']['downvotes'] ) ) {
							echo intval( $q['votes']['downvotes'] );
						} else {
							echo '0';
						} ?>)</span>
				</div>
				<div class="cr-qna-q-voting-spinner cr-qna-q-voting-spinner-<?php echo $q['id']; ?>">
					<img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/spinner-dots-2.svg'; ?>" alt="Loading" />
				</div>
			</div>
		</div>
	</div>
	<?php
}
