<?php

namespace AIPSTX;

if (!defined('ABSPATH'))
    exit;

if (!class_exists('\\AIPSTX\\AIPSTX_ConvertRatio')) {
    class AIPSTX_ConvertRatio {

        private static $instance = null;

        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct() {
        }

        public function calculate_aspect_ratio($width, $height) {
            $gcd = function ($a, $b) use (&$gcd) {
                return ($b == 0) ? $a : $gcd($b, $a % $b);
            };
            $divisor = $gcd($width, $height);
            return ($width / $divisor) . ':' . ($height / $divisor);
        }

        // Aspect ratio'ları eşleştirme
        public function get_aspect_ratio($resolution) {
            $aspect_ratios = [
                "16:9" => ["1024x576", "1280x720", "1920x1080", "1152x896", "1344x768"],
                "1:1" => ["1024x1024", "1152x1152", "512x512"],
                "21:9" => ["2520x1080", "3360x1440", "1536x640"],
                "2:3" => ["896x1344", "1152x1728", "896x1152", "768x1344"],
                "3:2" => ["1152x768", "1728x1152", "1216x832"],
                "4:5" => ["768x960", "1152x1440"],
                "5:4" => ["1024x819", "1280x1024"],
                "9:16" => ["576x1024", "720x1280"],
                "9:21" => ["432x1008", "648x1512", "640x1536"]
            ];

            $resolution_parts = explode('x', $resolution);
            $width = (int) $resolution_parts[0];
            $height = (int) $resolution_parts[1];

            $input_aspect_ratio = $this->calculate_aspect_ratio($width, $height);

            foreach ($aspect_ratios as $aspect_ratio => $res) {
                foreach ($res as $r) {
                    $parts = explode('x', $r);
                    $w = (int) $parts[0];
                    $h = (int) $parts[1];

                    if ($this->calculate_aspect_ratio($w, $h) == $input_aspect_ratio) {
                        return $aspect_ratio;
                    }
                }
            }

            return null;
        }
    }

    AIPSTX_ConvertRatio::get_instance();
}
