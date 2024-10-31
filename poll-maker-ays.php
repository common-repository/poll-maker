<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ays-pro.com/
 * @since             1.0.0
 * @package           Poll_Maker_Ays
 *
 * @wordpress-plugin
 * Plugin Name:       Poll Maker
 * Plugin URI:        https://ays-pro.com/index.php/wordpress/poll-maker/
 * Description:       This is a simple polls maker.
 * Version:           1.0.0
 * Author:            Ays-pro
 * Author URI:        https://ays-pro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       poll-maker-ays
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'POLL_MAKER_AYS_VERSION', '1.0.0' );
define( 'POLL_MAKER_AYS_NAME', 'poll-maker-ays' );

if( ! defined( 'POLL_MAKER_AYS_DIR' ) )
    define( 'POLL_MAKER_AYS_DIR', plugin_dir_path( __FILE__ ) );
if( ! defined( 'POLL_MAKER_AYS_BASE_URL' ) ) {
    define( 'POLL_MAKER_AYS_BASE_URL', plugin_dir_url(__FILE__ ) );
}
if( ! defined( 'POLL_MAKER_AYS_ADMIN_URL' ) )
    define( 'POLL_MAKER_AYS_ADMIN_URL', plugin_dir_url( __FILE__ ) . 'admin' );

if( ! defined( 'POLL_MAKER_AYS_PUBLIC_URL' ) )
    define( 'POLL_MAKER_AYS_PUBLIC_URL', plugin_dir_url( __FILE__ ) . 'public' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-poll-maker-ays-activator.php
 */
function activate_poll_maker_ays() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-poll-maker-ays-activator.php';
	Poll_Maker_Ays_Activator::ays_poll_update_db_check();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-poll-maker-ays-deactivator.php
 */
function deactivate_poll_maker_ays() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-poll-maker-ays-deactivator.php';
	Poll_Maker_Ays_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_poll_maker_ays' );
register_deactivation_hook( __FILE__, 'deactivate_poll_maker_ays' );

add_action( 'plugins_loaded', 'activate_poll_maker_ays' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-poll-maker-ays.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_poll_maker_ays() {
	add_action('admin_notices', 'poll_maker_admin_notice');
	$plugin = new Poll_Maker_Ays();
	$plugin->run();

}
function poll_maker_admin_notice(){
    if ( isset($_GET['page']) && strpos($_GET['page'], POLL_MAKER_AYS_NAME) !== false ) {
        ?>
             <div class="ays-notice-banner">
                <div class="navigation-bar">
                    <div id="navigation-container">
                        <a class="logo-container" href="http://ays-pro.com/" target="_blank">
                            <img class="logo" src="<?php echo POLL_MAKER_AYS_ADMIN_URL . '/images/ays_pro.png'; ?>" alt="AYS Pro logo" title="AYS Pro logo"/>
                        </a>
                        <ul id="menu">
                            <li><a class="ays-btn" href="http://freedemo.ays-pro.com/poll-maker-demo-free/" target="_blank">Demo</a></li>
                            <li><a class="ays-btn" href="http://ays-pro.com/index.php/wordpress/poll-maker/" target="_blank">PRO</a></li>
                            <li><a class="ays-btn" href="https://wordpress.org/support/plugin/poll-maker/" target="_blank">Support Chat</a></li>
                            <li><a class="ays-btn" href="http://ays-pro.com/index.php/contact/" target="_blank">Contact us</a></li>
                        </ul>
                    </div>
                </div>
             </div>
        <?php
    }
}
run_poll_maker_ays();
