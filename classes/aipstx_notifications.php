<?php

namespace AIPSTX;

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\\AIPSTX\\AIPSTX_notifications')) {
    class AIPSTX_notifications {

        private static $instance = null;

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct() {
            // Initialization can be done here
        }

        public function send_error_message($error_message) {
            $response = array('message' => $this->parse_error_message($error_message));
            wp_send_json_error($response);
        }

        private function parse_error_message($error_message) {
            // İlk olarak JSON formatında olup olmadığını kontrol ediyoruz.
            $decoded_message = json_decode($error_message, true);

            // Eğer düzgün bir şekilde JSON formatına dönüştürülebildiyse...
            if (is_array($decoded_message) && isset($decoded_message['error'])) {
                // 'error' anahtarının değerini kontrol ediyoruz.
                $error_info = $decoded_message['error'];

                // Eğer 'error' içinde 'message' anahtarı varsa onu döndürüyoruz.
                if (is_array($error_info) && isset($error_info['message'])) {
                    return $error_info['message'];
                }
            }

            // Eğer 'message' içinde bir JSON string'i varsa, bunu da kontrol ediyoruz.
            if (isset($decoded_message['message'])) {
                // İkinci seviye JSON string'ini decode etmeye çalışıyoruz.
                $inner_decoded_message = json_decode($decoded_message['message'], true);

                // Eğer bu da düzgün bir şekilde decode edildiyse ve içinde 'message' anahtarı varsa...
                if (is_array($inner_decoded_message) && isset($inner_decoded_message['error']) && isset($inner_decoded_message['error']['message'])) {
                    // En içteki 'message' anahtarının değerini döndürüyoruz.
                    return $inner_decoded_message['error']['message'];
                } else {
                    // İkinci seviyede 'message' değeri yoksa, dıştaki 'message' değerini döndürüyoruz.
                    return $decoded_message['message'];
                }
            }

            if (gettype($error_message) === "string" && strpos($error_message, "message")) {
                preg_match('/"message": "(.*)"/', $error_message, $message_matches);
                if (!empty($message_matches[1])) {
                    return $message_matches[1];
                }
            }

            // Hiçbir JSON formatına uymuyorsa, orijinal hata mesajını olduğu gibi döndürüyoruz.
            return $error_message;
        }

    }

    AIPSTX_notifications::get_instance();
}