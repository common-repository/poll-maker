<?php
ob_start();
class Pma_Categories_List_Table extends WP_List_Table {
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct([
            'singular' => __('Category', $this->plugin_name), //singular name of the listed records
            'plural' => __('Categories', $this->plugin_name), //plural name of the listed records
            'ajax' => false, //does this table support ajax?
        ]);
        add_action('admin_notices', array($this, 'poll_category_notices'));

    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_poll_categories($per_page = 5, $page_number = 1) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_categories";

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

    public function get_poll_category($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_categories WHERE id=" . absint(intval($id));

        $result = $wpdb->get_row($sql, 'ARRAY_A');
        return $result;
    }

    public function add_edit_poll_category($data) {
        global $wpdb;
        $cats_table = $wpdb->prefix . 'ayspoll_categories';
        $ays_change_type = (isset($data['ays_change_type'])) ? $data['ays_change_type'] : '';

        if (isset($data["poll_category_action"]) && wp_verify_nonce($data["poll_category_action"], 'poll_category_action')) {
            $id = absint(intval($data['id']));
            $title = stripslashes(sanitize_text_field($data['ays_title']));
            $description = stripslashes(sanitize_textarea_field($data['ays_description']));
            $message = '';
            if ($id == 0) {
                $result = $wpdb->insert(
                    $cats_table,
                    [
                        'title' => $title,
                        'description' => $description,
                    ],
                    ['%s', '%s']
                );
                $message = 'created';
            } else {
                $result = $wpdb->update(
                    $cats_table,
                    [
                        'title' => $title,
                        'description' => $description,
                    ],
                    ['id' => $id],
                    ['%s', '%s'],
                    ['%d']
                );
                $message = 'updated';
            }

            if ($result >= 0) {
                if ($ays_change_type != '') {
                    $url = esc_url_raw(remove_query_arg(false)) . '&status=' . $message;
                    wp_redirect($url);
                } else {
                    $url = esc_url_raw(remove_query_arg(['action', 'poll'])) . '&status=' . $message;
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
    public static function delete_poll_categories($id) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}ayspoll_categories",
            ['id' => $id],
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

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ayspoll_categories";

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e('There are no poll categories yet.', $this->plugin_name);
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
        case 'description':
            return $item[$column_name];
            break;
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
            '<input type="checkbox" name="bulk-delete[]" value="%s">', $item['id']
        );
    }

    function column_shortcode($item) {
        return sprintf('<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_poll cat_id=%s]" />', $item["id"]);
    }
    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_title($item) {
        $delete_nonce = wp_create_nonce($this->plugin_name . '-delete-poll-category');

        $title = sprintf('<a href="?page=%s&action=%s&poll_category=%d"><strong>' . $item['title'] . '</strong></a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id']));

        $actions = [
            'edit' => sprintf('<a href="?page=%s&action=%s&poll_category=%d">' . __('Edit', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'edit', absint($item['id'])),
            'delete' => sprintf('<a href="?page=%s&action=%s&poll_category=%s&_wpnonce=%s">' . __('Delete', $this->plugin_name) . '</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
        ];

        return $title . $this->row_actions($actions);
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox">',
            'title' => __('Title', $this->plugin_name),
            'description' => __('Description', $this->plugin_name),
            'shortcode' => __('ShortCode', $this->plugin_name),
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

        $per_page = $this->get_items_per_page('poll_cats_per_page', 5);
        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_poll_categories($per_page, $current_page);
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, $this->plugin_name . '-delete-poll-category')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_poll_categories(absint($_GET['poll_category']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw(remove_query_arg(['action', 'poll_category', '_wpnonce'])) . '&status=deleted';
                wp_redirect($url);
            }

        }

        // If the delete bulk action is triggered
        if ((isset($_POST['action']) && $_POST['action'] == 'bulk-delete')
            || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete')
        ) {

            $delete_ids = esc_sql($_POST['bulk-delete']);

            // loop over the array of record IDs and delete them
            foreach ($delete_ids as $id) {
                self::delete_poll_categories($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw(remove_query_arg(['action', 'poll_category', '_wpnonce'])) . '&status=deleted';
            wp_redirect($url);
        }
    }

    public function poll_category_notices() {
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

        if (empty($status)) {
            return;
        }

        if ('created' == $status) {
            $updated_message = esc_html(__('Poll category created.', $this->plugin_name));
        } elseif ('updated' == $status) {
            $updated_message = esc_html(__('Poll category saved.', $this->plugin_name));
        } elseif ('deleted' == $status) {
            $updated_message = esc_html(__('Poll category deleted.', $this->plugin_name));
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