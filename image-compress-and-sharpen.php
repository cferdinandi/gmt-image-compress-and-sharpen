<?php

/* ======================================================================

	Plugin Name: Image Compress & Sharpen
	Plugin URI: http://github.com/cferdinandi/image-compress-and-sharpen
	Description: Change the default WordPress compression rate for JPGs, and optionally sharpen images. Adjust your image compression settings under <a href="options-general.php?page=imgcs_theme_options">Settings &rarr; Image Compression</a>.
	Version: 1.3
	Author: Chris Ferdinandi
	Author URI: http://gomakethings.com
	License: GPL v3

	Progressive JPG functionality added by James Foster.
	http://exisweb.net/

	Forked from the Sharpen Resized Images plugin.
	http://unsalkorkmaz.com/ajx-sharpen-resized-images/

 * ====================================================================== */

require_once( dirname( __FILE__) . '/imgcs-options.php' );
require_once( dirname( __FILE__) . '/imgcs-process.php' );

?>