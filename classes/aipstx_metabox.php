<?php

namespace AIPSTX;

if (!defined('ABSPATH'))
	exit;
if (!class_exists('\\AIPSTX\\AIPSTX_metabox')) {
	class AIPSTX_metabox {

		private static $instance = null;

		public static function get_instance() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}


		public function __construct() {


			add_action('add_meta_boxes', array($this, 'add_custom_meta_box'));
		}

		public function add_custom_meta_box() {
			if (get_option('aipstx_show_metabox')) {
				add_meta_box(
					'aipstx_meta_box', // Meta box ID
					'AI Postpix', // Meta box başlığı
					array($this, 'aipstx_meta_box_html'), // Meta box içeriği için callback fonksiyon
					'post', // Gösterileceği ekran türü (burada 'post' olarak belirlendi)
					'normal', // Meta box'ın gösterileceği alan (normal, side, advanced)
					'high' // Öncelik seviyesi
				);
			}
		}


		public function aipstx_meta_box_html($post) {
			$aipstx_edenai_key = get_option('aipstx_edenai_key');
			$aipstx_openai_key = get_option('aipstx_openai_key');
			$aipstx_stability_key = get_option('aipstx_stability_key');


	?>
<?php if (empty($aipstx_edenai_key) && empty($aipstx_openai_key) && empty($aipstx_stability_key)): ?>
	<div style="background-color: #f8f3ff; width: 100%; height: 300px; top: 50%; left: 0; z-index: 1000; text-align: center;">
		<div class="content" style="position: relative; top: 40%;">
		<div style="margin-bottom:20px;">
			<p style="color:#585656; font-size: 20px;">
				<?php esc_html_e('To use this plugin, please enter an API key from at least one of these services: OpenAI, StabilityAI, or EdenAI.', 'ai-postpix'); ?>
			</p></div>
            <a href="<?php echo esc_url('/wp-admin/admin.php?page=ai-postpix-settings'); ?>" class="aipstx-key-settings-button"><?php esc_html_e('Go to settings', 'ai-postpix'); ?></a>
		</div>
	</div>
	<?php return; endif; ?>
	<?php
			$aipstx_img_styles = class_exists('\\AIPSTX\\AIPSTX_img_styles') ? AIPSTX_img_styles::get_instance()->aipstx_get_styles() : [];
			$aipstx_img_artists = class_exists('\\AIPSTX\\AIPSTX_img_artists') ? AIPSTX_img_artists::get_instance()->aipstx_get_artists() : [];
			$aipstx_img_photographs = class_exists('\\AIPSTX\\AIPSTX_img_photographs') ? AIPSTX_img_photographs::get_instance()->aipstx_get_photographs() : [];
			$aipstx_img_lightings = class_exists('\\AIPSTX\\AIPSTX_img_lightings') ? AIPSTX_img_lightings::get_instance()->aipstx_get_lightings() : [];
			$aipstx_img_cameras = class_exists('\\AIPSTX\\AIPSTX_img_cameras') ? AIPSTX_img_cameras::get_instance()->aipstx_get_cameras() : [];
			$aipstx_img_effects = class_exists('\\AIPSTX\\AIPSTX_img_effects') ? AIPSTX_img_effects::get_instance()->aipstx_get_effects() : [];

			?>
			<div id="postpix-alert" class="postpix-alert" style="display:none;"></div>
			<div class="top">
				<div class="left-group">
					<div class="pv_prompt_actions">
						<button id="aipstx_find_prompt" class="button"><i class="fas fa-magic"></i>&nbsp;&nbsp;Find My
							Prompt</button>
						<div id="improve-section">
							<?php
							$is_aipstx_imp_available = class_exists('\\AIPSTX\\AIPSTX_improve');
							$upgrade_url = aipstx_fs()->get_upgrade_url();
							?>

							<button id="improve-prompt-button" class="improve-button-metabox" <?php if (!$is_aipstx_imp_available): ?>
									onclick="window.open('<?php echo esc_url($upgrade_url); ?>', '_blank')" <?php endif; ?>>
								<i class="fas fa-hand-sparkles"></i>&nbsp;&nbsp;Improve My Prompt <var>PRO</var>
							</button>
						</div>
					</div>
					<div class="prompt-area">
						<div class="prompt-container">
							<label for="pv_prompt">Prompt for Images:</label>
							<div class="prompt-loader" style="display: none;"></div>
						</div>
						<textarea id="pv_prompt" name="pv_prompt"
							placeholder="Type your own prompt or click on the 'Find My Prompt' button."></textarea>
						<!--<div class="prompt-container">
							<label for="negative_prompt">Negative Prompt (Optional)</label>
						</div>
						 <textarea id="negative_prompt" name="negative_prompt"
							placeholder="Describe what you don't want in the image. e.g. Without any humans, animals, vehicles, or buildings."></textarea>
					--></div><button type="button" id="aipstx_create_image" class="button button-primary">
						<i class="fas fa-paint-brush"></i>&nbsp;&nbsp;Generate Images
					</button>
				</div>
				<div class="right-group">
					<div class="groups-container">
						<div class="option-group">
							<label for="engine">Generate Image with:</label>
							<select name="engine" id="engine">
								       <optgroup label="OpenAI Models" class="styled-option-group">
            <option class="styled-option" value="dall-e3">DALL-E 3 - (OpenAI)</option>
        </optgroup>
        <optgroup label="StabilityAI Models" class="styled-option-group">
            <option class="styled-option" value="sd3">Stable Diffusion 3.0 - (StabilityAI)</option>
            <option class="styled-option" value="sd3-turbo">Stable Diffusion 3.0 Turbo - (StabilityAI)</option>
            <option class="styled-option" value="stable-diffusion-core">Stable Image Core - (StabilityAI)</option>
            <option class="styled-option" value="stable-diffusion-v1-6">Stable-Diffusion v1-6 - (StabilityAI)</option>
            <option class="styled-option" value="stable-diffusion-xl-1024-v1-0">SDXL 1.0 - (StabilityAI)</option>
        </optgroup>
        <optgroup label="EdenAI Models" class="styled-option-group">
			<option class="styled-option" value="openai">DALL-E 2 - (EdenAI)</option>
            <option class="styled-option" value="amazon">Amazon Titan - (EdenAI)</option>
            <option class="styled-option" value="deepai">Deep AI - (EdenAI)</option>
            <option class="styled-option" value="replicate">Replicate - (EdenAI)</option>
			<option class="styled-option" value="stabilityai">Stable-Diffusion - (EdenAI)</option>
        </optgroup>
							</select>
						</div>
						<div class="option-group">
							<label for="resolution">Resolution:</label>
							<select name="resolution" id="resolution">
 <optgroup label="1:1 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="512x512">512x512 (1:1)</option>
            <option class="styled-option" value="1024x1024" selected>1024x1024 (1:1)</option>
            <option class="styled-option" value="1152x1152">1152x1152 (1:1)</option>
        </optgroup>
        <optgroup label="16:9 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="1152x896">1152x896 (16:9)</option>
            <option class="styled-option" value="1344x768">1344x768 (16:9)</option>
            <option class="styled-option" value="1024x576">1024x576 (16:9)</option>
            <option class="styled-option" value="1280x720">1280x720 (16:9)</option>
            <option class="styled-option" value="1920x1080">1920x1080 (16:9)</option>
        </optgroup>
        <optgroup label="2:3 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="896x1152">896x1152 (2:3)</option>
            <option class="styled-option" value="768x1344">768x1344 (2:3)</option>
            <option class="styled-option" value="896x1344">896x1344 (2:3)</option>
            <option class="styled-option" value="1152x1728">1152x1728 (2:3)</option>
        </optgroup>
        <optgroup label="3:2 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="1216x832">1216x832 (3:2)</option>
            <option class="styled-option" value="1152x768">1152x768 (3:2)</option>
            <option class="styled-option" value="1728x1152">1728x1152 (3:2)</option>
        </optgroup>
        <optgroup label="4:5 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="768x960">768x960 (4:5)</option>
            <option class="styled-option" value="1152x1440">1152x1440 (4:5)</option>
        </optgroup>
        <optgroup label="5:4 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="1024x819">1024x819 (5:4)</option>
            <option class="styled-option" value="1280x1024">1280x1024 (5:4)</option>
        </optgroup>
        <optgroup label="21:9 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="1536x640">1536x640 (21:9)</option>
            <option class="styled-option" value="2520x1080">2520x1080 (21:9)</option>
            <option class="styled-option" value="3360x1440">3360x1440 (21:9)</option>
        </optgroup>
        <optgroup label="9:21 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="640x1536">640x1536 (9:21)</option>
            <option class="styled-option" value="648x1512">648x1512 (9:21)</option>
            <option class="styled-option" value="432x1008">432x1008 (9:21)</option>
        </optgroup>
        <optgroup label="9:16 aspect ratios" class="styled-option-group">
            <option class="styled-option" value="576x1024">576x1024 (9:16)</option>
            <option class="styled-option" value="720x1280">720x1280 (9:16)</option>
        </optgroup>

							</select>
						</div>
							<div class="option-group number-img">
							<label for="postpix_image_count">Number of Images:</label>
<input type="range" id="postpix_image_count" name="postpix_image_count" min="1" max="10" value="1" oninput="updateRangeDisplay(this.value)">
                            <span id="rangeValueDisplay">1</span> 
						</div>
						<p> If you cannot select a resolution, the currently selected model does not have that resolution. If you cannot select a model, the selected resolution does not apply to that model. The same situation applies when choosing the number of images.</p>
					</div>
					<div class="style-options-container">
						<div class="style-options-header">
							<span>Image Customization (Optional)</span>
						</div>

						<div class="style-options">
							<label for="style">Image Style:</label>
							<select id="style" name="style" class="style-option-select">
								<option value="">Select a style...</option>
								<?php foreach ($aipstx_img_styles as $aipstx_style_key => $aipstx_style_label): ?>
									<option value="<?php echo esc_attr($aipstx_style_key); ?>">
										<?php echo esc_html($aipstx_style_label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="style-options">
							<label for="effects">Effect:</label>
							<select id="effects" name="effects" class="style-option-select">
								<option value="">Select an effect style...</option>
								<?php foreach ($aipstx_img_effects as $aipstx_effect_key => $aipstx_effect_label): ?>
									<option value="<?php echo esc_attr($aipstx_effect_key); ?>">
										<?php echo esc_html($aipstx_effect_label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="style-options">
							<label for="artist">Artist:</label>
							<select id="artist" name="artist" class="style-option-select">
								<option value="">Select an artist...</option>
								<?php foreach ($aipstx_img_artists as $aipstx_artist_key => $aipstx_artist_label): ?>
									<option value="<?php echo esc_attr($aipstx_artist_key); ?>">
										<?php echo esc_html($aipstx_artist_label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="style-options">
							<label for="photography">Photography:</label>
							<select id="photographs" name="photographs" class="style-option-select">
								<option value="">Select a photography style...</option>
								<?php foreach ($aipstx_img_photographs as $aipstx_photography_key => $aipstx_photography_label): ?>
									<option value="<?php echo esc_attr($aipstx_photography_key); ?>">
										<?php echo esc_html($aipstx_photography_label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
						
												<div class="style-options">
							<label for="cameras">Camera:</label>
							<select id="cameras" name="cameras" class="style-option-select">
								<option value="">Select a camera style...</option>
								<?php foreach ($aipstx_img_cameras as $aipstx_camera_key => $aipstx_camera_label): ?>
															<option value="<?php echo esc_attr($aipstx_camera_key); ?>">
																<?php echo esc_html($aipstx_camera_label); ?>
															</option>
														<?php endforeach; ?>
													</select>
												</div>

						<div class="style-options">
							<label for="lightings">Lighting:</label>
							<select id="lightings" name="lightings" class="style-option-select">
								<option value="">Select a lighting style...</option>
								<?php foreach ($aipstx_img_lightings as $aipstx_lighting_key => $aipstx_lighting_label): ?>
									<option value="<?php echo esc_attr($aipstx_lighting_key); ?>">
										<?php echo esc_html($aipstx_lighting_label); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<p> For the styles to work correctly, you must delete the style statements in your prompt (e.g.: a highly
							detailed
							and realistic digital painting) and you should make a maximum of 3 adjustments, more than that and you may not get the desired result. </p>
					</div>
				</div>
			</div>

			<div id="error-modal" class="error-modal" style="display:none;">
	<div class="error-modal-content">
		<span class="error-modal-close">&times;</span>
		<i class="fas fa-exclamation-triangle"></i>
			<h2>An error occurred</h2>
		<p id="error-message"></p> 
	</div>
</div>

			<div id="prompt-modal" style="display:none;">
				<div id="modal-header">
					<span class="modal-title">Prompt Suggestions</span>
					<button class="close-modal">&times;</button>
				</div>
				<div id="prompt-suggestions">
					<!-- AJAX ile doldurulacak prompt önerileri burada olacak -->
				</div>
			</div>

			<div id="pv_images_container" data-context="metabox" style="margin: 20px;">
    		<!-- Metabox ile yüklenen görseller buraya eklenecek -->
			</div>
		
			<?php
		}
	}

	aipstx_metabox::get_instance();
}