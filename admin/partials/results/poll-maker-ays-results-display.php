<?php
/**
 * Created by PhpStorm.
 * User: biggie18
 * Date: 6/25/18
 * Time: 1:32 PM
 */
;?>
<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php
echo esc_html(get_admin_page_title());
?>
    </h1>

    <!-- <a href="https://ays-pro.com/index.php/wordpress/quiz-maker/" target="_blank"><button class="disabled-button" style="float: right;    margin-right: 5px;" title="This property aviable only in pro version" >Export</button></a> -->
    <!-- <div class="nav-tab-wrapper">
        <a href="#tab1" class="nav-tab nav-tab-active">Results</a>
        <a href="#tab2" class="nav-tab">Statistics</a>
    </div> -->
    <div id="tab1" class="ays-quiz-tab-content ays-quiz-tab-content-active">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="get" id="filter-div" class="alignleft actions bulkactions">
                            <label for="bulk-action-selector-top" class="screen-reader-text">Select Filter Type</label>
                            <input type="hidden" name="page" value="poll-maker-ays-results">
                            <select name="orderbypoll" id="bulk-action-selector-top">
                                <option value="0" selected disabled>Results by Poll</option>
                                <?php
foreach ($this->results_obj->get_polls() as $poll) {?>
                                    <option value="<?=$poll['id'];?>" <?=(isset($_REQUEST['orderbypoll']) && $_REQUEST['orderbypoll'] == $poll['id']) ? 'selected' : '';?>><?=$poll['title'];?></option>
                                <?php }
?>
                            </select>
                            <input type="submit" id="doaction" class="button action" value="Filter" style="width: 3.7rem;">
                        </form>
                        <form method="post">
                            <?php
$this->results_obj->prepare_items();
$this->results_obj->display();
?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>

    <!-- <div id="tab2" class="ays-quiz-tab-content" >
        <a href="https://ays-pro.com/index.php/wordpress/quiz-maker/" target="_blank" title="This property aviable only in pro version">
            <img src="<?php // echo plugins_url() . '/quiz-maker/admin/images/chart_screen.png'; ;;;;;;;;;;;;;;;?>" alt="Statistics" style="opacity: 0.5" >
        </a>
    </div>
    <div id="ays-results-modal" class="ays-modal">
        <div class="ays-modal-content">
            <div class="ays-modal-header">
                <span class="ays-close" id="ays-close-results">&times;</span>
                <h2><?php // echo __("Results for", $this->plugin_name); ;;;;;;;;;;;;;;;?></h2>
            </div>
            <div class="ays-modal-body" id="ays-results-body">
                <iframe style="width: 100%; height: 450px;" src="https://www.youtube.com/embed/2N6HQyd6xS8" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <p class="ays-notification-for-pro">
                    Liked what we do? Checkout the <a href="http://ays-pro.com/index.php/wordpress/quiz-maker" target="_blank">PRO version</a> and track the results of your passers-by for each quiz.
                </p>
            </div>

        </div>
    </div> -->
</div>
