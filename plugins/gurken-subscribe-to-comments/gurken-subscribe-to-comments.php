<?php
/*
Plugin Name: Gurken Subscribe to Comments
Version: 1.7
Plugin URI: http://www.infogurke.de/gurken-subscribe-to-comments/
Description: Subscribe to Comments with Double-Opt-In
Author: Martin Spuetz
Author URI: http://www.infogurke.de
*/

/* This is the code that is inserted into the comment form */
function show_subscription_checkbox ($id = '0')
{
    global $sg_subscribe;
    sg_subscribe_start();

    if ($sg_subscribe->checkbox_shown) {
        return $id;
    }

    if (!($email = $sg_subscribe->current_viewer_subscription_status())) :
        $checked_status = ( !empty($_COOKIE['subscribe_checkbox_'.COOKIEHASH]) && 'checked' == $_COOKIE['subscribe_checkbox_'.COOKIEHASH] ) ? true : false;
    ?>

<?php /* ------------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who are NOT subscribed */ ?>
<?php /* ------------------------------------------------------------------- */ ?>

    <p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
    <input type="checkbox" name="subscribe" id="subscribe" value="subscribe" style="width: auto;" <?php if ( $checked_status ) echo 'checked="checked" '; ?>/>
    <label for="subscribe"><?php echo $sg_subscribe->not_subscribed_text; ?></label>
    </p>

<?php /* ------------------------------------------------------------------- */ ?>

<?php elseif ( $email == 'admin' && current_user_can('manage_options') ) : ?>

<?php /* ------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for the author of the post */ ?>
<?php /* ------------------------------------------------------------- */ ?>

    <p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
    <?php echo str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->author_text); ?>
    </p>

<?php else : ?>

<?php /* --------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who ARE subscribed */ ?>
<?php /* --------------------------------------------------------------- */ ?>

    <p <?php if ($sg_subscribe->clear_both) echo 'style="clear: both;" '; ?>class="subscribe-to-comments">
    <?php echo str_replace('[manager_link]', $sg_subscribe->manage_link($email, true, false), $sg_subscribe->subscribed_text); ?>
    </p>

<?php /* --------------------------------------------------------------- */ ?>

<?php endif;

    $sg_subscribe->checkbox_shown = true;
    return $id;
}


/* -------------------------------------------------------------------- */
/* This function outputs a "subscribe without commenting" form.         */
/* Place this somewhere within "the loop", but NOT within another form  */
/* This is NOT inserted automaticallly... you must place it yourself    */
/* -------------------------------------------------------------------- */
function show_manual_subscription_form()
{
    global $id, $sg_subscribe, $user_email;
    sg_subscribe_start();
    $sg_subscribe->show_errors('solo_subscribe', '<div class="solo-subscribe-errors">', '</div>', __('<strong>Error: </strong>', 'subscribe-to-comments'), '<br />');

    if (!$sg_subscribe->current_viewer_subscription_status()) :
    get_currentuserinfo(); ?>

<?php /* ------------------------------------------------------------------- */ ?>
<?php /* This is the text that is displayed for users who are NOT subscribed */ ?>
<?php /* ------------------------------------------------------------------- */ ?>

    <form action="" method="post">
    <p class="solo-subscribe-to-comments">
    <input type="hidden" name="solo-comment-subscribe" value="solo-comment-subscribe" />
    <input type="hidden" name="postid" value="<?php echo (int) $id; ?>" />
    <input type="hidden" name="ref" value="<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . attribute_escape($_SERVER['REQUEST_URI'])); ?>" />
    <?php _e('Subscribe without commenting', 'subscribe-to-comments'); ?>
    <br />
    <label for="solo-subscribe-email"><?php _e('E-Mail:', 'subscribe-to-comments'); ?>
    <input type="text" name="email" id="solo-subscribe-email" size="22" value="<?php echo $user_email; ?>" /></label>
    <input type="submit" name="submit" value="<?php _e('Subscribe', 'subscribe-to-comments'); ?>" />
    </p>
    </form>

<?php /* ------------------------------------------------------------------- */ ?>

<?php endif;
}

/* -------------------------
Use this function on your comments display - to show whether a user is subscribed to comments on the post or not.
Note: this must be used within the comments loop!  It will not work properly outside of it.
------------------------- */
function comment_subscription_status()
{
    global $comment;

    if ($comment->comment_subscribe == 'Y') {
        return true;
    } else {
        return false;
    }
}

// Do not change anything below this line
include(dirname(__FILE__) . "/Subscribe.class.php");
include(dirname(__FILE__) . "/Settings.class.php");

function stc_checkbox_state($data)
{
    if (isset($_POST['subscribe'])) {
        setcookie('subscribe_checkbox_'. COOKIEHASH, 'checked', time() + 30000000, COOKIEPATH);
    } else {
        setcookie('subscribe_checkbox_'. COOKIEHASH, 'unchecked', time() + 30000000, COOKIEPATH);
    }

    return $data;
}

function sg_subscribe_start() {
    global $sg_subscribe;

    if (!$sg_subscribe) {

        $path = PLUGINDIR . "/gurken-subscribe-to-comments/extras";

        load_plugin_textdomain('subscribe-to-comments', $path);
        $sg_subscribe = new sg_subscribe();
    }
}

register_activation_hook(__FILE__, array("sg_subscribe", "install"));

if (function_exists("register_uninstall_hook")) {
    register_uninstall_hook(__FILE__, array("sg_subscribe", "uninstall"));
}

// This will be overridden if the user manually places the function
// in the comments form before the comment_form do_action() call
add_action('comment_form', 'show_subscription_checkbox');

// priority is very low (50) because we want to let anti-spam plugins have their way first.
add_action('comment_post', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->send_notifications($a);'), 50);
add_action('comment_post', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->add_subscriber($a);'));

add_action('wp_set_comment_status', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); return $sg_subscribe->send_notifications($a);'));
add_action('admin_menu', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); $sg_subscribe->add_admin_menu();'));
add_action('admin_head', create_function('$a', 'global $sg_subscribe; sg_subscribe_start(); $sg_subscribe->sg_wp_head();'));
add_action('edit_comment', array('sg_subscribe', 'on_edit'));

// save users' checkbox preference
add_filter('preprocess_comment', 'stc_checkbox_state', 1);


function sg_subscribe_hook_init()
{
    global $sg_subscribe;

    //  detect "subscribe without commenting" attempts
    if (isset($_POST["solo-comment-subscribe"], $_POST["postid"], $_POST["email"])) {
        if ($_POST["solo-comment-subscribe"] == "solo-comment-subscribe" && is_numeric($_POST["postid"])) {
            sg_subscribe_start();
            $sg_subscribe->solo_subscribe(stripslashes($_POST["email"]), (int) $_POST["postid"]);
        }
    }
}
add_action("init", "sg_subscribe_hook_init");

if (isset($_REQUEST['wp-subscription-manager'])) {
    add_action('template_redirect', 'sg_subscribe_admin_standalone');
}

function sg_subscribe_admin_standalone()
{
    sg_subscribe_admin(true);
}

function sg_subscribe_admin($standalone = false)
{
    global $wpdb, $sg_subscribe, $wp_version;

    sg_subscribe_start();

    if ($standalone) {
        $sg_subscribe->form_action = get_option('home') . '/?wp-subscription-manager=1';
        $sg_subscribe->standalone = true;
        ob_start(create_function('$a', 'return str_replace("<title>", "<title> " . __("Subscription Manager", "subscribe-to-comments") . " &raquo; ", $a);'));
    } else {

        if (version_compare($wp_version, '2.7.0') === 1) {
            $sg_subscribe->form_action = 'tools.php?page=stc-management';
        } else {
            $sg_subscribe->form_action = 'edit.php?page=stc-management';
        }

        $sg_subscribe->standalone = false;
    }

    $sg_subscribe->manager_init();

    get_currentuserinfo();

    if (!$sg_subscribe->validate_key()) {
        die ( __('You may not access this page without a valid key.', 'subscribe-to-comments'));
    }

    $sg_subscribe->determine_action();

    switch ($sg_subscribe->action) :

        case "change_email" :
            if ( $sg_subscribe->change_email() ) {
                $sg_subscribe->add_message(sprintf(__('All notifications that were formerly sent to <strong>%1$s</strong> will now be sent to <strong>%2$s</strong>!', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->new_email));
                // change info to the new email
                $sg_subscribe->email = $sg_subscribe->new_email;
                unset($sg_subscribe->new_email);
                $sg_subscribe->key = $sg_subscribe->generate_key($sg_subscribe->email);
                $sg_subscribe->validate_key();
            }
            break;

        case "remove_subscriptions" :
            $postsremoved = $sg_subscribe->remove_subscriptions($_POST['subscrips']);
            if ( $postsremoved > 0 )
                $sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> %2$s removed successfully.', 'subscribe-to-comments'), $postsremoved, ($postsremoved != 1) ? __('subscriptions', 'subscribe-to-comments') : __('subscription', 'subscribe-to-comments')));
            break;

        case "remove_block" :
            if ( $sg_subscribe->remove_block($sg_subscribe->email) )
                $sg_subscribe->add_message(sprintf(__('The block on <strong>%s</strong> has been successfully removed.', 'subscribe-to-comments'), $sg_subscribe->email));
            else
                $sg_subscribe->add_error(sprintf(__('<strong>%s</strong> isn\'t blocked!', 'subscribe-to-comments'), $sg_subscribe->email), 'manager');
            break;

        case "email_change_request" :
            if ( $sg_subscribe->is_blocked($sg_subscribe->email) )
                $sg_subscribe->add_error(sprintf(__('<strong>%s</strong> has been blocked from receiving notifications.  You will have to have the administrator remove the block before you will be able to change your notification address.', 'subscribe-to-comments'), $sg_subscribe->email));
            else
                if ($sg_subscribe->change_email_request($sg_subscribe->email, $sg_subscribe->new_email))
                    $sg_subscribe->add_message(sprintf(__('Your change of e-mail request was successfully received.  Please check your new account (<strong>%s</strong>) in order to confirm the change.', 'subscribe-to-comments'), $sg_subscribe->new_email));
            break;

        case "block_request" :
            if ($sg_subscribe->block_email_request($sg_subscribe->email ))
                $sg_subscribe->add_message(sprintf(__('Your request to block <strong>%s</strong> from receiving any further notifications has been received.  In order for you to complete the block, please check your e-mail and click on the link in the message that has been sent to you.', 'subscribe-to-comments'), $sg_subscribe->email));
            break;

        case "solo_subscribe" :
            $sg_subscribe->add_message(__('Please check your emails and click on the confirmation link.', 'subscribe-to-comments'));
            //$sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> has been successfully subscribed to %2$s', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->entry_link($_GET['subscribeid'])));
            break;

        case "block" :
            if ($sg_subscribe->add_block($sg_subscribe->email))
                $sg_subscribe->add_message(sprintf(__('<strong>%1$s</strong> has been added to the "do not mail" list. You will no longer receive any notifications from this site. If this was done in error, please contact the <a href="mailto:%2$s">site administrator</a> to remove this block.', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->site_email));
            else
                $sg_subscribe->add_error(sprintf(__('<strong>%s</strong> has already been blocked!', 'subscribe-to-comments'), $sg_subscribe->email), 'manager');
            $sg_subscribe->key = $sg_subscribe->generate_key($sg_subscribe->email);
            $sg_subscribe->validate_key();
            break;

        case "opt_in":
            $sg_subscribe->add_opt_in_subscriber($_REQUEST["id"]);
            break;

        case "solo_opt_in":
            $extra_key = empty($_GET["extra_key"]) ? NULL : $_GET["extra_key"];
            $sg_subscribe->solo_opt_in_subscriber($_REQUEST["id"], $extra_key);
            break;

    endswitch;



    if ( $sg_subscribe->standalone ) {
        if ( !$sg_subscribe->use_wp_style && !empty($sg_subscribe->header) ) {
        @include($sg_subscribe->header);
        echo $sg_subscribe->before_manager;
    } else { ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html>
    <head>
    <title><?php printf(__('%s Comment Subscription Manager', 'subscribe-to-comments'), bloginfo('name')); ?></title>

    <style type="text/css" media="screen">
        body {
            width: 800px;
            font-family: Verdana,Arial,"Bitstream Vera Sans",sans-serif;
            font-size: 11px;
            background-color: #fff;
            color: #000;
        }
        .updated-error {
            background-color: #FF8080;
            border: 1px solid #F00;
            padding-left: 5px;
        }
        fieldset legend {
            font-weight: bold;
        }
    </style>

    <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />

    <?php $sg_subscribe->sg_wp_head(); ?>

    </head>
    <body>
    <?php } ?>
    <?php } ?>


    <?php $sg_subscribe->show_messages(); ?>

    <?php $sg_subscribe->show_errors(); ?>


    <div class="wrap">
    <h2><?php printf(__('%s Comment Subscription Manager', 'subscribe-to-comments'), bloginfo('name')); ?></h2>

    <?php if (!empty($sg_subscribe->ref)) : ?>
    <?php $sg_subscribe->add_message(sprintf(__('Return to the page you were viewing: %s', 'subscribe-to-comments'), $sg_subscribe->entry_link(url_to_postid($sg_subscribe->ref), $sg_subscribe->ref))); ?>
    <?php $sg_subscribe->show_messages(); ?>
    <?php endif; ?>



    <?php if ( $sg_subscribe->is_blocked() ) { ?>

        <?php if ( current_user_can('manage_options') ) : ?>

        <fieldset class="options">
            <legend><?php _e('Remove Block', 'subscribe-to-comments'); ?></legend>

            <p>
            <?php printf(__('Click the button below to remove the block on <strong>%s</strong>.  This should only be done if the user has specifically requested it.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
            </p>

            <form name="removeBlock" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
            <input type="hidden" name="removeBlock" value="removeBlock /">
    <?php $sg_subscribe->hidden_form_fields(); ?>

            <p class="submit">
            <input type="submit" name="submit" value="<?php _e('Remove Block &raquo;', 'subscribe-to-comments'); ?>" />
            </p>
            </form>
        </fieldset>

    <?php else : ?>

        <fieldset class="options">
            <legend><?php _e('Blocked', 'subscribe-to-comments'); ?></legend>

            <p>
            <?php printf(__('You have indicated that you do not wish to receive any notifications at <strong>%1$s</strong> from this site. If this is incorrect, or if you wish to have the block removed, please contact the <a href="mailto:%2$s">site administrator</a>.', 'subscribe-to-comments'), $sg_subscribe->email, $sg_subscribe->site_email); ?>
            </p>
        </fieldset>

    <?php endif; ?>


    <?php } else { ?>


    <?php $postlist = $sg_subscribe->subscriptions_from_email(); ?>

<?php

if (isset($sg_subscribe->email) && !is_array($postlist) && $sg_subscribe->email != $sg_subscribe->site_email && $sg_subscribe->email != get_bloginfo('admin_email')) {

    if (is_email($sg_subscribe->email)) {

        if ($sg_subscribe->is_subscribed_not_approved($sg_subscribe->email)) {
            $sg_subscribe->add_error(
                sprintf(
                    __('<strong>%s</strong> is subscribed but the comment has not been approved yet.', 'subscribe-to-comments'),
                    $sg_subscribe->email
                )
            );
        } else {
            $sg_subscribe->add_error(sprintf(__('<strong>%s</strong> is not subscribed to any posts on this site.', 'subscribe-to-comments'), $sg_subscribe->email));
        }

        if ($sg_subscribe->is_subscribed_not_confirmed()) {
            $sg_subscribe->add_error(__("You have a pending subscription, please check your emails and click on the confirmation link.", 'subscribe-to-comments'));
        }

    } else {
        $sg_subscribe->add_error(sprintf(__('<strong>%s</strong> is not a valid e-mail address.', 'subscribe-to-comments'), $sg_subscribe->email));
    }
}
?>

    <?php $sg_subscribe->show_errors(); ?>




    <?php if ( current_user_can('manage_options') ) { ?>

        <fieldset class="options">
            <?php if ( $_REQUEST['email'] ) : ?>
                <p><a href="<?php echo $sg_subscribe->form_action; ?>"><?php _e('&laquo; Back'); ?></a></p>
            <?php endif; ?>

            <legend><?php _e('Find Subscriptions', 'subscribe-to-comments'); ?></legend>

            <p>
            <?php _e('Enter an e-mail address to view its subscriptions or undo a block.', 'subscribe-to-comments'); ?>
            </p>

            <form name="getemail" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
            <input type="hidden" name="ref" value="<?php echo $sg_subscribe->ref; ?>" />

            <p>
            <input name="email" type="text" id="email" size="40" />
            <input type="submit" value="<?php _e('Search &raquo;', 'subscribe-to-comments'); ?>" />
            </p>
            </form>
        </fieldset>

<?php if ( !$_REQUEST['email'] ) : ?>
        <fieldset class="options">
            <?php if ( !$_REQUEST['showallsubscribers'] ) : ?>
                <legend><?php _e('Top Subscriber List', 'subscribe-to-comments'); ?></legend>
            <?php else : ?>
                <legend><?php _e('Subscriber List', 'subscribe-to-comments'); ?></legend>
            <?php endif; ?>

<?php
            $stc_limit = ( !$_REQUEST['showallsubscribers'] ) ? 'LIMIT 25' : '';
            $all_ct_subscriptions = $wpdb->get_results("SELECT distinct LCASE(comment_author_email) as email, count(distinct comment_post_ID) as ccount FROM $wpdb->comments WHERE comment_subscribe='Y' AND comment_approved = '1' GROUP BY email ORDER BY ccount DESC $stc_limit");
            $all_pm_subscriptions = $wpdb->get_results("SELECT distinct LCASE(meta_value) as email, count(post_id) as ccount FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' GROUP BY email ORDER BY ccount DESC $stc_limit");
            $all_subscriptions = array();

            foreach ( array('all_ct_subscriptions', 'all_pm_subscriptions') as $each ) {
                foreach ( (array) $$each as $sub ) {
                    if ( !isset($all_subscriptions[$sub->email]) )
                        $all_subscriptions[$sub->email] = (int) $sub->ccount;
                    else
                        $all_subscriptions[$sub->email] += (int) $sub->ccount;
                }
            }

if ( !$_REQUEST['showallsubscribers'] ) : ?>
    <p><a href="<?php echo attribute_escape(add_query_arg('showallsubscribers', '1', $sg_subscribe->form_action)); ?>"><?php _e('Show all subscribers', 'subscribe-to-comments'); ?></a></p>
<?php elseif ( !$_REQUEST['showccfield'] ) : ?>
    <p><a href="<?php echo add_query_arg('showccfield', '1'); ?>"><?php _e('Show list of subscribers in <code>CC:</code>-field format (for bulk e-mailing)', 'subscribe-to-comments'); ?></a></p>
<?php else : ?>
    <p><a href="<?php echo attribute_escape($sg_subscribe->form_action); ?>"><?php _e('&laquo; Back to regular view'); ?></a></p>
    <p><textarea cols="60" rows="10"><?php echo implode(', ', array_keys($all_subscriptions) ); ?></textarea></p>
<?php endif;


            if ( $all_subscriptions ) {
                if ( !$_REQUEST['showccfield'] ) {
                    echo "<ul>\n";
                    foreach ( (array) $all_subscriptions as $email => $ccount ) {
                        $enc_email = urlencode($email);
                        echo "<li>($ccount) <a href='" . attribute_escape($sg_subscribe->form_action . "&email=$enc_email") . "'>" . wp_specialchars($email) . "</a></li>\n";
                    }
                    echo "</ul>\n";
                }
?>
                <legend><?php _e('Top Subscribed Posts', 'subscribe-to-comments'); ?></legend>
                <?php
                $top_subscribed_posts1 = $wpdb->get_results("SELECT distinct comment_post_ID as post_id, count(distinct comment_author_email) as ccount FROM $wpdb->comments WHERE comment_subscribe='Y' AND comment_approved = '1' GROUP BY post_id ORDER BY ccount DESC LIMIT 25");
                $top_subscribed_posts2 = $wpdb->get_results("SELECT distinct post_id, count(distinct meta_value) as ccount FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' GROUP BY post_id ORDER BY ccount DESC LIMIT 25");
                $all_top_posts = array();

                foreach ( array('top_subscribed_posts1', 'top_subscribed_posts2') as $each ) {
                    foreach ( (array) $$each as $pid ) {
                        if ( !isset($all_top_posts[$pid->post_id]) )
                            $all_top_posts[$pid->post_id] = (int) $pid->ccount;
                        else
                            $all_top_posts[$pid->post_id] += (int) $pid->ccount;
                    }
                }
                arsort($all_top_posts);

                echo "<ul>\n";
                foreach ( $all_top_posts as $pid => $ccount ) {
                    echo "<li>($ccount) <a href='" . get_permalink($pid) . "'>" . get_the_title($pid) . "</a></li>\n";
                }
                echo "</ul>";
                ?>

    <?php } ?>

        </fieldset>

<?php endif; ?>

    <?php } ?>

    <?php if ( count($postlist) > 0 && is_array($postlist) ) { ?>


<script type="text/javascript">
<!--
function checkAll(form) {
    for ( i = 0, n = form.elements.length; i < n; i++ ) {
        if ( form.elements[i].type == "checkbox" ) {
            if ( form.elements[i].checked == true )
                form.elements[i].checked = false;
            else
                form.elements[i].checked = true;
        }
    }
}
//-->
</script>

        <fieldset class="options">
            <legend><?php _e('Subscriptions', 'subscribe-to-comments'); ?></legend>

                <p>
                <?php printf(__('<strong>%s</strong> is subscribed to the posts listed below. To unsubscribe to one or more posts, click the checkbox next to the title, then click "Remove Selected Subscription(s)" at the bottom of the list.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
                </p>

                <form name="removeSubscription" id="removeSubscription" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
                <input type="hidden" name="removesubscrips" value="removesubscrips" />
    <?php $sg_subscribe->hidden_form_fields(); ?>

                <ol>
                <?php for ($i = 0; $i < count($postlist); $i++) { ?>
                    <li><label for="subscrip-<?php echo $i; ?>"><input id="subscrip-<?php echo $i; ?>" type="checkbox" name="subscrips[]" value="<?php echo $postlist[$i]; ?>" /> <?php echo $sg_subscribe->entry_link($postlist[$i]); ?></label></li>
                <?php } ?>
                </ol>

                <p>
                <a href="javascript:;" onclick="checkAll(document.getElementById('removeSubscription')); return false; "><?php _e('Invert Checkbox Selection', 'subscribe-to-comments'); ?></a>
                </p>

                <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Remove Selected Subscription(s) &raquo;', 'subscribe-to-comments'); ?>" />
                </p>
                </form>
        </fieldset>
    </div>

    <div class="wrap">
    <h2><?php _e('Advanced Options', 'subscribe-to-comments'); ?></h2>

        <fieldset class="options">
            <legend><?php _e('Block All Notifications', 'subscribe-to-comments'); ?></legend>

                <form name="blockemail" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
                <input type="hidden" name="blockemail" value="blockemail" />
    <?php $sg_subscribe->hidden_form_fields(); ?>

                <p>
                <?php printf(__('If you would like <strong>%s</strong> to be blocked from receiving any notifications from this site, click the button below.  This should be reserved for cases where someone is signing you up for notifications without your consent.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
                </p>

                <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Block Notifications &raquo;', 'subscribe-to-comments'); ?>" />
                </p>
                </form>
        </fieldset>

        <fieldset class="options">
            <legend><?php _e('Change E-mail Address', 'subscribe-to-comments'); ?></legend>

                <form name="changeemailrequest" method="post" action="<?php echo $sg_subscribe->form_action; ?>">
                <input type="hidden" name="changeemailrequest" value="changeemailrequest" />
    <?php $sg_subscribe->hidden_form_fields(); ?>

                <p>
                <?php printf(__('If you would like to change the e-mail address for your subscriptions, enter the new address below.  You will be required to verify this request by clicking a special link sent to your new address.', 'subscribe-to-comments'), $sg_subscribe->email); ?>
                </p>

                <p>
                <?php _e('New E-mail Address:', 'subscribe-to-comments'); ?>
                <input name="new_email" type="text" id="new_email" size="40" />
                </p>

                <p class="submit">
                <input type="submit" name="submit" value="<?php _e('Change E-mail Address &raquo;', 'subscribe-to-comments'); ?>" />
                </p>
                </form>
        </fieldset>

            <?php } ?>
    <?php } //end if not in do not mail ?>
    </div>

    <?php if ( $sg_subscribe->standalone ) : ?>
    <?php if ( !$sg_subscribe->use_wp_style ) :
    echo $sg_subscribe->after_manager;

    if ( !empty($sg_subscribe->sidebar) )
        @include_once($sg_subscribe->sidebar);
    if ( !empty($sg_subscribe->footer) )
        @include_once($sg_subscribe->footer);
    ?>
    <?php else : ?>
    </body>
    </html>
    <?php endif; ?>
    <?php endif; ?>


<?php die(); // stop WP from loading ?>
<?php } ?>
