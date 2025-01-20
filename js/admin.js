jQuery(document).ready(function ($) {
  let mediaUploader;

  $('#upload_header_image').click(function (e) {
    e.preventDefault();

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media({
      title: 'Select Header Image',
      button: {
        text: 'Use this image'
      },
      multiple: false
    });

    mediaUploader.on('select', function () {
      const attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#header_image').val(attachment.url);
      $('#header_image_preview').attr('src', attachment.url).show();
      $('#remove_header_image').show();
    });

    mediaUploader.open();
  });

  $('#remove_header_image').click(function () {
    $('#header_image').val('');
    $('#header_image_preview').hide();
    $(this).hide();
  });
});
