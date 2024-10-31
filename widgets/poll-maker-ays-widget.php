<?php
if (!defined('AYS_POLL_URL')) {
    define('AYS_POLL_URL', plugins_url(plugin_basename(dirname(__FILE__))));}
class Poll_Maker_Widget extends WP_Widget {
    public $poll_maker_ays;
    public function __construct() {
        $widget_ops = array(
            'classname' => 'poll_maker_ays',
            'description' => 'Poll Maker Widget',
        );
        parent::__construct('poll_maker_ays', 'Poll_Maker_Widget', $widget_ops);
    }
    public function get_poll_by_id($id) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_polls WHERE id=" . absint(intval($id));
        $poll = $wpdb->get_row($sql, 'ARRAY_A');

        $sql = "SELECT * FROM {$wpdb->prefix}ayspoll_answers WHERE poll_id=" . absint(intval($id)) . " ORDER BY id ASC";
        $poll['answers'] = $wpdb->get_results($sql, 'ARRAY_A');
        $json = $poll['styles'];
        $poll['styles'] = json_decode($json, TRUE);
        return $poll;
    }
    function form($instance) {

        // Check values
        if ($instance) {
            $width = esc_attr($instance['poll_maker_ays_width']);
            $poll_id = esc_attr($instance['poll_maker_ays_id']);
        } else {
            $width = 400;
            $poll_id = 0;
        }
        global $wpdb;
        $polls = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ayspoll_polls", 'ARRAY_A');

        ?>
        <p>
            <select class="widefat" id="<?=$this->get_field_id('ays-polls');?>" name="<?=$this->get_field_name('poll_maker_ays_id');?>">
                <option value="0" selected disabled>Select poll</option>
                <?php
foreach ($polls as $poll) {?>
            <option value="<?=$poll['id'];?>" <?=$poll['id'] == $poll_id ? "selected" : "";?> ><?=$poll['title'];?></option>
        <?php }
        ?>
        </select>
    </p>
        <p>
			<label for="<?=$this->get_field_id('poll_maker_ays_width');?>">Width (px):</label>
			<input class="widefat" id="<?=$this->get_field_id('poll_maker_ays_width');?>" name="<?=$this->get_field_name('poll_maker_ays_width');?>" type="number" placeholder="400" value="<?=$width;?>" />
        </p>
        <?php
}
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Fields
        $instance['poll_maker_ays_id'] = absint($new_instance['poll_maker_ays_id']);
        $instance['poll_maker_ays_width'] = absint($new_instance['poll_maker_ays_width']);
        return $instance;
    }
    function widget($args, $instance) {

        $id = $instance['poll_maker_ays_id'];
        $poll = $this->get_poll_by_id($id);
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
                            <label class='emoji' for='radio-$index-$this_poll_id'>" . $emoji[count($poll['answers']) - $index - 1] . "</label></div>";
                }
                break;
            }
            break;
        }

        $content .= "</div>" . wp_nonce_field('ays_finish_poll', 'ays_finish_poll') . "<div class='apm-button-box'><input type='button' name='ays_finish_poll' class='btn ays-poll-btn {$poll['type']}-btn ' data-form='$this_poll_id' data-id='{$poll['id']}' value='" . (isset($styles['btn_text']) && '' != $styles['btn_text'] ? $styles['btn_text'] : 'Vote') . "'></div></div></form>";

        echo $args['before_widget'];
        echo $content;
        echo $args['after_widget'];
    }

}
