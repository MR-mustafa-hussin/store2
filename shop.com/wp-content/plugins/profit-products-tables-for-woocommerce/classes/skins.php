<?php

/**
 * WOOT Skins
 *
 * Handles skins for [woot] shortcodes
 *
 * @since   1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOT_Skins {

    private $skin_folder = null;

    public function __construct() {
        $this->skin_folder = WOOT_PATH . 'skins/';
    }

    /**
     * Get system skins
     *
     * @since 1.0.0
     * 
     * @return array
     */
    public function get_skins() {
        $skins = [
            'default' => esc_html__('Default', 'profit-products-tables-for-woocommerce'),
        ];

        $results = $this->get_css_files($this->skin_folder);
        sort($results, SORT_NATURAL);

        if ($results) {
            foreach ($results as $key => $value) {
                $skins[basename($value, '.css')] = basename($value, '.css');
            }
        }

        //***

        if (is_dir($this->get_wp_theme_dir())) {
            $results = $this->get_css_files($this->get_wp_theme_dir());
            if ($results) {
                foreach ($results as $key => $value) {
                    $skins[basename($value, '.css')] = basename($value, '.css');
                }
            }
        }

        return $skins;
    }

    /**
     * Get css files for skins
     *
     * @since 1.0.0
     * 
     * @return array
     */
    private function get_css_files($folder) {
        return glob("{{$folder}*.css}", GLOB_BRACE);
    }

    /**
     * Get table skin $table_id
     *
     * @since 1.0.0
     * 
     * @return string
     */
    public function get($table_id) {
        $skin = 'default';

        if (woot()->tables->get($table_id)) {
            $skin = woot()->tables->get($table_id)['skin'];

            if (!$skin) {
                $skin = 'default';
            }
        }

        return $skin;
    }

    /**
     * Get custom skins dir path
     *
     * @since 1.0.0
     * 
     * @return string
     */
    private function get_wp_theme_dir() {
        return get_stylesheet_directory() . '/woot-skins/';
    }

    /**
     * Get selected skin CSS
     *
     * @since 1.0.0
     * @param int|string $table_id_or_skin table ID or skin
     * @param string $table_html_id table HTML id
     * @return string
     */
    public function get_theme_css($table_id_or_skin, $table_html_id) {
        if (!empty($table_id_or_skin)) {
            $css = '';
            if (is_int($table_id_or_skin)) {
                $skin = $this->get($table_id_or_skin);
            } else {
                $skin = $table_id_or_skin;
            }

            if ($skin !== 'default') {
                $file = $this->get_wp_theme_dir() . $skin . '.css';
                if (is_file($file)) {
                    $css = file_get_contents($file);
                } else {
                    $file = $this->skin_folder . $skin . '.css';
                    if (is_file($file)) {
                        $css = file_get_contents($file);
                    }
                }

                if (!empty($css)) {
                    $css = str_replace('tid', $table_html_id, $css);
                    $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
                    $css = preg_replace('/\s{2,}/', ' ', $css);
                    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
                    $css = preg_replace('/}/', '} ', $css);
                    return $css;
                }
            }
        }

        return '';
    }

}
