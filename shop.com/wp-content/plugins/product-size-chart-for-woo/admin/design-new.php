<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Design' ) ) {
	class PSCW_PRODUCT_SIZE_CHART_F_WOO_Design {
		function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'woo_cs_meta_box' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'dataTransmission' ) );
			add_action( 'save_post', array( $this, 'save_data_meta_box' ), 1, 3 );
			add_action( 'wp_ajax_size_chart_search_product', array( $this, 'woo_sc_ajax_search' ) );
			add_filter( 'manage_pscw-size-chart_posts_columns', array( $this, 'custom_post_columns' ) );
			add_action( 'manage_pscw-size-chart_posts_custom_column', array( $this, 'show_custom_columns' ) );
			add_action( 'post_action_pscfw_duplicate', array( $this, 'duplicate_template' ) );
			add_filter( 'post_row_actions', array( $this, 'post_add_action' ), 20, 2 );

			$enable = get_option( 'woo_sc_setting' );
			if ( $enable['enable'] === "1" ) {
				add_shortcode( 'PSCW_SIZE_CHART', [ $this, 'short_code_content' ] );
			}
		}

		public function register_scripts() {
			$current_screen = get_current_screen()->id;
			if ( $current_screen == 'pscw-size-chart' ) {
				wp_enqueue_script( 'size_chart_transition_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/transition.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_dimmer_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/dimmer.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_modal_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/vi-modal.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );

				wp_enqueue_style( 'size_chart_modal_css',
					PSCW_SIZE_CHART_PLUGIN_URL . 'css/vi-modal.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_dimmer_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/dimmer.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_transition_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/transition.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_select2_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/select2.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_back-end_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/woo_size_chart_back_end.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'woo_size_chart_design_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/woo_size_chart_design.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_select2_js', PSCW_SIZE_CHART_PLUGIN_URL . '/js/select2.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );

				wp_enqueue_style( 'size_chart_button', PSCW_SIZE_CHART_PLUGIN_URL . 'css/button.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_label', PSCW_SIZE_CHART_PLUGIN_URL . 'css/label.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_input', PSCW_SIZE_CHART_PLUGIN_URL . 'css/input.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_form_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/form.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_dropdown_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/dropdown.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_tran_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/transition.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_form_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/form.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_dropdown_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/dropdown.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_tran_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/transition.min.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );

			}
			if ( $current_screen == 'edit-pscw-size-chart' ) {
				wp_enqueue_style( 'size_chart_back-end_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/woo_size_chart_back_end.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_script( 'size_chart_edit_woo_sc_js', PSCW_SIZE_CHART_PLUGIN_URL . 'js/woo_size_chart_edit_woo_size_chart.js', array( 'jquery' ), PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_input', PSCW_SIZE_CHART_PLUGIN_URL . 'css/input.min.css', '', PSCW_SIZE_CHART_VERSION );
				wp_enqueue_style( 'size_chart_icon_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/icon.min.css', '', PSCW_SIZE_CHART_VERSION );

			}
		}

		public function duplicate_template() {
			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}
			$dup_id = ! empty( $_GET['id'] ) ? sanitize_text_field( $_GET['id'] ) : '';
			if ( $dup_id ) {
				$current_post = get_post( $dup_id );

				$args          = [
					'post_title' => 'Copy of ' . $current_post->post_title,
					'post_type'  => $current_post->post_type,
				];
				$new_id        = wp_insert_post( $args );
				$dup_post_meta = get_post_meta( $dup_id, 'woo_sc_size_chart_data', true );
				update_post_meta( $new_id, 'woo_sc_size_chart_data', $dup_post_meta );
				wp_safe_redirect( admin_url( "post.php?post={$new_id}&action=edit" ) );
				exit;
			}
		}

		public function post_add_action( $actions, $post ) {
			if ( $post->post_type == "pscw-size-chart" ) {
				$href    = admin_url( "post.php?action=pscfw_duplicate&id={$post->ID}" );
				$actions = [ 'pscfw_duplicate' => "<a href='{$href}'>" . esc_html__( 'Duplicate', 'product-size-chart-for-woo' ) . "</a>" ] + $actions;
			}

			return $actions;
		}

		public function woo_cs_meta_box() {
			$current_screen = get_current_screen()->id;
			if ( $current_screen == 'pscw-size-chart' ) {
				add_meta_box( 'design_size_chart', esc_html__( 'Design', 'product-size-chart-for-woo' ), array(
					$this,
					'woo_sc_size_chart_design'
				), 'pscw-size-chart', 'normal' );
				add_meta_box( 'img_size_chart', esc_html__( 'Size chart image ', 'product-size-chart-for-woo' ), array(
					$this,
					'woo_sc_img_upload_fun'
				), 'pscw-size-chart', 'side' );
				add_meta_box( 'assign_categories', esc_html__( 'Assign', 'product-size-chart-for-woo' ), array(
					$this,
					'assign_categories_callBack'
				), 'pscw-size-chart', 'side' );
			}
		}

		public function woo_sc_img_upload_fun( $post_id ) {
			$meta_box_data = get_post_meta( $post_id->ID, 'woo_sc_size_chart_data', true );
			?>
            <div class="woo_sc_img_container">
                <div class="woo_sc_img">
                    <div class="woo_sc_img_upload">
						<?php if ( ! empty( $meta_box_data['img_link'] ) ) {
							?>
                            <img class="image_Uploaded" style="width:95%;display:inline-block;"
                                 src="<?php echo esc_attr( $meta_box_data['img_link'] ); ?> ">
							<?php
						} else {
							?><img class="image_Default" style="width:95%;display:inline-block;"
                                   src="<?php echo PSCW_SIZE_CHART_PLUGIN_URL . 'img/Photos-new-icon.png'; ?>">
							<?php
						} ?>
                    </div>
                </div>
                <div class="woo_sc_width_img_margin" style="display: none">
					<?php esc_html_e( 'Width:', 'product-size-chart-for-woo' ) ?>
                    <div class="vi-ui right labeled input">
                        <input type="number" placeholder="Enter percent..." id="woo_sc_width_img"
                               name="woo_sc_width_img"
                               min="0" max="100"
                               value="<?php if ( ! empty( $meta_box_data['img_width'] ) ) {
							       echo esc_attr( $meta_box_data['img_width'] );
						       } ?>">
                        <div class="vi-ui basic label">
                            %
                        </div>
                    </div>
                </div>
                <div class="woo_sc_img_buttom">
                    <div class="woo_sc_up_image">
                        <a name="woo_sc_up_image" id="woo_sc_up_image"
                           class="vi-ui small green button"><?php esc_html_e( 'Select Image', 'product-size-chart-for-woo' ) ?></a>
                    </div>
                    <div class="remove_image_button">
                        <a type="button" class="remove_image_button vi-ui small red button" id="remove_image_button"
                           style="display:none;"><?php esc_html_e( 'Remove image', 'product-size-chart-for-woo' ) ?></a>
                    </div>
                </div>
            </div>
			<?php
		}

		public function woo_sc_size_chart_design( $post_id ) {
			wp_nonce_field( 'woo_sc_check_nonce', 'woo_sc_nonce' );

			$meta_box_data = get_post_meta( $post_id->ID, 'woo_sc_size_chart_data', true );
			?>
            <h3><?php esc_html_e( 'Tools', 'product-size-chart-for-woo' ) ?></h3>
            <input
                    type="hidden"
                    name="prevent_delete_meta_movetotrash"
                    id="prevent_delete_meta_movetotrash"
                    value="<?php
					echo wp_create_nonce( plugin_basename( __FILE__ ) . $post_id->ID );
					?>">
            <table class="woo_sc_table_tools vi-ui form">
                <tr>
                    <td>
						<?php esc_html_e( 'Template', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <select name="woo_sc_template" id="woo_sc_template" class="vi-ui dropdown woo_sc_table_tools">
                            <option value="select"><?php esc_html_e( 'Select template', 'product-size-chart-for-woo' ) ?></option>
                            <option value="tem_black" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_black' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Black', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_blue" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_blue' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Blue', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_red" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_red' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Red', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_teal" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_teal' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Teal', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_orange" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_orange' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Orange', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_oliver" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_oliver' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Oliver', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_green" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_green' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Green', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_purple" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_purple' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Purple', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_pink" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_pink' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Pink', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="tem_yellow" <?php if ( ! empty( $meta_box_data['template'] ) && $meta_box_data['template'] == 'tem_yellow' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Yellow', 'product-size-chart-for-woo' ) ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Hide on size chart', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <select name="woo_sc_hide" id="woo_sc_hide" class="vi-ui dropdown woo_sc_table_tools">
                            <option value="none"><?php esc_html_e( 'None', 'product-size-chart-for-woo' ) ?></option>
                            <option value="hide_table" <?php if ( ! empty( $meta_box_data['hide'] ) && $meta_box_data['hide'] === 'hide_table' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Table', 'product-size-chart-for-woo' ) ?></option>
                            <option value="hide_image" <?php if ( ! empty( $meta_box_data['hide'] ) && $meta_box_data['hide'] === 'hide_image' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Image', 'product-size-chart-for-woo' ) ?></option>
                            <option value="hide_all" <?php if ( ! empty( $meta_box_data['hide'] ) && $meta_box_data['hide'] === 'hide_all' ) {
								echo esc_attr( 'selected' );
							} ?>><?php esc_html_e( 'Hide all', 'product-size-chart-for-woo' ) ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Rows', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td class="addrowsnumber">
                        <div class="woo_sc_add_del_center">
                            <input type="number" min="1" max="500" name="woo_sc_number_rows" id="woo_sc_number_rows"
                                   class="woo_sc_tools_input quick_add"
                                   value="1">
                            <button type="button" class="vi-ui green small button add_btn quick_add"
                                    id="woo_sc_quick_add"
                            "><?php esc_html_e( 'Add', 'product-size-chart-for-woo' ) ?>
                            </button>

                            <button type="button" class="vi-ui red small button del_btn quick_add"
                                    id="woo_sc_quick_del"><?php esc_html_e( 'Delete', 'product-size-chart-for-woo' ) ?></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Columns', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td class="addcoloumsnumber">
                        <div class="woo_sc_add_del_center">
                            <input type="number" min="1" max="500" name="woo_sc_number_cols" id="woo_sc_number_cols"
                                   class="woo_sc_tools_input quick_add"
                                   value="1">

                            <button type="button" class="vi-ui green small button  add_btn quick_add"
                                    id="woo_sc_quick_add_col">
								<?php esc_html_e( 'Add', 'product-size-chart-for-woo' ) ?>
                            </button>
                            <button type="button" class="vi-ui red small button del_btn quick_add"
                                    id="woo_sc_quick_del_col"><?php esc_html_e( 'Delete', 'product-size-chart-for-woo' ) ?></button>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="woo_sc_create_table">
				<?php
				if ( empty( $meta_box_data['table_array'] ) ) {
					?>
                    <div class="woo_sc_table_scroll">
                        <table class="woo_sc_table_design">
                            <tbody class="bodyTable">
                            <tr class="woo_sc_col_header">
                                <td>
                                    <div class="woo_sc_div_tablebutton_cols">
                                        <button type="button" class="tableButton addcol">+</button>
                                        <button type="button" class="tableButton delcol" disabled>-</button>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="tr">
                                <td class="td">
                                    <div class="woo_sc_table_input">
                                        <input type="text"
                                               placeholder='<?php esc_html_e( 'Enter text..', 'product-size-chart-for-woo' ) ?>'
                                               class="input" name='inputTable' autocomplete="off">
                                    </div>
                                </td>
                                <td class="woo_sc_row_header">
                                    <div class="woo_sc_div_tablebutton_rows">
                                        <button type="button" class="tableButton addrows">+</button>
                                        <button type="button" class="tableButton delrows" disabled>-</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
					<?php
				} else {
					$arr = json_decode( $meta_box_data['table_array'] );
					?>
                    <div class="woo_sc_table_scroll">
                        <table class='woo_sc_table_design'>
                            <tbody class='bodyTable'>
                            <tr class='woo_sc_col_header'>
								<?php
								foreach ( $arr[0] as $value ) {
									?>
                                    <td>
                                        <div class="woo_sc_div_tablebutton_cols">
                                            <button type="button" class="tableButton addcol">+</button>
                                            <button type="button" class="tableButton delcol" disabled>-</button>
                                        </div>
                                    </td>
									<?php
								}
								?>
                                <td></td>
                            </tr>
							<?php
							foreach ( $arr as $key => $val ) {
								?>
                                <tr class="tr">
									<?php
									foreach ( $val as $value ) {
										$value = esc_attr( $value );
										$pl    = esc_html__( 'Enter text..', 'product-size-chart-for-woo' ); ?>
                                        <td class='td'>
                                            <div class="woo_sc_table_input">
                                                <input type='text'
                                                       placeholder='<?php echo $pl; ?>'
                                                       class='input' name='inputTable'
                                                       value='<?php echo $value; ?>'
                                                       autocomplete='off'>
                                            </div>
                                        </td>
										<?php
									}
									?>
                                    <td class="woo_sc_row_header">
                                        <div class="woo_sc_div_tablebutton_rows">
                                            <button type="button" class="tableButton addrows">+</button>
                                            <button type="button" class="tableButton delrows" disabled>-</button>
                                        </div>
                                    </td>
                                </tr>
								<?php
							}
							?>
                            </tbody>
                        </table>
                    </div>
					<?php
				};
				?>
                <div class="vi-ui modal">
                    <div class="header">
						<?php esc_html_e( 'Preview', 'product-size-chart-for-woo' ) ?>
                    </div>
                    <div class="woo_sc_padding_modal">
                        <div class="woo_sc_content">
                            <div class="woo_sc_modal_backend_content content">
                            </div>
                            <div class="woo_sc_popup_img">
                            </div>
                            <div class="description">
                                <div class="printTable woo_sc_table_scroll">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <div class="vi-ui primary button woo_sc_close">
							<?php esc_html_e( 'OK', 'product-size-chart-for-woo' ) ?></div>
                    </div>
                </div>
            </div>
            <table class="import_reset">
                <tr>
                    <td>
                        <input type="file" id="sc_import_csv" style="display: none">
                        <button type="button" id="sc_import_csv_btn" class="vi-ui green small button">
							<?php esc_html_e( 'Import CSV', 'product-size-chart-for-woo' ) ?>
                        </button>
                    </td>
                    <td>
                        <button type="button" class="vi-ui red small button sc_btn_reset">
							<?php esc_html_e( 'Reset', 'product-size-chart-for-woo' ) ?></button>
                    </td>
                </tr>
            </table>
            <table class="woo_sc_style_table vi-ui form-table">
                <tr class="woo_sc_table_header">
                    <td>
                        <h3><?php esc_html_e( 'Table Color', 'product-size-chart-for-woo' ) ?></h3>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Header background', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_head_color" id="woo_sc_head_color"
                               value="<?php if ( ! empty( $meta_box_data['head_color'] ) ) {
							       echo esc_attr( $meta_box_data['head_color'] );
						       } else {
							       echo esc_attr( '#2185d0' );
						       }
						       ?>">
                    </td>
                    <td>
						<?php esc_html_e( 'Text header', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_text_head_color"
                               id="woo_sc_text_head_color"
                               value="<?php if ( ! empty( $meta_box_data['text_head_color'] ) ) {
							       echo esc_attr( $meta_box_data['text_head_color'] );
						       } else {
							       echo esc_attr( "#ffffff" );
						       } ?>">
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Even row background', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_even_rows_color"
                               id="woo_sc_even_rows_color"
                               value="<?php if ( ! empty( $meta_box_data['even_rows_color'] ) ) {
							       echo esc_attr( $meta_box_data['even_rows_color'] );
						       } else {
							       echo esc_attr( '#cceafc' );
						       } ?>">
                    </td>
                    <td>
						<?php esc_html_e( 'Even row text', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_even_rows_text_color"
                               id="woo_sc_even_rows_text_color"
                               value="<?php if ( ! empty( $meta_box_data['even_rows_text_color'] ) ) {
							       echo esc_attr( $meta_box_data['even_rows_text_color'] );
						       } else {
							       echo esc_attr( '#000000' );
						       }
						       ?>">
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Odd row background', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_odd_rows_color"
                               id="woo_sc_odd_rows_color"
                               value="<?php if ( ! empty( $meta_box_data['odd_rows_color'] ) ) {
							       echo esc_attr( $meta_box_data['odd_rows_color'] );
						       } else {
							       echo esc_attr( '#ffffff' );
						       } ?>">
                    </td>
                    <td>
						<?php esc_html_e( 'Odd row text', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="woo_sc_odd_rows_text_color"
                               id="woo_sc_odd_rows_text_color"
                               value="<?php if ( ! empty( $meta_box_data['odd_rows_text_color'] ) ) {
							       echo esc_attr( $meta_box_data['odd_rows_text_color'] );
						       } else {
							       echo esc_attr( '#000000' );
						       } ?>">
                    </td>
                </tr>
                <tr class="woo_sc_table_header">
                    <td>
                        <h3><?php esc_html_e( 'Border', 'product-size-chart-for-woo' ) ?></h3>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Horizontal width', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <div class="vi-ui input">
                            <input type="number" name="woo_sc_horizontal_width" class="border_field"
                                   id="woo_sc_horizontal_width" min="0"
                                   max="100"
                                   value="<?php if ( isset( $meta_box_data['horizontal_width'] ) && $meta_box_data['horizontal_width'] !== "" ) {
								       echo esc_attr( $meta_box_data['horizontal_width'] );
							       } else {
								       echo 1;
							       } ?>">
                        </div>
                    </td>
                    <td>
						<?php esc_html_e( 'Horizontal border style', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <select name="woo_sc_horizontal_border_style" id="woo_sc_horizontal_border_style"
                                class="vi-ui dropdown border_field">
                            <option value="solid"
								<?php if ( ! empty( $meta_box_data['horizontal_border_style'] ) && $meta_box_data['horizontal_border_style'] == 'solid' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Solid', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="dashed"
								<?php if ( ! empty( $meta_box_data['horizontal_border_style'] ) && $meta_box_data['horizontal_border_style'] == 'dashed' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Dashed', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="dotted"
								<?php if ( ! empty( $meta_box_data['horizontal_border_style'] ) && $meta_box_data['horizontal_border_style'] == 'dotted' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Dotted', 'product-size-chart-for-woo' ) ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Vertical width', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <div class="vi-ui input">
                            <input type="number" name="woo_sc_vertical_width" class="border_field"
                                   id="woo_sc_vertical_width" min="0"
                                   max="100"
                                   value="<?php if ( isset( $meta_box_data['vertical_width'] ) && $meta_box_data['vertical_width'] != "" ) {
								       echo esc_attr( $meta_box_data['vertical_width'] );
							       } else {
								       echo 1;
							       } ?>">
                        </div>
                    </td>
                    <td>
						<?php esc_html_e( 'Vertical border style', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <select name="woo_sc_vertical_border_style" id="woo_sc_vertical_border_style"
                                class="vi-ui dropdown border_field">
                            <option value="solid"
								<?php if ( ! empty( $meta_box_data['vertical_border_style'] ) && $meta_box_data['vertical_border_style'] == 'solid' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Solid', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="dashed"
								<?php if ( ! empty( $meta_box_data['vertical_border_style'] ) && $meta_box_data['vertical_border_style'] == 'dashed' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Dashed', 'product-size-chart-for-woo' ) ?>
                            </option>
                            <option value="dotted"
								<?php if ( ! empty( $meta_box_data['vertical_border_style'] ) && $meta_box_data['vertical_border_style'] == 'dotted' ) {
									echo esc_attr( 'selected' );
								} ?>><?php esc_html_e( 'Dotted', 'product-size-chart-for-woo' ) ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
						<?php esc_html_e( 'Border color', 'product-size-chart-for-woo' ) ?>
                    </td>
                    <td>
                        <input type="text" class="color_picker" name="border_color" id="woo_sc_border_color"
                               value="<?php if ( ! empty( $meta_box_data['border_color'] ) ) {
							       echo esc_attr( $meta_box_data['border_color'] );
						       } else {
							       echo esc_attr( "#cccccc" );
						       } ?>">
                    </td>
                </tr>
            </table>
            <button type="button" name="Preview" class="vi-ui primary button previewBtn" id="woo_sc_preview_btn">
				<?php esc_html_e( 'PREVIEW', 'product-size-chart-for-woo' ) ?></button>
			<?php
		}

		public function assign_categories_callBack( $post_id ) {
			$meta_box_data = get_post_meta( $post_id->ID, 'woo_sc_size_chart_data', true );
			if ( ! empty( $meta_box_data['categories'] ) ) {
				$loadSaveCategories = $meta_box_data['categories'];
			}
			$cat_tree = $this->data_tree();
			?>
            <div class="assign_cate_list">
                <div class="woo_sc_assign_product">
                    <div class="woo_sc_assign_product_text"><?php esc_html_e( 'Products', 'product-size-chart-for-woo' ) ?></div>
                    <div class="woo_sc_select_product">
                        <select class="sc_input_search_product" id="sc_input_search_product_id"
                                name="sc_input_search_product[]"
                                multiple="multiple">
							<?php
							$meta_box_data = get_post_meta( $post_id->ID, 'woo_sc_size_chart_data', true );
							if ( ! empty( $meta_box_data['search_product'] ) ) {
								$sc_load_option_product_search = $meta_box_data['search_product'];
								if ( ! empty( $sc_load_option_product_search ) ) {
									foreach ( $sc_load_option_product_search as $value ) {
										$get_product         = wc_get_product( $value );
										$assign_product_name = $get_product->get_name();
										?>
                                        <option selected
                                                value='<?php echo esc_attr( $value ); ?>'><?php echo esc_attr( $assign_product_name ); ?></option>
										<?php
									}
								}
							} ?>
                        </select>
                    </div>

                    <div class="woo_sc_label">
		                                <span><?php
			                                esc_html_e( 'Prioritize to display when product has multi size charts', 'product-size-chart-for-woo' );
			                                ?></span>
                    </div>
                </div>
                <div class="woo_sc_assign_categories_label">
					<?php
					esc_html_e( 'Categories', 'product-size-chart-for-woo' );
					?>
                </div>
                <div class="assign_cate_scroll">
                    <ul>
						<?php
						foreach ( $cat_tree as $item ) {
							if ( $item->data_name === 'child_0' ) {
								?>
                                <li class="item_level_<?php echo esc_attr( $item->level ) . ' ' . esc_attr( $item->data_name ); ?>"
                                    style="margin-left:<?php echo ( $item->level = $item->level * 20 ) . 'px' ?> ">
                                    <input type="checkbox" value="<?php echo esc_attr( $item->term_id ) ?>"
                                           class="check_box_cate"
                                           name="check_box_select_cate[]"
                                           id="<?php echo esc_attr( $item->term_id ) ?>"
										<?php
										if ( ! empty( $loadSaveCategories ) ) {
											foreach ( $loadSaveCategories as $category ) {
												if ( ! empty( $loadSaveCategories ) ) {
													if ( $category == $item->term_id ) {
														echo esc_attr( 'checked' );
													}
												}
											}
										}
										?>>
                                    <label class="size_chart_header"
                                           for="<?php echo esc_attr( $item->term_id ) ?>">
										<?php echo esc_attr( $item->name ); ?></label>
                                </li>
								<?php
							}
							if ( $item->data_name === 'child_1' ) {
								?>
                                <li class="item_level_<?php echo esc_attr( $item->level ) . ' ' . esc_attr( $item->data_name ); ?>"
                                    style="margin-left:<?php echo esc_attr( $item->level ) * 20; ?>px">
                                    <input type="checkbox"
                                           data-parent="<?php echo esc_attr( $item->parent ); ?>"
                                           value="<?php echo esc_attr( $item->term_id ); ?>"
                                           class="check_box_cate"
                                           name="check_box_select_cate[]"
                                           id="<?php echo esc_attr( $item->term_id ) ?>"
										<?php
										if ( ! empty( $loadSaveCategories ) ) {
											foreach ( $loadSaveCategories as $category ) {
												if ( ! empty( $loadSaveCategories ) ) {
													if ( $category == $item->term_id ) {
														echo esc_attr( 'checked' );
													}
												}
											}
										}
										?>
                                    >
                                    <label for="<?php echo esc_attr( $item->term_id ); ?>"> <?php echo esc_attr( $item->name ); ?> </label>
                                </li>
								<?php
							}
						}
						?>
                    </ul>
                </div>
            </div>
			<?php
		}

		public function data_tree( $parent_id = 0, $level = 0, $data_name = 'child_0' ) {
			$get_categories = get_categories( array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'name',
				'hide_empty' => 0
			) );
			$result         = [];
			if ( ! empty( $get_categories ) && is_array( $get_categories ) ) {
				foreach ( $get_categories as $key => $item ) {
					if ( $item->parent == $parent_id ) {
						$item->level     = $level;
						$item->data_name = $data_name;
						$result[]        = $item;
						unset( $get_categories[ $key ] );
						$child  = $this->data_tree( $item->term_id, $level + 1, 'child_1' );
						$result = array_merge( $result, $child );
					}
				}
			}

			return $result;
		}

		public function woo_sc_ajax_search() {
			$search_key = sanitize_text_field( $_POST['key_search'] );
			$args       = array(
				'post_type' => 'product',
				'order'     => 'name',
				's'         => $search_key
			);
			$query      = new WP_Query ( $args );
			$result     = $query->get_posts();
			foreach ( $result as $val ) {
				$product_name_id[ $val->ID ] = $val->post_title;
			}
			if ( ! empty( $product_name_id ) ) {
				foreach ( $product_name_id as $id => $name ) {
					$a['id']            = $id;
					$a['text']          = $name;
					$b[]                = $a;
					$results['results'] = $b;
				}
				wp_send_json( $results );
			}
			die;
		}

		function save_data_meta_box( $post_id ) {
			if ( ! current_user_can( "edit_post", $post_id ) ) {
				return $post_id;
			}
			if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
				return $post_id;
			}
			if ( isset( $_POST['woo_sc_nonce'] ) ) {
				$woo_sc_nonce = sanitize_text_field( $_POST['woo_sc_nonce'] );
				if ( ! isset( $woo_sc_nonce ) ) {
					return;
				}
				if ( ! wp_verify_nonce( $woo_sc_nonce, 'woo_sc_check_nonce' ) ) {
					return;
				}
			}
			if ( ! wp_verify_nonce( $_POST['prevent_delete_meta_movetotrash'], plugin_basename( __FILE__ ) . $post_id ) ) {
				return $post_id;
			}

			$meta_box_select_categories_value       = "";
			$meta_box_img_link_value                = "";
			$meta_box_template_value                = "";
			$meta_box_head_color_value              = "";
			$meta_box_text_head_color_value         = "";
			$meta_box_even_rows_color_value         = "";
			$meta_box_even_rows_text_color_value    = "";
			$meta_box_odd_rows_color_value          = "";
			$meta_box_odd_rows_text_color_value     = "";
			$meta_box_horizontal_width_value        = "";
			$meta_box_vertical_width_value          = "";
			$meta_box_horizontal_border_style_value = "";
			$meta_box_vertical_border_style_value   = "";
			$meta_box_border_color_value            = "";
			$meta_box_table_array_value             = "";
			$meta_box_search_product                = "";
			$meta_box_hide                          = "";
			$meta_box_img_width                     = "";

			if ( isset( $_POST['check_box_select_cate'] ) ) {
				$meta_box_select_categories_value = array_map( 'sanitize_text_field', $_POST['check_box_select_cate'] );
			}
			if ( isset( $_POST['woo_sc_template'] ) ) {
				$meta_box_template_value = sanitize_text_field( $_POST['woo_sc_template'] );
			}
			if ( isset( $_POST["woo_sc_head_color"] ) ) {
				$meta_box_head_color_value = sanitize_text_field( $_POST["woo_sc_head_color"] );
			}
			if ( isset( $_POST["woo_sc_text_head_color"] ) ) {
				$meta_box_text_head_color_value = sanitize_text_field( $_POST["woo_sc_text_head_color"] );
			}
			if ( isset( $_POST["woo_sc_even_rows_color"] ) ) {
				$meta_box_even_rows_color_value = sanitize_text_field( $_POST["woo_sc_even_rows_color"] );
			}
			if ( isset( $_POST["woo_sc_odd_rows_color"] ) ) {
				$meta_box_odd_rows_color_value = sanitize_text_field( $_POST["woo_sc_odd_rows_color"] );
			}
			if ( isset( $_POST["woo_sc_odd_rows_text_color"] ) ) {
				$meta_box_odd_rows_text_color_value = sanitize_text_field( $_POST["woo_sc_odd_rows_text_color"] );
			}
			if ( isset( $_POST["woo_sc_even_rows_text_color"] ) ) {
				$meta_box_even_rows_text_color_value = sanitize_text_field( $_POST["woo_sc_even_rows_text_color"] );
			}
			//border style
			if ( isset( $_POST["woo_sc_horizontal_width"] ) && is_numeric( $_POST["woo_sc_horizontal_width"] ) ) {
				$meta_box_horizontal_width_value = sanitize_text_field( $_POST["woo_sc_horizontal_width"] );
			}
			if ( isset( $_POST["woo_sc_vertical_width"] ) && is_numeric( $_POST["woo_sc_vertical_width"] ) ) {
				$meta_box_vertical_width_value = sanitize_text_field( $_POST["woo_sc_vertical_width"] );
			}
			if ( isset( $_POST["woo_sc_horizontal_border_style"] ) ) {
				$meta_box_horizontal_border_style_value = sanitize_text_field( $_POST["woo_sc_horizontal_border_style"] );
			}
			if ( isset( $_POST["woo_sc_vertical_border_style"] ) ) {
				$meta_box_vertical_border_style_value = sanitize_text_field( $_POST["woo_sc_vertical_border_style"] );
			}
			if ( isset( $_POST["border_color"] ) ) {
				$meta_box_border_color_value = sanitize_text_field( $_POST["border_color"] );
			}
			if ( isset( $_POST["tableArray"] ) ) {
				$meta_box_table_array_value = sanitize_text_field( $_POST["tableArray"] );
			}
			if ( isset( $_POST["imgLink"] ) ) {
				$meta_box_img_link_value = sanitize_text_field( $_POST["imgLink"] );
			}
			if ( isset( $_POST['sc_input_search_product'] ) ) {
				$meta_box_search_product = array_map( 'sanitize_text_field', $_POST['sc_input_search_product'] );
			}
			if ( isset( $_POST['woo_sc_hide'] ) ) {
				$meta_box_hide = sanitize_text_field( $_POST['woo_sc_hide'] );
			}
			if ( ! empty( $_POST['woo_sc_width_img'] ) && $_POST['woo_sc_width_img'] > 0 && $_POST['woo_sc_width_img'] <= 100 ) {
				$meta_box_img_width = sanitize_text_field( $_POST['woo_sc_width_img'] );
			}

			$meta_box_data = [
				'categories'              => $meta_box_select_categories_value,
				'img_link'                => $meta_box_img_link_value,
				'template'                => $meta_box_template_value,
				'head_color'              => $meta_box_head_color_value,
				'text_head_color'         => $meta_box_text_head_color_value,
				'even_rows_color'         => $meta_box_even_rows_color_value,
				'even_rows_text_color'    => $meta_box_even_rows_text_color_value,
				'odd_rows_color'          => $meta_box_odd_rows_color_value,
				'odd_rows_text_color'     => $meta_box_odd_rows_text_color_value,
				'horizontal_width'        => $meta_box_horizontal_width_value,
				'vertical_width'          => $meta_box_vertical_width_value,
				'horizontal_border_style' => $meta_box_horizontal_border_style_value,
				'vertical_border_style'   => $meta_box_vertical_border_style_value,
				'border_color'            => $meta_box_border_color_value,
				'table_array'             => $meta_box_table_array_value,
				'search_product'          => $meta_box_search_product,
				'hide'                    => $meta_box_hide,
				'img_width'               => $meta_box_img_width

			];
			update_post_meta( $post_id, "woo_sc_size_chart_data", $meta_box_data );
		}

		public function dataTransmission() {
			$tran_arr = array(
				'link'      => PSCW_SIZE_CHART_PLUGIN_URL . 'img/Photos-new-icon.png',
				'adminAjax' => admin_url( "admin-ajax.php" )
			);
			wp_localize_script( 'woo_size_chart_design_js', 'tran_link', $tran_arr );
		}

		//add custom columns for short code
		public function custom_post_columns( $columns ) {
			$columns['assign_cate']    = esc_html( 'Assign Categories' );
			$columns['assign_product'] = esc_html( 'Assign Products' );
			$columns['short-code']     = esc_html( 'Short Code' );
			unset( $columns['date'] );

			return $columns;
		}

		public function show_custom_columns( $name ) {
			global $post;
			$this->woo_sc_function = new PSCW_PRODUCT_SIZE_CHART_F_WOO_Function();
			$categories            = [];
			$categories            = $this->woo_sc_function->woo_sc_get_categories();
			$cate_name             = "";
			$get_post_meta         = get_post_meta( $post->ID, 'woo_sc_size_chart_data' );
			$categoties_name       = [];
			if ( ! empty( $get_post_meta ) ) {
				foreach ( $get_post_meta as $key => $value ) {
					if ( ! empty( $value['categories'] ) ) {
						foreach ( $value['categories'] as $k => $v ) {
							if ( ! empty( $v ) ) {
								if ( ! empty( $categories ) ) {
									foreach ( $categories as $item ) {
										if ( $v == $item->term_id ) {
											$categoties_name[] = $item->name;
										}
										$cate_name = implode( ', ', $categoties_name );
									}
								} else {
									$cate_name = 'null';
								}
							}
						}
					}
					$product_id       = [];
					$product_name     = "";
					$product          = [];
					$get_product_name = [];
					$product_id       = $value['search_product'];
					if ( ! empty( $product_id ) && is_array( $product_id ) ) {
						foreach ( $product_id as $id ) {
							$product            = wc_get_product( $id );
							$get_product_name[] = $product->get_name();
						}
						$product_name = implode( ', ', $get_product_name );
					}
				}
			}

			switch ( $name ) {
				case 'assign_cate':
					echo esc_html( $cate_name );
					break;
				case 'assign_product';
					echo esc_html( $product_name );
					break;
				case 'short-code':
					?>
                    <div class="vi-ui icon input">
                        <input type="text" class="woo_sc_short_code" readonly
                               value="[PSCW_SIZE_CHART ID=<?php echo "'" . esc_attr( $post->ID ) . "'"; ?>]">
                        <i class="copy icon"></i>
                        <span class="woo_sc_copied"><?php esc_html_e( 'Copied', 'product-size-chart-for-woo' ); ?></span>
                    </div>
					<?php
					break;
			}
		}

		public function short_code_content( $atts ) {
			$this->woo_sc_function = new PSCW_PRODUCT_SIZE_CHART_F_WOO_Function();
			wp_enqueue_style( 'woo_sc_product_size_chart_css', PSCW_SIZE_CHART_PLUGIN_URL . 'css/sizechart_frontend_css.css', '', PSCW_SIZE_CHART_VERSION );
			wp_register_style( 'woo_table_style', false );
			$atts = shortcode_atts( array( 'id' => '' ), $atts );
			if ( ! $atts['id'] ) {
				return '';
			}
			$result = $this->woo_sc_function->content_data( $atts['id'] );

			return $result;
		}
	}
}