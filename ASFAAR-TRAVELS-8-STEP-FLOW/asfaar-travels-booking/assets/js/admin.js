jQuery(document).ready(function($) {
    console.log('AFSAR Admin loaded');
    
    // WordPress Media Uploader for Images
    var afsarMediaUploader;
    
    $(document).on('click', '.afsar-upload-image-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrapper = button.closest('.afsar-image-wrapper');
        var hiddenInput = wrapper.find('.afsar-image-url');
        var preview = wrapper.find('.afsar-image-preview');
        var removeBtn = wrapper.find('.afsar-remove-image-btn');
        
        console.log('Upload image button clicked');
        
        if (afsarMediaUploader) {
            afsarMediaUploader.open();
            return;
        }
        
        afsarMediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Use This Image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        afsarMediaUploader.on('select', function() {
            var attachment = afsarMediaUploader.state().get('selection').first().toJSON();
            console.log('Image selected:', attachment.url);
            
            hiddenInput.val(attachment.url);
            preview.html('<img src="' + attachment.url + '" style="max-width:300px;height:auto;border:1px solid #ddd;border-radius:4px;padding:5px;">');
            preview.show();
            removeBtn.show();
            button.text('Change Image');
        });
        
        afsarMediaUploader.open();
    });
    
    $(document).on('click', '.afsar-remove-image-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var wrapper = button.closest('.afsar-image-wrapper');
        var hiddenInput = wrapper.find('.afsar-image-url');
        var preview = wrapper.find('.afsar-image-preview');
        var uploadBtn = wrapper.find('.afsar-upload-image-btn');
        
        console.log('Remove image clicked');
        
        hiddenInput.val('');
        preview.html('');
        preview.hide();
        button.hide();
        uploadBtn.text('Upload Image');
    });
});
