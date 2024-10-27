<?php

namespace AIPSTX;

if (!defined('ABSPATH'))
	exit;

if (!class_exists('\\AIPSTX\\AIPSTX_btn')) {
	class AIPSTX_btn {

		private static $instance = null;

		public static function get_instance() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		public function __construct() {
			add_action('wp_ajax_aipstx_add_media_library', array($this, 'aipstx_add_media_library'));
			add_action('wp_ajax_aipstx_ensure_image_and_set_featured', array($this, 'aipstx_ensure_image_and_set_featured'));
			add_action('wp_ajax_aipstx_add_post_content', array($this, 'aipstx_add_post_content'));
		}

		public function aipstx_add_media_library() {
			if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {
				wp_send_json_error(array('message' => 'Nonce verification failed.'));
				return;
			}

			$image_url = isset($_POST['image_url']) ? $_POST['image_url'] : '';
			$image_name = isset($_POST['image_name']) ? sanitize_text_field($_POST['image_name']) : '';
			$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

			if (empty($image_url)) {
				wp_send_json_error(array('message' => 'Invalid image URL.'));
				return;
			}

			$post_title = $post_id ? get_the_title($post_id) : $image_name;

			if (empty($post_title)) {
				wp_send_json_error(array('message' => 'Please provide a name for the image.'));
				return;
			}

			$title_cleaned = iconv('UTF-8', 'ASCII//TRANSLIT', $post_title);
			$title_cleaned = sanitize_title_with_dashes($title_cleaned);
			$title_cleaned = str_replace('-', '_', $title_cleaned);

			if (strpos($image_url, 'data:image') === 0) {
				list($type, $data) = explode(';', $image_url);
				list(, $data) = explode(',', $data);
				$image_data = base64_decode($data);
				$image_type = explode('/', $type)[1];
				$file_extension = $image_type === 'jpeg' ? 'jpg' : $image_type;
			} else {
				$response = wp_remote_get($image_url);
				if (is_wp_error($response)) {
					wp_send_json_error(array('message' => 'Error downloading image.'));
					return;
				}
				$image_data = wp_remote_retrieve_body($response);
				$file_extension = 'png';
			}

			if (empty($image_data)) {
				wp_send_json_error(array('message' => 'Error retrieving image data.'));
				return;
			}

			$image_hash = md5($image_data);

			global $wpdb;
			$existing_attachment_id = $wpdb->get_var($wpdb->prepare(
				"SELECT post_id FROM $wpdb->postmeta 
                WHERE meta_key = '_wp_attachment_image_hash' 
                AND meta_value = %s",
				$image_hash
			));

			if ($existing_attachment_id) {
				wp_send_json_success(array('attachment_id' => $existing_attachment_id));
				return;
			}

			$upload_dir = wp_upload_dir();
			$file_name_base = $title_cleaned;
			$file_name = $file_name_base . '.' . $file_extension;
			$i = 0;
			while (file_exists($upload_dir['path'] . '/' . $file_name)) {
				$i++;
				$file_name = $file_name_base . '_' . $i . '.' . $file_extension;
			}

			$file_path = $upload_dir['path'] . '/' . $file_name;

			global $wp_filesystem;
			if (empty($wp_filesystem)) {
				require_once (ABSPATH . 'wp-admin/includes/file.php');
				WP_Filesystem();
			}

			if (!$wp_filesystem->put_contents($file_path, $image_data, FS_CHMOD_FILE)) {
				wp_send_json_error(array('message' => 'Error writing file using WP_Filesystem.'));
				return;
			}

			$file_type = wp_check_filetype($file_name, null);
			$attachment = array(
				'guid' => $upload_dir['url'] . '/' . basename($file_path),
				'post_mime_type' => $file_type['type'],
				'post_title' => $post_title,
				'post_content' => '',
				'post_status' => 'inherit',
			);

			$attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

			require_once (ABSPATH . 'wp-admin/includes/image.php');
			$attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
			wp_update_attachment_metadata($attach_id, $attach_data);
			update_post_meta($attach_id, '_wp_attachment_image_alt', $post_title);
			add_post_meta($attach_id, '_wp_attachment_image_hash', $image_hash, true);

			wp_send_json_success(array('attachment_id' => $attach_id));
		}

		public function aipstx_ensure_image_and_set_featured() {
			if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {
				wp_send_json_error(array('message' => 'Nonce verification failed.'));
				return;
			}

			$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
			$attachment_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;

			if (!$post_id || !$attachment_id) {
				wp_send_json_error(array('message' => "Invalid post ID or image ID."));
				return;
			}

			$result = set_post_thumbnail($post_id, $attachment_id);
			if ($result) {
				wp_update_post(array(
					'ID' => $attachment_id,
					'post_parent' => $post_id
				));

				$featured_image_url = wp_get_attachment_url($attachment_id);
				wp_send_json_success(array('message' => 'Featured image set with new library image.', 'featured_image_url' => $featured_image_url));
			} else {
				wp_send_json_error(array('message' => 'Failed to set featured image for post ID ' . $post_id));
			}
		}

		public function aipstx_add_post_content() {
			if (!check_ajax_referer('aipstx_nonce', 'nonce', false)) {
				wp_send_json_error(array('message' => 'Nonce verification failed.'));
				return;
			}

			$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
			$media_id = isset($_POST['media_id']) ? intval($_POST['media_id']) : 0;

			if (!$post_id || !$media_id) {
				wp_send_json_error('Invalid post ID or media ID.');
				return;
			}

			$post_title = get_the_title($post_id);
			if (!$post_title) {
				wp_send_json_error('Unable to retrieve post title.');
				return;
			}

			$media_url = wp_get_attachment_url($media_id);
			if (!$media_url) {
				wp_send_json_error('Invalid media ID.');
				return;
			}

			$post_content = get_post_field('post_content', $post_id);
			$image_html = '<img src="' . esc_url($media_url) . '" alt="' . esc_attr($post_title) . '"/>';
			$new_post_content = $post_content . $image_html;

			wp_update_post(array(
				'ID' => $media_id,
				'post_parent' => $post_id
			));

			wp_update_post(array(
				'ID' => $post_id,
				'post_content' => $new_post_content
			));

			wp_send_json_success(array('image_html' => $image_html));
		}
	}

	AIPSTX_btn::get_instance();
}