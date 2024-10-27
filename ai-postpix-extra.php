<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/classes/aipstx_util.php';
require_once __DIR__ . '/classes/aipstx_ajax_handler.php';
require_once __DIR__ . '/classes/aipstx_fprompt.php';
require_once __DIR__ . '/classes/aipstx_genimg.php';
require_once __DIR__ . '/classes/aipstx_btn.php';
require_once __DIR__ . '/classes/aipstx_metabox.php';
require_once __DIR__ . '/classes/aipstx_hook.php';
require_once __DIR__ . '/classes/aipstx_img_styles.php';
require_once __DIR__ . '/classes/aipstx_img_artists.php';
require_once __DIR__ . '/classes/aipstx_img_photography.php';
require_once __DIR__ . '/classes/aipstx_img_lighting.php';
require_once __DIR__ . '/classes/aipstx_img_camera.php';
require_once __DIR__ . '/classes/aipstx_img_effect.php';
require_once __DIR__ . '/classes/aipstx_notifications.php';
require_once __DIR__ . '/classes/aipstx_admin_genimg.php';
require_once __DIR__ . '/classes/aipstx_convert_ratio.php';
if (\AIPSTX\aipstx_util_core()->aipstx_is_pro()) {
    if (file_exists(__DIR__ . '/lib/aipstx__premium_only.php')) {
        require_once __DIR__ . '/lib/aipstx__premium_only.php';
    }
}
