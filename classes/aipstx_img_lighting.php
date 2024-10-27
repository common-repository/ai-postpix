<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_lightings')) {
    class AIPSTX_img_lightings {
        private static $instance = null;
        public $aipstx_lightings = [
            'Ambient Light' => 'Ambient Light',
            'Backlight' => 'Backlight',
            'Backlit' => 'Backlit',
            'Bright Light' => 'Bright Light',
            'Candlelight' => 'Candlelight',
            'Clarity' => 'Clarity',
            'Cloudy Lighting' => 'Cloudy Lighting',
            'Dappled Sunlight' => 'Dappled Sunlight',
            'Daylight' => 'Daylight',
            'Diffuse Light' => 'Diffuse Light',
            'Diffused Light' => 'Diffused Light',
            'Directional Light' => 'Directional Light',
            'Dramatic Lighting' => 'Dramatic Lighting',
            'Dusk Lighting' => 'Dusk Lighting',
            'Even Lighting' => 'Even Lighting',
            'Filtered Light' => 'Filtered Light',
            'Firelight' => 'Firelight',
            'Flash Lighting' => 'Flash Lighting',
            'Flickering Light' => 'Flickering Light',
            'Flood Light' => 'Flood Light',
            'Fluorescent Light' => 'Fluorescent Light',
            'Golden Hour Light' => 'Golden Hour Light',
            'Hard Light' => 'Hard Light',
            'Harsh Light' => 'Harsh Light',
            'High Contrast Light' => 'High Contrast Light',
            'Indirect Light' => 'Indirect Light',
            'Interior Lighting' => 'Interior Lighting',
            'Low Light' => 'Low Light',
            'Moonlight' => 'Moonlight',
            'Mood Lighting' => 'Mood Lighting',
            'Natural Light' => 'Natural Light',
            'Neon Light' => 'Neon Light',
            'Overcast Lighting' => 'Overcast Lighting',
            'Overhead Light' => 'Overhead Light',
            'Reflected Light' => 'Reflected Light',
            'Rim Light' => 'Rim Light',
            'Scattered Light' => 'Scattered Light',
            'Shadow Light' => 'Shadow Light',
            'Side Lighting' => 'Side Lighting',
            'Soft Light' => 'Soft Light',
            'Spotlight' => 'Spotlight',
            'Stage Lighting' => 'Stage Lighting',
            'Stroboscopic Light' => 'Stroboscopic Light',
            'Studio Lighting' => 'Studio Lighting',
            'Sunlight' => 'Sunlight',
            'Sunset Lighting' => 'Sunset Lighting',
            'Task Lighting' => 'Task Lighting',
            'Top Light' => 'Top Light',
            'Twilight' => 'Twilight',
            'Underwater Lighting' => 'Underwater Lighting',
            'Warm Light' => 'Warm Light',
            'Window Light' => 'Window Light',
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_lightings_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_lighting = isset($_POST['lighting']) ? sanitize_text_field($_POST['lighting']) : '';
            return $aipstx_lighting;
        }

        public function aipstx_get_lightings() {
            return $this->aipstx_lightings;
        }
    }

    AIPSTX_img_lightings::get_instance();
}
