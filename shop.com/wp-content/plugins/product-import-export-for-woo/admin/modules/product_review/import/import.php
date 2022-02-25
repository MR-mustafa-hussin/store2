<?php

if (!defined('WPINC')) {
    exit;
}

class Wt_Import_Export_For_Woo_Basic_Product_Review_Import {

    public $parent_module = null;
    public $parsed_data = array();
    
    var $merge;
    var $skip_new;
    var $use_sku;
    var $cmd_type;
    var $merge_empty_cells;
    var $delete_existing;
    var $new_id = array();
    var $parent_data = '';
    var $csv_last_start = '';
    
    var $processed_posts = array();
    // Results
    var $import_results = array();
    
    public $is_comment_exist = false;    
        
    var $row;
    var $post_defaults;		// Default post data
    var $postmeta_defaults;		// default post meta
    var $postmeta_allowed;		// post meta validation

    public function __construct($parent_object) {

        $this->parent_module = $parent_object;
        
//        if (WC()->version < '2.7.0') {
//            $this->log = new WC_Logger();
//        } else {
//            $this->log = wc_get_logger();
//        }
        
        $this->post_defaults     = apply_filters('alter_review_export_fields', array(
                        'comment_ID'			=> '',
                        'comment_post_ID'		=> '',
                        'comment_author'		=> '',
                        'comment_author_url'            => '',
                        'comment_author_email'		=> '',
                        'comment_author_IP'             => '',
                        'comment_date'			=> '',
                        'comment_date_gmt'		=> '',
                        'comment_content'		=> '',
                        'comment_approved'		=> '',
                        'comment_parent'		=> '',
                        'user_id'			=> '',
                        'comment_alter_id'		=> '',
                        'rating'			=> '',
                ));
        $this->postmeta_defaults = apply_filters( 'product_reviews_csv_product_postmeta_defaults', array(
                        'rating'				=> '',
                        'verified'				=> '',
                        'title'                                 => ''
                ) );
        $this->postmeta_allowed  = apply_filters( 'product_reviews_csv_product_postmeta_allowed',array(
                                'rating'				=> array(1,2,3,4,5),
                                'verified'				=> array(0,1)
                        ));
        
    }
    
    public function hf_log_data_change($content = 'review-csv-import', $data = '') {
//        $content = 'wt-revamp-review-csv-import';
//        if (WC()->version < '2.7.0') {
//            $this->log->add($content, $data);
//        } else {
//            $context = array('source' => $content);
//            $this->log->log("debug", $data, $context);
//        }
        
        Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', $data);
    }
    
    public function prepare_data_to_import($import_data,$form_data,$batch_offset,$is_last_batch){ 
                        
        $this->merge = isset($form_data['advanced_form_data']['wt_iew_merge']) ? $form_data['advanced_form_data']['wt_iew_merge'] : 0;
        $this->skip_new = isset($form_data['advanced_form_data']['wt_iew_skip_new']) ? $form_data['advanced_form_data']['wt_iew_skip_new'] : 0;
        $this->merge_empty_cells = isset($form_data['advanced_form_data']['wt_iew_merge_empty_cells']) ? $form_data['advanced_form_data']['wt_iew_merge_empty_cells'] : 0; 
//        $this->delete_existing = !empty($form_data['advanced_form_data']['wt_iew_delete_existing']) ? 1 : 0;
        $this->use_sku = isset($form_data['advanced_form_data']['wt_iew_use_sku']) ? $form_data['advanced_form_data']['wt_iew_use_sku'] : 0;       
        
        wp_defer_term_counting(true);
        wp_defer_comment_counting(true);
        wp_suspend_cache_invalidation(true);
        
        Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Preparing for import.");
        
        $success = 0;
        $failed = 0;
        $msg = 'Product Review imported successfully.';        
        foreach ($import_data as $key => $data) { 
           $row = $batch_offset+$key+1;
           Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing item.");
           $parsed_data = $this->parse_product_review($data, $this->merge);           
           if (!is_wp_error($parsed_data)){
               Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing item.");
                $result = $this->process_product_reviews($parsed_data);                
                if(!is_wp_error($result)){
                    if($this->is_comment_exist){
                        $msg = 'Product Review updated successfully.';
                    }
                    $this->import_results[$row] = array('row'=>$row, 'message'=>$msg, 'status'=>true, 'post_id'=>$result['id']);
                    Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - ".$msg);
                    $success++;                     
                }else{
                   $this->import_results[$row] = array('row'=>$row, 'message'=>$result->get_error_message(), 'status'=>false, 'post_id'=>'');
                   Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Prosessing failed. Reason: ".$result->get_error_message());
                   $failed++;
                } 
           }else{               
               $this->import_results[$row] = array('row'=>$row, 'message'=>$parsed_data->get_error_message(), 'status'=>false, 'post_id'=>'');
               Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing failed. Reason: ".$parsed_data->get_error_message());
               $failed++; 
           }           
            unset($data, $parsed_data);            
        }
        
        $this->clean_after_import();
                  
        $import_response=array(
                'total_success'=>$success,
                'total_failed'=>$failed,
                'log_data'=>$this->import_results,
            );
        
        return $import_response;
                             
    }
    
    /**
	 * Parse product review
	 * @param  array  $item
	 * @param  integer $merge_empty_cells
	 * @return array
	 */
	public function parse_product_review( $data, $merge ) { 
            try {
                $data = apply_filters('wt_woocommerce_product_review_importer_pre_parse_data', $data); 
                $item = $data['mapping_fields'];
                foreach ($data['meta_mapping_fields'] as $value) {
                    $item = array_merge($item,$value);            
                }
                
		global $wpdb;
		$this->row++;
		$postmeta = $product_review = array();
		$post_id = ( ! empty( $item['comment_ID'] ) ) ? $item['comment_ID'] : 0;
                                                
                // Check if post exists when importing
                $found_review_id = $this->product_review_exists($post_id);
                                               
		if ( $merge ) {
			$product_review['merging'] = true;
			$this->hf_log_data_change( 'review-csv-import', sprintf('> Row %s - preparing for merge.', $this->row ) );
			
			// Required fields
			if ( ! $found_review_id )
			{
				$this->hf_log_data_change( 'review-csv-import', '> > Cannot merge without id. Importing instead.' );
				$merge = false;
			} 
			else 
			{
                               
				if ( ! $found_review_id ) 
				{
                                        if($this->skip_new){
                                            $this->hf_log_data_change( 'review-csv-import', '> > Skipping new item.' );
                                            return new WP_Error( 'parse-error',  'Skipping new item on merge.' );                                            
                                        }
					$this->hf_log_data_change( 'review-csv-import', sprintf('> > Skipped. Cannot find product reviews with ID %s. Importing instead.', $item['comment_ID']) );
					$merge = false;
                                        
				} 
				else 
				{
                                    $this->is_comment_exist = true;
					$post_id = $found_review_id;
					$this->hf_log_data_change( 'review-csv-import', sprintf('> > Found product reviews with ID %s.', $post_id) );
				}
			}
		}

		if ( ! $merge ) {                    

			$product_review['merging'] = false;
			$this->hf_log_data_change( 'review-csv-import', sprintf('> Row %s - preparing for import.', $this->row ) );
                        
                        
                        if ($found_review_id) {
                            $usr_msg = "Product review skipped. Review with $found_review_id is alredy exists";
                            $this->hf_log_data_change('review-csv-import', sprintf('> &#8220;%s&#8221;' . $usr_msg, esc_html($found_review_id)), true);
                            return new WP_Error( 'parse-error',  sprintf('> &#8220;%s&#8221;' . $usr_msg, esc_html($found_review_id)) );
                        }
                        
                        
			// Required fields                        
			if ( !isset($item['comment_content']) || $item['comment_content'] === '')
			{
				$this->hf_log_data_change( 'review-csv-import', '> > Skipped. No comment content set for new product reviews.' );
				return new WP_Error( 'parse-error',  'No comment content set for new product reviews.' );
			}
			
			if($this->use_sku == 1 && (!isset($item['product_SKU']) || $item['product_SKU'] === ''))
			{
                                $this->hf_log_data_change( 'review-csv-import', '> > Skipped. No Product SKU given, for which new comment is to be imported');
                                return new WP_Error( 'parse-error', __( 'Product SKU is empty, Skipped the review.', 'wf_csv_import_export' ) );
			}
			elseif ( $this->use_sku == 0 && ( !isset($item['comment_post_ID']) || $item['comment_post_ID'] === '') )
			{
                                $this->hf_log_data_change( 'review-csv-import','> > Skipped. No post(product) id found, for which new comment is to be imported' );
                                return new WP_Error( 'parse-error', 'No product id found, Skipped the review.', 'wf_csv_import_export' );
			}

                    if($this->skip_new){
                        $this->hf_log_data_change( 'review-csv-import', '> > Skipping new item.' );
                        return new WP_Error( 'parse-error',  'Skipping new item import.' );                                            
                    }
		}

		if($this->use_sku == 1 && $item['product_SKU'])
		{
			$temp_product_id = wc_get_product_id_by_sku( $item['product_SKU'] );
			if(! $temp_product_id)
			{
				$this->hf_log_data_change( 'review-csv-import', '> > Skipped. No Product found for given SKU, for which new comment is to be imported');
				return new WP_Error( 'parse-error', 'No Product found for given SKU, Skipped the review.');
			}
		}
		elseif($item['comment_post_ID'] )
		{
			$temp_post = get_post( $item['comment_post_ID'] );
			if(! $temp_post || $temp_post->post_type != 'product')
			{
				$this->hf_log_data_change( 'review-csv-import', '> > Skipped. No product found for given product id, for which new comment is to be imported');
				return new WP_Error( 'parse-error', 'Post is not a product, Skipped the review.' );
			}
		}
			
		$product_review['post_id'] = $post_id; 
                		
		// Get post fields
		foreach ( $this->post_defaults as $column => $default ) {
			if ( isset( $item[ $column ] ) )
				$product_review[ $column ] = $item[ $column ];
			if($column == 'comment_post_ID' && $this->use_sku == 1)
				$product_review[ $column ] = !empty($temp_product_id) ? $temp_product_id : null;
		}
                
		
		// Get custom fields
		foreach ( $this->postmeta_defaults as $column => $default ) {
		    
			if ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];
			elseif ( isset( $item[$column] ) )
				$postmeta[$column] = (string) $item[$column];

			// Check custom fields are valid
			if ( isset( $postmeta[$column] ) && isset( $this->postmeta_allowed[$column] ) && ! in_array( $postmeta[$column], $this->postmeta_allowed[$column] ) ) {
				$postmeta[$column] = $this->postmeta_defaults[$column];
			}
		}

		if ( ! $merge ) {
			// Merge post meta with defaults
			$product_review  = wp_parse_args( $product_review, $this->post_defaults );
			$postmeta = wp_parse_args( $postmeta, $this->postmeta_defaults );
		}
		
		// Put set core product postmeta into product array
		foreach ( $postmeta as $key => $value ) {
			$product_review['postmeta'][] = array( 'key' 	=> esc_attr($key), 'value' => $value );
		}

		/**
		 * Handle other columns
		 */
		foreach ( $item as $key => $value ) 
		{

			if ( empty($item['post_parent']) && $value == "" )
				continue;


			/**
			 * Handle meta: columns - import as custom fields
			 */
			elseif ( strstr( $key, 'meta:' ) ) {

				// Get meta key name
//				$meta_key = ( isset( $WF_CSV_Product_Review_Import->raw_headers[$key] ) ) ? $WF_CSV_Product_Review_Import->raw_headers[$key] : $key;
				$meta_key = trim( str_replace( 'meta:', '', $meta_key ) );
                                 if (is_object(json_decode($value))) //PIEPFW-512
                                { 
                                $value=json_decode($value,TRUE);
                                }
				// Add to postmeta array
				$product_review['postmeta'][] = array(
					'key' 	=> esc_attr( $meta_key ),
					'value' => $value
				);
			}		

		}		

		$product_review['comment_content'] = ( ! empty( $item['comment_content'] ) ) ? $item['comment_content'] : '';
		unset( $item, $postmeta );
                
		return $product_review;
            } catch (Exception $e) {
                return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
            }
	}
        
                
    /**
     * Create new posts based on import information
     */
    public function process_product_reviews($post) {  
        try {

            $processing_product_id = absint($post['comment_ID']);
            $merge = !empty($post['merging']);

            $processing_product_title = $processing_product_id;

            if (!empty($processing_product_id) && isset($this->processed_posts[$processing_product_id])) {
    //            $this->add_import_result('skipped', __('Product review already processed', 'wf_csv_import_export'), $processing_product_id);
                $this->hf_log_data_change('review-csv-import', '> Post ID already processed. Skipping.', true);
                unset($post);
                return new WP_Error( 'parse-error',  '> Product review already processed. Skipping.' );
            }

            if (!empty($post['post_status']) && $post['post_status'] == 'auto-draft') {
    //            $this->add_import_result('skipped', __('Skipping auto-draft', 'wf_csv_import_export'), $processing_product_id);
                $this->hf_log_data_change('review-csv-import', '> Skipping auto-draft.', true);
                unset($post);
                return new WP_Error( 'parse-error',  '> Skipping auto-draft.' );
            }

            $this->cmd_type = 'review';


            if ($merge && !empty($processing_product_id)) {

                // Only merge fields which are set
                $post_id = $processing_product_id;

                $this->hf_log_data_change('review-csv-import', sprintf('> Merging post ID %s.', $post_id), true);

                if (!empty($post['comment_post_ID']) || $this->merge_empty_cells) {
                    $postdata['comment_post_ID'] = $post['comment_post_ID'];
                }

                if (!empty($post['comment_author']) || $this->merge_empty_cells) {
                    $postdata['comment_author'] = $post['comment_author'];
                }
                if (!empty($post['comment_author_url']) || $this->merge_empty_cells) {
                    $postdata['comment_author_url'] = $post['comment_author_url'];
                }
                if (!empty($post['comment_date']) || $this->merge_empty_cells) {
                    $postdata['comment_date'] = date("Y-m-d H:i:s", strtotime($post['comment_date']));
                }
                if (!empty($post['comment_date_gmt']) || $this->merge_empty_cells) {
                    $postdata['comment_date_gmt'] = date("Y-m-d H:i:s", strtotime($post['comment_date_gmt']));
                }
                if (!empty($post['comment_author_email']) || $this->merge_empty_cells) {
                    $postdata['comment_author_email'] = $post['comment_author_email'];
                }
                if (!empty($post['comment_author_IP']) || $this->merge_empty_cells) {
                    $postdata['comment_author_IP'] = $post['comment_author_IP'];
                }
                if (!empty($post['comment_content']) || $this->merge_empty_cells) {
                    $postdata['comment_content'] = $post['comment_content'];
                }
                if (!empty($post['comment_approved']) || $this->merge_empty_cells) {
                    $postdata['comment_approved'] = $post['comment_approved'];
                }
                $postdata['comment_type'] = 'review';

                if (!empty($post['comment_parent']) || $this->merge_empty_cells) {
                    $postdata['comment_parent'] = $post['comment_parent'];
                }
                if (!empty($post['user_id']) || $this->merge_empty_cells) {
                    $postdata['user_id'] = $post['user_id'];
                }

                // Update product review
                if (sizeof($postdata) > 1) {
                    global $wpdb;                
                    $result = $wpdb->update('wp_comments', $postdata, array('comment_ID' => $post_id));
                    if(!$result){
                        if (!empty($post_id)) {
                            $postdata['comment_ID'] = $post_id;
                        }
                        $result = wp_update_comment($postdata);
                    }
                    if (!empty($post['rating']) || $this->merge_empty_cells)
                        update_comment_meta($post_id, 'rating', $post['rating']);
                }
            } else {                
                if ($this->csv_last_start === '') {
                    $last_cmt_id = $this->get_last_comment_id();
                    update_option('xa_rev_im_ex_last_import_csv_start_col', $last_cmt_id);
                    $this->csv_last_start = $last_cmt_id;
                }

                // Insert product review
                $this->hf_log_data_change('review-csv-import', sprintf('> Inserting %s', esc_html($processing_product_id)), true);
                
                
                $review_parent = $post['comment_parent'];
                $comment_parent_session= unserialize( get_option( 'wf_prod_review_alter_id'));
                if ($post['comment_parent']!= 0 && $post['comment_parent']!= '') {
                          $arr_index = $post['comment_parent'];
                          if (isset($comment_parent_session['wt_review_basic']) && array_key_exists($arr_index, $comment_parent_session['wt_review_basic'])) {
                              $review_parent = $comment_parent_session['wt_review_basic'][$arr_index];
                          }

                  }

                
               

                
//                if(isset($post['comment_parent']) && $post['comment_parent']!='' ){
//                    if ($post['comment_parent'] == '0') {
//                        $this->parent_data = $post['comment_parent'];
//                        $_SESSION['new_id'][$post['comment_alter_id']] = $this->get_last_comment_id();
//                    } else {
//                        if (!empty($_SESSION['new_id'][$post['comment_parent']])) {
//                            $this->parent_data = $_SESSION['new_id'][$post['comment_parent']];
//                        } else {
//                            $this->parent_data = $post['comment_parent'];
//                        }                    
//                        $_SESSION['new_id'][$post['comment_alter_id']] = $this->get_last_comment_id();
//
//                    }
//                    
//
//                    
//
////                    if ($post['comment_parent'] === '0') {
////                        $this->cmd_type = 'review';
////                    } else {
////                        $this->cmd_type = '';
////                    }
//                } else {
//                    $this->parent_data = $post['comment_parent'];
//                    $this->cmd_type = 'review';
//                }
                $comment_approved = isset( $post['comment_approved'] ) ? wc_string_to_bool($post['comment_approved']) : 0;
        
                $postdata = array(
                    // 'comment_ID' => $processing_product_id, // this will not work for wp_insert_comment
                    'comment_post_ID' => $post['comment_post_ID'],
                    'comment_date' => ( $post['comment_date'] ) ? date('Y-m-d H:i:s', strtotime($post['comment_date'])) : current_time( 'mysql' ),
                    'comment_date_gmt' => ( $post['comment_date_gmt'] ) ? date('Y-m-d H:i:s', strtotime($post['comment_date_gmt'])) : get_gmt_from_date( current_time( 'mysql' ) ),
                    'comment_author' => $post['comment_author'],
                    'comment_author_url' => $post['comment_author_url'],
                    'comment_author_email' => $post['comment_author_email'],
                    'comment_author_IP' => $post['comment_author_IP'],
                    'comment_content' => ( $post['comment_content'] ) ? $post['comment_content'] : '',
                    'comment_approved' => $comment_approved ? 1 : 0,
                    'comment_type' => $this->cmd_type,
                    'comment_parent' => $review_parent,
                    'user_id' => $post['user_id'],
                );            

                $post_id = wp_insert_comment($postdata, true);

                $comment_parent_session['wt_review_basic'][$post['comment_alter_id']] = $post_id;
                update_option('wf_prod_review_alter_id', serialize($comment_parent_session));
                unset($comment_parent_session);
                
                
                if (is_wp_error($post_id)) {

    //                $this->add_import_result('failed', __('Failed to import product review', 'wf_csv_import_export'), $processing_product_id);
                    $this->hf_log_data_change('review-csv-import', sprintf('Failed to import product review &#8220;%s&#8221;', esc_html($processing_product_title)));
                    unset($post);
                    return new WP_Error( 'parse-error',  sprintf('Failed to import product review &#8220;%s&#8221;', esc_html($processing_product_title)) );
                } else {

                    $this->hf_log_data_change('review-csv-import', sprintf('> Inserted - post ID is %s.', $post_id));
                }
            }
            unset($postdata);
            // map pre-import ID to local ID
            if (empty($processing_product_id)) {
                $processing_product_id = (int) $post_id;
            }
            $this->processed_posts[intval($processing_product_id)] = (int) $post_id;

            if (!empty($post['postmeta']) && is_array($post['postmeta'])) {
                if ($this->cmd_type === '') {
                    update_comment_meta($post_id, 'verified', $post['postmeta'][1]['value']);
                } else {
    //                update_comment_meta($post_id, 'verified', $post['postmeta'][1]['value']);
    //                update_comment_meta($post_id, 'rating', $post['postmeta'][0]['value']);
    //                update_comment_meta($post_id, 'title', $post['postmeta'][2]['value']);
                    foreach ($post['postmeta'] as $meta) {                        
                        if('' == $meta['value'] && !$this->merge_empty_cells)
                            continue;                                                   
                        update_comment_meta($post_id, $meta['key'], $meta['value']);
                    }
                }
            }

//            update_option('xa_rev_im_ex_last_import_csv_end_col', $post_id);

            if ($merge) {
    //            $this->add_import_result('merged', 'Merge successful', $post_id);
                $this->hf_log_data_change('review-csv-import', sprintf('> Finished merging post ID %s.', $post_id));
            } else {
    //            $this->add_import_result('imported', 'Import successful', $post_id);
                $this->hf_log_data_change('review-csv-import', sprintf('> Finished importing post ID %s.', $post_id));
            }            
            unset($post);
            
            return array('id'=>$post_id);
            
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }
    }
    

    public function product_review_exists($id) {
        global $wpdb;
        $posts_that_exist = $wpdb->get_var($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_ID = %d AND comment_approved != 'trash'", $id));
        if ($posts_that_exist) {
            return $posts_that_exist;
        }
        return false;
//				// Check product to merge exists
//				$db_query = $wpdb->prepare("
//						SELECT comment_ID
//						FROM $wpdb->comments
//						WHERE comment_ID = %d",$post_id);
//				$found_review_id = $wpdb->get_var($db_query); 
    }
    
    public function get_last_comment_id() {
        global $wpdb;
//        $query = "SELECT MAX(comment_ID) FROM $wpdb->comments";
//        $results = $wpdb->get_var($query);
//        return $results + 1;
        
        $get_id = $wpdb->get_row("SHOW TABLE STATUS LIKE '".$wpdb->prefix."comments'"); 
        $last_id = $get_id->Auto_increment;
        return $last_id;
    }
    
    public function clean_after_import() {
        wp_suspend_cache_invalidation(false);
        wp_defer_term_counting(false);
        wp_defer_comment_counting(false);
        global $wpdb;
        if(class_exists('WC_Product_Reviews_Pro')){
            $wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('%_transient_wc_product_reviews_pro_review_count_%')");
        }
    }
    
}
