<?php
/*
Plugin Name: Marketplace Items
Version: 1.5.3
Plugin URI: https://getbutterfly.com/wordpress-plugins/
Description: Display your Envato marketplace portfolio inside a post or a page.
Author: Ciprian Popescu
Author URI: https://getbutterfly.com/
Text Domain: marketplace-items

Copyright 2012-2023 Ciprian Popescu (email: getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
 * Usage: [envato type="loose"]
 * Usage: [envato type="compact"]
 */

if(!defined('ABSPATH')) exit; // Exit if accessed directly
//

define('EI_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('ENVATOMI_VERSION', '1.5.3');

function ei_styles() {
	wp_enqueue_style('ei-style', EI_PLUGIN_URL . '/css/style.css');	
}

add_action('wp_print_styles', 'ei_styles');


/**
 * Register/enqueue plugin scripts and styles (back-end)
 */
function envatomi_enqueue_scripts() {
    wp_enqueue_style('envatomi', plugins_url('css/admin.css', __FILE__), [], ENVATOMI_VERSION);
}
add_action('admin_enqueue_scripts', 'envatomi_enqueue_scripts');


function envato_items($atts) {
	extract(shortcode_atts([
		'type' 		=> 'compact',
		'username' 	=> 'butterflymedia',
		'market' 	=> 'codecanyon',
		'price' 	=> true,
		'ref' 		=> 'butterflymedia',
		'currency' 	=> '$'
	], $atts));

    $username = (string) sanitize_text_field($username);
    $market = (string) sanitize_text_field($market);

    $envatomi_api_key = get_option('envatomi_api_key');
    $envatomi_cache = ((int) get_option('envatomi_cache') > 0 ) ? (int) get_option('envatomi_cache') : 24;

    $envatomi_username = ($username !== '') ? $username : get_option('envatomi_username');
    $envatomi_marketplace = ($market !== '') ? $market : get_option('envatomi_marketplace');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.envato.com/v1/market/new-files-from-user:$envatomi_username,$envatomi_marketplace.json");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; getButterfly Marketplace API Wrapper)');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
		"Authorization: Bearer $envatomi_api_key"
	]);
	
    $ch_data = curl_exec($ch);
    curl_close($ch);

    $json_data = json_decode($ch_data, true);
	$newFilesFromUser = (array) $json_data['new-files-from-user'];
    $data_count = count($newFilesFromUser) - 1;

    // Cache result
    $data = get_transient('envatomi-' . $type);

    if ($data === false) {

		$data = '<ul class="envato-wrap envato-wrap--' . $type . '">';
			if ((string) $type === 'compact') {
				for ($i = 0; $i <= $data_count; $i++) {
					$url = str_replace('http://', 'https://', $json_data['new-files-from-user'][$i]['url']);
					$data .= '<li><div class="envato-thumb-compact"><a href="' . $json_data['new-files-from-user'][$i]['url'] . '?ref=' . $ref . '" title="' . $json_data['new-files-from-user'][$i]['url'] . '"><img src="' . $json_data['new-files-from-user'][$i]['thumbnail'] . '" width="80" height="80" loading="lazy" alt="' . $json_data['new-files-from-user'][$i]['item'] . '"></a></div></li>';
				}
			} else if ((string) $type === 'loose') {
                for ($i = 0; $i <= $data_count; $i++) {
                    $url = str_replace('http://', 'https://', $json_data['new-files-from-user'][$i]['url']);

                    $data .= '<li>
                        <div class="envato-thumb">
                            <a href="' . $json_data['new-files-from-user'][$i]['url'] . '?ref='.$ref.'" title="'. $json_data['new-files-from-user'][$i]['url'] .'"><img src="'.$json_data['new-files-from-user'][$i]['thumbnail'].'" width="80" height="80" loading="lazy" alt="'. $json_data['new-files-from-user'][$i]['item'] .'"></a>
                        </div>
                        <div class="envato-link">
                            <a href="' . $json_data['new-files-from-user'][$i]['url'] . '?ref='.$ref.'" title="'. $json_data['new-files-from-user'][$i]['url'] .'">' . $json_data['new-files-from-user'][$i]['item'] . '</a>
                            <br><small><a href="' . $url . '?ref=' . $ref . '">' . $json_data['new-files-from-user'][$i]['user'] . '</a></small>
                        </div>';

                        if ((bool) $price === true){
                            $data .= '<div class="envato-price">'.$currency . $json_data['new-files-from-user'][$i]['cost'] . '</div>';
                        }

                        $data .= '<div class="envato-category"><code>' . $json_data['new-files-from-user'][$i]['category'] . '</code></div>
                        <div class="envato-author envato-quiet">
                            ' . $json_data['new-files-from-user'][$i]['sales'] . ' ' . __('downloads', 'marketplace-items') . '
                        </div>
                    </li>';
				}
			}
		$data .= '</ul>';

        set_transient('envatomi-' . $type, $data, $envatomi_cache * HOUR_IN_SECONDS);

    }

	return $data;	
}

add_shortcode('envato', 'envato_items');
add_shortcode('marketplace', 'envato_items');



function envatomi_menu_links() {
    add_options_page('Envato Marketplace Items Settings', 'Envato Marketplace', 'manage_options', 'mi', 'envatomi_build_admin_page');
}

add_action('admin_menu', 'envatomi_menu_links', 10);

function envatomi_build_admin_page() {
    $tab = (filter_has_var(INPUT_GET, 'tab')) ? filter_input(INPUT_GET, 'tab') : 'dashboard';
    $section = 'admin.php?page=mi&amp;tab=';
	?>
    <div class="wrap">
        <h1>Envato Marketplace Items Settings</h1>

        <h2 class="nav-tab-wrapper">
            <a href="<?php echo $section; ?>dashboard" class="nav-tab <?php echo $tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
            <a href="<?php echo $section; ?>help" class="nav-tab <?php echo $tab === 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
        </h2>

        <?php if ($tab === 'dashboard') {
            global $wpdb;

            if (isset($_POST['save_envatomi_settings'])) {
                update_option('envatomi_api_key', (string) sanitize_text_field($_POST['envatomi_api_key']));
                update_option('envatomi_username', (string) sanitize_text_field($_POST['envatomi_username']));
                update_option('envatomi_marketplace', (string) sanitize_text_field($_POST['envatomi_marketplace']));

                update_option('envatomi_cache', (int) $_POST['envatomi_cache']);

                echo '<div class="updated notice is-dismissible"><p>Settings updated successfully!</p></div>';
            }
            ?>

            <div class="pl-ad" id="pl-ad">
                <h3 class="pl-ad--header"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 68 68"><defs/><rect width="100%" height="100%" fill="none"/><g class="currentLayer"><path fill="#fff" d="M34.76 33C22.85 21.1 20.1 13.33 28.23 5.2 36.37-2.95 46.74.01 50.53 3.8c3.8 3.8 5.14 17.94-5.04 28.12-2.95 2.95-5.97 5.84-5.97 5.84L34.76 33"/><path fill="#fff" d="M43.98 42.21c5.54 5.55 14.59 11.06 20.35 5.3 5.76-5.77 3.67-13.1.98-15.79-2.68-2.68-10.87-5.25-18.07 1.96-2.95 2.95-5.96 5.84-5.96 5.84l2.7 2.7m-1.76 1.75c5.55 5.54 11.06 14.59 5.3 20.35-5.77 5.76-13.1 3.67-15.79.98-2.69-2.68-5.25-10.87 1.95-18.07 2.85-2.84 5.84-5.96 5.84-5.96l2.7 2.7" class="selected"/><path fill="#fff" d="M33 34.75c-11.9-11.9-19.67-14.67-27.8-6.52-8.15 8.14-5.2 18.5-1.4 22.3 3.8 3.79 17.95 5.13 28.13-5.05 3.1-3.11 5.84-5.97 5.84-5.97L33 34.75"/></g></svg> Thank you for using Envato Marketplace Items!</h3>
                <div class="pl-ad--content">
                    <p>If you enjoy this plugin, do not forget to <a href="https://wordpress.org/support/plugin/marketplace-items/reviews/?filter=5" rel="external">rate it</a>! We work hard to update it, fix bugs, add new features and make it compatible with the latest web technologies.</p>
                    <p>Have you tried my other <a href="https://profiles.wordpress.org/lumiblog/#content-plugins">WordPress plugins</a>?</p>
                </div>
                <div class="pl-ad--footer">
                    <p>For support, feature requests and bug reporting, please visit the <a href="https://wordpress.org/support/plugin/marketplace-items/" rel="external">Support Forum</a>.<br>Built by <a href="https://wpcorner.co/" rel="external"><strong>WP Corner</strong></a> &middot; <small>Code wrangling since 2005</small></p>

                    <p>
                        <small>You are using PHP <?php echo PHP_VERSION; ?> and MySQL <?php echo $wpdb->db_version(); ?>.</small>
                    </p>
                </div>
            </div>

            <h2><span class="dashicons dashicons-superhero"></span> Dashboard</h2>

            <form method="post">
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Envato API Key</label></th>
                            <td>
                                <p>
                                    <input type="text" class="regular-text" name="envatomi_api_key" value="<?php echo get_option('envatomi_api_key'); ?>"> Envato API Key
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Envato Username</label></th>
                            <td>
                                <p>
                                    <input type="text" class="regular-text" name="envatomi_username" value="<?php echo get_option('envatomi_username'); ?>"> Envato Username
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Envato Marketplace</label></th>
                            <td>
                                <p>
                                    <input type="text" class="regular-text" name="envatomi_marketplace" value="<?php echo get_option('envatomi_marketplace'); ?>"> Envato Marketplace
                                    <br><small>e.g. <code>codecanyon</code>, <code>themeforest</code></small>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label>Envato API Cache</label></th>
                            <td>
                                <p>
                                    <input type="number" name="envatomi_cache" value="<?php echo get_option('envatomi_cache'); ?>" min="1" max="9999" step="1"> Hours to cache results
                                    <br><small>A higher number is recommended.</small>
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><input type="submit" name="save_envatomi_settings" class="button button-primary" value="Save Changes"></th>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        <?php } else if ($tab === 'help') { ?>
            <h2><span class="dashicons dashicons-editor-help"></span> Help</h2>

            <p>Display your Envato marketplace portfolio inside a post or a page. Works with CodeCanyon, ThemeForest, VideoHive, AudioJungle, GraphicRiver, PhotoDune and 3DOcean.</p>

            <h3>Shortcodes and shortcode parameters:</h3>

            <p><code>[envato type="compact" username="butterflymedia" market="codecanyon" price="false" ref="butterflymedia" currency="$"]</code></p>

            <p><small>or</small></p>

            <p><code>[marketplace type="compact" username="butterflymedia" market="codecanyon" price="false" ref="butterflymedia" currency="$"]</code></p>

            <ul>
                <li><code>type</code> (<code>compact|loose</code>) – show or hide name and price</li>
                <li><code>username</code> (your Envato username)</li>
                <li><code>market</code> (<code>codecanyon</code> or <code>themeforest</code> or <code>graphicriver</code>)</li>
                <li><code>price</code> (<code>true|false</code>) – show or hide price</li>
                <li><code>ref</code> (your referral username)</li>
                <li><code>currency</code> (<code>$</code>)</li>
            </ul>
        <?php } ?>
    </div>
	<?php
}
