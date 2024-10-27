<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
if (!class_exists('\\AIPSTX\\AIPSTX_img_styles')) {
    class AIPSTX_img_styles {
        private static $instance = null;
        public $aipstx_styles = [
            'realistic' => 'Realistic, lifelike details',
            '3d_render' => '3D rendering style',
            'anime_manga' => 'Anime style, exaggerated expressions',
            'cartoon' => 'Cartoonish, exaggerated features',
            'pixel_art' => 'Pixel art',
            'pop_art' => 'Pop art style, vibrant colors',
            'art_nouveau' => 'Art Nouveau style, fluid lines',
            'steampunk' => 'Steampunk style, Victorian industrial',
            'watercolor' => 'Watercolor style, translucent colors',
            'graffiti' => 'Graffiti style, bold and dynamic',
            'concept_art' => 'Concept art style, detailed',
            'gothic_horror' => 'Gothic horror style, dark atmosphere',
            'vintage' => 'Vintage style, nostalgic',
            'neo_classical' => 'Neo-Classical style, balanced composition',
            'science_fiction' => 'Science fiction style, futuristic',
            'impressionism' => 'Impressionistic style, soft brushstrokes',
            'surrealism' => 'Surreal style, dream-like elements',
            'cubism' => 'Cubist style, geometric shapes',
            'chiaroscuro' => 'Chiaroscuro technique, strong contrasts',
            'art_deco' => 'Art Deco style, geometric patterns',
            'minimalism' => 'Minimalist style, simple forms',
            'gothic' => 'Gothic style, dark and mystical',
            'futurism' => 'Futuristic style, dynamic motion',
            'baroque' => 'Baroque style, dramatic, detailed',
            'abstract' => 'Abstract style, non-representational',
            'expressionism' => 'Expressionist style, emotional portrayal',
            'digital_painting' => 'Digital painting style, brush-like effects',
            'fantasy' => 'Fantasy style, magical and mythical elements',
            'renaissance' => 'Renaissance style, classical techniques',
            'cyberpunk' => 'Cyberpunk style, high-tech and urban',
            'folk_art' => 'Folk art style, traditional and cultural motifs'
        ];

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function aipstx_get_styles_from_post() {
            // Nonce kontrolü

            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {

                wp_send_json_error(array('message' => 'Nonce verification failed.'));

                return;

            }
            $aipstx_style = isset($_POST['style']) ? sanitize_text_field($_POST['style']) : '';
            return $aipstx_style;
        }

        public function aipstx_get_styles() {
            return $this->aipstx_styles;
        }
    }

    AIPSTX_img_styles::get_instance();
}
