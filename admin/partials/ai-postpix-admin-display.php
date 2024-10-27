<?php if (!defined('ABSPATH'))
	exit; // Exit if accessed directly      
?>
<div id="postpixadmin">
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
							<div class="postpix-nav-list-item"><a
						href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=ai-postpix" rel="nofollow"><i class="fas fa-magic"></i></i> AI Image Generator</a></div>
			<div class="postpix-nav-list-item active"><a
						href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=ai-postpix-settings" rel="nofollow"><i
						class="fas fa-cog"></i> Settings</a></div>
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
		<?php $aipstx_edenai_key = get_option('aipstx_edenai_key');
				$aipstx_openai_key = get_option('aipstx_openai_key');
				$aipstx_stability_key = get_option('aipstx_stability_key');
		if (empty($aipstx_edenai_key) && empty($aipstx_openai_key) && empty($aipstx_stability_key)): ?>
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
			</div>
	<?php endif; ?>
		<div class="admin-loader" style="display:none;"></div>
		<div class="postpix-settings-form">
			<div class="admin-left">

				<form method="post" action="" id="postpix-settings-form">
					<?php
					wp_nonce_field('aipstx_settings_action', 'aipstx_settings_nonce');
					?>
					<table class="aipstx-form-table">
						<tr valign="top">
							<th scope="row">OpenAI API Key:</th>
							<td>
								<div class="input-button-group">
									<input type="text" id="aipstx_openai_key" name="aipstx_openai_key"
										value="<?php echo esc_attr(get_option('aipstx_openai_key')); ?>"
										placeholder="Enter Your OpenAI API Key Here">
									<button type="button" id="aipstx_test_openai_button"
										class="button button-secondary">Test My OpenAI
										API</button>
								</div>
								<a style="font-size: 15px; color:#a0a0a0;"
									href="https://help.openai.com/en/articles/4936850-where-do-i-find-my-api-key"
									target="_blank"><i class="fas fa-info-circle"></i> How can you access the OpenAI API
									key?</a>
							</td>
						</tr>
					</table>
					<table class="aipstx-form-table">
						<tr valign="top">
							<th scope="row">StabilityAI API Key:</th>
							<td>
								<div class="input-button-group">
									<input type="text" id="aipstx_stability_key" name="aipstx_stability_key"
										value="<?php echo esc_attr(get_option('aipstx_stability_key')); ?>"
										placeholder="Enter Your Stability AI API Key Here">
								</div>
								<a style="font-size: 15px; color:#a0a0a0;" href="https://platform.stability.ai/account/keys"
									target="_blank"><i class="fas fa-info-circle"></i> How can you access the Stability AI API key?</a>
							</td>
						</tr>
					</table>

					<table class="aipstx-form-table">
						<tr valign="top">
							<th scope="row">Eden AI API Key:</th>
							<td>
								<div class="input-button-group">
									<input type="text" id="aipstx_edenai_key" name="aipstx_edenai_key"
										value="<?php echo esc_attr(get_option('aipstx_edenai_key')); ?>"
										placeholder="Enter Your EdenAI API Key Here">
									<button type="button" id="test_api_button" class="button button-secondary">Test My
										EdenAI API</button>
								</div>
								<a style="font-size: 15px; color:#a0a0a0;"
									href="https://docs.edenai.co/reference/start-your-ai-journey-with-edenai"
									target="_blank"><i class="fas fa-info-circle"></i> How can you access the EdenAI API
									key?</a>
							</td>
						</tr>
					</table>

					<table class="aipstx-form-table">
						<tr>
							<td colspan="2" class="prompt-title">Engine for Find Prompt:</td>
						</tr>
						<td>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine" value="gpt-4o" <?php checked('gpt-4o', get_option('aipstx_prompt_engine', 'gpt-4o')); ?> /> <span class="slider round"></span>
							</label><span class="option-text">GPT-4o (New)</span><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine" value="gpt-4" <?php checked('gpt-4', get_option('aipstx_prompt_engine', 'gpt-4')); ?> /> <span
									class="slider round"></span>
							</label><span class="option-text">GPT-4</span><a class="fpromptlink"
								href="https://help.openai.com/en/articles/7102672-how-can-i-access-gpt-4"
								target="_blank"><i class="fas fa-info-circle"></i> How can you access the GPT-4 API?
								(paid and conditional)</a><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine" value="gpt-4-0613"
									<?php checked('gpt-4-0613', get_option('aipstx_prompt_engine', 'gpt-4-0613')); ?> />
								<span class="slider round"></span>
							</label><span class="option-text">gpt-4-0613 (paid and conditional)</span><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine"
									value="gpt-3.5-turbo-1106" <?php checked('gpt-3.5-turbo-1106', get_option('aipstx_prompt_engine', 'gpt-3.5-turbo-1106')); ?> /> <span
									class="slider round"></span>
							</label><span class="option-text">gpt-3.5-turbo-1106</span><a class="fpromptlink"
								href="https://help.openai.com/en/articles/4936850-where-do-i-find-my-api-key"
								target="_blank"><i class="fas fa-info-circle"></i> How can you access the OpenAI API
								key?</a><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine" value="gpt-3.5-turbo"
									<?php checked('gpt-3.5-turbo', get_option('aipstx_prompt_engine', 'gpt-3.5-turbo')); ?> />
								<span class="slider round"></span>
							</label><span class="option-text">gpt-3.5-turbo (most stable)</span><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine"
									value="gpt-3.5-turbo-16k" <?php checked('gpt-3.5-turbo-16k', get_option('aipstx_prompt_engine', 'gpt-3.5-turbo-16k')); ?> /> <span
									class="slider round"></span>
							</label><span class="option-text">gpt-3.5-turbo-16k</span><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine"
									value="gpt-3.5-turbo-instruct" <?php checked('gpt-3.5-turbo-instruct', get_option('aipstx_prompt_engine', 'gpt-3.5-turbo-instruct')); ?> /> <span
									class="slider round"></span>
							</label><span class="option-text">gpt-3.5-turbo-instruct</span><br>
							<label class="switch"><input type="radio" name="aipstx_prompt_engine" value="eden_ai" <?php checked('eden_ai', get_option('aipstx_prompt_engine', 'eden_ai')); ?> /> <span class="slider round"></span>
							</label><span class="option-text">EdenAI</span><a class="fpromptlink"
								href="https://docs.edenai.co/reference/start-your-ai-journey-with-edenai" target="_blank"><i
									class="fas fa-info-circle"></i> How can you access the EdenAI API key
								for free? (with $1 Free API
								credit)</a><br>
							<p style="font-size: 15px; color:#a0a0a0;">*You can find how to obtain API keys for EdenAI,
								OpenAI and GPT4 in the
								respective links.</p>
							</tr>
					</table>
					<table class="aipstx-form-table">
						<tr valign="top">
							<th scope="row">Metabox for Posts:</th>
							<?php $is_checked = get_option('aipstx_show_metabox') ? 'checked' : ''; ?>
							<td><label class="switch"><input type="checkbox" id="aipstx_show_metabox" name="aipstx_show_metabox" <?php echo esc_attr($is_checked); ?>><span class="slider round"></span></label></td>
						</tr>
						<tr valign="top">
							<th scope="row">Theme Style:</th>
							<td>
								<label class="switch"><input type="radio" name="aipstx_theme_style" value="light" <?php checked(get_option('aipstx_theme_style', 'light'), 'light', true); ?> /><span
										class="slider round"></span>
								</label><span class="option-text">Light Mode</span><br>
								<label class="switch"><input type="radio" name="aipstx_theme_style" value="dark" <?php checked(get_option('aipstx_theme_style', 'light'), 'dark'); ?> /><span
										class="slider round"></span>
								</label><span class="option-text">Dark Mode</span></p>
							</td>
						</tr>
					</table>
					<?php submit_button('Save Changes', 'primary', 'postpix-settings-submit'); ?>
				</form>
			</div>
			<script>
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
					<h3>How to use AI Postpix step by step?</h3>
					<ul>
						<li>After entering your API keys in the relevant fields you can see the AI Postpix area at the
							bottom of WordPress blog posts. </li>
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