<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_photographs')) {
    class AIPSTX_img_photographs {
        private static $instance = null;
        public $aipstx_photographs = [
            'Abstract' => 'Abstract',
            'Action' => 'Action',
            'Aerial' => 'Aerial',
            'Agricultural' => 'Agricultural',
            'Animal' => 'Animal',
            'Architectural' => 'Architectural',
            'Astrophotography' => 'Astrophotography',
            'Automotive' => 'Automotive',
            'Aviation' => 'Aviation',
            'Bird photography' => 'Bird photography',
            'Black and white' => 'Black and white',
            'Candid' => 'Candid',
            'Cityscape' => 'Cityscape',
            'Close-up' => 'Close-up',
            'Commercial' => 'Commercial',
            'Conceptual' => 'Conceptual',
            'Corporate' => 'Corporate',
            'Documentary' => 'Documentary',
            'Drone' => 'Drone',
            'Editorial' => 'Editorial',
            'Event' => 'Event',
            'Experimental' => 'Experimental',
            'Family' => 'Family',
            'Fashion' => 'Fashion',
            'Fine art' => 'Fine art',
            'Food' => 'Food',
            'Food photography' => 'Food photography',
            'Forensic' => 'Forensic',
            'Glamour' => 'Glamour',
            'Historical' => 'Historical',
            'Industrial' => 'Industrial',
            'Infrared' => 'Infrared',
            'Interior' => 'Interior',
            'Journalistic' => 'Journalistic',
            'Landscape' => 'Landscape',
            'Lifestyle' => 'Lifestyle',
            'Macro' => 'Macro',
            'Marine' => 'Marine',
            'Medical' => 'Medical',
            'Micro' => 'Micro',
            'Military' => 'Military',
            'Nature' => 'Nature',
            'Night' => 'Night',
            'Panoramic' => 'Panoramic',
            'Pet' => 'Pet',
            'Portrait' => 'Portrait',
            'Product' => 'Product',
            'Real estate' => 'Real estate',
            'Scientific' => 'Scientific',
            'Sports' => 'Sports',
            'Still life' => 'Still life',
            'Street' => 'Street',
            'Theatre' => 'Theatre',
            'Time-lapse' => 'Time-lapse',
            'Travel' => 'Travel',
            'Underwater' => 'Underwater',
            'Urban' => 'Urban',
            'Vehicular' => 'Vehicular',
            'War' => 'War',
            'Wedding' => 'Wedding',
            'Wildlife' => 'Wildlife',
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_photographs_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_photography = isset($_POST['photography']) ? sanitize_text_field($_POST['photography']) : '';
            return $aipstx_photography;
        }

        public function aipstx_get_photographs() {
            return $this->aipstx_photographs;
        }
    }

    AIPSTX_img_photographs::get_instance();
}
