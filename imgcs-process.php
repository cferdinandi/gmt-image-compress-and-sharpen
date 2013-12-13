<?php

/* ======================================================================

	Image Compress & Sharpen Process
	Compress and sharpen images.

 * ====================================================================== */

function imgcs_compress_and_sharpen_img( $resized_file ) {

	// Get user settings
	$img_compression = imgcs_get_compression_rate();
	$do_progressive = imgcs_get_do_progressive();
	$do_sharpen = imgcs_get_do_sharpen();

	// Define the image to be resized
	$image = imagecreatefromstring( file_get_contents( $resized_file ) );

	// Get the image's dimensions
	$size = @getimagesize( $resized_file );

	// If dimensions can't be determined, produce an error message
	if ( !$size ) {
		return new WP_Error('invalid_image', __('Could not read image size'), $file);
	}

	// Otherwise, define dimensions in an array
	list($orig_w, $orig_h, $orig_type) = $size;

	// Check image extension
	switch ( $orig_type ) {

		// if image is a JPG
		case IMAGETYPE_JPEG:

			// If sharpening is on, sharpen image
			if ( $do_sharpen ) {

				// Define sharpen variables
				$matrix = array(
					array(apply_filters('sharpen_resized_corner',-1.2), apply_filters('sharpen_resized_side',-1), apply_filters('sharpen_resized_corner',-1.2)),
					array(apply_filters('sharpen_resized_side',-1), apply_filters('sharpen_resized_center',20), apply_filters('sharpen_resized_side',-1)),
					array(apply_filters('sharpen_resized_corner',-1.2), apply_filters('sharpen_resized_side',-1), apply_filters('sharpen_resized_corner',-1.2)),
				);
				$divisor = array_sum(array_map('array_sum', $matrix));
				$offset = 0;

				// Sharpen the image
				imageconvolution($image, $matrix, $divisor, $offset);

			}

			// If progressive JPGs are on, convert to progressive JPG
			if ( $do_progressive ) {
				imageinterlace($image, true);
			}

			// Compress image
			imagejpeg($image, $resized_file,apply_filters( 'jpeg_quality', $img_compression, 'edit_image' ));

			// Return the image
			break;

		// If it's a PNG or GIF, do nothing
		case IMAGETYPE_PNG:
			return $resized_file;
		case IMAGETYPE_GIF:
			return $resized_file;
	}

	// we don't need images in memory anymore
	imagedestroy( $image );

	// Return the image
	return $resized_file;
}

add_filter('image_make_intermediate_size', 'imgcs_compress_and_sharpen_img', 900);

?>