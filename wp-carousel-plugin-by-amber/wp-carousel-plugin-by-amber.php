<?php
/*
  Plugin Name: WP logo carousel created by Mateusz Ambroży
  Version: 1.0
  Description: Plugin adds logo carousel
  Author: Mateusz Ambroży
  Author URI: http://kreatywnestrony.eu
 */

 
 /**
 * database
 * @global $wpdb
 */

define( 'RMLC_PATH', plugin_dir_path( __FILE__ ) );
require RMLC_PATH.'model.php';


function rmlc_install() {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $rmlc_tablename = $prefix . "rm_logo_carousel";
 
    $rmlc_db_version = "1.0";
 
    if ($wpdb->get_var("SHOW TABLES LIKE '" . $rmlc_tablename . "'") != $rmlc_tablename) {
        $query = "CREATE TABLE " . $rmlc_tablename . " ( 
        id int(9) NOT NULL AUTO_INCREMENT, 
        image varchar(250) NOT NULL,  
        link varchar(250) NOT NULL,  
        title varchar(250) NOT NULL, 
        PRIMARY KEY  (id)
        )";
 
        $wpdb->query($query);
 
        add_option("rmlc_db_version", $rmlc_db_version);
        add_option( 'rmlc_speed', '2000' );
        add_option( 'rmlc_interval', '2000');
        add_option( 'rmlc_type', 'vertical');
    }
}

/* wpdb potrzeba pobrać prefix tabeli, później potrzebujemy poinformować Wordpress, że funkcja ma być uruchamiana podczas aktywności */

register_activation_hook(__FILE__, 'rmlc_install');

/**
 * Usuwa tabele bazy danych
 * @global $wpdb $wpdb
 */
function rmlc_uninstall() {
    global $wpdb;
    $prefix = $wpdb->prefix;
    $rmlc_tablename = $prefix . "rm_logo_carousel";
    $query ='DROP TABLE '.$rmlc_tablename;
        $wpdb->query($query);
}
register_deactivation_hook(__FILE__, 'rmlc_uninstall');


/**
 * logo do menu
 */
function rmlc_plugin_menu() {
    add_menu_page('Logo Carousel', 'Logo Carousel', 'administrator', 'rmlc_settings', 'rmlc_display_settings');
    add_submenu_page('rmlc_settings', __('Images'), __('Images'), 'edit_themes', 'rmlc_images', 'rmlc_images');
}
add_action('admin_menu', 'rmlc_plugin_menu');


/**
 * Wyswietla formularz do modyfikacji ustawien pluginu
 */
function rmlc_display_settings() {
 
    $slider_horizontal = (get_option('rmlc_type') == 'horizontal') ? 'selected="selected" ' : '';
 
    $slider_vertical = (get_option('rmlc_type') == 'vertical') ? 'selected="selected" ' : '';
 
    $interval = (get_option('rmlc_interval') != '') ? get_option('rmlc_interval') : '2000';
    $speed = (get_option('rmlc_speed') != '') ? get_option('rmlc_speed') : '2000';
 
    $html = '
<div class="wrap"><form action="options.php" method="post" name="options">
<h2>Select Your Settings</h2>
' . wp_nonce_field('update-options') . '
<table class="form-table" width="100%" cellpadding="10">
<tbody>
<tr>
<td scope="row" align="left">
 <label>Slider type: </label>
<select name="rmlc_type">
<option '.$slider_horizontal.' value="horizontal">Horizontal</option>
<option '.$slider_vertical.' value="vertical">Vertical</option></select></td>
</tr>
<tr>
<td scope="row" align="left">
 <label>Transition Interval: </label><input type="text" name="rmlc_interval" value="' . $interval . '" /></td>
</tr>
<tr>
<td scope="row" align="left">
 <label>Speed: </label><input type="text" name="rmlc_speed" value="' . $speed . '" /></td>
</tr>
</tbody>
</table>
 <input type="hidden" name="action" value="update'.__('Update').'" />
 
 <input type="hidden" name="page_options" value="rmlc_speed,rmlc_type,rmlc_interval" />
 
 <input type="submit" name="Submit" value="Update" /></form></div>
';
 
    echo $html;
}

function rmlc_scripts() {
    wp_enqueue_script('jquery');
    wp_register_script('jcarousel_core', plugins_url('js/jcarousellite_1.0.1.min.js', __FILE__), array("jquery"));
    wp_enqueue_script('jcarousel_core');
 
    wp_register_script('jcarousel_init', plugins_url('js/rmlc_slider_init.js', __FILE__));
    wp_enqueue_script('jcarousel_init');
 
    $type = (get_option('rmlc_type') == '') ? "horizontal" : get_option('rmlc_type');
    $interval = (get_option('rmlc_interval') == '') ? 2000 : get_option('rmlc_interval');
    $speed = (get_option('rmlc_speed') == '') ? 500 : get_option('rmlc_speed');
    $vertical=($type == 'vertical') ? 'true' : 'false';
    $config_array = array(
        'interval' => $interval,
        'speed' => $speed,
        'vertical' => $vertical,
    );
 
    wp_localize_script('jcarousel_init', 'setting', $config_array);
}

function rmlc_show_logo() {
    rmlc_scripts();
    $model=new LogoCarousel();
    $results = $model->getAll();
 
    echo '<div id="rmlc_logo_carousel"><div><ul>';
    foreach ($results as $result) {
        echo '<li>';
        if ($result['link'] != '') {
            echo '<a href="' . $result['link'] . '" title="' . $result['title'] . '"><img src="' . $result['image'] . '" alt="' . $result['title'] . '" /></a>';
        } else {
            echo '<img src="' . $result['image'] . '" alt="' . $result['title'] . '" />';
        }
        echo '</li>';
    }
    echo '</ul></div></div>';
}
rmlc_show_logo();
?>