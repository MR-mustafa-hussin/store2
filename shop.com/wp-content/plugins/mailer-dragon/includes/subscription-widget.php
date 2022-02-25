<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages scheduled_disc settings
 *
 * Here all scheduled_disc settings are defined and managed.
 *
 * @version		1.0.0
 * @package		mailer-dragon/includes
 * @author 		Norbert Dreszer
 */
class ic_mailer_widgets {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
		register_widget( 'ic_mailer_subscription' );
	}

}

$ic_mailer_widgets = new ic_mailer_widgets;

class ic_mailer_subscription extends WP_Widget {

	function __construct() {
		$label		 = __( 'Subscription Form', 'ecommerce-product-catalog' );
		$sublabel	 = __( 'Shows newsletter subscription form', 'ecommerce-product-catalog' );
		$widget_ops	 = array( 'classname' => 'ic_mailer_subscription', 'description' => $sublabel );
		parent::__construct( 'ic_mailer_subscription', $label, $widget_ops );
	}

	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ], $instance, $this->id_base );

		echo $args[ 'before_widget' ];
		if ( $title ) {
			echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}
		echo '<style>.mailer-form br {margin-bottom: 10px;content: " ";display:block;}</style>';
		echo do_shortcode( '[subscribe_form paragraphs=1]' );
		echo $args[ 'after_widget' ];
	}

	function form( $instance ) {
		$instance	 = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title		 = $instance[ 'title' ];
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ecommerce-product-catalog' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p><?php
	}

}
