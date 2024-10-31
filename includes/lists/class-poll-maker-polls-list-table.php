<?php
ob_start();
class Polls_List_Table extends WP_List_Table {
    private $plugin_name;
    public $themes;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct([
            'singular' => __('Poll', $this->plugin_name), //singular name of the listed records
            'plural' => __('Polls', $this->plugin_name), //plural name of the listed records
            'ajax' => false, //does this table support ajax?
        ]);
        add_action('admin_notices', array($this, 'poll_notices'));
        $this->themes = [
            'personal',
            [
                'name' => 'light',
                'main_color' => '#0a100d',
                'text_color' => '#0a100d',
                'icon_color' => '#0a100d',
                'bg_color' => '#fcfcfc',
            ],
            [
                'name' => 'dark',
                'main_color' => '#fcfcfc',
                'text_color' => '#fcfcfc',
                'icon_color' => '#fcfcfc',
                'bg_color' => '#0a100d',
            ],
        ];
    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_polls($per_page = 5, $page_number = 1) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_polls";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' DESC';
        } else {
            $sql .= ' ORDER BY id DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_poll_by_id($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_polls WHERE id=" . absint(intval($id));
        $poll = $wpdb->get_row($sql, 'ARRAY_A');

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE poll_id=" . absint(intval($id)) . " ORDER BY id ASC";
        $poll['answers'] = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = explode(',', $poll['categories']);
        $poll['categories'] = !empty($cats) ? $cats : [];
        $json = $poll['styles'];
        $poll['styles'] = json_decode($json, TRUE);

        return $poll;
    }
    public function get_categories() {
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_categories";
        return $wpdb->get_results($sql, 'ARRAY_A');
    }

    public function add_or_edit_polls($data) {
        // print_r()
        // wp_die($data);
        global $wpdb;
        $poll_table = $wpdb->prefix . 'ayspoll_polls';
        $answer_table = $wpdb->prefix . 'ayspoll_answers';
        $ays_change_type = (isset($data['ays_change_type'])) ? $data['ays_change_type'] : '';

        if (isset($data["poll_action"]) && wp_verify_nonce($data["poll_action"], 'poll_action')) {
            $id = absint(intval($data['id']));
            $title = sanitize_text_field($data['ays-poll-title']);
            $show_title = isset($data['show_title']) && 'show' == $data['show_title'] ? 1 : 0;
            $categories = isset($data['ays-poll-categories']) ? ',' . implode(',', $data['ays-poll-categories']) . ',' : '';
            $description = sanitize_textarea_field($data['ays-poll-description']);
            $type = sanitize_text_field($data['ays-poll-type']);
            $question = wpautop($data['ays_poll_question']);
            $image = wp_http_validate_url($data['ays_poll_image']);
            $theme_id = absint($data['ays_poll_theme']);
            $main_color = sanitize_text_field($data['ays_poll_main_color']);
            $text_color = sanitize_text_field($data['ays_poll_text_color']);
            $icon_color = sanitize_text_field($data['ays_poll_icon_color']);
            $bg_color = sanitize_text_field($data['ays_poll_bg_color']);
            $icon_size = absint($data['ays_poll_icon_size']) >= 10 ? absint($data['ays_poll_icon_size']) : 24;
            $width = absint($data['ays_poll_width']);
            $btn_text = sanitize_text_field($data['ays_poll_btn_text']);
            $border_style = sanitize_text_field($data['ays_poll_border_style']);
            $css = wpautop($data['ays_custom_css']);
            $styles = json_encode([
                'main_color' => $main_color,
                'text_color' => $text_color,
                'icon_color' => $icon_color,
                'icon_size' => $icon_size,
                'width' => $width,
                'btn_text' => $btn_text,
                'border_style' => $border_style,
                'bg_color' => $bg_color,
            ]);

            switch ($type) {
            case 'choosing':
                $view_type = '';
                $answers = $data['ays-poll-answers'];
                $answers_ids = $data['ays-poll-answers-ids'];
                break;
            case 'voting':
                $view_type = $data['ays-poll-vote-type'];
                $answers = [1, -1];
                break;
            case 'rating':
                $view_type = $data['ays-poll-rate-type'];
                $rate_value = $data['ays-poll-rate-value'];
                $answers = range(1, $rate_value);
            default:
                # code...
                break;
            }
            if (0 == $id) {
                $poll_result = $wpdb->insert(
                    $poll_table,
                    [
                        'title' => $title,
                        'description' => $description,
                        'type' => $type,
                        'question' => $question,
                        'view_type' => $view_type,
                        'categories' => $categories,
                        'image' => $image,
                        'show_title' => $show_title,
                        'styles' => $styles,
                        'custom_css' => $css,
                        'theme_id' => $theme_id,
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                    ]
                );
                $last_id = $wpdb->insert_id;
                foreach ($answers as $answer) {
                    $wpdb->insert(
                        $answer_table,
                        array(
                            'poll_id' => $last_id,
                            'answer' => sanitize_text_field($answer),
                        ),
                        array(
                            '%d',
                            '%s',
                        )
                    );
                }
                $message = 'created';
            } else {
                $poll = $this->get_poll_by_id($id);
                $poll_result = $wpdb->update(
                    $poll_table,
                    [
                        'title' => $title,
                        'description' => $description,
                        'type' => $type,
                        'question' => $question,
                        'view_type' => $view_type,
                        'categories' => $categories,
                        'image' => $image,
                        'show_title' => $show_title,
                        'styles' => $styles,
                        'custom_css' => $css,
                        'theme_id' => $theme_id,
                    ],
                    ['id' => $id],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                    ],
                    ['%d']
                );
                if ($type != $poll['type']) {
                    $wpdb->delete(
                        $answer_table,
                        ['poll_id' => $id],
                        ['%d']
                    );
                    foreach ($answers as $answer) {
                        $wpdb->insert(
                            $answer_table,
                            array(
                                'poll_id' => $id,
                                'answer' => sanitize_text_field($answer),
                            ),
                            array(
                                '%d',
                                '%s',
                            )
                        );
                    }
                }
                if ('choosing' == $type && $type == $poll['type']) {
                    foreach ($poll['answers'] as $answer) {
                        $old_id = $answer['id'];
                        $index = array_search($old_id, $answers_ids);
                        if ($index !== false) {
                            $new_answer = $answers[$index];
                            $wpdb->update(
                                $answer_table,
                                ['answer' => sanitize_text_field($new_answer)],
                                ['id' => $old_id],
                                ['%s'], ['%d']
                            );
                        } else {
                            $wpdb->delete(
                                $answer_table,
                                ['id' => $old_id],
                                ['%d']
                            );
                        }
                    }
                    foreach ($answers_ids as $index => $value) {
                        if (0 == $value) {
                            $wpdb->insert(
                                $answer_table,
                                [
                                    'poll_id' => $id,
                                    'answer' => sanitize_text_field($answers[$index]),
                                ],
                                ['%d', '%s']
                            );
                        }
                    }
                }
                if (count($poll['answers']) != $rate_value && $type == 'rating') {
                    if (count($poll['answers']) > $rate_value) {
                        for ($i = $rate_value; $i < count($poll['answers']); $i++) {
                            $wpdb->delete(
                                $answer_table,
                                ['id' => $poll['answers'][$i]['id']],
                                ['%d']
                            );
                        }
                    } else {
                        for ($i = count($poll['answers']); $i < $rate_value; $i++) {
                            $wpdb->insert(
                                $answer_table,
                                [
                                    'poll_id' => $id,
                                    'answer' => sanitize_text_field($answers[$i]),
                                ],
                                ['%d', '%s']
                            );
                        }
                    }
                }
                $message = 'updated';
            }
            if ($poll_result >= 0) {
                if ('' != $ays_change_type) {
                    $url = esc_url_raw(remove_query_arg(false)) . '&status=' . $message;
                    wp_redirect($url);
                } else {
                    $url = esc_url_raw(remove_query_arg(['action', 'question'])) . '&status=' . $message;
                    wp_redirect($url);
                }
            }
        }
    }
    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_polls($id) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}ayspoll_polls",
            ['id' => $id],
            ['%d']
        );
        $wpdb->delete(
            "{$wpdb->prefix}ayspoll_answers",
            ['poll_id' => $id],
            ['%d']
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ayspoll_polls";

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        __('There are no polls yet.', $this->plugin_name);
    }

    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name) {
        switch ($column_name) {
        case 'title':
        case 'categories':
        case 'type':
        case 'shortcode':
        case 'id':
            return $item[$column_name];
            break;
        default:
            return print_r($item, true); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_title($item) {
        $delete_nonce = wp_create_nonce($this->plugin_name . '-delete-poll');

        $title = sprintf('<a href="?page=%s&action=%s&poll=%d">' . stripslashes($item['title']) . '</a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id']));

        $actions = [
            'edit' => sprintf('<a href="?page=%s&action=%s&poll=%d">' . __('Edit', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id'])),
            'delete' => sprintf('<a href="?page=%s&action=%s&poll=%s&_wpnonce=%s">' . __('Delete', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
        ];

        return $title . $this->row_actions($actions);
    }

    function column_shortcode($item) {
        return sprintf('<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_poll id=%s]" />', $item["id"]);
    }

    function column_categories($item) {
        if ($item['categories'] == '') {
            return '';
        }
        global $wpdb;
        $cats_ids = explode(',', $item['categories']);
        $cats_content = "<ul id='cats-in-table'>";
        foreach ($cats_ids as $id) {
            if (empty($id)) {
                continue;
            }
            $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_categories WHERE id='$id'", "ARRAY_A");
            if (empty($result)) {
                continue;
            }
            $cats_content .= "<li>{$result['title']}</li>";
        }
        $cats_content .= "</ul>";
        return $cats_content;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', $this->plugin_name),
            'type' => __('Type', $this->plugin_name),
            'shortcode' => __('Shortcode', $this->plugin_name),
            'categories' => __('Categories', $this->plugin_name),
            'id' => __('ID', $this->plugin_name),
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'title' => array('title', true),
            'type' => array('type', true),
            'id' => array('id', true),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', $this->plugin_name),
        ];

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('polls_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_polls($per_page, $current_page);
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, $this->plugin_name . '-delete-poll')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_polls(absint($_GET['poll']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw(remove_query_arg(['action', 'poll', '_wpnonce'])) . '&status=' . $message;
                wp_redirect($url);
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && 'bulk-delete' == $_POST['action'])
            || (isset($_POST['action2']) && 'bulk-delete' == $_POST['action2'])
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_polls($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw(remove_query_arg(['action', 'poll', '_wpnonce'])) . '&status=' . $message;
            wp_redirect($url);
        }
    }

    public function poll_notices() {
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

        if (empty($status)) {
            return;
        }

        if ('created' == $status) {
            $updated_message = esc_html(__('Poll created.', $this->plugin_name));
        } elseif ('updated' == $status) {
            $updated_message = esc_html(__('Poll saved.', $this->plugin_name));
        } elseif ('deleted' == $status) {
            $updated_message = esc_html(__('Poll deleted.', $this->plugin_name));
        }

        if (empty($updated_message)) {
            return;
        }

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
}
}
