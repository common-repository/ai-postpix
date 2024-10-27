<?php
namespace AIPSTX;

if (!defined('ABSPATH'))
	exit;
if (!class_exists('\\AIPSTX\\AIPSTX_admin_genimg')) {
	class AIPSTX_admin_genimg {

		private static $instance = null;

		public static function get_instance() {
			if (is_null(self::$instance)) {
				self::$instance = new self();
			}
			return self::$instance;
		}


		public function __construct() {

		}

        public function aipstx_display() {
			$aipstx_edenai_key = get_option('aipstx_edenai_key');
			$aipstx_openai_key = get_option('aipstx_openai_key');
            $aipstx_stability_key = get_option('aipstx_stability_key');

			$aipstx_img_styles = class_exists('\\AIPSTX\\AIPSTX_img_styles') ? AIPSTX_img_styles::get_instance()->aipstx_get_styles() : [];
			$aipstx_img_artists = class_exists('\\AIPSTX\\AIPSTX_img_artists') ? AIPSTX_img_artists::get_instance()->aipstx_get_artists() : [];
			$aipstx_img_photographs = class_exists('\\AIPSTX\\AIPSTX_img_photographs') ? AIPSTX_img_photographs::get_instance()->aipstx_get_photographs() : [];
			$aipstx_img_lightings = class_exists('\\AIPSTX\\AIPSTX_img_lightings') ? AIPSTX_img_lightings::get_instance()->aipstx_get_lightings() : [];
			$aipstx_img_cameras = class_exists('\\AIPSTX\\AIPSTX_img_cameras') ? AIPSTX_img_cameras::get_instance()->aipstx_get_cameras() : [];
			$aipstx_img_effects = class_exists('\\AIPSTX\\AIPSTX_img_effects') ? AIPSTX_img_effects::get_instance()->aipstx_get_effects() : [];


?>
<div id="postpixadmin" class="clearfix">
    <div class="wrap">
        <div class="postpix-nav">
            <div class="postpix-nav-logo">
                <div class="logo"></div>
                <div class="postpix-nav-logo-text">
                    <div class="plugin-version">
                        v<?php echo esc_html(AIPSTX_VERSION); ?></div>
                </div>
            </div>
            <div class="postpix-nav-list">
                <div class="postpix-nav-list-item active"><a
                        href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=ai-postpix"
                        rel="nofollow"><i class="fas fa-magic"></i></i> AI Image Generator</a></div>
                <div class="postpix-nav-list-item"><a
                        href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=ai-postpix-settings"
                        rel="nofollow"><i class="fas fa-cog"></i> Settings</a></div>
            </div>
            <div class="postpix-nav-additions">
                <a href="#" rel="nofollow" class="notifications-status" id="openInstructions"><i
                        class="fas fa-info-circle"></i> How to Use in Posts?</a>
                <a href="#" rel="nofollow" class="additions-menu"><i class="fas fa-ellipsis-h"></i></a>
                <div class="support-menu">
                    <ul>
                        <li><a href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=ai-postpix-pricing"
                                target="_blank" rel="nofollow"><i class="fas fa-star"></i> Buy Pro Version</a></li>
                        <li><a href="https://wordpress.org/support/plugin/ai-postpix/" target="_blank" rel="nofollow"><i
                                    class="fas fa-headset"></i> Support</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="postpix-save-alert"><?php
        settings_errors('aipstx_settings');
        ?></div>
    <?php if (empty($aipstx_edenai_key) && empty($aipstx_openai_key) && empty($aipstx_stability_key)): ?>
        <div class="key-alert alert-danger key-alert-white rounded">
            <div class="content">
                <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                <strong style="font-size:17px">
                    <?php esc_html_e('To use this plugin, please enter an API key from at least one of these services: OpenAI, StabilityAI, or EdenAI.', 'ai-postpix'); ?>
                </strong><br>
                <?php esc_html_e('You can only use the service for which you provide an API key. Get your OpenAI API Key from your', 'ai-postpix'); ?>
                <a href="<?php echo esc_url('https://platform.openai.com/account/usage/'); ?>" target="_blank"
                    rel="nofollow"><?php esc_html_e('OpenAI Account', 'ai-postpix'); ?></a>,
                <?php esc_html_e('your StabilityAI API Key from your', 'ai-postpix'); ?>
                <a href="<?php echo esc_url('https://platform.stability.ai/account/keys'); ?>" target="_blank"
                    rel="nofollow"><?php esc_html_e('StabilityAI Account', 'ai-postpix'); ?></a>,<br>
                <?php esc_html_e('or your EdenAI API Key from your', 'ai-postpix'); ?>
                <a href="<?php echo esc_url('https://app.edenai.run/user/login/'); ?>" target="_blank"
                    rel="nofollow"><?php esc_html_e('EdenAI Account', 'ai-postpix'); ?></a>.
            </div>
     <a href="<?php echo esc_url('/wp-admin/admin.php?page=ai-postpix-settings'); ?>"
        class="aipstx-key-settings-button"><?php esc_html_e('Go to settings', 'ai-postpix'); ?></a>
        </div>
    <?php endif; ?>
        <div class="postpix-genimg-form">
            <div class="ai-image-generator-container">
                 <div class="ai-image-generator-split"><span>SETTINGS</span></div>
                <div class="prompt-section"><div class="prompt-things">
                    <label for="prompt">Prompt:</label>
                    <div class="prompt-loader" style="display: none;"></div>
                                    <?php
                                        $is_aipstx_imp_available = class_exists('\\AIPSTX\\AIPSTX_improve');
                                        $upgrade_url = aipstx_fs()->get_upgrade_url();
                                        ?>
                                    
                                    <button id="improve-prompt-button" class="improve-button" <?php if (!$is_aipstx_imp_available): ?>
                                            onclick="window.open('<?php echo esc_url($upgrade_url); ?>', '_blank')" <?php endif; ?>>
                                        <i class="fas fa-hand-sparkles"></i>&nbsp;&nbsp;<div class="improve-text">Improve My Prompt</div><var>PRO</var>
                                    </button></div>
                    <textarea id="pv_prompt" name="pv_prompt"
							placeholder="Describe the image you want to generate."></textarea>

                   <!-- <label for="negative-prompt">Negative Prompt (Optional)</label>
                    <textarea id="negative_prompt" name="negative_prompt"
							placeholder="Describe what you don't want in the image. e.g. Without any humans, animals, vehicles, or buildings."></textarea>
                --></div> 
                <div class="ai-image-generator-split"></div>
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
                            <div class="postpix-form-group">
							<label for="postpix_image_count">Number of Images:</label>
<input type="range" id="postpix_image_count" name="postpix_image_count" min="1" max="10" value="1" oninput="updateRangeDisplay(this.value)">
                            <span id="rangeValueDisplay">1</span> 
                            </div>
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
                <p style="color:#9c68e0;font-size: 0.7rem"> If you cannot select a resolution, the currently selected model does not have that resolution. If you cannot select a model, the selected resolution does not apply to that model. The same situation applies when choosing the number of images.</p>
               <button type="button" id="aipstx_create_image" class="button button-primary">
						<i class="fas fa-paint-brush"></i>&nbsp;&nbsp;Generate Images
					</button>
            </div>
              <div class="ai-image-generator-container-middle">
           <div id="pv_images_container" style="margin: 20px;">
				<!-- Oluşturulan görseller burada gösterilecek -->
			</div>
        </div>
            <div class="ai-image-generator-container-right"> 
                <div class="ai-image-generator-split"><span>CUSTOMIZATION (OPTIONAL)</span></div>
                <div class="filter-section">
                <label for="style">Image Style:</label>
							<select id="style" name="style">
								<option value="">Select a style...</option>
								<?php foreach ($aipstx_img_styles as $aipstx_style_key => $aipstx_style_label): ?>
                        <option value="<?php echo esc_attr($aipstx_style_key); ?>">
                            <?php echo esc_html($aipstx_style_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="effects">Effect:</label>
							<select id="effects" name="effects">
								<option value="">Select an effect style...</option>
								<?php foreach ($aipstx_img_effects as $aipstx_effect_key => $aipstx_effect_label): ?>
                        <option value="<?php echo esc_attr($aipstx_effect_key); ?>">
                            <?php echo esc_html($aipstx_effect_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="artist">Artist:</label>
							<select id="artist" name="artist">
								<option value="">Select an artist...</option>
								<?php foreach ($aipstx_img_artists as $aipstx_artist_key => $aipstx_artist_label): ?>
                        <option value="<?php echo esc_attr($aipstx_artist_key); ?>">
                            <?php echo esc_html($aipstx_artist_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                	<label for="photography">Photography:</label>
							<select id="photographs" name="photographs">
								<option value="">Select a photography style...</option>
								<?php foreach ($aipstx_img_photographs as $aipstx_photography_key => $aipstx_photography_label): ?>
                            <option value="<?php echo esc_attr($aipstx_photography_key); ?>">
                                <?php echo esc_html($aipstx_photography_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="cameras">Camera:</label>
							<select id="cameras" name="cameras">
								<option value="">Select a camera style...</option>
								<?php foreach ($aipstx_img_cameras as $aipstx_camera_key => $aipstx_camera_label): ?>
                            <option value="<?php echo esc_attr($aipstx_camera_key); ?>">
                                <?php echo esc_html($aipstx_camera_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="lightings">Lighting:</label>
							<select id="lightings" name="lightings">
								<option value="">Select a lighting style...</option>
								<?php foreach ($aipstx_img_lightings as $aipstx_lighting_key => $aipstx_lighting_label): ?>
                            <option value="<?php echo esc_attr($aipstx_lighting_key); ?>">
                                <?php echo esc_html($aipstx_lighting_label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>


                </div><p style="color:#9c68e0;"> For the styles to work correctly, you must delete the style statements in your prompt (e.g.: a highly
							detailed
							and realistic digital painting) and you should make a maximum of 3 adjustments, more than that and you may not get the desired result.</p>
            </div>
                <div id="error-modal" class="error-modal" style="display:none;">
	<div class="error-modal-content">
		<span class="error-modal-close">&times;</span>
		<i class="fas fa-exclamation-triangle"></i>
			<h2>An error occurred</h2>
		<p id="error-message"></p> 
	</div>
</div>
<!-- Image Name Modal -->
<div id="image-name-modal" class="naming-modal">
  <div class="naming-modal-content">
    <span class="naming-close">&times;</span>
    <p>Please enter a name for the image:</p>
    <input type="text" id="image-name-input" placeholder="Image name">
    <button id="save-image-name-btn">Save to Library</button>
    <div id="naming-modal-feedback" class="naming-modal-feedback"></div>
  </div>
</div>


<!-- Improve Prompt -->
<div id="prompt-modal" style="display:none;">
				<div id="modal-header">
					<span class="modal-title">Prompt Suggestions</span>
					<button class="close-modal">&times;</button>
				</div>
				<div id="prompt-suggestions">
					<!-- AJAX ile doldurulacak prompt önerileri burada olacak -->
				</div>
			</div>
            
            <script>
                function updateRangeDisplay(value) {
                document.getElementById('rangeValueDisplay').textContent = value;
                    }
                document.addEventListener('DOMContentLoaded', function () {
                    var openButton = document.getElementById('openInstructions');
                    var closeButton = document.getElementById('closePopup');
                    var popupWindow = document.getElementById('aipstx-instructions');

                    // Bağlantıya tıklandığında popup'ı göster
                    openButton.addEventListener('click', function (event) {
                        event.preventDefault(); // Varsayılan bağlantı işlemini durdur
                        popupWindow.style.display = 'flex';
                    });

                    // Kapatma butonuna tıklandığında popup'ı gizle
                    closeButton.addEventListener('click', function () {
                        popupWindow.style.display = 'none';
                    });

                    // Popup dışındaki bir alana tıklandığında popup'ı gizle
                    window.addEventListener('click', function (event) {
                        if (event.target == popupWindow) {
                            popupWindow.style.display = 'none';
                        }
                    });
                });
            </script>
            <div class="aipstx-instructions" id="aipstx-instructions">
                <div class="popup-content">
                    <span class="close-button" id="closePopup">&times;</span>
                    <h3>How to use AI Postpix in Posts step by step?</h3>
                    <ul>
                        <li>After entering your API keys in the relevant fields, <span>Go to any of your posts where you want to create a image and you can see the AI Postpix area at the
                            bottom.</span> </li>
                        <li>When you have completed your blog post, select any engine of your choice in the
                            <span>"Generate
                                Image with:"</span> field on the right side of the AI Postpix area. <div class="spacer">
                                <span>(This choice affects both the prompt you will find and the model you will use to
                                    generate the image.)</span>
                            </div>
                        </li>
                        <li>To find the most suitable prompt for your blog post, click on the <span>"Find Prompt"</span>
                            button and wait for the AI to analyze your post. After that you can edit the generated
                            prompt
                            according to your wishes. </li>
                        <li>With the <span>"Improve My Prompt"</span> button, one of the features of the Pro version, 5
                            improved versions of your existing prompt are created and you can choose the one you want
                            and
                            use it. You can also use this feature unlimitedly for each prompt <div class="spacer">
                                <span>(Prompts become more detailed and longer as they are improved).</span>
                            </div>
                        </li>
                        <li>Select how many images you want to create in the "Number of Images" section and in which
                            resolutions you want to create images in the resolution section.<div class="spacer">
                                <span>(Some
                                    models may not have some resolutions and number of images selection.)</span>
                            </div>
                        </li>
                        <li>With <span>"Image Styles"</span>, you can create images
                            in
                            any custom style you wish.</li>
                        <li>Then click on <span>"Create Images"</span> and wait until the images are created. After the
                            images are created, you can add the created images to your posts with the <span>"Add to
                                Post"</span> button and make them the featured images of your posts with the <span>"Set
                                as
                                Featured Image"</span> button.</li>
                        <li>You can save the image to your library with the <span>"Save to Library"</span> button on the
                            top
                            right of the created images, and you can download it directly to your computer with the
                            <span>"Save to PC"</span> button.
                        </li>
                    </ul>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var scrollButton = document.getElementById('scrollButton');
                            var popupcontent = document.querySelector('.popup-content');

                            function checkScroll() {
                                if (popupcontent.scrollTop < (popupcontent.scrollHeight - popupcontent.offsetHeight)) {
                                    scrollButton.style.opacity = 1;
                                } else {
                                    scrollButton.style.opacity = 0;
                                }
                            }

                            popupcontent.addEventListener('scroll', checkScroll);

                            scrollButton.addEventListener('click', function () {
                                popupcontent.scrollBy({
                                    top: popupcontent.offsetHeight,
                                    behavior: 'smooth'
                                });
                            });

                            checkScroll();
                        });
                    </script> <button id="scrollButton"><i class="fas fa-arrow-down"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
        }
    }

    AIPSTX_admin_genimg::get_instance();
}