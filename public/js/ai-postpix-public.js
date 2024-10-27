jQuery(document).ready(function ($) {

	var button = document.getElementById('aipstx_create_image');
	var container = document.getElementById('pv_images_container');

	if (button && container) {
		button.addEventListener('click', function () {
			container.style.display = 'flex';
		});
	}

	var textarea = document.getElementById('pv_prompt');
	var button = document.getElementById('aipstx_create_image');

	// Buton ve textarea varsa, fonksiyonları ve olay dinleyicilerini etkinleştir
	if (textarea && button) {
		// Butonun başlangıç durumunu ayarla
		toggleButtonState();

		// Textarea'daki değişiklikleri dinle
		textarea.addEventListener('input', function () {
			toggleButtonState();
		});
	}

	function toggleButtonState() {
		// Eğer textarea boşsa butonu devre dışı bırak, değilse etkinleştir
		if (textarea.value.trim() === '') {
			button.disabled = true;
		} else {
			button.disabled = false;
		}
	}

	function showAlert(message, success = true) {
		var alertBox = $('#postpix-alert');
		alertBox.removeClass('postpix-alert-success postpix-alert-error');

		if (success) {
			alertBox.addClass('postpix-alert-success').text(message);
		} else {
			alertBox.addClass('postpix-alert-error').text(message);
		}

		alertBox.fadeIn(500, function () {
			setTimeout(function () {
				alertBox.fadeOut(500);
			}, 3000); // 3 saniye sonra kaybolacak
		});
	}

	// Function to add image to post content after adding it to the media library
	var postId = jQuery("#post_ID").val();

	function addToPostContent(imageUrl, postId, button) {
		var mediaId = null; // This will be set after the AJAX call

		// AJAX call to add image to media library
		$.ajax({
			url: aipstxAjax.ajax_url,
			type: "POST",
			data: {
				action: "aipstx_add_media_library",
				nonce: aipstxAjax.nonce,
				image_url: imageUrl,
				post_id: postId,
				prompt: $("#pv_prompt").val() // Getting the prompt value from the input
			},
			success: function (response) {
				if (response.success) {
					mediaId = response.data.attachment_id; // Set the media ID from the AJAX response

					// AJAX call to add image to post content
					$.ajax({
						url: aipstxAjax.ajax_url,
						type: "POST",
						data: {
							action: "aipstx_add_post_content",
							nonce: aipstxAjax.nonce,
							post_id: postId,
							media_id: mediaId
						},
						beforeSend: function () {
								button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
							},
						success: function (response) {
							if (response.success) {
								// Check if the Gutenberg editor is available
								if (wp.data && wp.data.select("core/editor")) {
									// For Gutenberg, update the post content
									var currentContent = wp.data.select("core/editor").getEditedPostContent();
									var newContent = currentContent + response.data.image_html;
									wp.data.dispatch("core/editor").editPost({ content: newContent });
								} else {
									// For the Classic Editor, update the post content
									if (typeof tinyMCE !== 'undefined' && tinyMCE.get('content')) {
										tinyMCE.get('content').setContent(tinyMCE.get('content').getContent() + response.data.image_html);
									} else {
										// Fallback for the text area (no TinyMCE)
										var textArea = $('#content');
										textArea.val(textArea.val() + response.data.image_html);
									}
								}
								showAlert("Successfully added an image to the post content. If it does not appear, refresh the page.", true);
								// Modify only the clicked button
								button.prop('disabled', true).text('Added to post').addClass('button-disabled');
							} else {
								showAlert("Failed to add image to post content.", false);
							}
						},
						error: function () {
							showAlert("An error occurred while adding the image to the post content.", false);
						}
					});
				} else {
					showAlert("Failed to add image to media library.", false);
				}
			},
			error: function () {
				showAlert("An error occurred while adding the image to the media library.", false);
			}
		});
	}

	

	// Event listener for the 'Add to Library' button
	$(document).on("click", ".add-to-post-btn", function () {
		var imageUrl = $(this).data("image-url");
		var postId = jQuery("#post_ID").val();
		var button = $(this); // Get the current button that was clicked
		addToPostContent(imageUrl, postId, button); // Pass the button to the function
	});

$("#aipstx_find_prompt").click(function (e) {
    e.preventDefault();
    // Yükleme göstergesini göster
    $(".prompt-loader").show();

    var postContent, postTitle;
    if (window.wp && wp.data && wp.data.select("core/editor")) {
        postContent = wp.data.select("core/editor").getEditedPostContent();
        postTitle = wp.data.select("core/editor").getEditedPostAttribute('title');
    } else {
        postContent = $("#content").val();
        postTitle = $("#title").val(); // Eski editör için başlık
    }

    // İçerik veya başlık boşsa hata ver ve işlemi durdur
    if (!postContent.trim() || !postTitle.trim()) {
        showErrorModal("Error: Title and content cannot be empty.");
        $(".prompt-loader").hide(); // Yükleme göstergesini gizle
        return; // Fonksiyondan çık
    }

    // Post içeriğini temizle
    var cleanContent = postContent
        .replace(/<!--(.|\s)*?-->/g, '') // WordPress blok yorumlarını kaldır
        .replace(/<[^>]+>/g, '') // HTML etiketlerini kaldır
        .replace(/http[s]?:\/\/[^\s]+/g, '') // Görsel URL'lerini kaldır
        .replace(/&nbsp;/g, ' ') // &nbsp; karakterlerini normal boşlukla değiştir
        .replace(/\s+/g, ' ') // Fazladan boşlukları kaldır
        .trim(); // İçeriğin başında ve sonunda kalan boşlukları kaldır

    var engineText = $("#engine option:selected").text();

    var postData = {
        action: "aipstx_find_prompt",
        nonce: aipstxAjax.nonce,
        postTitle: postTitle,
        postContent: cleanContent,
        engine: engineText
    };

    $.post(aipstxAjax.ajax_url, postData, function (response) {
        if (response.success) {
            $("#pv_prompt").val(response.data);
            toggleButtonState();
        } else {
            // Hata mesajının modal popup ile gösterilmesi
            var errorMessage = response.data && response.data.message ? response.data.message : "Unknown error occurred";
            showErrorModal("Error: " + errorMessage);
        }
        // İşlem bittiğinde yükleme göstergesini gizle
        $(".prompt-loader").hide();
    });
});



	$("#aipstx_create_image").click(function (e) {
		e.preventDefault();
		var prompt = $("#pv_prompt").val();
		var negativePrompt = $("#negative_prompt").val(); 
		var imageCount = $("#postpix_image_count").val(); // Dropdown'dan alınan görsel sayısı
		var engine = $("#engine").val();
		var resolution = $("#resolution").val();
		var aipstx_style = $('#style').val();
		var aipstx_artist = $('#artist').val();
		var aipstx_photographs = $('#photographs').val();
		var aipstx_lightings = $('#lightings').val();
		var aipstx_cameras = $('#cameras').val();
		var aipstx_effects = $('#effects').val();

		// Yükleme animasyonu başlatılıyor ve buton devre dışı bırakılıyor
    	$("#aipstx_create_image").prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Generating...');
    	$("#pv_images_container").html('<div class="aipstx-loader"><div class="aipstx-load-inner load-one"></div><div class="aipstx-load-inner load-two"></div><div class="aipstx-load-inner load-three"></div><span class="aipstx-loader-text">Generating...</span></div>');
		$.ajax({
			url: aipstxAjax.ajax_url,
			type: "POST",
			data: {
				action: "aipstx_create_image",
				nonce: aipstxAjax.nonce,
				prompt: prompt,
				negative_prompt: negativePrompt,
				image_count: imageCount,
				resolution: resolution,
				engine: engine,
				style: aipstx_style,
				artist: aipstx_artist,
				photography: aipstx_photographs,
				lighting: aipstx_lightings,
				camera: aipstx_cameras,
				effect: aipstx_effects

			},
			success: function (response) {
				$("#pv_images_container").empty();
				if (response.success) {
					response.data.forEach(function (imageUrl) {
            var imageHtml = '<div class="pv_image_container">';
            imageHtml += '<img src="' + imageUrl + '" class="pv_image_preview" alt="Generated Image">';

            // Konteyner 'metabox' kontekstindeyse butonları ekle
            if ($("#pv_images_container").data('context') === 'metabox') {
                imageHtml += '<button type="button" class="add-to-post-btn" data-image-url="' + imageUrl + '">Add to Post</button>';
                imageHtml += '<button type="button" class="set-featured-image-btn" data-image-url="' + imageUrl + '">Set as Featured Image</button>';
            }

            imageHtml += "</div>"; // pv_image_container div'ini kapat
            $("#pv_images_container").append(imageHtml);
        });
					// Butonun metnini değiştir

					$("#aipstx_create_image")
						.text("Generate More Images").prop('disabled', false); // Butonu tekrar etkinleştir

					var isSettingFeaturedImage = false;
					$(document).off("click", ".set-featured-image-btn").on("click", ".set-featured-image-btn", function () {
						 if(isSettingFeaturedImage) {
        					return;
    						}
    					// İşlem başlatıldı olarak işaretleyin
    					isSettingFeaturedImage = true;
						var button = $(this);
						//    var postId = button.data('post-id'); // The ID of the post to set the featured image for
						var imageUrl = button.data("image-url"); // The URL of the image to be set as featured

						// First, add the image to the media library
						$.ajax({
							url: aipstxAjax.ajax_url,
							type: "POST",
							data: {
								action: "aipstx_add_media_library",
								image_url: imageUrl,
								post_id: postId,
								nonce: aipstxAjax.nonce,
								prompt: $("#pv_prompt").val(), // Getting the prompt value from the input
							},
							beforeSend: function () {
								button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Setting...');
							},
							success: function (response) {
								if (response.success) {
									isSettingFeaturedImage = false;
									// The image was successfully added to the media library, now set it as the featured image
									var attachmentId = response.data.attachment_id; // The attachment ID returned from the AJAX response
									setAsFeaturedImage(postId, attachmentId, button);
									showAlert("The image was successfully set as the featured image.", true);
								} else {
									button.prop("disabled", false).text("Set as Featured Image");
									showAlert("Failed to add image to media library.", false);
								}
							},
							error: function () {
								isSettingFeaturedImage = false;
								button.prop("disabled", false).text("Set as Featured Image");
								showAlert("An error occurred.", false);
							},
						});
					});

    $('.pv_image_container').each(function() {
        var imageUrl = $(this).find('img').attr('src');
        var saveButtonHtml = '<button type="button" class="save-to-library-btn" data-image-url="' + imageUrl + '"><i class="fas fa-save"></i><span class="button-text">Save to Library</span></button>';
        var saveToPcButtonHtml = '<button type="button" class="save-to-pc-btn" data-image-url="' + imageUrl + '"><i class="fas fa-download"></i><span class="button-text">Save to PC</span></button>';

        $(this).prepend(saveButtonHtml).prepend(saveToPcButtonHtml);
    });

    $(document).on('click', '.save-to-library-btn', function() {
        var $button = $(this);
        var imageUrl = $button.data('image-url');
        var postId = $("#post_ID").val();

        if (postId) {
            disableButton($button, true, 'saving');
            saveImageToLibrary(imageUrl, '', $button, postId);
        } else {
            $('#image-name-modal').data('image-url', imageUrl).show();
        }
    });

    $('#save-image-name-btn').off("click").on("click", function() {
        var imageName = $("#image-name-input").val().trim();
        var imageUrl = $('#image-name-modal').data('image-url');
        var $saveLibraryButton = $('.save-to-library-btn[data-image-url="' + imageUrl + '"]');
        var $saveModalButton = $(this);

        if (imageName) {
            disableButton($saveLibraryButton, true, 'saving');
            disableButton($saveModalButton, true, 'saving');
            saveImageToLibrary(imageUrl, imageName, $saveLibraryButton);
        } else {
            alert("Please enter a name for the image.");
        }
    });

    function disableButton($button, disable, state = 'normal') {
        if (disable) {
            if (state === 'saving') {
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>&nbsp; &nbsp; Saving...');
            } else if (state === 'saved') {
                $button.prop('disabled', true).addClass('button-disabled').html('<i class="fas fa-check-circle"></i>&nbsp; &nbsp; Saved');
            }
        } else {
            $button.prop('disabled', false).removeClass('button-disabled').html('<i class="fas fa-save"></i><span class="button-text"> Save to Library</span>');
        }
    }

    function saveImageToLibrary(imageUrl, imageName, $button, postId = '') {
        var isSavingToLibrary = false;
        if (isSavingToLibrary) return;
        isSavingToLibrary = true;

        $.ajax({
            url: aipstxAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'aipstx_add_media_library',
                image_url: imageUrl,
                image_name: imageName,
                post_id: postId,
                nonce: aipstxAjax.nonce
            },
            beforeSend: function() {
                $('#naming-modal-feedback').removeClass('success').hide();
            },
            success: function(response) {
				if (response.success) {
					disableButton($button, true, 'saved');
					$('#save-image-name-btn').prop('disabled', true).html('<i class="fas fa-check-circle"></i> Saved');
                    $('#naming-modal-feedback').addClass('success').text('Image saved successfully!').show();
					setTimeout(function () {
						$('#naming-modal-feedback').fadeOut(500);
                        $('#image-name-modal').fadeOut();
                    }, 1500); // Increase delay here to allow message to be read
                } else {
                    alert(response.data.message);
                    disableButton($button, false);
                    disableButton($('#save-image-name-btn'), false);
                }
                isSavingToLibrary = false;
            },
            error: function() {
                alert('Failed to save image.');
                disableButton($button, false);
                disableButton($('#save-image-name-btn'), false);
                isSavingToLibrary = false;
            }
        });
    }

 // Modal kapatma işlevi
function closeModal() {
  $('#image-name-modal').hide();
  $("#image-name-input").val(''); // Input alanını temizle
	$('#naming-modal-feedback').empty().hide(); // Geribildirim mesajlarını temizle ve gizle
	$('#save-image-name-btn').prop('disabled', false).html('<i class="fas fa-save"></i> Save to Library');
}

$(document).ready(function() {
  // Kapatma butonuna tıklandığında modalı kapat
  $('.naming-close').on('click', closeModal);

  // Dokümanın herhangi bir yerine tıklandığında kontrol et
  $(document).mouseup(function(e) {
    var container = $(".naming-modal-content");

    // Eğer tıklanan alan modal-content içinde değilse modalı kapat
    if (!container.is(e.target) && container.has(e.target).length === 0) {
      closeModal();
    }
  });
});

// 'Save to PC' butonuna tıklandığında indirme işlemi gerçekleştirilir
$(document).on('click', '.save-to-pc-btn', function (event) {
    event.preventDefault(); // Olayın varsayılan davranışını engelle

    var imageUrl = $(this).data('image-url');

    if (imageUrl.startsWith('data:image')) {
        // Base64 kodlanmış görseli işleme
        var link = document.createElement('a');
        link.href = imageUrl;
        link.download = 'aipostpix-image.png';

        // Linki bir kere tıklamak için kullan
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } else {
        // Normal URL'li görseli işleme
        var link = document.createElement('a');
        link.href = imageUrl;
        link.target = '_blank';
        link.click();
    }
});






					// Function to set the uploaded image as the featured image of the post
					function setAsFeaturedImage(postId, attachmentId, button) {
						$.ajax({
							url: aipstxAjax.ajax_url,
							type: "POST",
							data: {
								action: "aipstx_ensure_image_and_set_featured",
								post_id: postId,
								image_id: attachmentId,
								nonce: aipstxAjax.nonce,
							},
							success: function (response) {
								if (response.success) {
									var newFeaturedImageUrl = response.data.featured_image_url;

									// Gutenberg editörü için
									if (wp.data && wp.data.select("core/editor")) {
										// Öne çıkan görselin URL'sini güncelle

										// Gutenberg veri deposunu güncelleyin
										wp.data.dispatch('core/editor').editPost({ featured_media: attachmentId });

										// "Öne Çıkan Görsel Belirle" düğmesini gizle
										$('.components-button editor-post-featured-image__toggle').hide();

									} else {
										// Klasik editör için
										var $thumbnailImg = jQuery('#set-post-thumbnail img');
										if ($thumbnailImg.length > 0) {
											// Eğer görsel varsa, 'src' ve 'srcset' niteliklerini güncelle
											var newImageUrlWithTimestamp = newFeaturedImageUrl + '?t=' + new Date().getTime();
											$thumbnailImg.attr('src', newImageUrlWithTimestamp);
											$thumbnailImg.attr('srcset', newImageUrlWithTimestamp); // srcset'i de güncelle
										} else {
											// Eğer görsel yoksa, yeni bir <img> etiketi oluştur
											var newImageTag = '<img src="' + newFeaturedImageUrl + '" width="266" height="266" class="attachment-266x266 size-266x266" alt="" decoding="async" loading="lazy">';
											jQuery('#set-post-thumbnail').prepend(newImageTag);
										}
										// Görseli göstermek için var olan bağlantıyı göster
										jQuery('#set-post-thumbnail').show();
									}

									button.prop("disabled", true).text("Featured Image Set").addClass('button-disabled');
								} else {
									button.prop("disabled", false).text("Set as Featured Image");
									showAlert("Failed to set featured image.", false);
								}
							},
							error: function (err) {
								button.prop("disabled", false).text("Set as Featured Image");
								showAlert("An error occurred while setting the featured image.", false);
								console.log(err);
							},
						});
					}

				} else {
					var errorMessage = response.data && response.data.message ? response.data.message : 'An unknown error occurred. Please check your API keys.';
        			showErrorModal('Error: ' + errorMessage);
					$("#aipstx_create_image").prop('disabled', false).text("Generate Images");
				}
			},
				error: function (jqXHR, textStatus, errorThrown) {
    // Hata meydana geldiğinde bu blok çalışacak
    var errorMessage = 'An unknown error occurred. Please check your API keys.'; // Varsayılan hata mesajı

    // Hata mesajını denetle ve ayarla
    if (jqXHR.responseJSON && jqXHR.responseJSON.data && jqXHR.responseJSON.data.message) {
        errorMessage = jqXHR.responseJSON.data.message;
    } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
        errorMessage = jqXHR.responseJSON.message;
    }

    showErrorModal('Error: ' + errorMessage);
    $("#aipstx_create_image").prop('disabled', false).text("Generate Images");
},
		});
	});


	function showErrorModal(message) {
  // Mesajı modal içine yerleştir
  document.getElementById('error-message').textContent = message;

  // Modalı göster
  var modal = document.getElementById('error-modal');
  modal.style.display = "block";

  // Kapat butonuna tıklandığında modalı kapat
  modal.querySelector('.error-modal-close').onclick = function() {
    modal.style.display = "none";
  }

  // Modalın dışına tıklandığında modalı kapat
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
	}
	
	const engineSelect = document.getElementById('engine');
    const imageCountRange = document.getElementById('postpix_image_count');
	const rangeValueDisplay = document.getElementById('rangeValueDisplay');
	const resolutionSelect = document.getElementById('resolution');

if (engineSelect && imageCountRange && rangeValueDisplay && resolutionSelect) {	
    engineSelect.addEventListener('change', function() {
        const selectedEngine = engineSelect.value;
        if (selectedEngine === 'openai' || selectedEngine === 'replicate' || selectedEngine === 'deepai') {
            imageCountRange.disabled = true;
            imageCountRange.value = 1;
            updateRangeDisplay(1);
        } else {
            imageCountRange.disabled = false;
        }
    });

    imageCountRange.addEventListener('input', function() {
        updateRangeDisplay(this.value);
    });

    function updateRangeDisplay(value) {
        rangeValueDisplay.textContent = value;
	}
	
// Initial check to apply disabling logic on page load
handleEngineChange();
handleResolutionChange();

engineSelect.addEventListener('change', function() {
    handleEngineChange();
});

resolutionSelect.addEventListener('change', function() {
    handleResolutionChange();
});

imageCountRange.addEventListener('input', function() {
    updateRangeDisplay(this.value);
});

function handleEngineChange() {
    const selectedEngine = engineSelect.value;

    // Reset resolution disabling
    disableAllResolutions(false);

    // Handle range input disabling
    if (selectedEngine === 'openai' || selectedEngine === 'replicate' || selectedEngine === 'deepai') {
        imageCountRange.disabled = true;
        imageCountRange.value = 1;
        updateRangeDisplay(1);
    } else {
        imageCountRange.disabled = false;
    }

    // Handle resolution disabling based on engine selection
    if (selectedEngine === 'dall-e3' || selectedEngine === 'openai') {
        disableResolutionOptions(['1024x1024'], false);
        disableResolutionOptions(['512x512', '1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'deepai') {
        disableResolutionOptions(['512x512'], false);
        disableResolutionOptions(['1024x1024', '1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'amazon') {
        disableResolutionOptions(['512x512', '1024x1024'], false);
        disableResolutionOptions(['1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'stabilityai') {
        disableResolutionOptions(['1024x1024', '1152x896', '1216x832', '1344x768', '1536x640', '640x1536', '768x1344', '896x1152'], false);
        disableResolutionOptions(['512x512', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'replicate') {
        disableResolutionOptions(['512x512', '1024x1024'], false);
        disableResolutionOptions(['1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'stable-diffusion-xl-1024-v1-0' || selectedEngine === 'stable-diffusion-v1-6') {
        disableResolutionOptions(['1024x1024', '1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536'], false);
        disableResolutionOptions(['512x512', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'], true);
    } else if (selectedEngine === 'stable-diffusion-core' || selectedEngine === 'sd3' || selectedEngine === 'sd3-turbo') {
        disableResolutionRatios(['16:9', '1:1', '21:9', '2:3', '3:2', '4:5', '5:4', '9:16', '9:21'], false);
    }
}

function handleResolutionChange() {
    const selectedResolution = resolutionSelect.value;

    // Reset engine disabling
    disableAllEngines(false);

    // Handle engine disabling based on resolution selection
    if (selectedResolution === '1024x1024') {
        disableEngineOptions(['deepai'], true);
    } else if (selectedResolution === '512x512') {
        disableEngineOptions(['dall-e3', 'openai'], true);
    } else if (['1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536'].includes(selectedResolution)) {
        disableEngineOptions(['dall-e3', 'openai', 'deepai', 'amazon', 'stabilityai', 'replicate'], true);
    } else if (['1024x576', '1280x720', '1920x1080', '1152x896', '1344x768', '2520x1080', '3360x1440', '1152x1152', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'].includes(selectedResolution)) {
        disableEngineOptions(['dall-e3', 'openai', 'deepai', 'amazon', 'stabilityai', 'replicate', 'stable-diffusion-xl-1024-v1-0', 'stable-diffusion-v1-6'], true);
    }
}

function updateRangeDisplay(value) {
    rangeValueDisplay.textContent = value;
}

function disableAllResolutions(disable) {
    const allResolutions = ['512x512', '1024x1024', '1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'];
    allResolutions.forEach(function(value) {
        const option = resolutionSelect.querySelector(`option[value="${value}"]`);
        if (option) {
            option.disabled = disable;
        }
    });
}

function disableAllEngines(disable) {
    const allEngines = ['dall-e3', 'openai', 'deepai', 'amazon', 'stabilityai', 'replicate', 'stable-diffusion-xl-1024-v1-0', 'stable-diffusion-v1-6', 'stable-diffusion-core', 'sd3', 'sd3-turbo'];
    allEngines.forEach(function(value) {
        const option = engineSelect.querySelector(`option[value="${value}"]`);
        if (option) {
            option.disabled = disable;
        }
    });
}

function disableResolutionOptions(values, disable) {
    values.forEach(function(value) {
        const option = resolutionSelect.querySelector(`option[value="${value}"]`);
        if (option) {
            option.disabled = disable;
        }
    });
}

function disableEngineOptions(values, disable) {
    values.forEach(function(value) {
        const option = engineSelect.querySelector(`option[value="${value}"]`);
        if (option) {
            option.disabled = disable;
        }
    });
}

function disableResolutionRatios(ratios, disable) {
    const allResolutions = ['512x512', '1024x1024', '1152x896', '896x1152', '1216x832', '1344x768', '768x1344', '1536x640', '640x1536', '1024x576', '1280x720', '1920x1080', '1152x896', '1152x1152', '2520x1080', '3360x1440', '896x1344', '1152x1728', '1152x768', '1728x1152', '768x960', '1152x1440', '1024x819', '1280x1024', '576x1024', '720x1280', '648x1512', '432x1008'];
    const ratioResolutions = {
        '16:9': ['1024x576', '1280x720', '1920x1080', '1152x896', '1344x768', '2520x1080', '3360x1440'],
        '1:1': ['512x512', '1024x1024', '1152x1152'],
        '21:9': ['1536x640', '640x1536', '2520x1080', '3360x1440', '648x1512', '432x1008'],
        '2:3': ['896x1152', '768x1344', '896x1344', '1152x1728'],
        '3:2': ['1216x832', '1728x1152', '1152x768'],
        '4:5': ['768x960', '1152x1440'],
        '5:4': ['1024x819', '1280x1024'],
        '9:16': ['576x1024', '720x1280'],
        '9:21': ['648x1512', '432x1008']
    };
    
    allResolutions.forEach(function(resolution) {
        const option = resolutionSelect.querySelector(`option[value="${resolution}"]`);
        if (option) {
            const isAllowed = ratios.some(ratio => ratioResolutions[ratio].includes(resolution));
            option.disabled = !isAllowed && disable;
        }
    });
}
	}
	
});