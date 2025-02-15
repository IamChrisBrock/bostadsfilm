jQuery(document).ready(function($) {
    // Make sure we're on the project gallery edit screen
    if (!$('body').hasClass('post-type-project_gallery')) {
        return;
    }

    // Function to initialize MediaElement players
    function initMediaElementPlayers() {
        $('.pg-video').each(function() {
            if (!$(this).data('mediaelementplayer')) {
                $(this).mediaelementplayer({
                    videoWidth: '100%',
                    videoHeight: '100%',
                    stretching: 'none',
                    enableAutosize: true,
                    features: ['playpause', 'progress', 'volume'],
                    success: function(mediaElement, originalNode, instance) {
                        // Set dimensions to match container
                        var container = $(originalNode).closest('.pg-video-container');
                        if (container.length) {
                            $(mediaElement).css({
                                width: '100%',
                                height: '100%',
                                position: 'absolute',
                                top: 0,
                                left: 0
                            });
                            
                            // Force resize event
                            instance.setPlayerSize('100%', '100%');
                        }
                        
                        // Pause other videos when playing one
                        mediaElement.addEventListener('play', function() {
                            $('.pg-video').each(function() {
                                if (this !== originalNode) {
                                    this.pause();
                                }
                            });
                        });
                    }
                });
            }
        });
    }

    // Initialize sortable
    $('#project-gallery-media-container').sortable({
        items: '.media-item',
        cursor: 'move',
        placeholder: 'media-item-placeholder',
        update: function() {
            updateMediaOrder();
        }
    }).disableSelection();

    // Media uploader
    var mediaUploader;
    $('#add-project-media').on('click', function(e) {
        e.preventDefault();

        // If the uploader object has already been created, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create the media uploader
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Media',
            button: {
                text: 'Add to Gallery'
            },
            multiple: true,
            library: {
                type: ['image', 'video']
            }
        });

        // When media is selected, run callback
        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            var container = $('#project-gallery-media-container');
            
            attachments.forEach(function(attachment) {
                var type = attachment.type;
                var thumbnail;

                if (type === 'video') {
                    // Try to get the video thumbnail
                    if (attachment.image && attachment.image.src) {
                        thumbnail = attachment.image.src;
                    } else if (attachment.thumb && attachment.thumb.src) {
                        thumbnail = attachment.thumb.src;
                    } else if (attachment.icon) {
                        thumbnail = attachment.icon;
                    } else {
                        thumbnail = wp.media.view.settings.defaultImagePlaceholder;
                    }
                } else {
                    thumbnail = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : 
                               (attachment.url || wp.media.view.settings.defaultImagePlaceholder);
                }

                var mediaContent = '';
                if (type === 'video') {
                    mediaContent = '<div class="pg-media-preview">' +
                        '<div class="pg-video-container">' +
                        '<video class="pg-video" preload="metadata" controls="controls">' +
                        '<source type="' + attachment.mime + '" src="' + attachment.url + '" />' +
                        '</video>' +
                        '</div>' +
                        '</div>' +
                        '<div class="media-tags">' +
                        '<input type="text" class="media-tag-input" placeholder="Add tags..." data-attachment-id="' + attachment.id + '">' +
                        '<div class="media-tag-list"></div>' +
                        '</div>';
                } else {
                    mediaContent = '<div class="pg-media-preview">' +
                        '<div class="pg-image-container">' +
                        '<img class="pg-image" src="' + thumbnail + '" alt="' + (attachment.title || 'Image') + '">' +
                        '</div>' +
                        '</div>' +
                        '<div class="media-tags">' +
                        '<input type="text" class="media-tag-input" placeholder="Add tags..." data-attachment-id="' + attachment.id + '">' +
                        '<div class="media-tag-list"></div>' +
                        '</div>';
                }

                var mediaItem = $('<div class="media-item" data-id="' + attachment.id + '" data-type="' + type + '">' +
                    mediaContent +
                    '<span class="media-type">' + type.charAt(0).toUpperCase() + type.slice(1) + '</span>' +
                    '<button type="button" class="remove-media" title="Remove">&times;</button>' +
                    '</div>');

                container.append(mediaItem);
                initTagAutocomplete(mediaItem.find('.media-tag-input'));
            });

            updateMediaOrder();
            // Add slight delay to ensure DOM is ready
            setTimeout(initMediaElementPlayers, 100);
        });

        // Open the uploader
        mediaUploader.open();
    });

    // Remove media item
    $(document).on('click', '.remove-media', function(e) {
        e.preventDefault();
        $(this).closest('.media-item').remove();
        updateMediaOrder();
    });

    // Update hidden input with media IDs
    function updateMediaOrder() {
        var mediaIds = [];
        $('#project-gallery-media-container .media-item').each(function() {
            mediaIds.push($(this).data('id'));
        });
        $('#project-gallery-media').val(mediaIds.join(','));
    }

    // Initialize tag input autocomplete
    function initTagAutocomplete(input) {
        $(input).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'get_tag_suggestions',
                        term: request.term
                    },
                    success: function(data) {
                        if (data.success) {
                            response(data.data);
                        } else {
                            response([]);
                        }
                    }
                });
            },
            minLength: 2,
            position: { my: 'left top+2', at: 'left bottom' },
            select: function(event, ui) {
                event.preventDefault();
                var input = $(this);
                var attachmentId = input.data('attachment-id');
                addTagToMedia(attachmentId, ui.item.value, input);
                input.val('');
                return false;
            }
        });
    }

    // Initialize autocomplete for existing inputs
    $('.media-tag-input').each(function() {
        initTagAutocomplete(this);
    });

    // Handle tag input
    $(document).on('keydown', '.media-tag-input', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            var input = $(this);
            var tag = input.val().trim();
            if (tag) {
                var attachmentId = input.data('attachment-id');
                addTagToMedia(attachmentId, tag, input);
                input.val('');
            }
        }
    });

    // Remove tag when clicked
    $(document).on('click', '.media-tag', function() {
        var tag = $(this);
        var attachmentId = tag.closest('.media-tags').find('.media-tag-input').data('attachment-id');
        removeTagFromMedia(attachmentId, tag.text(), tag);
    });

    // Function to check if tag already exists
    function tagExists(input, tagText) {
        var exists = false;
        input.siblings('.media-tag-list').find('.media-tag').each(function() {
            if ($(this).text().toLowerCase() === tagText.toLowerCase()) {
                exists = true;
                // Add highlight class for animation
                var existingTag = $(this);
                existingTag.addClass('tag-highlight');
                setTimeout(function() {
                    existingTag.removeClass('tag-highlight');
                }, 1000);
                return false; // Break the loop
            }
        });
        return exists;
    }

    // Function to add tag
    function addTagToMedia(attachmentId, tag, input) {
        // Check for duplicates
        if (tagExists(input, tag)) {
            return; // Don't add duplicate tag
        }
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'add_media_tag',
                attachment_id: attachmentId,
                tag: tag,
                nonce: $('#project_gallery_media_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    var tagElement = $('<span class="media-tag">' + tag + '</span>');
                    input.siblings('.media-tag-list').append(tagElement);
                }
            }
        });
    }

    // Function to remove tag
    function removeTagFromMedia(attachmentId, tag, tagElement) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'remove_media_tag',
                attachment_id: attachmentId,
                tag: tag,
                nonce: $('#project_gallery_media_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    tagElement.remove();
                }
            }
        });
    }


    // Add styles for media items
    $('<style>\n' +
        '.pg .media-item { display: inline-block; position: relative; margin: 10px; cursor: move; width: 250px; background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 8px; }\n' +
        '.pg .media-preview { position: relative; width: 100%; margin-bottom: 10px; }\n' +
        '.pg .media-preview { position: relative; width: 100%; height:200px; background: #f8f9fa; border-radius: 3px; overflow: hidden; }\n' +
        '.pg .media-preview img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: contain; }\n' +
        '.pg-video-container { position: relative !important; width: 100% !important; height: 200px !important; background: #000; }\n' +
        '.mejs-container, .mejs-overlay, .mejs-mediaelement { position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; }\n' +
        '.mejs-mediaelement video { position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; max-width: 100% !important; max-height: 100% !important; margin: 0 !important; }\n' +
        '.pg-video-container { position: relative; width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; background: #000; }\n' +
        '.pg-video-container .mejs-container { max-width: 100% !important; max-height: 100% !important; position: absolute !important; }\n' +
        '.pg-video-container .mejs-container, .pg-video-container .mejs-overlay, .pg-video-container .mejs-layers { width: 100% !important; height: 100% !important; }\n' +
        '.pg-video-container .mejs-mediaelement { display: flex; align-items: center; justify-content: center; }\n' +
        '.pg-video-container video { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; }\n' +
        '.pg .media-item .media-type { position: absolute; top: 5px; left: 5px; background: rgba(0,0,0,0.5); color: white; padding: 2px 5px; font-size: 12px; z-index: 10; }\n' +
        '.pg .media-item .remove-media { position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.7); color: white; border: none; cursor: pointer; padding: 0 5px; z-index: 10; }\n' +
        '.pg .media-item-placeholder { border: 2px dashed #ccc; width: 250px; height: 150px; display: inline-block; vertical-align: top; margin: 10px; }\n' +
        '.pg .media-tags { margin-top: 5px; }\n' +
        '.pg .media-tag-input { width: 100%; margin-bottom: 5px; padding: 5px; border: 1px solid #ddd; border-radius: 3px; }\n' +
        '.pg .media-tag-list { display: flex; flex-wrap: wrap; gap: 3px; }\n' +
        '.pg .media-tag { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 12px; cursor: pointer; }\n' +
        '.pg .media-tag:hover { background: #dee2e6; }\n' +
        '.mejs-mediaelement video { object-fit: cover; height: 100% !important; }\n' +
        '.mejs-overlay-button { transform: scale(0.6); margin-left: -25px !important; margin-top: -25px !important; }\n' +
        '.mejs-controls { bottom: -35px; transition: bottom 0.2s; }\n' +
        '.media-preview:hover .mejs-controls { bottom: 0; }\n' +
        '.media-tags { padding: 5px; }\n' +
        '.media-tag-input { width: 100%; margin-bottom: 5px; border: 1px solid #ddd; padding: 3px; }\n' +
        '.media-tag-list { display: flex; flex-wrap: wrap; gap: 3px; }\n' +
        '.media-tag { background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 12px; cursor: pointer; }\n' +
        '.media-tag:hover { background: #dee2e6; }\n' +
        '.ui-autocomplete { background: #fff; border: 1px solid #ddd; border-radius: 3px; max-height: 200px; overflow-y: auto; z-index: 100000; }\n' +
        '.ui-autocomplete .ui-menu-item { padding: 5px 10px; cursor: pointer; font-size: 12px; }\n' +
        '.ui-autocomplete .ui-menu-item:hover { background: #f8f9fa; }\n' +
        '.ui-autocomplete .ui-menu-item-wrapper { padding: 2px 5px; }\n' +
        '.ui-autocomplete .ui-state-active { background: #e9ecef; border: none; margin: 0; }\n' +
        '@keyframes tagHighlight { from { background: #ffd700; } to { background: #e9ecef; } }\n' +
        '.tag-highlight { animation: tagHighlight 1s; }\n' +
    '</style>').appendTo('head');
});
