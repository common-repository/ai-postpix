<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_effects')) {
    class AIPSTX_img_effects {
        private static $instance = null;
        public $aipstx_effects = [
            'Abstract' => 'Abstract',
            'Acid Wash' => 'Acid Wash',
            'Animation' => 'Animation',
            'Antique' => 'Antique',
            'Black and White' => 'Black and White',
            'Bloom' => 'Bloom',
            'Bokeh' => 'Bokeh',
            'Brightness Effect' => 'Brightness Effect',
            'Cartoonize' => 'Cartoonize',
            'Charcoal Drawing' => 'Charcoal Drawing',
            'Chromatic Aberration' => 'Chromatic Aberration',
            'Color Gradient' => 'Color Gradient',
            'Color Shift' => 'Color Shift',
            'Color Splash' => 'Color Splash',
            'Colorize' => 'Colorize',
            'Comic Book' => 'Comic Book',
            'Contour Drawing' => 'Contour Drawing',
            'Crosshatch' => 'Crosshatch',
            'Crystallize' => 'Crystallize',
            'Cyberpunk' => 'Cyberpunk',
            'Digital Drawing' => 'Digital Drawing',
            'Double Exposure' => 'Double Exposure',
            'Duotone' => 'Duotone',
            'Edge Detection' => 'Edge Detection',
            'Emboss' => 'Emboss',
            'Engraving' => 'Engraving',
            'Fluid Art' => 'Fluid Art',
            'Frost Effect' => 'Frost Effect',
            'Glitch' => 'Glitch',
            'Gouache' => 'Gouache',
            'Gradient Overlay' => 'Gradient Overlay',
            'Halftone' => 'Halftone',
            'Hand Drawn' => 'Hand Drawn',
            'Haze' => 'Haze',
            'HDR' => 'HDR',
            'Heatmap' => 'Heatmap',
            'High Key' => 'High Key',
            'High Pass' => 'High Pass',
            'Infrared' => 'Infrared',
            'Invert' => 'Invert',
            'Kaleidoscope' => 'Kaleidoscope',
            'Lomo Effect' => 'Lomo Effect',
            'Low Key' => 'Low Key',
            'Low Pass' => 'Low Pass',
            'Matte Finish' => 'Matte Finish',
            'Miniature' => 'Miniature',
            'Mirror Effect' => 'Mirror Effect',
            'Mozaik' => 'Mozaik',
            'Motion Blur' => 'Motion Blur',
            'Neon Glow' => 'Neon Glow',
            'Oil Painting' => 'Oil Painting',
            'Outline' => 'Outline',
            'Pastel' => 'Pastel',
            'Pen and Ink' => 'Pen and Ink',
            'Pencil Shading' => 'Pencil Shading',
            'Pixelate' => 'Pixelate',
            'Pointillism' => 'Pointillism',
            'Pop Art' => 'Pop Art',
            'Posterize' => 'Posterize',
            'Radar' => 'Radar',
            'Radial Blur' => 'Radial Blur',
            'Relief' => 'Relief',
            'Retro' => 'Retro',
            'Selective Color' => 'Selective Color',
            'Sepia' => 'Sepia',
            'Shadow Effect' => 'Shadow Effect',
            'Silhouette' => 'Silhouette',
            'Sketch' => 'Sketch',
            'Smudge' => 'Smudge',
            'Soft Light' => 'Soft Light',
            'Solarize' => 'Solarize',
            'Texture Addition' => 'Texture Addition',
            'Thermal Vision' => 'Thermal Vision',
            'Tritone' => 'Tritone',
            'Underwater' => 'Underwater',
            'Vignette' => 'Vignette',
            'Vintage' => 'Vintage',
            'Water Reflection' => 'Water Reflection',
            'Watercolor' => 'Watercolor',
            'Wind Blur' => 'Wind Blur',
            'Zoom Blur' => 'Zoom Blur',
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_effects_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_effect = isset($_POST['effect']) ? sanitize_text_field($_POST['effect']) : '';
            return $aipstx_effect;
        }

        public function aipstx_get_effects() {
            return $this->aipstx_effects;
        }
    }

    AIPSTX_img_effects::get_instance();
}
