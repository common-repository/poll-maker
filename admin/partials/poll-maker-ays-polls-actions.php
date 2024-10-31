<?php
$action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading = '';
$image_text = __('Add Image', $this->plugin_name);

$id = (isset($_GET['poll'])) ? absint(intval($_GET['poll'])) : null;

$poll = [
    'title' => '',
    'description' => '',
    'categories' => [],
    'image' => '',
    'question' => '',
    'type' => '',
    'view_type' => '',
    'answers' => [],
    'show_title' => 1,
    'styles' => '',
    'custom_css' => '',
    'theme_id' => 1,
];
switch ($action) {
case 'add':
    $heading = __('Add new poll', $this->plugin_name);
    break;
case 'edit':
    $heading = __('Edit poll', $this->plugin_name);
    $poll = $this->polls_obj->get_poll_by_id($id);
    $options = $poll['styles'];
    break;
}
$categories = $this->polls_obj->get_categories();

if (isset($_POST['ays_submit']) || isset($_POST['ays_submit_top'])) {
    $_POST['id'] = $id;
    $this->polls_obj->add_or_edit_polls($_POST);
}
if (isset($_POST['ays_apply_top']) || isset($_POST['ays_apply'])) {
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->polls_obj->add_or_edit_polls($_POST);
}
$style = null;
if ('' != $poll['image']) {
    $style = "display: block;";
    $image_text = __('Edit Image', $this->plugin_name);
}

?>
<div class="wrap">
    <div class="container-fluid">
        <form class="ays-poll-category-form" id="ays-poll-category-form" method="post">
            <h1 class="wp-heading-inline">
                <?php
echo "$heading ";
$other_attributes = array('id' => 'ays-button-top');
submit_button(__('Save Poll', $this->plugin_name), 'primary', 'ays_submit_top', false, $other_attributes);
if (null != $id) {
    submit_button(__('Apply Poll', $this->plugin_name), '', 'ays_apply_top', false, $other_attributes);
}
?>
            </h1>
            <div class="nav-tab-wrapper">
                <a href="#tab1" class="nav-tab nav-tab-active">General</a>
                <a href="#tab3" class="nav-tab">Settings</a>
                <a href="#tab2" class="nav-tab">Styles</a>
            </div>
            <div id="tab1" class="ays-poll-tab-content ays-poll-tab-content-active">
            <p class="ays-subtitle">Poll options</p>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label for='ays-poll-title'><?=__('Title', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The name of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="ays-text-input" id='ays-poll-title' name='ays-poll-title' required value="<?=stripslashes(htmlentities($poll['title']));?>"/>
                    </div>
                </div>
                <hr>
                <div class='form-group row'  style="display:none">
                    <div class="col-sm-2">
                        <label for='ays-poll-description'><?=__('Description', $this->plugin_name);?></label>
                    </div>
                    <div class="col-sm-10">
                        <textarea class="ays-textarea" name="ays-poll-description" id="ays-poll-description" cols="30" rows="10"><?=stripslashes($poll['description']);?></textarea>
                    </div>
                </div>
                <div class='form-group row'>
                    <div class="col-sm-2">
                        <label for='ays-poll-category'><?=__('Categories', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The categories of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <?php if (!empty($categories)) {
    ?>
                        <select class="select2" name="ays-poll-categories[]" multiple>
                            <?php
foreach ($categories as $cat) {?>
                                <option value="<?=$cat['id'];?>" <?=in_array($cat['id'], $poll['categories']) ? 'selected' : '';?>><?=$cat['title'];?></option>
                            <?php }
    ?>
                        </select>
                            <?php } else {?>
                                <a href="?page=poll-maker-ays-cats&action=add">Create category</a>
                                <?php }?>
                    </div>
                </div>
                <hr>
                <div class="ays-field form-group">
                    <label for='ays-poll-question'><?=__('Question', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The question of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a>
                        <a href="javascript:void(0)" class="add-question-image button"><?=$image_text;?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Add image on the question">
                                      <i class="fas fa-info-circle"></i>
                                  </a></a>
                    </label>
                    <div class="ays-poll-question-image-container" style="<?=$style;?>">
                        <span class="ays-remove-question-img"></span>
                        <img src="<?=$poll['image'];?>" id="ays-poll-img"/>
                        <input type="hidden" name="ays_poll_image" id="ays-poll-image" value="<?=$poll['image'];?>"/>
                    </div>
                    <?php
$content = stripslashes($poll["question"]);
$editor_id = 'ays-poll-question';
$settings = array('editor_height' => '15', 'textarea_name' => 'ays_poll_question', 'editor_class' => 'ays-textarea', 'media_buttons' => true);
wp_editor($content, $editor_id, $settings);
?></div>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label for="ays-poll-type"><?=__('Poll type', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The type of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <select class="ays-select" id="ays-poll-type" name="ays-poll-type" required>
                            <option value='choosing' selected><?=__('Choosing', $this->plugin_name);?></option>
                            <option value="rating" <?=$poll['type'] == 'rating' ? "selected" : "";?>><?=__('Rating', $this->plugin_name);?></option>
                            <option value="voting" <?=$poll['type'] == 'voting' ? "selected" : "";?>><?=__('Voting', $this->plugin_name);?></option>
                        </select>
                        <span class="ays-poll-change-notice"><?=__('If you change the type, the number of counted answers will be annulled.', $this->plugin_name);?></span>
                    </div>
                </div>
                <hr>
                <div class="if-choosing form-group row">
                    <div class="col-sm-2">
                        <label for="ays-poll-answer"><?=__('Answers', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Answers from which the choice is made">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                    <i class="far fa-plus-square" title="Add answer" id="add-answer"></i>
                        <?php
if (count($poll['answers']) > 0 && 'choosing' == $poll['type']) {
    $answer_id = 1;
    foreach ($poll['answers'] as $answer) {?>
                                <div>
                                <input type="text" class="ays-text-input ays-text-input-short" name='ays-poll-answers[]' data-id="<?=$answer_id;?>" value="<?=stripcslashes($answer['answer']);?>">
                                <input type="hidden" data-id="<?=$answer_id;?>" name="ays-poll-answers-ids[]" value="<?=$answer['id'];?>">
                                <?=$answer_id > 2 ? "<i class='remove-answer fas fa-minus-square' data-id='$answer_id'></i>" : "";?>
                                </div>
                            <?php $answer_id++;}
} else {
    ?>
                        <div>
                        <input type="text" class="ays-text-input ays-text-input-short" name='ays-poll-answers[]'>
                        <input type="hidden" name="ays-poll-answers-ids[]" value="0">
                        </div><div>
                        <input type="text" class="ays-text-input ays-text-input-short" name='ays-poll-answers[]'>
                        <input type="hidden" name="ays-poll-answers-ids[]" value="0">
                        </div>
                        <?php }?>
                    </div>
                </div>
                <div class="if-voting form-group row">
                    <div class="col-sm-2">
                        <label for="ays-poll-vote-type"><?=__('Answers', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The appearance of the voting type of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <select class="ays-select" id="ays-poll-vote-type" name="ays-poll-vote-type">
                            <option value="" selected disabled><?=__('Select view of vote', $this->plugin_name);?></option>
                            <option value='hand' <?=$poll['view_type'] == 'hand' ? "selected" : "";?>><?=__('Hand', $this->plugin_name);?></option>
                            <option value="emoji" <?=$poll['view_type'] == 'emoji' ? "selected" : "";?>><?=__('Emoji', $this->plugin_name);?></option>
                        </select>
                        <i id="vote-res"></i>
                    </div>
                </div>
                <div class="if-rating form-group row">
                    <div class="col-sm-2">
                        <label for="ays-poll-rate-type"><?=__('Answers', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The appearance of the rating type of the poll and scaling system">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <select class="ays-select" id="ays-poll-rate-type" name="ays-poll-rate-type">
                            <option value="" selected disabled><?=__('Select view of rate', $this->plugin_name);?></option>
                            <option value='star' <?=$poll['view_type'] == 'star' ? "selected" : "";?>><?=__('Stars', $this->plugin_name);?></option>
                            <option value="emoji" <?=$poll['view_type'] == 'emoji' ? "selected" : "";?>><?=__('Emoji', $this->plugin_name);?></option>
                        </select>
                        <select class="ays-select" id="ays-poll-rate-value" name="ays-poll-rate-value">
                            <option value="<?=count($poll['answers']);?>" selected><?=count($poll['answers']);?></option>
                        </select>
                        <i id="rate-res"></i>
                    </div>
                </div>
                </div>
    <div id="tab2" class="ays-poll-tab-content">
                <p class="ays-subtitle">Poll styles</p>
                <hr>
                <div class="col">
                    <div class="row">
                        <div class="col-lg-6 col-sm-12">
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-theme'><?=__('Poll Theme', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The color theme of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                        <select name="ays_poll_theme" id="ays-poll-theme">
                                            <option value="1" <?=$poll['theme_id'] == 1 ? "selected" : "";?>>Light</option>
                                            <option value="2" <?=$poll['theme_id'] == 2 ? "selected" : "";?>>Dark</option>
                                        </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-main-color'><?=__('Main Color', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The central color of the poll (border color, the color of the rate percentage and the background color of sending button)">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="ays-text-input" id='ays-poll-main-color' name='ays_poll_main_color'
                                           value="<?=(isset($options['main_color'])) ? $options['main_color'] : '#0C6291';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-text-color'><?=__('Poll Text Color', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The color of the text in the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="ays-text-input" id='ays-poll-text-color'
                                           name='ays_poll_text_color'
                                           value="<?=(isset($options['text_color'])) ? $options['text_color'] : '#0C6291';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-icon-color'><?=__('Icons Color', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The icons color in voting and rating types">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="ays-text-input" id='ays-poll-icon-color'
                                           name='ays_poll_icon_color'
                                           value="<?=(isset($options['icon_color'])) ? $options['icon_color'] : '#0C6291';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-bg-color'><?=__('Background Color', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Background color of the poll">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="ays-text-input" id='ays-poll-bg-color'
                                           name='ays_poll_bg_color'
                                           value="<?=(isset($options['bg_color'])) ? $options['bg_color'] : '#FBFEF9';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-icon-size'><?=__('Icon size (px)', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The size of the icons in rating and voting types of the poll (it should be 10 and more)">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" class="ays-text-input ays-text-input-short" id='ays-poll-icon-size'
                                           name='ays_poll_icon_size'
                                           value="<?=(isset($options['icon_size'])) ? $options['icon_size'] : '24';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-width'><?=__('Poll width (px)', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The width of the poll (in case of 0 it will be 100%)">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                    <input type="number" step="50" min="0" class="ays-text-input ays-text-input-short"
                                           id='ays-poll-width'
                                           name='ays_poll_width'
                                           value="<?=(isset($options['width'])) ? $options['width'] : '0';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays-poll-btn-text"><?=__("Vote button text", $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The text of the vote button">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label><br>
                                </div>
                                <div class="col-sm-8">
                                    <input type="text" class="ays-text-input ays-text-input-short"
                                           id="ays-poll-btn-text"
                                           name="ays_poll_btn_text"
                                           value="<?=(isset($options['btn_text']) && '' != $options['btn_text']) ? $options['btn_text'] : 'Vote';?>"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for='ays-poll-border-style'><?=__('Border style', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="The style of the border">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                                </div>
                                <div class="col-sm-8">
                                        <select name="ays_poll_border_style" id="ays-poll-border-style">
                                            <option value="solid" <?=$options['border_style'] == "solid" ? 'selected' : '';?>>Solid</option>
                                            <option value="dashed" <?=$options['border_style'] == "dashed" ? 'selected' : '';?>>Dashed</option>
                                            <option value="dotted" <?=$options['border_style'] == "dotted" ? 'selected' : '';?>>Dotted</option>
                                            <option value="double" <?=$options['border_style'] == "double" ? 'selected' : '';?>>Double</option>
                                            <option value="groove" <?=$options['border_style'] == "groove" ? 'selected' : '';?>>Groove</option>
                                            <option value="ridge" <?=$options['border_style'] == "ridge" ? 'selected' : '';?>>Ridge</option>
                                            <option value="inset" <?=$options['border_style'] == "inset" ? 'selected' : '';?>>Inset</option>
                                            <option value="outset" <?=$options['border_style'] == "outset" ? 'selected' : '';?>>Outset</option>
                                            <option value="none" <?=$options['border_style'] == "none" ? 'selected' : '';?>>None</option>
                                        </select>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="ays_custom_css">Custom CSS</label><a class="ays_help" data-toggle="tooltip" data-placement="top" title="You can add your css">
                                      <i class="fas fa-info-circle"></i>
                                  </a><br>
                                </div>
                                <div class="col-sm-8">
                                <textarea class="ays-textarea" id="ays_custom_css" name="ays_custom_css" cols="30"
                                          rows="10"><?=(isset($options['custom_css']) && '' != $options['custom_css']) ? $options['custom_css'] : '';?></textarea>
                                </div>
                            </div>

                        </div>
</div>
</div>
</div>
<div id="tab3" class="ays-poll-tab-content">
            <p class="ays-subtitle">Poll settings</p>
                <hr>
                <div class="form-group row">
                    <div class="col-sm-2">
                        <label for='ays-poll-title'><?=__('Show Title', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Show or hide the name of the poll ">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                    </div>
                    <div class="col-sm-10">
                        <input type="checkbox" name="show_title" id="show-title" value="show"  <?=$poll['show_title'] ? 'checked' : '';?>>
                    </div>
                </div>
                <hr>
</div>
            <?php
wp_nonce_field('poll_action', 'poll_action');
$other_attributes = array('id' => 'ays-button');
submit_button(__('Save Poll', $this->plugin_name), 'primary', 'ays_submit', false, $other_attributes);
if (null != $id) {
    submit_button(__('Apply Poll', $this->plugin_name), '', 'ays_apply', false, $other_attributes);
}
?>
        </form>
    </div>
</div>
