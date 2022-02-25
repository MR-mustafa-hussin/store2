<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'PSCW_PRODUCT_SIZE_CHART_F_WOO_Function' ) ) {
	class PSCW_PRODUCT_SIZE_CHART_F_WOO_Function {
		public function product_cat_id() {
			global $post;
			//product category id
			$product_cat_id = [];
			$term_product   = get_the_terms( $post->ID, 'product_cat' );
			if ( ! empty( $term_product ) && is_array( $term_product ) ) {
				foreach ( $term_product as $term ) {
					$product_cat_id[] = $term->term_id;
				}
			}

			return $product_cat_id;
		}

		public function get_posts_categories() {
			//get all posts type Size chart
			$get_posts = get_posts(
				array(
					'post_type'      => 'pscw-size-chart',
					'posts_per_page' => - 1,
					'numberposts'    => - 1
				) );
			if ( empty( $get_posts ) || ! is_array( $get_posts ) ) {
				return false;
			} else {
				foreach ( $get_posts as $val ) {
					$sc_post_id[] = $val->ID;
				}
				foreach ( $sc_post_id as $id ) {
					$meta_box_cate = get_post_meta( $id, 'woo_sc_size_chart_data', true );
					if ( ! empty( $meta_box_cate['categories'] ) ) {
						$get_posts_categories[ $id ] = $meta_box_cate['categories'];
					}
				}
				if ( ! empty( $get_posts_categories ) && is_array( $get_posts_categories ) ) {
					return $get_posts_categories;
				} else {
					return false;
				}
			}
		}

		public function get_posts_id() {
			$sc_post_id = [];
			$get_posts  = get_posts(
				array(
					'post_type'      => 'pscw-size-chart',
					'posts_per_page' => - 1,
					'numberposts'    => - 1
				) );

			if ( ! empty( $get_posts ) && is_array( $get_posts ) ) {
				foreach ( $get_posts as $val ) {
					$sc_post_id[] = $val->ID;
				}

			}

			return $sc_post_id;

		}

		public function woo_sc_get_categories() {
			$get_categories = get_categories( array(
				'taxonomy'   => 'product_cat',
				'orderby'    => 'name',
				'hide_empty' => "0"
			) );

			return $get_categories;
		}

		public function get_products_in_cate( $assign_cate ) {
			$cate_product        = [];
			$all_product_in_post = [];
			if ( is_array( $assign_cate ) && ! empty( $assign_cate ) ) {
				foreach ( $assign_cate as $cate_id ) {
					$cate_product[] = get_posts( array(
						'post_type'   => 'product',
						'numberposts' => - 1,
						'post_status' => 'publish',
						'fields'      => 'ids',
						'tax_query'   => array(
							array(
								'taxonomy' => 'product_cat',
								'field'    => 'term_id',
								'terms'    => $cate_id,
								'operator' => 'IN',
							)
						),
					) );
				}
			}
			if ( ! empty( $cate_product ) && is_array( $cate_product ) ) {
				foreach ( $cate_product as $item ) {
					foreach ( $item as $ids ) {
						$all_product_in_post[] = $ids;

					}
				}
			}

			return $all_product_in_post;
		}

		public function content_data( $content_id ) {
			$meta_box_data = get_post_meta( $content_id, 'woo_sc_size_chart_data', true );
			$content_post  = get_post( $content_id );
			$content       = $content_post->post_content;
			ob_start();
			echo "<div class='woo_sc_data_content'>";
			echo wp_kses_post( $content );

			if ( isset( $meta_box_data['hide'] ) && $meta_box_data['hide'] !== 'hide_image' ) {
				if ( ! empty( $meta_box_data['img_link'] ) ) {
					?>
                    <div class="woo_size_chart_img sc_id_<?php echo esc_attr( $content_id ); ?>"><img
                                src="<?php echo esc_attr( $meta_box_data['img_link'] ); ?>"></div>
					<?php
				}
			}
			if ( isset( $meta_box_data['hide'] ) && $meta_box_data['hide'] !== 'hide_table' ) {
				if ( ! empty( $meta_box_data['table_array'] ) ) {
					$table = $meta_box_data['table_array'];
				}
				if ( ! empty( $table ) ) {
					$table = json_decode( $table );
					?>
                    <div class='woo_sc_table_scroll'>
                        <table class='woo_sc_view_table sc_id_<?php echo esc_attr( $content_id ); ?>'>
							<?php
							foreach ( $table as $key => $rows ) {
								?>
                                <tr>
									<?php
									foreach ( $rows as $cols ) {
										if ( $key === 0 ) {
											?>
                                            <th>
												<?php
												if ( $cols === "" ) {
													echo esc_attr( '-' );
												} else {
													echo esc_attr( $cols );
												}
												?>
                                            </th>
											<?php
										} else {
											?>
                                            <td>
												<?php
												if ( $cols === "" ) {
													echo esc_attr( '-' );
												} else {
													echo esc_attr( $cols );
												}
												?>
                                            </td>
											<?php
										}
									}
									?>
                                </tr>
								<?php
							}
							?>
                        </table>
                    </div>
					<?php
				}
			}

			/*Inline Style*/
			wp_enqueue_style( 'woo_table_style' );

			$get_option     = get_option( 'woo_sc_setting' );
			$btn_horizontal = 'right:0%';
			$btn_vertical   = '0';
			if ( ! empty( $get_option['btn_horizontal'] ) ) {
				if ( $get_option['btn_horizontal'] === 'left' ) {
					$btn_horizontal = 'left:0%';
				}
				if ( $get_option['btn_horizontal'] === 'right' ) {
					$btn_horizontal = 'right:0%';
				}
			}
			if ( isset( $get_option['btn_vertical'] ) && is_numeric( $get_option['btn_vertical'] ) ) {
				$btn_vertical = esc_attr( $get_option['btn_vertical'] );
			}
			$icon_url    = esc_url( PSCW_SIZE_CHART_PLUGIN_URL . 'img/icon-size.png' );
			$button_icon = "";
			if ( ! empty( $get_option['button_type'] ) && $get_option['button_type'] === "icon" ) {
				$button_icon = "div.woo_sc_btn_span span.woo_sc_size_icon {
                background: url({$icon_url}) no-repeat;
                background-size: contain;
                }";
			}

			/*Button color*/
			$btn_color  = "#2185d0";
			$text_color = "#ffffff";
			if ( ! empty( $get_option['btn_color'] ) ) {
				$btn_color = esc_attr( $get_option['btn_color'] );
			}
			if ( ! empty( $get_option['btn_color'] ) ) {
				$text_color = esc_attr( $get_option['text_color'] );
			}
			/*Custom CSS*/
			$custom_css = "";
			if ( ! empty( $get_option['custom_css'] ) ) {
				$custom_css = esc_attr( $get_option['custom_css'] );
			};

			$css = "#woo_sc_show_popup{top:{$btn_vertical}%;{$btn_horizontal}}
				.woo_sc_data_content{clear:both; margin:15px auto;}
				.woo_sc_view_table.sc_id_{$content_id} {
				border:none;
				border-collapse: collapse;
				margin:5px 0px;
				}
				{$button_icon}
				.woo_sc_call_popup{background-color:{$btn_color};color:{$text_color}}
				.woo_sc_view_table.sc_id_{$content_id} td, .woo_sc_view_table.sc_id_{$content_id} th{
				text-align:center;
				border-collapse: collapse;
				padding: 10px ;
				min-height:40px;
				min-width:40px;
				vertical-align:middle}
				.woo_sc_view_table.sc_id_{$content_id} tr:nth-child(even) td {background-color:{$meta_box_data['odd_rows_color']};color:{$meta_box_data['odd_rows_text_color']}}
				.woo_sc_view_table.sc_id_{$content_id} tr:nth-child(odd) td {background-color:{$meta_box_data['even_rows_color']};color:{$meta_box_data['even_rows_text_color']}}
				.woo_sc_view_table.sc_id_{$content_id} th{
				background-color:{$meta_box_data['head_color']};
				color:{$meta_box_data['text_head_color']};
				border-top:{$meta_box_data['horizontal_width']}px {$meta_box_data['horizontal_border_style']} {$meta_box_data['border_color']};
				border-bottom:{$meta_box_data['horizontal_width']}px {$meta_box_data['horizontal_border_style']} {$meta_box_data['border_color']};
				border-left:{$meta_box_data['vertical_width']}px {$meta_box_data['vertical_border_style']} {$meta_box_data['border_color']};
				border-right:{$meta_box_data['vertical_width']}px {$meta_box_data['vertical_border_style']} {$meta_box_data['border_color']};
				}
				.woo_sc_view_table.sc_id_{$content_id} td{
				border-top:{$meta_box_data['horizontal_width']}px {$meta_box_data['horizontal_border_style']} {$meta_box_data['border_color']};
				border-bottom:{$meta_box_data['horizontal_width']}px {$meta_box_data['horizontal_border_style']} {$meta_box_data['border_color']};
				border-left:{$meta_box_data['vertical_width']}px {$meta_box_data['vertical_border_style']} {$meta_box_data['border_color']};
				border-right:{$meta_box_data['vertical_width']}px {$meta_box_data['vertical_border_style']} {$meta_box_data['border_color']};
				}
				.woo_size_chart_img.sc_id_{$content_id} img{
				width:{$meta_box_data['img_width']}%;
				}
				{$custom_css}
				";
			$css = apply_filters( 'woo_sc_filter_style', $css );
			wp_add_inline_style( 'woo_table_style', esc_html( $css ) );
			echo '</div>';
			$result = ob_get_clean();
			if ( wp_doing_ajax() ) {
				$css .= '.woo_sc_modal_content{width: 80%;}';
				echo '<style>' . esc_html( $css ) . '</style>';
			}

			return $result;
		}
	}
}

