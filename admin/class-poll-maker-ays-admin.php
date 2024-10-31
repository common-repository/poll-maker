<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/admin
 * @author     Poll Maker Team <info@ays-pro.com>
 */
class Poll_Maker_Ays_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $polls_obj;
    private $cats_obj;
    private $results_obj;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix) {
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        if (false === strpos($hook_suffix, $this->plugin_name)) {
            return;
        }

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Poll_Maker_Ays_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Poll_Maker_Ays_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style('ays_poll_font_awesome', 'https://use.fontawesome.com/releases/v5.4.1/css/all.css', array(), $this->version, 'all');
        wp_enqueue_style('ays_poll_bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style('ays-poll-select2', plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/poll-maker-ays-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix) {
        if (false === strpos($hook_suffix, $this->plugin_name)) {
            return;
        }

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Poll_Maker_Ays_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Poll_Maker_Ays_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script('jquery');
        wp_enqueue_media();

        wp_enqueue_script('ays_poll_popper', plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('ays_poll_bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('ays_poll_select2', plugin_dir_url(__FILE__) . 'js/select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script('ays-poll-admin-js', plugin_dir_url(__FILE__) . 'js/poll-maker-ays-admin.js', array('jquery', 'wp-color-picker'), $this->version, true);
        // wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/poll-maker-admin-ajax.js', array('jquery'), $this->version, true);
        // wp_localize_script($this->plugin_name . '-ajax', 'poll_maker_ajax', array('ajax_url' => admin_url('admin-ajax.php')));

    }
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        $hook_polls = add_menu_page('Poll Maker', 'Poll Maker', 'manage_options', $this->plugin_name, array($this, 'display_plugin_polls_page'), POLL_MAKER_AYS_ADMIN_URL . '/images/icons/icon-128x128.png', '6.33');

        add_action("load-$hook_polls", [$this, 'screen_option_polls']);

        $hook_cats = add_submenu_page(
            $this->plugin_name,
            __('Categories', $this->plugin_name),
            __('Categories', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-cats',
            array($this, 'display_plugin_cats_page')
        );
        add_action("load-$hook_cats", [$this, 'screen_option_cats']);

        $hook_results = add_submenu_page(
            $this->plugin_name,
            __('Results', $this->plugin_name),
            __('Results', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-results',
            array($this, 'display_plugin_results_page')
        );
        add_action("load-$hook_results", [$this, 'screen_option_results']);

        add_options_page('Poll Maker', 'Poll Maker', 'manage_options', $this->plugin_name, array($this, 'display_plugin_polls_page'));
    }
/**
 * Add settings action link to the plugins page.
 *
 * @since    1.0.0
 */

    public function add_action_links($links) {
        /*
         *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
         */
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
            '<a href="https://ays-pro.com/index.php/wordpress/poll-maker/" target="_blank" style="color:red;">' . __('BUY NOW', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);

    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */

    public function display_plugin_polls_page() {
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
        case 'add':
            include_once 'partials/poll-maker-ays-polls-actions.php';
            break;
        case 'edit':
            include_once 'partials/poll-maker-ays-polls-actions.php';
            break;
        default:
            include_once 'partials/poll-maker-ays-admin-display.php';
        }
    }

    public function display_plugin_cats_page() {
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
        case 'add':
            include_once 'partials/categories/actions/poll-maker-ays-categories-actions.php';
            break;
        case 'edit':
            include_once 'partials/categories/actions/poll-maker-ays-categories-actions.php';
            break;
        default:
            include_once 'partials/categories/poll-maker-ays-categories-display.php';
        }
    }

    public function display_plugin_results_page() {
        include_once 'partials/results/poll-maker-ays-results-display.php';
    }

    public static function set_screen($status, $option, $value) {
        return $value;
    }

    public function screen_option_polls() {
        $option = 'per_page';
        $args = [
            'label' => __('Polls', $this->plugin_name),
            'default' => 5,
            'option' => 'polls_per_page',
        ];

        add_screen_option($option, $args);
        $this->polls_obj = new Polls_List_Table($this->plugin_name);
    }

    public function screen_option_cats() {
        $option = 'per_page';
        $args = [
            'label' => __('Categories', $this->plugin_name),
            'default' => 5,
            'option' => 'cats_per_page',
        ];

        add_screen_option($option, $args);
        $this->cats_obj = new Pma_Categories_List_Table($this->plugin_name);
    }

    public function screen_option_results() {
        $option = 'per_page';
        $args = [
            'label' => __('Results', $this->plugin_name),
            'default' => 5,
            'option' => 'poll_results_per_page',
        ];

        add_screen_option($option, $args);
        $this->results_obj = new Pma_Results_List_Table($this->plugin_name);
    }
    public function register_poll_ays_widget() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ayspoll_polls";

        $c = $wpdb->get_var($sql);
        if ($c == 0) {return;} else {
            register_widget('Poll_Maker_Widget');
        }
    }

}
