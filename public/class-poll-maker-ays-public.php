<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/public
 * @author     Poll Maker Team <info@ays-pro.com>
 */
class Poll_Maker_Ays_Public {

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

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

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
        wp_enqueue_style('font-awesome', "https://use.fontawesome.com/releases/v5.5.0/css/all.css", array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/poll-maker-ays-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

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
        wp_enqueue_script("jquery-effects-core");
        wp_enqueue_script($this->plugin_name . '-ajax-public', plugin_dir_url(__FILE__) . 'js/poll-maker-public-ajax.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/poll-maker-ays-public.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name . '-ajax-public', 'poll_maker_ajax_public', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    public function ays_poll_initialize_shortcode() {
        add_shortcode('ays_poll', array($this, 'ays_generate_poll'));
    }

    public function ays_generate_poll($attr) {
        $id = (isset($attr['id'])) ? absint(intval($attr['id'])) : null;
        $poll = $this->get_poll_by_id($id);
        // echo '<pre>';
        // var_dump($poll);
        //wp_die();
        if (count($poll) == 2) {
            return;
        }

        $this_poll_id = uniqid("ays-poll-id-");
        $styles = $poll['styles'];
        $emoji = [
            "<i class='far fa-dizzy'></i>",
            "<i class='far fa-smile'></i>",
            "<i class='far fa-meh'></i>",
            "<i class='far fa-frown'></i>",
            "<i class='far fa-tired'></i>",
        ];
        $content = "<style>

        .$this_poll_id.box-apm {
            width: " . ((int) $styles['width'] > 0 ? (int) $styles['width'] . "px" : "100%") . ";
            margin: 0 auto;
            border-style: {$styles['border_style']};
            border-color: {$styles['main_color']};
            background-color: {$styles['bg_color']};
        }
        .$this_poll_id .answer-percent {
            background-color: {$styles['main_color']};
            color: {$styles['bg_color']} !important;
        }
        .$this_poll_id .ays-poll-btn {
            background-color: {$styles['main_color']} !important;
            color: {$styles['bg_color']} !important;
        }
        .$this_poll_id.box-apm * {
            color: {$styles['text_color']};
        }
        .$this_poll_id.box-apm i {
            color: {$styles['icon_color']};
            font-size: {$styles['icon_size']}px;
        }
        .$this_poll_id.choosing-poll label {
            background-color: {$styles['bg_color']};
            border: 1px solid {$styles['main_color']};
        }
        .$this_poll_id.choosing-poll input[type='radio']:checked + label,
        .$this_poll_id.choosing-poll label:hover {
            background-color: {$styles['text_color']};;
            color: {$styles['bg_color']};
        }

        {$poll['custom_css']}
        </style>
        <form style='margin:1rem auto'>
		<div class='box-apm {$poll['type']}-poll $this_poll_id' id='$this_poll_id'>";
        $content .= 1 == $poll['show_title'] ? "<div class='apm-title-box'><h5>{$poll['title']}</h5></div>" : "";
        $content .= $poll['image'] ? "<div class='apm-img-box'><img class='ays-poll-img' src='{$poll['image']}'></div>" : "";
        $content .= "<div class='$this_poll_id question'>" . stripslashes($poll['question']) . "</div><div class='apm-answers'>";
        switch ($poll['type']) {
        case 'choosing':
            foreach ($poll['answers'] as $index => $answer) {
                $content .= "<div class='apm-choosing answer-$this_poll_id'><input type='radio' name='answer' id='radio-$index-$this_poll_id' value='{$answer['id']}'>
                    <label for='radio-$index-$this_poll_id'>" . stripcslashes($answer['answer']) . "</label></div>";
            }
            break;
        case 'voting':
            switch ($poll['view_type']) {
            case 'hand':
                foreach ($poll['answers'] as $index => $answer) {
                    $content .= "<div class='apm-voting answer-$this_poll_id'><input type='radio' name='answer' id='radio-$index-$this_poll_id' value='{$answer['id']}'>
                            <label for='radio-$index-$this_poll_id'>";
                    $content .= ((int) $answer['answer'] > 0 ? "<i class='far fa-thumbs-up'></i>" : "<i class='far fa-thumbs-down'></i>") . "</label></div>";
                }
                break;
            case 'emoji':
                foreach ($poll['answers'] as $index => $answer) {
                    $content .= "<div class='apm-voting answer-$this_poll_id'><input type='radio' name='answer' id='radio-$index-$this_poll_id' value='{$answer['id']}'>
                            <label for='radio-$index-$this_poll_id'>";
                    $content .= ((int) $answer['answer'] > 0 ? $emoji[1] : $emoji[3]) . "</label></div>";
                }
                break;
            }
            break;
        case 'rating':
            switch ($poll['view_type']) {
            case 'star':
                foreach ($poll['answers'] as $index => $answer) {
                    $content .= "<div class='apm-rating answer-$this_poll_id'><input type='radio' name='answer' id='radio-$index-$this_poll_id' value='{$answer['id']}'>
                            <label for='radio-$index-$this_poll_id'><i class='far fa-star'></i></label></div>";
                }
                break;
            case 'emoji':
                foreach ($poll['answers'] as $index => $answer) {
                    $content .= "<div class='apm-rating answer-$this_poll_id'><input type='radio' name='answer' id='radio-$index-$this_poll_id' value='{$answer['id']}'>
                            <label class='emoji' for='radio-$index-$this_poll_id'>" . $emoji[count($poll['answers']) / 2 - $index + 1.5] . "</label></div>";
                }
                break;
            }
            break;
        }

        $content .= "</div>" . wp_nonce_field('ays_finish_poll', 'ays_finish_poll') . "<div class='apm-button-box'><input type='button' name='ays_finish_poll' class='btn ays-poll-btn {$poll['type']}-btn ' data-form='$this_poll_id' data-id='{$poll['id']}' value='" . (isset($styles['btn_text']) && '' != $styles['btn_text'] ? $styles['btn_text'] : 'Vote') . "'></div></div></form>";
        return $content;

    }

    private function get_poll_by_id($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_polls WHERE id=" . absint(intval($id));
        $poll = $wpdb->get_row($sql, 'ARRAY_A');

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE poll_id=" . absint(intval($id)) . " ORDER BY id ASC";
        $poll['answers'] = $wpdb->get_results($sql, 'ARRAY_A');
        $json = $poll['styles'];
        $poll['styles'] = json_decode($json, TRUE);
        return $poll;
    }
    private function get_answer_by_id($id) {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE id=" . absint(intval($id));
        return $wpdb->get_row($sql, 'ARRAY_A');
    }
    public function ays_finish_poll() {
        if (isset($_REQUEST["ays_finish_poll"]) && wp_verify_nonce($_REQUEST["ays_finish_poll"], 'ays_finish_poll')) {

            $poll_id = absint(intval($_REQUEST['poll_id']));
            $answer_id = absint(intval($_REQUEST['answer']));
            $answer = $this->get_answer_by_id($answer_id);
            $votes = $answer['votes'];
            $votes++;

            global $wpdb;
            $wpdb->update(
                "{$wpdb->prefix}ayspoll_answers",
                ['votes' => $votes],
                ['id' => $answer_id],
                ['%d'], ['%d']
            );
            $wpdb->insert(
                "{$wpdb->prefix}ayspoll_reports",
                [
                    'answer_id' => $answer_id,
                    'user_ip' => $this->get_user_ip(),
                    'vote_date' => date('Y-m-d G:i:s'),
                ],
                ['%d', '%s', '%s']
            );

            echo json_encode($this->get_poll_by_id($poll_id));
            wp_die();
        }
    }

    private function get_user_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
}
