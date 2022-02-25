<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Setting' ) ) {
	class PSCW_PRODUCT_SIZE_CHART_F_WOO_Setting {
		public function __construct() {
			add_action( 'init', array( $this, 'woo_sc_post_type' ) );
			add_action( 'admin_menu', array( $this, 'woo_cs_setting_page' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		}

		public function register_scripts() {
			$current_screen = get_current_screen()->id;
			if ( $current_screen == 'pscw-size-chart_page_pscw-size-chart-setting' ) {
				//enqueue css
				wp_enqueue_style( 'size_chart_segment_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/segment.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_button_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/button.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_menu_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/menu.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_form_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/form.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_dropdown_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/dropdown.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_checkbox_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/checkbox.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_tran_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/transition.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_back_end_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/woo_size_chart_back_end.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_icon_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/icon.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_label', PSCW_SIZE_CHART_PLUGIN_URL . 'css/label.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_input', PSCW_SIZE_CHART_PLUGIN_URL . 'css/input.min.css', '', PSCW_SIZE_CHART_VERSION );
				//enqueue js
				wp_enqueue_script( 'size_chart_menu_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/tab.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_checkbox_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/checkbox.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_setting_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/woo_size_chart_setting.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_form_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/form.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_dropdown_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/dropdown.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_tran_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/transition.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'iris', PSCW_SIZE_CHART_PLUGIN_URL . 'js/iris.min.js', array(
					'jquery-ui-draggable',
					'jquery-ui-slider',
					'jquery-touch-punch'
				), false, 1
				);
			}
		}

		public function woo_sc_post_type() {
			$icon_url = esc_url( PSCW_SIZE_CHART_PLUGIN_URL . 'img/sc_logo.png' );
			$label    = array(
				'name'          => esc_html__( 'Size Chart', 'product-size-chart-for-woo' ),
				'singular_name' => esc_html__( 'Size Chart', 'product-size-chart-for-woo' ),
				'add_new'       => esc_html__( 'Add New', 'product-size-chart-for-woo' ),
				'all_items'     => esc_html__( 'All Size Charts', 'product-size-chart-for-woo' ),
				'add_new_item'  => esc_html__( 'Add Item', 'product-size-chart-for-woo' )
			);
			$args     = array(
				'labels'              => $label,
				'description'         => esc_html__( 'Product Size Chart', 'product-size-chart-for-woo' ),
				'supports'            => array(
					'title',
					'editor',
				),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => false,
				'menu_position'       => "5",
				'menu_icon'           => $icon_url,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'post'

			);
			register_post_type( 'pscw-size-chart', $args );
		}

		public function woo_cs_setting_page() {
			add_submenu_page( 'edit.php?post_type=pscw-size-chart',
				__( 'Size Chart', 'product-size-chart-for-woo' ),
				__( 'Settings', 'product-size-chart-for-woo' ),
				'manage_options',
				'pscw-size-chart-setting',
				array( $this, 'woo_sc_setting_page' ), 2 );
		}

		public function get_save_option() {
			if ( isset( $_POST['woo_sc_setting_nonce'] ) ) {
				$woo_sc_setting_nonce = sanitize_text_field( $_POST['woo_sc_setting_nonce'] );
				if ( ! isset( $woo_sc_setting_nonce ) ) {
					return;
				}
				if ( ! wp_verify_nonce( $woo_sc_setting_nonce, 'woo_sc_check_setting_nonce' ) ) {
					return;
				}
			}
			if ( isset( $_POST['woo_sc_save_setting'] ) ) {
				if ( ! empty( $_POST['enable'] ) ) {
					$save_option['enable'] = "1";
				} else {
					$save_option['enable'] = "0";
				}
				if ( ! empty( $_POST['woo_sc_multi_sc'] ) ) {
					$save_option['multi_sc'] = "1";
				} else {
					$save_option['multi_sc'] = "0";
				}
				if ( ! empty( $_POST['woo_cs_select_position'] ) ) {
					$save_option['position'] = sanitize_text_field( $_POST['woo_cs_select_position'] );
				}
				if ( ! empty( $_POST['woo_sc_btn_horizontal'] ) ) {
					$save_option['btn_horizontal'] = sanitize_text_field( $_POST['woo_sc_btn_horizontal'] );
				}
				if ( isset( $_POST['woo_sc_btn_vertical'] ) && $_POST['woo_sc_btn_vertical'] <= 95 && $_POST['woo_sc_btn_vertical'] >= 0 ) {
					$save_option['btn_vertical'] = sanitize_text_field( $_POST['woo_sc_btn_vertical'] );
				}
				if ( ! empty( $_POST['woo_sc_name'] ) ) {
					$save_option['size_chart_name'] = sanitize_text_field( $_POST['woo_sc_name'] );
				}
				if ( ! empty( $_POST['woo_sc_button_type'] ) ) {
					$save_option['button_type'] = sanitize_text_field( $_POST['woo_sc_button_type'] );
				}
				if ( ! empty( $_POST['woo_sc_btn_color'] ) ) {
					$save_option['btn_color'] = sanitize_text_field( $_POST['woo_sc_btn_color'] );
				}
				if ( ! empty( $_POST['woo_sc_text_color'] ) ) {
					$save_option['text_color'] = sanitize_text_field( $_POST['woo_sc_text_color'] );
				}
				if ( isset( $_POST['woo_sc_textarea'] ) ) {
					$save_option['custom_css'] = sanitize_text_field( $_POST['woo_sc_textarea'] );
				}
				update_option( 'woo_sc_setting', $save_option );
			}
		}

		public function woo_sc_setting_page() {
			$this->get_save_option();
			$get_option = get_option( 'woo_sc_setting' );
			?>
            <div class="wrap woo_sc_space">
                <div class="woo_sc_title">
                    <h2><?php esc_html_e( 'General settings', 'product-size-chart-for-woo' ) ?></h2>
                </div>
                <form method="post" class="vi-ui form">
                    <div class="vi-ui segment woo_sc_setting_form">
						<?php wp_nonce_field( 'woo_sc_check_setting_nonce', 'woo_sc_setting_nonce' ); ?>
                        <table class="woo_sc_options_table form-table">
                            <tr>
                                <th>
                                    <div class="woo_sc_setting_th">
										<?php esc_html_e( 'Enable', 'product-size-chart-for-woo' ) ?>
                                    </div>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="checkbox"
                                               name="enable" <?php if ( isset( $get_option['enable'] ) && $get_option['enable'] == 1 ) {
											echo esc_attr( 'checked' );
										} ?>>
                                        <label></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <div class="woo_sc_setting_th">
										<?php esc_html_e( 'Size chart type', 'product-size-chart-for-woo' ) ?>
                                    </div>
                                </th>
                                <td>
                                    <select name="woo_cs_select_position" id="woo_cs_select_position"
                                            class="vi-ui dropdown setting_field">
                                        <option value="before_add_to_cart" <?php if ( isset( $get_option['position'] ) && $get_option['position'] == 'before_add_to_cart' ) {
											echo esc_attr( 'selected' );
										} ?>><?php esc_html_e( 'Before add to cart', 'product-size-chart-for-woo' ); ?>
                                        </option>
                                        <option value="after_add_to_cart" <?php echo esc_attr( isset( $get_option['position'] ) && $get_option['position'] == 'after_add_to_cart' ? 'selected' : '' ); ?>>
											<?php esc_html_e( 'After add to cart', 'product-size-chart-for-woo' ); ?>
                                        </option>
                                        <option value="pop-up" <?php if ( isset( $get_option['position'] ) && $get_option['position'] == 'pop-up' ) {
											echo esc_attr( 'selected' );
										} ?>><?php esc_html_e( 'Pop-up', 'product-size-chart-for-woo' ) ?>
                                        </option>
                                        <option value="product_tabs" <?php if ( isset( $get_option['position'] ) && $get_option['position'] == 'product_tabs' ) {
											echo esc_attr( 'selected' );
										} ?>><?php esc_html_e( 'Product tab', 'product-size-chart-for-woo' ) ?>
                                        </option>
                                        <option value="none" <?php if ( isset( $get_option['position'] ) && $get_option['position'] == 'none' ) {
											echo esc_attr( 'selected' );
										} ?>><?php esc_html_e( 'None', 'product-size-chart-for-woo' ) ?>
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="woo_sc_multi" style="display: none">
                                <th>
									<?php esc_html_e( 'Multi size charts', 'product-size-chart-for-woo' ) ?>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="checkbox"
                                               name="woo_sc_multi_sc" <?php if ( isset( $get_option['multi_sc'] ) && $get_option['multi_sc'] == 1 ) {
											echo esc_attr( 'checked' );
										} ?>>
                                        <label></label>
                                    </div>
                                    <div class="woo_sc_msg">
                                        <span><?php esc_html_e( 'Show all size charts for a category or product', 'product-size-chart-for-woo' ) ?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woo_sc_btn_popup_position" style="display: none">
                                <th>
									<?php esc_html_e( 'Horizontal', 'product-size-chart-for-woo' ); ?>
                                </th>
                                <td>
                                    <select name="woo_sc_btn_horizontal" class="vi-ui dropdown setting_field ">
                                        <option value="right" <?php if ( isset( $get_option['btn_horizontal'] ) && $get_option['btn_horizontal'] === "right" ) {
											echo esc_attr( "selected" );
										} ?>>
											<?php esc_html_e( 'Right', 'product-size-chart-for-woo' ) ?></option>
                                        <option value="left" <?php if ( isset( $get_option['btn_horizontal'] ) && $get_option['btn_horizontal'] === "left" ) {
											echo esc_attr( "selected" );
										} ?>>
											<?php esc_html_e( 'Left', 'product-size-chart-for-woo' ) ?></option>
                                    </select>
                                    <div class="woo_sc_msg">
                                        <span> <?php esc_html_e( 'Popup button position in horizontal', 'product-size-chart-for-woo' ); ?> </span>
                                    </div>

                                </td>
                            </tr>
                            <tr class="woo_sc_btn_popup_position" style="display: none">
                                <th>
									<?php esc_html_e( 'Vertical', 'product-size-chart-for-woo' ); ?>
                                </th>
                                <td>
                                    <div class="vi-ui right labeled input">
                                        <input type="number" min="0" max="100" name="woo_sc_btn_vertical"
                                               id="woo_sc_btn_vertical"
                                               value="<?php if ( isset( $get_option['btn_vertical'] ) && $get_option['btn_vertical'] !== "" && is_numeric( $get_option['btn_vertical'] ) ) {
											       echo esc_attr( $get_option['btn_vertical'] );
										       } else {
											       echo "100";
										       } ?>">
                                        <div class="vi-ui basic label">
                                            %
                                        </div>
                                    </div>
                                    <div class="woo_sc_msg">
                                        <span><?php esc_html_e( 'Popup button position in vertical', 'product-size-chart-for-woo' ); ?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woo_sc_btn_type" style="display: none">
                                <th>
                                    <div class="woo_sc_setting_th">
                                        <label><?php esc_html_e( 'Button type', 'product-size-chart-for-woo' ); ?></label>
                                    </div>
                                </th>
                                <td>
                                    <select name="woo_sc_button_type" class="vi-ui dropdown setting_field"
                                            id="woo_sc_type_btn">
                                        <option value="icon" <?php if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "icon" ) {
											echo esc_attr( "selected" );
										} ?>>
											<?php esc_html_e( 'Icon', 'product-size-chart-for-woo' ); ?></option>
                                        <option value="text" <?php if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "text" ) {
											echo esc_attr( "selected" );
										} ?>><?php esc_html_e( 'Text', 'product-size-chart-for-woo' ); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="woo_sc_sc_label" style="display: none">
                                <th>
                                    <div class="woo_sc_setting_th">
										<?php esc_html_e( 'Size chart label', 'product-size-chart-for-woo' ) ?>
                                    </div>
                                </th>
                                <td>
                                    <input type="text" name="woo_sc_name" class="vi-ui setting_field"
                                           placeholder="Size Chart"
                                           value="<?php if ( ! empty( $get_option['size_chart_name'] ) ) {
										       echo esc_attr( $get_option['size_chart_name'] );
									       } ?>">
                                    <div class="woo_sc_msg">
                                        <span> <?php esc_html_e( 'Label for size chart on front end', 'product-size-chart-for-woo' ); ?> </span>
                                    </div>
                                </td>
                            </tr>
                            <tr class="woo_sc_btn_color" style="display: none">
                                <th>
                                    <div class="woo_sc_setting_th">
										<?php esc_html_e( 'Button color', 'product-size-chart-for-woo' ); ?>
                                    </div>
                                </th>
                                <td class="woo_sc_div_btn_color">
                                    <input type="text" class="color-picker" id="woo_sc_btn_color"
                                           name="woo_sc_btn_color"
                                           autocomplete="off"
                                           value="<?php if ( ! empty( $get_option['btn_color'] ) ) {
										       echo esc_attr( $get_option['btn_color'] );
									       } else {
										       echo esc_attr( '#2185d0' );
									       } ?>"
                                           style="background-color: <?php if ( ! empty( $get_option['btn_color'] ) ) {
										       echo esc_attr( $get_option['btn_color'] );
									       } else {
										       echo esc_attr( '#2185d0' );
									       } ?>">
                                </td>
                            </tr>
                            <tr class="woo_sc_btn_color" style="display: none">
                                <th>
                                    <div class="woo_sc_setting_th">
										<?php esc_html_e( 'Text color', 'product-size-chart-for-woo' ) ?>
                                    </div>
                                </th>
                                <td>
                                    <input type="text" class="color-picker" id="woo_sc_text_color"
                                           name="woo_sc_text_color"
                                           autocomplete="off"
                                           value="<?php if ( ! empty( $get_option['text_color'] ) ) {
										       echo esc_attr( $get_option['text_color'] );
									       } else {
										       echo esc_attr( '#ffffff' );
									       } ?>"
                                           style="background-color:<?php if ( ! empty( $get_option['text_color'] ) ) {
										       echo esc_attr( $get_option['text_color'] );
									       } else {
										       echo esc_attr( '#ffffff' );
									       } ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>
									<?php esc_html_e( 'Custom CSS', 'product-size-chart-for-woo' ) ?>
                                </th>
                                <td>
                                    <textarea name="woo_sc_textarea" id="woo_sc_textarea" cols="30" rows="5"
                                              placeholder="<?php esc_html_e( 'Insert custom css here...', 'product-size-chart-for-woo' ); ?>"><?php if ( ! empty( $get_option['custom_css'] ) ) {
		                                    echo esc_attr( $get_option['custom_css'] );
	                                    } ?></textarea>
									<?php
									?>
                                </td>
                            </tr>
                        </table>
                        <div class="get_short_code" style="display: none">
                            <p><?php esc_html_e( 'Shortcode can be inserted to the content of page or post then converted to HTML on corresponding
                            page or post', 'product-size-chart-for-woo' ) ?></p>
                            <p><?php esc_html_e( 'You can take the shortcode in "All size charts" section.', 'product-size-chart-for-woo' ) ?></p>
                        </div>
                        <p>
                            <button type="submit" name="woo_sc_save_setting" id="woo_sc_btn_save_setting"
                                    class="vi-ui primary button">
                                <i class="send icon"></i><?php esc_attr_e( 'Save', 'product-size-chart-for-woo' ) ?>
                            </button>
                        </p>
                    </div>
                </form>
				<?php
				do_action( 'villatheme_support_pscw-size-chart-setting' );
				?>
            </div>
			<?php


		}
	}
}