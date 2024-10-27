<?php

namespace AIPSTX;

if (!defined('ABSPATH'))
    exit;
if (!class_exists('\\AIPSTX\\AIPSTX_finder')) {
    class AIPSTX_finder {

        private static $instance = null;

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }


        public function __construct() {


            add_action('wp_ajax_aipstx_find_prompt', array($this, 'aipstx_find_prompt'));
        }

        public function aipstx_find_prompt() {
            // Nonce kontrolü
            if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {
                AIPSTX_notifications::get_instance()->send_error_message('Nonce verification failed.');
                return;
            }
            $post_title = isset($_POST['postTitle']) ? sanitize_text_field($_POST['postTitle']) : '';
            // İstemci tarafından temizlenmiş post içeriği al
            $post_content = isset($_POST['postContent']) ? sanitize_textarea_field($_POST['postContent']) : '';

            // OpenAI ve Eden AI API anahtarlarını al
            $aipstx_openai_key = get_option('aipstx_openai_key');
            $aipstx_edenai_key = get_option('aipstx_edenai_key');
            $prompt_engine = get_option('aipstx_prompt_engine', 'gpt-3.5-turbo');
            $engine = isset($_POST['engine']) ? sanitize_text_field($_POST['engine']) : '';

            // Eğer iki API anahtarı da yoksa hata ver ve işlemi durdur
            if (empty($aipstx_openai_key) && empty($aipstx_edenai_key)) {
                AIPSTX_notifications::get_instance()->send_error_message('The Find my Prompt feature works with OpenAI or EdenAI. You have not entered an API key for OpenAI or EdenAI. Please enter the API key for at least one of the OpenAI or EdenAI services from the plugin\'s settings page.');
                wp_die();
            }

            $api_url = '';
            $args = [];

            // Prompt engine değerine göre API isteğini yap
            if ($prompt_engine === 'eden_ai') {
                // Eden AI API isteği yap
                $api_url = 'https://api.edenai.run/v2/text/chat';
                $args = array(
                    'method' => 'POST',
                    'timeout' => '60',
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_edenai_key
                    ),
                    'body' => wp_json_encode([
                        "providers" => "google",
                        "text" => "You are a prompt engineer. Your task is to carefully analyze the provided blog title and content. And create a prompt for the {$engine} model, enabling the generation of a visual that matches the text. This prompt should concisely capture the essence, main themes, and nuances of the provided blog post, aiming to facilitate the creation of the most accurate and engaging image that reflects the main message and context without being overly complex or detailed. The prompt must be that is simple enough to generate an image and not too detailed. You must create a different prompt each time a request is sent.  You must generate only a prompt. The prompt must be in English. Title: '{$post_title}', Content: '{$post_content}'"
                    ])
                );
            } else {
                // OpenAI API isteği yap
                $api_url = 'https://api.openai.com/v1/chat/completions';
                $args = array(
                    'method' => 'POST',
                    'timeout' => '60',
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $aipstx_openai_key
                    ),
                    'body' => wp_json_encode([
                        "model" => $prompt_engine,
                        "messages" => [
                            [
                                "role" => "system",
                                "content" => "You are a prompt engineer. Your task is to carefully analyze the provided blog title and content. And create a prompt for the {$engine} model, enabling the generation of a visual that matches the text. This prompt should concisely capture the essence, main themes, and nuances of the provided blog post, aiming to facilitate the creation of the most accurate and engaging image that reflects the main message and context without being overly complex or detailed. The prompt must be that is simple enough to generate an image and not too detailed. You must create a different prompt each time a request is sent. You must generate only a prompt. The prompt must be in English."
                            ],
                            [
                                "role" => "user",
                                "content" => "Title: '{$post_title}', Content: '{$post_content}'"
                            ]
                        ]
                    ])
                );
            }

            // API isteğini yap
            if (!empty($api_url)) {
                $response = wp_remote_request($api_url, $args);

                // API yanıtını kontrol et ve ekrana yazdır
                if (is_wp_error($response)) {
                    AIPSTX_notifications::get_instance()->send_error_message('API Response: ' . $response->get_error_message());
                } else {
                    // API'den alınan yanıtı işle
                    $decoded_response = json_decode(wp_remote_retrieve_body($response), true);
                    $prompt = '';
                    if ($prompt_engine === 'eden_ai' && isset($decoded_response['google']['generated_text'])) {
                        $prompt = $decoded_response['google']['generated_text'];
                    } elseif (isset($decoded_response['choices'][0]['message']['content'])) {
                        $prompt = $decoded_response['choices'][0]['message']['content'];
                    }

                    // ":" işaretinden sonraki metni al
                    $colonPos = strpos($prompt, ':');
                    if ($colonPos !== false) {
                        $prompt = trim(substr($prompt, $colonPos + 1));
                    }

                    // Çift ve tek tırnak işaretlerini sil
                    $prompt = str_replace(['"', "'", "*", ":"], '', $prompt);

                    if (!empty($prompt)) {
                        wp_send_json_success($prompt);
                    } else {
                        // API'den gelen yanıtı kullanıcıya gönder
                        AIPSTX_notifications::get_instance()->send_error_message('API Response: ' . wp_remote_retrieve_body($response));
                        
                    }
                }
            } else {
                AIPSTX_notifications::get_instance()->send_error_message('No valid API key provided');
            }
        }

    }

    AIPSTX_finder::get_instance();
}
