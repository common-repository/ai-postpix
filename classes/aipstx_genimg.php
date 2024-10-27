<?php

namespace AIPSTX;

if (!defined('ABSPATH'))
    exit;

if (!class_exists('\\AIPSTX\\AIPSTX_genimg')) {
    class AIPSTX_genimg {

        private static $instance = null;
        public $aipstx_styles = [];
        public $aipstx_artists = [];
        public $aipstx_photographs = [];
        public $aipstx_lightings = [];
        public $aipstx_cameras = [];
        public $aipstx_effects = [];


        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            add_action('wp_ajax_aipstx_create_image', array($this, 'aipstx_create_image'));
        }

        public function aipstx_process_options($style_selection, $artist_selection, $photography_selection, $lighting_selection, $camera_selection, $effect_selection) {
            $parts = [];

            if (!empty($style_selection) && $style_selection != 'Select a style...' && isset($this->aipstx_styles[$style_selection])) {
                $parts[] = 'Style: ' . $this->aipstx_styles[$style_selection];
            }

            if (!empty($artist_selection) && $artist_selection != 'Select an artist...' && isset($this->aipstx_artists[$artist_selection])) {
                if (!empty($parts)) {
                    $parts[] = ',';
                }
                $parts[] = 'Artist: ' . $artist_selection;
            }
            
            if (!empty($photography_selection) && $photography_selection != 'Select a photography style...' && isset($this->aipstx_photographs[$photography_selection])) {
                if (!empty($parts)) {
                    $parts[] = ',';
                }
                $parts[] = 'Photography: ' . $photography_selection;
            }

            if (!empty($lighting_selection) && $lighting_selection != 'Select a lighting style...' && isset($this->aipstx_lightings[$lighting_selection])) {
                if (!empty($parts)) {
                    $parts[] = ',';
                }
                $parts[] = 'Lighting: ' . $lighting_selection;
            }

            if (!empty($camera_selection) && $camera_selection != 'Select a camera style...' && isset($this->aipstx_cameras[$camera_selection])) {
                if (!empty($parts)) {
                    $parts[] = ',';
                }
                $parts[] = 'Camera: ' . $camera_selection;
            }

            if (!empty($effect_selection) && $effect_selection != 'Select an effect style...' && isset($this->aipstx_effects[$effect_selection])) {
                if (!empty($parts)) {
                    $parts[] = ',';
                }
                $parts[] = 'Effect: ' . $effect_selection;
            }

            return !empty($parts) ? 'with the following settings: ' . implode(' ', $parts) : '';
        }

        public function aipstx_create_image() {
            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {
                wp_send_json_error(array('message' => 'Nonce verification failed.'));
                return;
            }

            if (!current_user_can('edit_posts')) {
                wp_send_json_error('Unauthorized user');
                wp_die();
            }

            $prompt = isset($_POST['prompt']) ? sanitize_text_field($_POST['prompt']) : '';
            $negative_prompt = isset($_POST['negative_prompt']) ? sanitize_text_field($_POST['negative_prompt']) : '';

            if (empty($prompt)) {
                wp_send_json_error('Prompt cannot be empty. Please type a prompt in the Prompt for Images field.');
                wp_die();
            }

            // veriyi isle
            $styles_instance = AIPSTX_img_styles::get_instance();
            $this->aipstx_styles = $styles_instance->aipstx_get_styles();
            $style_selection = isset($_POST['style']) ? sanitize_text_field($_POST['style']) : '';

            $artists_instance = AIPSTX_img_artists::get_instance();
            $this->aipstx_artists = $artists_instance->aipstx_get_artists();
            $artist_selection = isset($_POST['artist']) ? sanitize_text_field($_POST['artist']) : '';

            $photographs_instance = AIPSTX_img_photographs::get_instance();
            $this->aipstx_photographs = $photographs_instance->aipstx_get_photographs();
            $photography_selection = isset($_POST['photography']) ? sanitize_text_field($_POST['photography']) : '';

            $lightings_instance = AIPSTX_img_lightings::get_instance();
            $this->aipstx_lightings = $lightings_instance->aipstx_get_lightings();
            $lighting_selection = isset($_POST['lighting']) ? sanitize_text_field($_POST['lighting']) : '';

            $cameras_instance = AIPSTX_img_cameras::get_instance();
            $this->aipstx_cameras = $cameras_instance->aipstx_get_cameras();
            $camera_selection = isset($_POST['camera']) ? sanitize_text_field($_POST['camera']) : '';

            $effects_instance = AIPSTX_img_effects::get_instance();
            $this->aipstx_effects = $effects_instance->aipstx_get_effects();
            $effect_selection = isset($_POST['effect']) ? sanitize_text_field($_POST['effect']) : '';

            $processed_settings = $this->aipstx_process_options($style_selection, $artist_selection, $photography_selection, $lighting_selection, $camera_selection, $effect_selection);

            // Eğer işlenmiş ayarlar boş değilse, prompt'a ekle
            if (!empty($processed_settings)) {
                $prompt .= ' ' . $processed_settings;
            }

            // Prompt'un sonuna nokta koyma işlemi gerekiyorsa burada yapılır
            if (!empty($prompt)) {
                $prompt .= '.';
            }

            $image_count = isset($_POST['image_count']) ? intval($_POST['image_count']) : 1;
            $engine = isset($_POST['engine']) ? sanitize_text_field($_POST['engine']) : 'openai';
            $resolution = isset($_POST['resolution']) ? sanitize_text_field($_POST['resolution']) : '1024x1024';

            // DALL-E 3 engine selected
            if ($engine == 'dall-e3') {
                $aipstx_openai_key = get_option('aipstx_openai_key');
                if (empty($aipstx_openai_key)) {
                    AIPSTX_notifications::get_instance()->send_error_message('Missing OpenAI API key for DALL-E 3. Please enter your OpenAI API key.');
                    wp_die();
                }

                $api_url = 'https://api.openai.com/v1/images/generations';
                $args = array(
                    'method' => 'POST',
                    'timeout' => 90,
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_openai_key
                    ),
                    'body' => wp_json_encode([
                        'prompt' => $prompt . $negative_prompt,
                        'n' => $image_count,
                        'size' => $resolution
                    ])
                );

                $response = wp_remote_post($api_url, $args);

                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message($response->get_error_message());
                    wp_die();
                }

                $response_data = json_decode(wp_remote_retrieve_body($response), true);

                if (isset($response_data['data'])) {
                    $images = array_map(function ($item) {
                        return $item['url'];
                    }, $response_data['data']);

                    wp_send_json_success($images);
                }

                if (isset($response_data['error'])) {
                    AIPSTX_notifications::get_instance()->send_error_message($response_data['error']['message']);
                    wp_die();
                } else {
                    AIPSTX_notifications::get_instance()->send_error_message('OpenAI Response: ' . wp_json_encode($response_data));
                    wp_die();
                }
            } elseif (in_array($engine, ['openai', 'amazon', 'deepai', 'stabilityai', 'replicate'])) {
                // EdenAI engine selected
                $aipstx_edenai_key = get_option('aipstx_edenai_key');
                if (empty($aipstx_edenai_key)) {
                    wp_send_json_error('Missing EdenAI API key for the selected engine. Please enter your EdenAI API key.');
                    wp_die();
                }

                $api_url = 'https://api.edenai.run/v2/image/generation';
                $args = array(
                    'method' => 'POST',
                    'timeout' => 90,
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_edenai_key
                    ),
                    'body' => wp_json_encode([
                        "response_as_dict" => true,
                        "attributes_as_list" => false,
                        "show_original_response" => false,
                        "resolution" => $resolution,
                        "num_images" => $image_count,
                        "providers" => $engine,
                        "text" => $prompt . $negative_prompt
                    ])
                );

                $response = wp_remote_post($api_url, $args);
                $response_body = wp_remote_retrieve_body($response);

                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message($response->get_error_message());
                    wp_die();
                }

                $response_data = json_decode(wp_remote_retrieve_body($response), true);

                if (!empty($response_data) && isset($response_data[$engine]) && isset($response_data[$engine]['status']) && $response_data[$engine]['status'] == 'success') {
                    $images = array_map(function ($item) {
                        return $item['image_resource_url'];
                    }, $response_data[$engine]['items']);

                    wp_send_json_success($images);

                }

                if (isset($response_data['error'])) {
                    AIPSTX_notifications::get_instance()->send_error_message($response_data['error']['message']);
                    wp_die();
                } elseif (isset($response_data[$engine]) && $response_data[$engine]['status'] === 'fail') {
                    AIPSTX_notifications::get_instance()->send_error_message($response_data[$engine]['error']['message']);
                    wp_die();
                } else {
                    AIPSTX_notifications::get_instance()->send_error_message('An unexpected error occurred with the EdenAI response. Please check your API credit and make sure it is sufficient.');
                    wp_die();
                }
            } elseif (in_array($engine, ['stable-diffusion-v1-6', 'stable-diffusion-xl-1024-v1-0'])) {

                $aipstx_stability_key = get_option('aipstx_stability_key');
                if (empty($aipstx_stability_key)) {
                    
                    wp_send_json_error('Missing StabilityAI API key for the selected engine. Please enter your StabilityAI API key.');
                    wp_die();
                }

                $api_url = "https://api.stability.ai/v1/generation/{$engine}/text-to-image";
                $args = array(
                    'method' => 'POST',
                    'timeout' => 90,
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_stability_key
                    ),
                    'body' => wp_json_encode([
                        'text_prompts' => [['text' => $prompt . $negative_prompt]],
                        'cfg_scale' => 7,
                        'height' => (int) explode('x', $resolution)[1],
                        'width' => (int) explode('x', $resolution)[0],
                        'samples' => $image_count,
                        'steps' => 30,
                    ])
                );

                $response = wp_remote_post($api_url, $args);

                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message($response->get_error_message());
                    wp_die();
                }

                $response_data = json_decode(wp_remote_retrieve_body($response), true);

                if (isset($response_data['artifacts'])) {
                    $images = array_map(function ($item) {
                        return 'data:image/png;base64,' . $item['base64'];
                    }, $response_data['artifacts']);

                    wp_send_json_success($images);
                }

                if (isset($response_data['error'])) {
                    AIPSTX_notifications::get_instance()->send_error_message($response_data['error']);
                    wp_die();
                } else {
                    AIPSTX_notifications::get_instance()->send_error_message('StabilityAI Response: ' . wp_json_encode($response_data));
                    wp_die();
                }
            } elseif (in_array($engine, ['stable-diffusion-core'])) {

                $aipstx_stability_key = get_option('aipstx_stability_key');
                if (empty($aipstx_stability_key)) {
                    AIPSTX_notifications::get_instance()->send_error_message('Missing StabilityAI API key for the selected engine. Please enter your StabilityAI API key.');
                    wp_die();
                }

                // StabilityAI API URL'sini kontrol edin ve doğru URL'yi kullanın
                $api_url = "https://api.stability.ai/v2beta/stable-image/generate/core";

                $aspect_ratio_calculator = \AIPSTX\AIPSTX_ConvertRatio::get_instance();

                // Çözünürlükten aspect ratio'ya dönüştürme
                $aspect_ratio = $aspect_ratio_calculator->get_aspect_ratio($resolution);
                if (is_null($aspect_ratio)) {
                    AIPSTX_notifications::get_instance()->send_error_message('Invalid resolution for aspect ratio');
                    wp_die();
                }

                // Form-data oluşturma
                $body = [
                    'prompt' => $prompt,
                    'aspect_ratio' => $aspect_ratio,
                    'negative_prompt' => $negative_prompt,
                    'cfg_scale' => 7,
                    'samples' => $image_count,
                ];

                // Boundary oluşturma
                $boundary = wp_generate_password(24, false);
                $body_string = '';
                foreach ($body as $key => $value) {
                    $body_string .= '--' . $boundary . "\r\n";
                    $body_string .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
                    $body_string .= $value . "\r\n";
                }
                $body_string .= '--' . $boundary . '--';

                $args = [
                    'method' => 'POST',
                    'timeout' => 90,
                    'headers' => [
                        'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_stability_key,
                    ],
                    'body' => $body_string,
                ];

                $response = wp_remote_post($api_url, $args);

                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message($response->get_error_message());
                    wp_die();
                }

                $response_body = wp_remote_retrieve_body($response);
                $response_code = wp_remote_retrieve_response_code($response);
                $response_message = wp_remote_retrieve_response_message($response);

                if ($response_code === 200) {
                    $response_data = json_decode($response_body, true);
                    if (isset($response_data['image'])) {
                        // Gelen yanıtı doğru bir şekilde işle ve döndür
                        $image_data = 'data:image/webp;base64,' . $response_data['image'];
                        wp_send_json_success([$image_data]);
                    } else {
                        AIPSTX_notifications::get_instance()->send_error_message('Unexpected API response format: ' . print_r($response_data, true));
                    }
                } else {
                    // Hata mesajını ve yanıtı daha ayrıntılı olarak döndür
                    $error_message = "StabilityAI Response: " . $response_code . " " . $response_message . " - " . $response_body;
                    AIPSTX_notifications::get_instance()->send_error_message($error_message);
                    wp_send_json_error($error_message);
                    wp_die();
                }
            } elseif (in_array($engine, ['sd3', 'sd3-turbo'])) {

                $aipstx_stability_key = get_option('aipstx_stability_key');
                if (empty($aipstx_stability_key)) {
                    AIPSTX_notifications::get_instance()->send_error_message('Missing StabilityAI API key for the selected engine. Please enter your StabilityAI API key.');
                    wp_die();
                }

                $api_url = "https://api.stability.ai/v2beta/stable-image/generate/sd3";
                
                $aspect_ratio_calculator = \AIPSTX\AIPSTX_ConvertRatio::get_instance();

                // Çözünürlükten aspect ratio'ya dönüştürme
                $aspect_ratio = $aspect_ratio_calculator->get_aspect_ratio($resolution);
                if (is_null($aspect_ratio)) {
                    AIPSTX_notifications::get_instance()->send_error_message('Invalid resolution for aspect ratio');
                    wp_die();
                }

                // Form-data oluşturma
                $body = [
                    'prompt' => $prompt,
                    'output_format' => 'png',
                    'model' => $engine,
                    'aspect_ratio' => $aspect_ratio,
                    'negative_prompt' => $negative_prompt,
                    'cfg_scale' => 7,
                    'samples' => $image_count,
                ];

                // Boundary oluşturma
                $boundary = wp_generate_password(24, false);
                $body_string = '';
                foreach ($body as $key => $value) {
                    $body_string .= '--' . $boundary . "\r\n";
                    $body_string .= 'Content-Disposition: form-data; name="' . $key . '"' . "\r\n\r\n";
                    $body_string .= $value . "\r\n";
                }
                $body_string .= '--' . $boundary . '--';

                $args = [
                    'method' => 'POST',
                    'timeout' => 90,
                    'headers' => [
                        'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_stability_key,
                    ],
                    'body' => $body_string,
                ];

                $response = wp_remote_post($api_url, $args);

                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message($response->get_error_message());
                    wp_die();
                }

                $response_body = wp_remote_retrieve_body($response);
                $response_code = wp_remote_retrieve_response_code($response);
                $response_message = wp_remote_retrieve_response_message($response);

                if ($response_code === 200) {
                    $response_data = json_decode($response_body, true);
                    if (isset($response_data['image'])) {
                        // Gelen yanıtı doğru bir şekilde işle ve döndür
                        $image_data = 'data:image/png;base64,' . $response_data['image'];
                        wp_send_json_success([$image_data]);
                    } else {
                        AIPSTX_notifications::get_instance()->send_error_message('Unexpected API response format: ' . print_r($response_data, true));
                    }
                } else {
                    // Hata mesajını ve yanıtı daha ayrıntılı olarak döndür
                    $error_message = "StabilityAI Response: " . $response_code . " " . $response_message . " - " . $response_body;
                    AIPSTX_notifications::get_instance()->send_error_message($error_message);
                    wp_send_json_error($error_message);
                    wp_die();
                }
            } else {
                AIPSTX_notifications::get_instance()->send_error_message('Please select a valid model.');
                wp_die();
            }

            wp_die();
        }

    }

    AIPSTX_genimg::get_instance();
}