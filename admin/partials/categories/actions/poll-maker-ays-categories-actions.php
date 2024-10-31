<?php
$action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
$heading = '';
$id = (isset($_GET['poll_category'])) ? absint(intval($_GET['poll_category'])) : null;
$category = [
    'id' => '',
    'title' => '',
    'description' => '',
];
switch ($action) {
case 'add':
    $heading = __('Add new category', $this->plugin_name);
    break;
case 'edit':
    $heading = __('Edit category', $this->plugin_name);
    $category = $this->cats_obj->get_poll_category($id);
    break;
}
if (isset($_POST['ays_submit'])) {
    $_POST['id'] = $id;
    $this->cats_obj->add_edit_poll_category($_POST);
}
if (isset($_POST['ays_apply'])) {
    $_POST["id"] = $id;
    $_POST['ays_change_type'] = 'apply';
    $this->cats_obj->add_edit_poll_category($_POST);
}
?>
<div class="wrap">
    <div class="container-fluid">
        <h1><?=$heading;?></h1>
        <hr/>
        <form class="ays-poll-category-form" id="ays-poll-category-form" method="post">
            <div class="form-group row">
                <div class="col-sm-2">
                    <label for='ays-title'><?=__('Title', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Kategoriai anvanumy">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                </div>
                <div class="col-sm-10">
                    <input class='ays-text-input' id='ays-title' name='ays_title' required type='text' value='<?=stripslashes($category['title']);?>'>
                </div>
            </div>

            <hr/>
            <div class='ays-field'>
                <label for='ays-description'><?=__('Description', $this->plugin_name);?><a class="ays_help" data-toggle="tooltip" data-placement="top" title="Kategoriai nkaragrutyuny">
                                      <i class="fas fa-info-circle"></i>
                                  </a></label>
                <?php
$content = stripslashes($category['description']);
$editor_id = 'ays-description';
$settings = array('editor_height' => '5', 'textarea_name' => 'ays_description', 'editor_class' => 'ays-textarea', 'media_buttons' => false);
wp_editor($content, $editor_id, $settings);
?>
            </div>
            <hr/>
            <?php
wp_nonce_field('poll_category_action', 'poll_category_action');
$other_attributes = array('id' => 'ays-button');
submit_button(__('Save Category', $this->plugin_name), 'primary', 'ays_submit', false, $other_attributes);
if ($id != null) {
    submit_button(__('Apply Category', $this->plugin_name), '', 'ays_apply', false, $other_attributes);
}
?>
        </form>
    </div>
</div>