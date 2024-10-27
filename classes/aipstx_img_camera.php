<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_cameras')) {
    class AIPSTX_img_cameras {
        private static $instance = null;
        public $aipstx_cameras = [
            'Aperture Priority' => 'Aperture Priority',
            'Auto Exposure' => 'Auto Exposure',
            'Auto Focus' => 'Auto Focus',
            'Bulb Mode' => 'Bulb Mode',
            'Burst Mode' => 'Burst Mode',
            'Depth of Field' => 'Depth of Field',
            'Digital Zoom' => 'Digital Zoom',
            'Exposure Compensation' => 'Exposure Compensation',
            'Exposure Triangle' => 'Exposure Triangle',
            'Flash Off' => 'Flash Off',
            'Flash On' => 'Flash On',
            'Focal Length' => 'Focal Length',
            'Focus Stacking' => 'Focus Stacking',
            'High Dynamic Range (HDR)' => 'High Dynamic Range (HDR)',
            'High ISO' => 'High ISO',
            'Image Stabilization' => 'Image Stabilization',
            'ISO Settings' => 'ISO Settings',
            'Long Exposure' => 'Long Exposure',
            'Low ISO' => 'Low ISO',
            'Macro Mode' => 'Macro Mode',
            'Manual Focus' => 'Manual Focus',
            'Manual Mode' => 'Manual Mode',
            'Mirror Lock-Up' => 'Mirror Lock-Up',
            'Multiple Exposure' => 'Multiple Exposure',
            'Night Mode' => 'Night Mode',
            'Noise Reduction' => 'Noise Reduction',
            'Panorama Mode' => 'Panorama Mode',
            'Portrait Mode' => 'Portrait Mode',
            'Program Mode' => 'Program Mode',
            'Rapid Fire' => 'Rapid Fire',
            'RAW Shooting' => 'RAW Shooting',
            'Remote Trigger' => 'Remote Trigger',
            'Scene Modes' => 'Scene Modes',
            'Shutter Priority' => 'Shutter Priority',
            'Shutter Speed' => 'Shutter Speed',
            'Slow Sync Flash' => 'Slow Sync Flash',
            'Smart HDR' => 'Smart HDR',
            'Telephoto' => 'Telephoto',
            'Tilt-Shift' => 'Tilt-Shift',
            'Time-Lapse' => 'Time-Lapse',
            'Timer' => 'Timer',
            'White Balance' => 'White Balance',
            'Wide Angle' => 'Wide Angle',
            'Zoom' => 'Zoom',
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_cameras_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_camera = isset($_POST['camera']) ? sanitize_text_field($_POST['camera']) : '';
            return $aipstx_camera;
        }

        public function aipstx_get_cameras() {
            return $this->aipstx_cameras;
        }
    }

    AIPSTX_img_cameras::get_instance();
}
