<?php

/* ======================================================================

    Image Compress & Sharpen Options
    Let's user specify compression level and turn sharpening on/off.

 * ====================================================================== */


/* ======================================================================
    THEME OPTION FIELDS
    Create the theme option fields.
 * ====================================================================== */

function imgcs_settings_field_compression_rate() {
    $options = imgcs_get_theme_options();
    ?>
    <input type="text" name="imgcs_theme_options[compression_rate]" id="compression-rate" value="<?php echo esc_attr( $options['compression_rate'] ); ?>" /><br />
    <label class="description" for="compression-rate"><?php _e( 'Default: <code>70</code>', 'imgcs' ); ?></label>
    <?php
}

function imgcs_settings_field_do_progressive() {
    $options = imgcs_get_theme_options();
    ?>
    <label for="do-progressive">
        <input type="checkbox" name="imgcs_theme_options[do_progressive]" id="do-progressive" <?php checked( 'on', $options['do_progressive'] ); ?> />
        <?php _e( 'Convert to progressive JPGs', 'imgcs' ); ?>
    </label>
    <?php
}

function imgcs_settings_field_do_sharpen() {
    $options = imgcs_get_theme_options();
    ?>
    <label for="do-sharpen">
        <input type="checkbox" name="imgcs_theme_options[do_sharpen]" id="do-sharpen" <?php checked( 'on', $options['do_sharpen'] ); ?> />
        <?php _e( 'Sharpen images', 'imgcs' ); ?>
    </label>
    <?php
}





/* ======================================================================
    THEME OPTIONS MENU
    Create the theme options menu.
 * ====================================================================== */

// Register the theme options page and its fields
function imgcs_theme_options_init() {
    register_setting(
        'imgcs_options', // Options group, see settings_fields() call in imgcs_theme_options_render_page()
        'imgcs_theme_options', // Database option, see imgcs_get_theme_options()
        'imgcs_theme_options_validate' // The sanitization callback, see imgcs_theme_options_validate()
    );

    // Register our settings field group
    add_settings_section(
        'general', // Unique identifier for the settings section
        '', // Section title (we don't want one)
        '__return_false', // Section callback (we don't want anything)
        'imgcs_theme_options' // Menu slug, used to uniquely identify the page; see imgcs_theme_options_add_page()
    );

    // Register our individual settings fields
    // add_settings_field( $id, $title, $callback, $page, $section );
    // $id - Unique identifier for the field.
    // $title - Setting field title.
    // $callback - Function that creates the field (from the Theme Option Fields section).
    // $page - The menu page on which to display this field.
    // $section - The section of the settings page in which to show the field.

    add_settings_field( 'compression_rate', 'Compression Rate', 'imgcs_settings_field_compression_rate', 'imgcs_theme_options', 'general' );
    add_settings_field( 'do_progressive', 'Progressive JPGs', 'imgcs_settings_field_do_progressive', 'imgcs_theme_options', 'general' );
    add_settings_field( 'do_sharpen', 'Sharpen Images', 'imgcs_settings_field_do_sharpen', 'imgcs_theme_options', 'general' );
}
add_action( 'admin_init', 'imgcs_theme_options_init' );



// Create theme options menu
// The content that's rendered on the menu page.
function imgcs_theme_options_render_page() {
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php _e( 'Image Compress & Sharpen', 'imgcs' ); ?></h2>

        <form method="post" action="options.php">
            <?php
                settings_fields( 'imgcs_options' );
                do_settings_sections( 'imgcs_theme_options' );
                submit_button();
            ?>
        </form>
    </div>
    <?php
}



// Add the theme options page to the admin menu
function imgcs_theme_options_add_page() {
    $theme_page = add_submenu_page(
        'options-general.php', // parent slug
        'Image Compression', // Label in menu
        'Image Compression', // Label in menu
        'edit_theme_options', // Capability required
        'imgcs_theme_options', // Menu slug, used to uniquely identify the page
        'imgcs_theme_options_render_page' // Function that renders the options page
    );
}
add_action( 'admin_menu', 'imgcs_theme_options_add_page' );



// Restrict access to the theme options page to admins
function imgcs_option_page_capability( $capability ) {
    return 'edit_theme_options';
}
add_filter( 'option_page_capability_imgcs_options', 'imgcs_option_page_capability' );







/* ======================================================================
    PROCESS THEME OPTIONS
    Process and save updates to the theme options.

    Each option field requires a default value under imgcs_get_theme_options(),
    and an if statement under imgcs_theme_options_validate();
 * ====================================================================== */

// Get the current options from the database.
// If none are specified, use these defaults.
function imgcs_get_theme_options() {
    $saved = (array) get_option( 'imgcs_theme_options' );
    $defaults = array(
        'compression_rate'     => '',
        'do_progressive'     => 'off',
        'do_sharpen'     => 'off',
    );

    $defaults = apply_filters( 'imgcs_default_theme_options', $defaults );

    $options = wp_parse_args( $saved, $defaults );
    $options = array_intersect_key( $options, $defaults );

    return $options;
}



// Sanitize and validate updated theme options
function imgcs_theme_options_validate( $input ) {
    $output = array();

    // The sample text input must be safe text with no HTML tags
    if ( isset( $input['compression_rate'] ) && ! empty( $input['compression_rate'] ) && is_numeric( $input['compression_rate'] ) && $input['compression_rate'] >= '0' && $input['compression_rate'] <= '100' )
        $output['compression_rate'] = wp_filter_nohtml_kses( $input['compression_rate'] );

    if ( isset( $input['do_progressive'] ) )
        $output['do_progressive'] = 'on';

    if ( isset( $input['do_sharpen'] ) )
        $output['do_sharpen'] = 'on';

    return apply_filters( 'imgcs_theme_options_validate', $output, $input );
}





/* ======================================================================
    GET THEME OPTIONS
    Retrieve and output theme options for use in other functions.
 * ====================================================================== */

function imgcs_get_compression_rate() {
    $options = imgcs_get_theme_options();
    if ( $options['compression_rate'] == '' ) {
        $setting = '70';
    } else {
        $setting = $options['compression_rate'];
    }
    return $setting;
}

function imgcs_get_do_progressive() {
    $options = imgcs_get_theme_options();
    if ( $options['do_progressive'] == 'on' ) {
        $setting = true;
    } else {
        $setting = false;
    }
    return $setting;
}

function imgcs_get_do_sharpen() {
    $options = imgcs_get_theme_options();
    if ( $options['do_sharpen'] == 'on' ) {
        $setting = true;
    } else {
        $setting = false;
    }
    return $setting;
}

?>