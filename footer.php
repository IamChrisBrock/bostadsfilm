<footer>
    <?php
    // Get the ACF field value for the background image
    $footer_background_id = get_theme_mod('footer_background_image');
    $footer_background = $footer_background_id ? wp_get_attachment_url($footer_background_id) : '';
    
    // Check if the image exists and retrieve the URL and dimensions
    if ($footer_background) {
        $image_meta = wp_get_attachment_metadata($footer_background_id);
        $background_url = esc_url($footer_background);
        
        if ($image_meta) {
            $image_width = $image_meta['width'];
            $image_height = $image_meta['height'];
            
            // Calculate the aspect ratio (height / width)
            $aspect_ratio = $image_height / $image_width;

            // Set the inline style to calculate height based on width (100%)
            $footer_height = $aspect_ratio * 100; // This will give you the percentage height
            $background_style = "background-image: url('$background_url'); height: {$footer_height}vw;";
        } else {
            // Fallback if we can't get image dimensions
            $background_style = "background-image: url('$background_url'); height: 50vw;";
        }
    } else {
        // Fallback to default background if none is set
        $background_style = 'height: 50vw;';
    }
    ?>

    <!-- Footer div with background image -->
    <div class="custom-footer" style="<?php echo $background_style; ?>">
        <div class="footer-content">
            <?php 
            // Optionally, you can display content from the footer post or custom HTML
            ?>
        </div>
    </div>
</footer>


<?php wp_footer(); ?>
</body>
</html>