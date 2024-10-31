<?php
ob_start();
class Pma_Results_List_Table extends WP_List_Table {
    private $plugin_name;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        parent::__construct([
            'singular' => __('Result', $this->plugin_name), //singular name of the listed records
            'plural' => __('Results', $this->plugin_name), //plural name of the listed records
            'ajax' => false, //does this table support ajax?
        ]);
        add_action('admin_notices', array($this, 'results_notices'));

    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_reports($per_page = 5, $page_number = 1) {

        global $wpdb;

        $sql = "SELECT
        {$wpdb->prefix}ayspoll_reports.id,
        {$wpdb->prefix}ayspoll_reports.answer_id,
        {$wpdb->prefix}ayspoll_reports.user_ip,
        {$wpdb->prefix}ayspoll_reports.vote_date
        FROM
        {$wpdb->prefix}ayspoll_reports
        JOIN
        {$wpdb->prefix}ayspoll_answers
        ON {$wpdb->prefix}ayspoll_answers.id = {$wpdb->prefix}ayspoll_reports.answer_id";

        if (isset($_REQUEST['orderbypoll'])) {
            $poll_id = absint($_REQUEST['orderbypoll']);

            $sql .= " WHERE {$wpdb->prefix}ayspoll_reports.answer_id IN (SELECT {$wpdb->prefix}ayspoll_answers.id FROM {$wpdb->prefix}ayspoll_answers WHERE {$wpdb->prefix}ayspoll_answers.poll_id='$poll_id')";

            // $result = $wpdb->get_results($sql, 'ARRAY_A');
            // return $result;
        }
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

    public function get_report_by_id($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_reports WHERE id=" . absint(intval($id));

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }
    public function get_polls() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_polls";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_reports($id) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}ayspoll_reports",
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

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}ayspoll_reports";

        return $wpdb->get_var($sql);
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        _e('There are no results yet.', $this->plugin_name);
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
        case 'poll_id':
        case 'poll_title':
        case 'user_ip':
        case 'answer':
        case 'vote_date':
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

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_poll_id($item) {
        global $wpdb;

        $delete_nonce = wp_create_nonce($this->plugin_name . '-delete-result');

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE id={$item['answer_id']}", "ARRAY_A");

        $res = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_polls WHERE id={$result['poll_id']}", "ARRAY_A");

        $title = absint($item['id']);

        $actions = [
            'delete' => sprintf('<a href="?page=%s&action=%s&result=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce),
        ];

        return $title . $this->row_actions($actions);
    }

    function column_poll_title($item) {
        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE id={$item['answer_id']}", "ARRAY_A");

        $res = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_polls WHERE id={$result['poll_id']}", "ARRAY_A");

        return stripslashes($res['title']);
    }

    function column_answer($item) {
        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE id={$item['answer_id']}", "ARRAY_A");

        return stripslashes($result['answer']);
    }

    function column_vote_date($item) {
        global $wpdb;

        $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}ayspoll_reports WHERE id={$item['id']}", "ARRAY_A");

        return date('H:i:s d.m.Y', strtotime($result['vote_date']));
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'poll_id' => __('Poll ID', $this->plugin_name),
            'poll_title' => __('Poll', $this->plugin_name),
            'user_ip' => __('User IP', $this->plugin_name),
            'answer' => __('Answer', $this->plugin_name),
            'vote_date' => __('Vote Datetime', $this->plugin_name),
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
            'vote_date' => array('vote_date', true),
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
            'bulk-delete' => 'Delete',
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

        $per_page = $this->get_items_per_page('poll_results_per_page', 5);

        $current_page = $this->get_pagenum();
        $total_items = self::record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page' => $per_page, //WE have to determine how many items to show on a page
        ]);

        $this->items = self::get_reports($per_page, $current_page);
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ('delete' === $this->current_action()) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr($_REQUEST['_wpnonce']);

            if (!wp_verify_nonce($nonce, $this->plugin_name . '-delete-result')) {
                die('Go get a life script kiddies');
            } else {
                self::delete_reports(absint($_GET['result']));

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw(remove_query_arg(['action', 'result', '_wpnonce'])) . '&status=' . $message;
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
                self::delete_reports($id);

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw(remove_query_arg(['action', 'result', '_wpnonce'])) . '&status=' . $message;
            wp_redirect($url);
        }
    }

    public function results_notices() {
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field($_REQUEST['status']) : '';

        if (empty($status)) {
            return;
        }

        if ('created' == $status) {
            $updated_message = esc_html(__('Result created.', $this->plugin_name));
        } elseif ('updated' == $status) {
            $updated_message = esc_html(__('Result saved.', $this->plugin_name));
        } elseif ('deleted' == $status) {
            $updated_message = esc_html(__('Result deleted.', $this->plugin_name));
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
