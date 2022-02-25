<?php
defined('ABSPATH') OR exit;

class Trustedsite
{
    public static function activate()
    {
        update_option('trustedsite_active', 1);
    }

    public static function scripts($hook){
        if (strpos($hook, "trustedsite-settings") !== false) {
            wp_enqueue_style('trustedsite-settings-fa', plugins_url('../css/font-awesome.min.css',__FILE__));
            wp_enqueue_style('trustedsite-settings-css', plugins_url('../css/settings.css',__FILE__));
            wp_enqueue_script('trustedsite-trustedsite-js', plugins_url('../js/trustedsite.js',__FILE__));
        }
    }

    public static function get_site_id(){
        $existing_site_id = get_option('trustedsite_site_id');
        if (!empty($existing_site_id)) {
            return $existing_site_id;
        }

        $endpoint_host = "https://cdn.trustedsite.com";
        $arrHost = parse_url(home_url('', $scheme = 'http'));
        $host = $arrHost['host'];

        $sitemap_req_url = $endpoint_host . "/api/v2/site-lookup.json?host=" . urlencode($host) . "&cache=" . get_the_date('i');
        $response = wp_remote_get($sitemap_req_url);
        
        if (is_array($response) && !is_wp_error($response)) {
            $rjson = json_decode($response['body'], true);
            $site_id = $rjson['site_id'];
            update_option('mcafeesecure_site_id', $site_id);
            return $site_id;
        }
        return false;
    }

    public static function install()
    {
        add_shortcode('mcafeesecure', 'Trustedsite::mfes_engagement_trustmark_shortcode');
        add_shortcode('trustedsite', 'Trustedsite::ts_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_form', 'Trustedsite::ts_form_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_checkout', 'Trustedsite::ts_checkout_engagement_trustmark_shortcode');
        add_shortcode('trustedsite_login', 'Trustedsite::ts_login_engagement_trustmark_shortcode');
        add_shortcode('mcafeesecure_sip', 'Trustedsite::mfes_sip_trustmark_shortcode');
        add_shortcode('trustedsite_sip', 'Trustedsite::ts_sip_shortcode');
        add_shortcode('trustedsite_sip_legacy', 'Trustedsite::ts_sip_legacy_shortcode');
        add_shortcode('trustedsite_banner', 'Trustedsite::ts_banner_shortcode');
        add_shortcode('trustedsite_testimonial', 'Trustedsite::ts_testimonial_shortcode');
        add_shortcode('mcafeesecure_hide', 'Trustedsite::hide_floating_trustmark_shortcode');
        add_shortcode('trustedsite_hide', 'Trustedsite::hide_floating_trustmark_shortcode');

        add_action('admin_menu', 'Trustedsite::admin_menus');
        add_action('admin_enqueue_scripts', 'Trustedsite::scripts');
        add_filter('plugin_action_links_trustedsite/trustedsite.php', 'Trustedsite::add_plugin_settings_link');

        if (get_option('mcafeesecure_active') === false) {
            add_action('do_robots', 'Trustedsite::robots');
            add_action('wp_footer', 'Trustedsite::inject_code');
            if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                Trustedsite::install_woocommerce();
            }
        }
    }

    public static function robots(){
        $site_id = Trustedsite::get_site_id();
        if(!empty($site_id)){
            echo "\nSitemap: https://cdn.ywxi.net/sitemap/".$site_id."/1.xml\n";
        }
    }

    public static function inject_sip_modal($order_id) {

        $order = wc_get_order($order_id);
        $email = $order->get_billing_email();
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $country_code = $order->get_billing_country();
        $state_code = $order->get_billing_state();

        echo <<<EOT
            <script type="text/javascript">
                (function() {
                    var sipScript = document.createElement('script');
                    sipScript.setAttribute("class","trustedsite-track-conversion");
                    sipScript.setAttribute("type","text/javascript");
                    sipScript.setAttribute("data-type","purchase");
                    sipScript.setAttribute("data-orderid", "$order_id");
                    sipScript.setAttribute("data-email", "$email");
                    sipScript.setAttribute("data-firstname", "$first_name");
                    sipScript.setAttribute("data-lastname", "$last_name");
                    sipScript.setAttribute("data-country", "$country_code");
                    sipScript.setAttribute("data-state", "$state_code");
                    sipScript.setAttribute("src", "https://cdn.ywxi.net/js/conversion.js");
                    document.getElementsByTagName("head")[0].appendChild(sipScript);
                })();
            </script>
EOT;
    }

    public static function install_woocommerce()
    {
        add_action('woocommerce_thankyou', 'Trustedsite::inject_sip_modal');
    }

    public static function deactivate()
    {
        delete_option("trustedsite_active");
    }

    public static function uninstall()
    {
        delete_option("trustedsite_active");
        delete_option("trustedsite_data");
        delete_option("trustedsite_site_id");
    }

    public static function mfes_engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='mfes-trustmark' data-type='102' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='202' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_form_engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='211' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_checkout_engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='212' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_login_engagement_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='trustedsite-trustmark' data-type='213' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function mfes_sip_trustmark_shortcode($atts = array())
    {
        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return "<div class='mfes-trustmark' data-type='103' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_sip_legacy_shortcode($atts = array())
    {

        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='203' data-width=" . $width . " data-ext='svg'></div>";
    }

    public static function ts_sip_shortcode($atts = array())
    {

        $a = shortcode_atts(array(
            'width' => 90,
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='204' data-width=" . $width . " data-ext='svg'></div>";
    }
    
    public static function ts_banner_shortcode($atts = array())
    {

        $a = shortcode_atts(array(
            'width' => '0',
        ), $atts);

        $width = intval($a['width']);
        return"<div class='trustedsite-trustmark' data-type='1001' data-width=" . $width . "></div>";
    }
    
    public static function ts_testimonial_shortcode($atts = array())
    {

        $a = shortcode_atts(array(
            'width' => '0',
            'height' => '0',
        ), $atts);


        $width = intval($a['width']);
        $height = intval($a['height']);
        return"<div class='trustedsite-trustmark' data-type='1002' data-width=" . $width . " data-height=" . $height . "></div>";
    }

    public static function hide_floating_trustmark_shortcode($atts = array())
    {
        return "<div class='trustedsite-tm-float-disable'></div>";
    }

    public static function admin_menus()
    {

        add_options_page(
            'TrustedSite', 
            'TrustedSite', 
            'activate_plugins', 
            'trustedsite-settings', 
            'Trustedsite::settings_page');

    }

    public static function add_plugin_settings_link( $links )
    {
        array_unshift( $links, '<a href="options-general.php?page=trustedsite-settings">Settings</a>' );
        return $links;
    }

    public static function settings_page()
    {
        require WP_PLUGIN_DIR . '/trustedsite/lib/settings_page.php';
    }

    public static function inject_code()
    {
        echo <<<EOT
            <script type="text/javascript">
              (function() {
                var sa = document.createElement('script'); sa.type = 'text/javascript'; sa.async = true;
                sa.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.ywxi.net/js/1.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(sa, s);
              })();
            </script>
EOT;
	}
}

?>
