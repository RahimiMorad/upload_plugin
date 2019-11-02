<?php
/*
Plugin Name: xyz uploader
Plugin URI: https://www.wp-sultan.com
Description: Upload files directly to the media by ajax and grant the access to subscribers .
Version: 1.0.0
Author: https://www.wp-sultan.com
Author URI: https://www.webdreamers.ir
 */

if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

function allow_subscriber_to_uploads()
{
    $subscriber = get_role('subscriber');
    if (!$subscriber->has_cap('upload_files')) {
        $subscriber->add_cap('upload_files');
    }
}
add_action('admin_init', 'allow_subscriber_to_uploads');

function scripts()
{
    wp_enqueue_style('xyz_style', plugin_dir_url(__FILE__).'css/xyz-style.css');
    wp_enqueue_script('upload-form-js', plugin_dir_url(__FILE__).'js/script.js', array('jquery'), '0.1.0', true);
    $data = array(
        'upload_url' => admin_url('async-upload.php'),
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('media-form'),
    );
    wp_localize_script('upload-form-js', 'xyz_config', $data);
}
add_action('wp_enqueue_scripts', 'scripts');

function upload_form_html()
{
    ob_start(); ?>
        <?php if (is_user_logged_in()): ?>
        <form action="" method="post" class="upload-form" enctype="multipart/form-data">
            <?php wp_nonce_field('upload-submission'); ?>
            <p class="upload-notice"></p>
            <div class='progress' id="progressDivId">
                <div class='progress-bar' id='progressBar'></div>
                <div class='percent' id='percent'>0%</div>
            </div>
            <p><input type="file" name="async-upload" class="upload-file" accept="" required></p>
            <input type="hidden" name="action" value="upload_submission">
            <hr>
            <p><input type="submit" value="Upload"></p>
        </form>
        <?php else: ?>
            <p>Please <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>">login</a> first to submit your File.</p>
        <?php endif; ?>
    <?php
$output = ob_get_clean();

    return $output;
}
add_shortcode('xyz_upload', 'upload_form_html');
