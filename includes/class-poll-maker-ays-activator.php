<?php
global $ays_poll_db_version;
$ays_poll_db_version = '1.0.0';
/**
 * Fired during plugin activation
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Poll_Maker_Ays
 * @subpackage Poll_Maker_Ays/includes
 * @author     Poll Maker Team <info@ays-pro.com>
 */
class Poll_Maker_Ays_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        global $wpdb;
        global $ays_poll_db_version;
        $installed_ver = get_option("ays_poll_db_version");
        $polls_table = $wpdb->prefix . 'ayspoll_polls';
        $cats_table = $wpdb->prefix . 'ayspoll_categories';
        $answers_table = $wpdb->prefix . 'ayspoll_answers';
        $reports_table = $wpdb->prefix . 'ayspoll_reports';
        $charset_collate = $wpdb->get_charset_collate();

        if ($installed_ver != $ays_poll_db_version) {
            $sql = "CREATE TABLE `" . $polls_table . "` (
                `id`        	INT(11)     UNSIGNED NOT NULL AUTO_INCREMENT,
                `title`     	VARCHAR(255)         NOT NULL,
                `description`   TEXT                 NOT NULL,
                `question` 		VARCHAR(255)  		 NOT NULL,
                `type` 		    VARCHAR(64)  		 NOT NULL,
                `view_type` 	VARCHAR(64)  		 NOT NULL,
                `categories` 	VARCHAR(255)  		 NOT NULL,
                `image`         TEXT                 DEFAULT '',
                `show_title`    INT(1)               DEFAULT 1,
                `styles`        TEXT                 DEFAULT '',
                `custom_css`    TEXT                 DEFAULT '',
                `theme_id`      INT(5)               DEFAULT 1,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta($sql);

            $sql = "CREATE TABLE `" . $cats_table . "` (
                `id`        	INT(11)     UNSIGNED NOT NULL AUTO_INCREMENT,
                `title`     	VARCHAR(255)         NOT NULL,
                `description`   TEXT                 NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta($sql);

            $sql = "CREATE TABLE `" . $answers_table . "` (
                `id`                INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `poll_id`       	INT(11) UNSIGNED NOT NULL,
                `answer`	        VARCHAR(256)	 NOT NULL,
                `votes`             INT(11)          NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta($sql);

            $sql = "CREATE TABLE `" . $reports_table . "` (
                `id` 		     INT(11)		NOT NULL AUTO_INCREMENT,
                `answer_id`      INT(11)		NOT NULL,
                `user_ip`	     VARCHAR(255)   NOT NULL,
                `vote_date`      DATETIME       NOT NULL,
                PRIMARY KEY (`id`)
            )$charset_collate;";
            dbDelta($sql);

            $sql = "ALTER TABLE {$reports_table}
                ADD CONSTRAINT `FK_answer_vote`
                FOREIGN KEY (`answer_id`) REFERENCES {$answers_table} (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE;";
            dbDelta($sql);

            $sql = "ALTER TABLE {$answers_table}
                ADD CONSTRAINT `FK_answer_question`
                FOREIGN KEY (`poll_id`) REFERENCES {$polls_table} (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE;";
            dbDelta($sql);

            update_option('ays_poll_db_version', $ays_poll_db_version);
        }
    }
    private static function insert_default_values() {
        global $wpdb;
        $answers_table = $wpdb->prefix . 'ayspoll_answers';
        $polls_table = $wpdb->prefix . 'ayspoll_polls';
        $cats_table = $wpdb->prefix . 'ayspoll_categories';

        $wpdb->insert($polls_table, array('title' => 'Default', 'description' => 'Plugin', 'question' => 'Did you like our plugin?', 'type' => 'choosing', 'styles' => '{"main_color":"#0C6291","text_color":"#0C6291","icon_color":"#0C6291","icon_size":24,"width":0,"btn_text":"Vote","border_style":"ridge","bg_color":"#FBFEF9"}'));
        $last_insert = $wpdb->insert_id;
        $wpdb->insert($answers_table, array('poll_id' => $last_insert, 'answer' => 'It was a mistake'));
        $wpdb->insert($answers_table, array('poll_id' => $last_insert, 'answer' => 'There was nothing special'));
        $wpdb->insert($answers_table, array('poll_id' => $last_insert, 'answer' => 'Everything\'s ok'));
        $wpdb->insert($answers_table, array('poll_id' => $last_insert, 'answer' => 'I enjoyed it'));
        $wpdb->insert($answers_table, array('poll_id' => $last_insert, 'answer' => 'It\'s amazing'));
        $wpdb->insert($cats_table, array('title' => 'Uncategorized', 'description' => 'Default poll category'));

    }

    public static function ays_poll_update_db_check() {
        global $ays_poll_db_version;
        if (get_site_option('ays_poll_db_version') != $ays_poll_db_version) {
            self::activate();
            self::insert_default_values();
        }
    }
}
