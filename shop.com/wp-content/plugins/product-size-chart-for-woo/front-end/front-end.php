<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Front_end' ) ) {
	class PSCW_PRODUCT_SIZE_CHART_F_WOO_Front_end {
		public $woo_sc_function;
		public $option;
		public $size_chart_id;

		public function __construct() {
			/*on/off size chart front end*/
			$check_enable = get_option( 'woo_sc_setting' );
			if ( $check_enable['enable'] == 0 ) {
				return;
			}
			$this->woo_sc_function = new PSCW_PRODUCT_SIZE_CHART_F_WOO_Function();
			add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_script' ) );
			add_action( 'woocommerce_before_single_product', array( $this, 'size_chart_id' ) );
			add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'button_before_add_to_cart' ) );
			add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'after_add_to_cart_form' ) );
			add_action( 'wp_footer', array( $this, 'size_chart_footer' ), 10 );
			add_filter( 'woocommerce_product_tabs', array( $this, 'custom_product_tabs' ) );


		}

		public function after_add_to_cart_form() {
			if ( ! is_product() ) {
				return;
			}
			$get_option = $this->option;
			if ( $get_option['position'] !== 'after_add_to_cart' ) {
				return;
			}
			if ( ! empty( $this->size_chart_id ) ) {
				$meta_data = $this->get_meta_box_data( $this->size_chart_id );
				if ( isset( $meta_data['hide'] ) && $meta_data['hide'] !== 'hide_all' ) {
					?>
                    <div class="woo_sc_frontend_btn">
                        <div id="woo_sc_after_add_to_cart"
                             class="woo_sc_price_btn_popup woo_sc_btn_span woo_sc_call_popup">
							<?php
							if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "icon" ) {
								?>
                                <div class="woo_sc_text_icon"><span
                                            class="woo_sc_size_icon"></span></div>
								<?php
							}
							?>
							<?php
							if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "text" ) {
								if ( ! empty( $get_option['size_chart_name'] ) ) {
									echo esc_html( $get_option['size_chart_name'] );
								} else {
									esc_html_e( 'Size Chart', 'product-size-chart-for-woo' );
								}
							}
							?>
                        </div>
                    </div>
					<?php
				}
			}
		}

		public function register_frontend_script() {
			$this->option        = get_option( 'woo_sc_setting' );
			$this->size_chart_id = '';
			wp_enqueue_style( 'woo_sc_product_size_chart_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/sizechart_frontend_css.css', '', PSCW_SIZE_CHART_VERSION );
			wp_register_style( 'woo_table_style', false );
			wp_enqueue_style( 'woo_sc_modal_css',
				PSCW_SIZE_CHART_PLUGIN_URL . 'css/modal.css', '', PSCW_SIZE_CHART_VERSION );
			wp_enqueue_script( 'woo_sc_modal_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/modal.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION, true );
		}

		public function size_chart_footer() {
			if ( ! is_product() ) {
				return;
			}
			$get_option = $this->option;
			if ( $get_option['position'] === 'pop-up' ) {
				if ( ! empty( $this->size_chart_id ) ) {
					$meta_data = $this->get_meta_box_data( $this->size_chart_id );
					if ( isset( $meta_data['hide'] ) && $meta_data['hide'] !== 'hide_all' ) {
						?>
                        <div id="woo_sc_show_popup"
                             class="woo_sc_btn_popup woo_sc_btn_span woo_sc_call_popup">
							<?php
							if ( isset( $get_option['button_type'] ) && $get_option['button_type'] === "icon" ) {
								?>
                                <div class="woo_sc_text_icon"><span
                                            class="woo_sc_size_icon"></span></div>
								<?php
							}
							?>
							<?php
							if ( isset( $get_option['button_type'] ) && $get_option['button_type'] === "text" ) {
								if ( ! empty( $get_option['size_chart_name'] ) ) {
									echo esc_html( $get_option['size_chart_name'] );
								} else {
									esc_html_e( 'Size Chart', 'product-size-chart-for-woo' );
								}
							}
							?>
                        </div>
						<?php
					}

				}
			}

			$sc_id = $this->size_chart_id;
			if ( ! empty( $sc_id ) ) {
				$this->html_pop_up( $sc_id );
			}
		}

		public function size_chart_id() {
			if ( ! is_product() ) {
				return false;
			}
			$sc_post_id   = '';
			$product_id   = get_the_ID();
			$sizechart_id = $this->woo_sc_function->get_posts_id();

			$sizechart_posts_data = [];
			if ( ! empty( $sizechart_id ) && is_array( $sizechart_id ) ) {
				//rsort array size chart post id by DESC
				rsort( $sizechart_id );
				foreach ( $sizechart_id as $sizechart_post_id ) {
					$sizechart_posts_data[ $sizechart_post_id ] = get_post_meta( $sizechart_post_id, 'woo_sc_size_chart_data', true );
				}
				foreach ( $sizechart_posts_data as $sc_id => $val ) {

					$assign_product               = ! empty( $val['search_product'] ) ? $val['search_product'] : [];
					$assign_cate                  = ! empty( $val['categories'] ) ? $val['categories'] : [];
					$all_pro_id_in_assign_cate    = $this->woo_sc_function->get_products_in_cate( $assign_cate );
					$all_product_ids_in_sizechart = array_merge( $all_pro_id_in_assign_cate, $assign_product );
					if ( ! empty( $all_product_ids_in_sizechart ) && is_array( $all_product_ids_in_sizechart ) ) {
						$unique_product_id = array_unique( $all_product_ids_in_sizechart );
					}
					if ( ! empty( $unique_product_id ) && is_array( $unique_product_id ) ) {
						if ( in_array( $product_id, $unique_product_id ) ) {
							$sc_post_id = $sc_id;
							break;
						}
					}
				}
			}
			$this->size_chart_id = $sc_post_id;
		}

		public function html_pop_up( $sc_id ) {
			$get_meta_box_data = $this->get_meta_box_data( $sc_id );
			if ( isset( $get_meta_box_data['hide'] ) && $get_meta_box_data['hide'] !== 'hide_all' ) {
				?>
                <div id="woo_sc_modal" class="woo_sc_modal">
                    <div class="woo_sc_modal_content">
                        <span class="woo_sc_modal_close">&times;</span>
                        <div class="woo_sc_scroll_content">
							<?php
							echo wp_kses_post( $this->woo_sc_function->content_data( $sc_id ) );
							?>
                        </div>
                    </div>
                </div>
				<?php
			}
		}

		public function get_meta_box_data( $id ) {
			$meta_box_product = get_post_meta( $id, 'woo_sc_size_chart_data', true );

			return $meta_box_product;
		}

		public function button_before_add_to_cart() {
			if ( ! is_product() ) {
				return;
			}
			$get_option = $this->option;
			if ( $get_option['position'] !== 'before_add_to_cart' ) {
				return;
			}
			if ( ! empty( $this->size_chart_id ) ) {
				$meta_data = $this->get_meta_box_data( $this->size_chart_id );
				if ( isset( $meta_data['hide'] ) && $meta_data['hide'] !== 'hide_all' ) {
					?>
                    <div class="woo_sc_frontend_btn">
                        <div id="woo_sc_before_add_to_cart"
                             class="woo_sc_price_btn_popup woo_sc_btn_span woo_sc_call_popup">
							<?php
							if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "icon" ) {
								?>
                                <div class="woo_sc_text_icon"><span
                                            class="woo_sc_size_icon"></span></div>
								<?php
							}
							?>
							<?php
							if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "text" ) {
								if ( ! empty( $get_option['size_chart_name'] ) ) {
									echo esc_html( $get_option['size_chart_name'] );
								} else {
									esc_html_e( 'Size Chart', 'product-size-chart-for-woo' );
								}
							}
							?>
                        </div>
                    </div>
					<?php
				}
			}
		}

		public function custom_product_tabs() {
			if ( ! is_product() ) {
				return;
			}
			if ( ! $this->size_chart_id ) {
				return;
			}
			$get_option = $this->option;
			if ( $get_option['position'] === 'product_tabs' ) {
				if ( ! empty( $get_option['size_chart_name'] ) ) {
					$title = $get_option['size_chart_name'];
				} else {
					$title = 'Size Chart';
				}
				$tabs['SizeChart_tab'] = array(
					'title'    => esc_html__( $title, 'product-size-chart-for-woo' ),
					'priority' => 50,
					'callback' => array( $this, 'custom_product_tabs_content' )
				);

				return $tabs;
			}
		}

		public function custom_product_tabs_content() {
			if ( ! is_product() ) {
				return false;
			}
			$product_id           = get_the_ID();
			$sizechart_id         = $this->woo_sc_function->get_posts_id();
			$sizechart_posts_data = [];
			if ( ! empty( $sizechart_id ) && is_array( $sizechart_id ) ) {
				//rsort array size chart post id by DESC
				rsort( $sizechart_id );
				foreach ( $sizechart_id as $sizechart_post_id ) {
					$sizechart_posts_data[ $sizechart_post_id ] = get_post_meta( $sizechart_post_id, 'woo_sc_size_chart_data', true );
				}
				foreach ( $sizechart_posts_data as $sc_id => $val ) {

					$assign_product               = ! empty( $val['search_product'] ) ? $val['search_product'] : [];
					$assign_cate                  = ! empty( $val['categories'] ) ? $val['categories'] : [];
					$all_pro_id_in_assign_cate    = $this->woo_sc_function->get_products_in_cate( $assign_cate );
					$all_product_ids_in_sizechart = array_merge( $all_pro_id_in_assign_cate, $assign_product );
					if ( ! empty( $all_product_ids_in_sizechart ) && is_array( $all_product_ids_in_sizechart ) ) {
						$unique_product_id = array_unique( $all_product_ids_in_sizechart );
					}
					if ( ! empty( $unique_product_id ) && is_array( $unique_product_id ) ) {
						if ( in_array( $product_id, $unique_product_id ) ) {
							$this->html_tab( $sc_id );
							if ( $this->option['multi_sc'] == "0" ) {
								break;
							}
						}
					}
				}
			}

		}

		public function html_tab( $sc_id ) {
			$get_meta_box_data = $this->get_meta_box_data( $sc_id );
			if ( isset( $get_meta_box_data['hide'] ) && $get_meta_box_data['hide'] !== 'hide_all' ) {
				echo wp_kses_post( $this->woo_sc_function->content_data( $sc_id ) );
			}
		}
	}
}