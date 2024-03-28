=== Marketplace Items ===
Contributors: lumiblog, wpcornerke
Tags: envato, themeforest, codecanyon, graphicriver, marketplace
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display your Envato marketplace portfolio inside a post or a page.

== Description ==

Display your Envato marketplace portfolio inside a post or a page. Works with CodeCanyon, ThemeForest, VideoHive, AudioJungle, GraphicRiver, PhotoDune and 3DOcean.

Shortcode parameters:

* **type** (`compact|loose`) - show or hide name and price (use the 'loose' type on a full-width page)
* **username** (your username)
* **market** (`codecanyon`, `themeforest` or `graphicriver`)
* **price** (`true|false`) - show or hide price
* **ref** (your referral username)
* **currency** (`$`)

Use:

`[envato type="compact" username="wpcorner" market="codecanyon" price="false" ref="wpcorner" currency="$"]`

or

`[marketplace type="compact" username="wpcorner" market="codecanyon" price="false" ref="wpcorner" currency="$"]`

== Installation ==

1. Upload the plugin folder to your `/wp-content/plugins/` directory
2. Activate the plugin via the Plugins menu in WordPress
3. Create and publish a new page and add the shortcode

`[envato type="compact" username="wpcorner" market="codecanyon" price="false" ref="wpcorner" currency="$"]`

or

`[marketplace type="compact" username="wpcorner" market="codecanyon" price="false" ref="wpcorner" currency="$"]`

Note: cURL is required

These are the classes used by the plugin:

`ul.envato-wrap {}
ul.envato-wrap li {}
ul.envato-wrap li .envato-thumb {}
ul.envato-wrap li .envato-link {}
ul.envato-wrap li .envato-link small {}
ul.envato-wrap li .envato-price {}
ul.envato-wrap li .envato-category {}
ul.envato-wrap li .envato-author {}
ul.envato-wrap li .envato-quiet {}`

== Changelog ==

= 1.5.4 =
* UPDATE: Updated plugin author info and changed the links.

= 1.5.3 =
* UPDATE: Updated WordPress compatibility

= 1.5.2 =
* UPDATE: Updated WordPress compatibility

= 1.5.1 =
* UPDATE: Added lazy loading to images

= 1.5.0 =
* UPDATE: Overhauled Envato API code
* UPDATE: Updated WordPress compatibility
* UPDATE: Updated PHP compatibility
* UPDATE: Added API result caching

= 1.4.4 =
* UPDATE: Updated WordPress compatibility

= 1.4.3 =
* UPDATE: Updated PHP requirements
* UPDATE: Updated WordPress compatibility

= 1.4.2 =
* UPDATE: Updated PHP requirements
* UPDATE: Updated WordPress compatibility

= 1.4.1 =
* FIX: Added missing changelog line for 1.4.0
* FIX: Added empty classes to style.css
* FIX: Fixed donation link
* UPDATE: Added more tags
* UPDATE: Updated i18n


https://wordpress.org/plugins/nd-stats-for-envato-sales-by-item/
https://forums.envato.com/t/envato-marketplace-items-portfolio-wordpress-plugin/66516

= 1.4.0 =
* FIX: Switched from HTTP GET to cURL for better compatibility
* FIX: Fixed proprietary styling
* FIX: Removed unused shortcode parameter
* FIX: Fixed potential security issue
* FIX: Fixed erroneous contributor username
* UPDATE: Added new shortcode for compatibility reasons
* UPDATE: Updated WordPress compatibility

= 1.3.3 =
* COMPATIBILITY: Checked WordPress 4.0 compatibility
* FEATURE: Added plugin icon
* GENERAL: Removed unused CSS styles

= 1.3.2 =
* COMPATIBILITY: Checked WordPress 3.8 compatibility
* GENERAL: Added license link
* GENERAL: Added donate link

= 1.3.1 =
* COMPATIBILITY: Checked WordPress 3.8-beta compatibility
* COMPATIBILITY: Added -moz prefix for CSS box-shadow

= 1.3 =
* GENERAL: Renamed the plugin in order to comply with Envato's terms of service
* GENERAL: Fixed the readme.txt file

= 1.2 =
* UI: Moved CSS style to separate file
* UI: Added a 5px padding to thumbnails
* FRAMEWORK: Added path variables

= 1.1 =
* First public release

== Upgrade Notice ==

= 1.5.4 =
* UPDATE: Updated plugin author info and changed the links.
