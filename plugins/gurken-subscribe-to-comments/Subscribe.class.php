<?php
/**
 * @author     Martin Spuetz <martin@infogurke.de>
 * @copyright  2009
 * @package    gurken-subscribe-to-comments
 *
 * Based on subscribe to comments 2.1.2 from http://txfx.net/code/wordpress/subscribe-to-comments/
 */

/* $Id: index.php 2315 2008-06-25 13:05:11Z martin $ */

class sg_subscribe {
    var $errors;
    var $messages;
    var $post_subscriptions;
    var $email_subscriptions;
    var $subscriber_email;
    var $site_email;
    var $site_name;
    var $standalone;
    var $form_action;
    var $checkbox_shown;
    var $use_wp_style;
    var $header;
    var $sidebar;
    var $footer;
    var $clear_both;
    var $before_manager;
    var $after_manager;
    var $email;
    var $new_email;
    var $ref;
    var $key;
    var $key_type;
    var $action;
    var $not_subscribed_text;
    var $subscribed_text;
    var $author_text;
    var $salt;
    var $settings;
    var $version = '1.5';

    function sg_subscribe() {
        global $wpdb;
        $this->db_upgrade_check();

        $this->settings = get_settings('sg_subscribe_settings');

        $this->salt = $this->settings['salt'];
        $this->site_email = ( is_email($this->settings['email']) && $this->settings['email'] != 'email@example.com' ) ? $this->settings['email'] : get_bloginfo('admin_email');
        $this->site_name = ( $this->settings['name'] != 'YOUR NAME' && !empty($this->settings['name']) ) ? $this->settings['name'] : get_bloginfo('name');

        $this->not_subscribed_text = $this->settings['not_subscribed_text'];
        $this->subscribed_text = $this->settings['subscribed_text'];
        $this->author_text = $this->settings['author_text'];
        $this->clear_both = $this->settings['clear_both'];

        $this->errors = '';
        $this->post_subscriptions = array();
        $this->email_subscriptions = '';
    }


    function manager_init() {
        $this->messages = '';
        $this->use_wp_style = ( $this->settings['use_custom_style'] == 'use_custom_style' ) ? false : true;
        if ( !$this->use_wp_style ) {
            $this->header = str_replace('[theme_path]', get_template_directory(), $this->settings['header']);
            $this->sidebar = str_replace('[theme_path]', get_template_directory(), $this->settings['sidebar']);
            $this->footer = str_replace('[theme_path]', get_template_directory(), $this->settings['footer']);
            $this->before_manager = $this->settings['before_manager'];
            $this->after_manager = $this->settings['after_manager'];
        }

        foreach ( array('email', 'key', 'ref', 'new_email') as $var )
            if ( isset($_REQUEST[$var]) && !empty($_REQUEST[$var]) )
                $this->{$var} = attribute_escape(trim(stripslashes($_REQUEST[$var])));
        if ( !$this->key )
            $this->key = 'unset';
    }


    function add_error($text='generic error', $type='manager') {
        $this->errors[$type][] = $text;
    }


    function show_errors($type='manager', $before_all='<div class="updated updated-error">', $after_all='</div>', $before_each='<p>', $after_each='</p>'){
                if ( is_array($this->errors[$type]) ) {
            echo $before_all;
            foreach ($this->errors[$type] as $error)
                echo $before_each . $error . $after_each;
            echo $after_all;
        }
        unset($this->errors);
    }


    function add_message($text) {
        $this->messages[] = $text;
    }


    function show_messages($before_all='', $after_all='', $before_each='<div class="updated"><p>', $after_each='</p></div>'){
        if ( is_array($this->messages) ) {
            echo $before_all;
            foreach ($this->messages as $message)
                echo $before_each . $message . $after_each;
            echo $after_all;
        }
        unset($this->messages);
    }


    function subscriptions_from_post($postid) {
        if ( is_array($this->post_subscriptions[$postid]) )
            return $this->post_subscriptions[$postid];
        global $wpdb;
        $postid = (int) $postid;
        $this->post_subscriptions[$postid] = $wpdb->get_col("SELECT comment_author_email FROM $wpdb->comments WHERE comment_post_ID = '$postid' AND comment_subscribe='Y' AND comment_author_email != '' AND comment_approved = '1' GROUP BY LCASE(comment_author_email)");
        $subscribed_without_comment = (array) get_post_meta($postid, '_sg_subscribe-to-comments');
        $this->post_subscriptions[$postid] = array_merge((array) $this->post_subscriptions[$postid], (array) $subscribed_without_comment);
        $this->post_subscriptions[$postid] = array_unique($this->post_subscriptions[$postid]);
        return $this->post_subscriptions[$postid];
    }


    function subscriptions_from_email($email='') {

        if ( is_array($this->email_subscriptions) )
            return $this->email_subscriptions;
        if ( !is_email($email) )
            $email = $this->email;
        global $wpdb;
        $email = $wpdb->escape(strtolower($email));

        $subscriptions = $wpdb->get_results("SELECT comment_post_ID FROM $wpdb->comments WHERE LCASE(comment_author_email) = '$email' AND comment_subscribe='Y' AND comment_approved = '1' GROUP BY comment_post_ID");
        foreach ( (array) $subscriptions as $subscription )
            $this->email_subscriptions[] = $subscription->comment_post_ID;
        $subscriptions = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sg_subscribe-to-comments' AND LCASE(meta_value) = '$email' GROUP BY post_id");
        foreach ( (array) $subscriptions as $subscription)
            $this->email_subscriptions[] = $subscription->post_id;
        if ( is_array($this->email_subscriptions) ) {
            sort($this->email_subscriptions, SORT_NUMERIC);
            return $this->email_subscriptions;
        }
        return false;
    }


    function solo_subscribe ($email, $postid) {
        global $wpdb, $cache_userdata, $user_email;
        $postid = (int) $postid;
        $email = strtolower($email);
        if ( !is_email($email) ) {
            get_currentuserinfo();
            if ( is_email($user_email) )
                $email = strtolower($user_email);
            else
                $this->add_error(__('Please provide a valid e-mail address.', 'subscribe-to-comments'),'solo_subscribe');
        }

        if ( ( $email == $this->site_email && is_email($this->site_email) ) || ( $email == get_settings('admin_email') && is_email(get_settings('admin_email')) ) )
            $this->add_error(__('This e-mail address may not be subscribed', 'subscribe-to-comments'),'solo_subscribe');

        if ( is_array($this->subscriptions_from_email($email)) )
            if (in_array($postid, (array) $this->subscriptions_from_email($email))) {
                // already subscribed
                setcookie('comment_author_email_' . COOKIEHASH, $email, time() + 30000000, COOKIEPATH);
                $this->add_error(__('You appear to be already subscribed to this entry.', 'subscribe-to-comments'),'solo_subscribe');
            }
        $email = $wpdb->escape($email);
        $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$postid' AND comment_status <> 'closed' AND ( post_status = 'static' OR post_status = 'publish')  LIMIT 1");

        if ( !$post )
            $this->add_error(__('Comments are not allowed on this entry.', 'subscribe-to-comments'),'solo_subscribe');

        if ( empty($cache_userdata[$post->post_author]) && $post->post_author != 0) {
            $cache_userdata[$post->post_author] = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = $post->post_author");
            $cache_userdata[$cache_userdata[$post->post_author]->user_login] =& $cache_userdata[$post->post_author];
        }

        $post_author = $cache_userdata[$post->post_author];

        if ( strtolower($post_author->user_email) == ($email) )
            $this->add_error(__('You appear to be already subscribed to this entry.', 'subscribe-to-comments'),'solo_subscribe');


        if (is_array($this->errors['solo_subscribe'])) {
            return;
        }

        if ($this->settings['double_opt_in_enable']) {
            // send double opt-in
            $c = get_post_meta($postid, "_sg_subscribe-to-comments_c", false);
            if (!is_array($c)) {
                $c = array($c);
            }

            foreach ($c as $line) {
                if (strpos($line, $email . "|") !== false) {
                    return $this->add_error(__('Please check your emails and click on the confirmation link.', 'subscribe-to-comments'),'solo_subscribe');
                }
            }

            $token = $postid . time() . (rand(1, 20) * rand(1, 10));
            if (isset($_SERVER["REMOTE_ADDR"])) {
                $token .= $_SERVER["REMOTE_ADDR"];
            }

            $key = md5($token);

            if (strpos($email, "|") !== false) {
                return $this->add_error(sprintf(__('<strong>%s</strong> is not a valid e-mail address.', 'solo_subscribe'), $email));
            }

            add_post_meta($postid, '_sg_subscribe-to-comments_c', $email . "|" . $key);

            // send confirmation email
            $this->send_double_opt_in($email, $postid, true, array("extra_key" => $key));

        } else {
            add_post_meta($postid, '_sg_subscribe-to-comments', $email);
        }

        setcookie('comment_author_email_' . COOKIEHASH, $email, time() + 30000000, COOKIEPATH);
        $location = $this->manage_link($email, false, false) . '&subscribeid=' . $postid;
        header("Location: $location");
        exit();
    }

    function add_subscriber($cid) {
        global $wpdb;
        $cid = (int) $cid;
        $id = (int) $id;

        $row = $wpdb->get_row("SELECT comment_author_email, comment_post_ID, comment_author_IP from $wpdb->comments WHERE comment_ID = '$cid'");

        $email = $row->comment_author_email;
        $postid = $row->comment_post_ID;
        $ip = $row->comment_author_IP;

        $email_sql = $wpdb->escape($email);

        $previously_subscribed = ( $wpdb->get_var("SELECT comment_subscribe from $wpdb->comments WHERE comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email_sql' AND comment_subscribe = 'Y' LIMIT 1") || in_array($email, (array) get_post_meta($postid, '_sg_subscribe-to-comments')) ) ? true : false;

        // If user wants to be notified or has previously subscribed, set the flag on this current comment
        if (($_POST['subscribe'] == 'subscribe' && is_email($email)) || $previously_subscribed) {
            delete_post_meta($postid, '_sg_subscribe-to-comments', $email);

            $subscribe = true;

            $sendOptIn = true;
            if ($previously_subscribed) {
                $sendOptIn = false;
            }

            // don't send opt-in email if feature is disabled
            if (!$this->settings['double_opt_in_enable']) {
                $sendOptIn = false;
            }

            if ($sendOptIn) {
                // check if author has subscribed to a comment in the last 3 months

                $sql = "SELECT COUNT(*)
                          FROM " . $wpdb->comments . "
                         WHERE DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <= comment_date_gmt
                           AND LCASE(comment_author_email) = '" . $email_sql . "'
                           AND comment_subscribe = 'Y'";

                $result = $wpdb->get_var($sql);
                if ($result >= 1) {
                    $sendOptIn = false;
                }
            }

            if ($sendOptIn) {
                // check if we already sent a mail in the last 24 hours

                $sql = "SELECT COUNT(*)
                          FROM " . $wpdb->comments . "
                         WHERE DATE_SUB(CURDATE(), INTERVAL 1 DAY) <= comment_date_gmt
                           AND LCASE(comment_author_email) = '" . $email_sql . "'
                           AND comment_subscribe = 'C'";

                $result = $wpdb->get_var($sql);
                if ($result >= 1) {
                    $sendOptIn = false;
                    $subscribe = false;
                }
            }

            if ($sendOptIn && !$this->is_blocked($email)) {

                $this->send_double_opt_in($email, $cid, false);

                $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'C' where comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email'");
            }

            if (!$sendOptIn && $subscribe) {
                $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'Y' where comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email'");
            } else {
                // mail sent but not confirmed, set comment_subscribe = C
                $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'C' where comment_post_ID = '$postid' AND LCASE(comment_author_email) = '$email'");
            }
        }

        return $cid;
    }

    function send_double_opt_in($email, $id, $solo = false, $options = array())
    {
        if (!is_array($options)) {
            $options = array();
        }

        $key = $this->generate_key($email . 'double-opt-in');

        // link
        $link  = get_option('home') . '/?wp-subscription-manager=1&email=' . urlencode($email) . '&opt-in=';

        if ($solo) {
            $link .= "2";
        } else {
            $link .= "1";
        }

        $link = add_query_arg('id', $id, $link);
        $link = add_query_arg('key', urlencode($key), $link);

        if (!empty($options["extra_key"])) {
            $link = add_query_arg('extra_key', urlencode($options["extra_key"]), $link);
        }

        $message = isset($this->settings['double_opt_in']) ? $this->settings['double_opt_in'] : "Click to confirm:\n[link]";

        if (function_exists("str_ireplace")) {
            $message = str_ireplace(
                array("[link]", "[manager_link]"),
                array($link, $this->manage_link($email, false, false)),
                $message
            );
        } else {
            $message = str_replace(
                array("[link]", "[manager_link]"),
                array($link, $this->manage_link($email, false, false)),
                $message
            );
        }

        $subject = isset($this->settings['double_opt_in_subject']) ? $this->settings['double_opt_in_subject'] : "Confirm the subscription";

        $this->send_mail($email, $subject, $message);
    }

    function add_opt_in_subscriber($cid)
    {
        global $wpdb;

        $cid = (int) $cid;
        $row = $wpdb->get_row("SELECT comment_author_email, comment_post_ID, comment_author_IP, comment_subscribe from $wpdb->comments WHERE comment_ID = '$cid'");

        if (!is_object($row)) {
            $this->add_error(__("Error while fetching database record", 'subscribe-to-comments'));
            return;
        }

        if ($row->comment_subscribe == "Y") {
            $this->add_error(__('You appear to be already subscribed to this entry.', 'subscribe-to-comments'));
            return;
        }

        $email = $row->comment_author_email;

        $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'Y' where comment_subscribe = 'C' AND LCASE(comment_author_email) = '$email'");

        $this->add_message(__("Successfully subscribed", 'subscribe-to-comments'));
    }

    function solo_opt_in_subscriber($postid, $key)
    {
        global $wpdb;

        $postid = (int) $postid;
        $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID = '$postid' AND comment_status <> 'closed' AND ( post_status = 'static' OR post_status = 'publish')  LIMIT 1");

        if (!$post) {
            return $this->add_error(__("Post doesn't exist or comments are not allowed on this entry.", 'subscribe-to-comments'));
        }

        $c = get_post_meta($postid, "_sg_subscribe-to-comments_c", false);

        foreach ($c as $c1) {
            $e = explode("|", $c1);

            if (!isset($e[1])) {
                continue;
            }

            if ($e[1] == $key) {
                add_post_meta($postid, '_sg_subscribe-to-comments', stripslashes($e[0]));
                delete_post_meta($postid, "_sg_subscribe-to-comments_c", $c1);
                $this->add_message(__("Successfully subscribed", 'subscribe-to-comments'));
                return;
            }
        }

        $this->add_error(__("Unknown error", 'subscribe-to-comments'));
    }

    function is_subscribed_not_confirmed($email = '')
    {
        global $wpdb;

        if (!is_email($email)) {
            $email = $this->email;
        }

        $email_sql = $wpdb->escape($email);

        $sql = "SELECT COUNT(*)
                  FROM " . $wpdb->comments . "
                 WHERE DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <= comment_date_gmt
                       AND LCASE(comment_author_email) = '" . $email_sql . "'
                       AND comment_subscribe = 'C'";

        $result = $wpdb->get_var($sql);
        if ($result >= 1) {
            return true;
        }

        return false;
    }

    function is_subscribed_not_approved($email = '')
    {
        global $wpdb;

        if (!is_email($email)) {
            $email = $this->email;
        }

        $email_sql = $wpdb->escape($email);

        $sql = "SELECT COUNT(*)
                  FROM " . $wpdb->comments . "
                 WHERE DATE_SUB(CURDATE(), INTERVAL 3 MONTH) <= comment_date_gmt
                   AND LCASE(comment_author_email) = '" . $email_sql . "'
                   AND comment_subscribe = 'Y'
                   AND comment_approved = '0'";

        $result = $wpdb->get_var($sql);
        if ($result >= 1) {
            return true;
        }

        return false;
    }

    function is_blocked($email='') {
        global $wpdb;
        if ( !is_email($email) )
            $email = $this->email;
        if ( empty($email) )
            return false;
        $email = strtolower($email);
        // add the option if it doesn't exist
        add_option('do_not_mail', '');
        $blocked = (array) explode (' ', get_settings('do_not_mail'));
        if ( in_array($email, $blocked) )
            return true;
        return false;
    }


    function add_block($email='') {
        if ( !is_email($email) )
            $email = $this->email;
        global $wpdb;
        $email = strtolower($email);

        // add the option if it doesn't exist
        add_option('do_not_mail', '');

        // check to make sure this email isn't already in there
        if ( !$this->is_blocked($email) ) {
            // email hasn't already been added - so add it
            $blocked = get_settings('do_not_mail') . ' ' . $email;
            update_option('do_not_mail', $blocked);
            return true;
            }
        return false;
    }


    function remove_block($email='') {
        if ( !is_email($email) )
            $email = $this->email;
        global $wpdb;
        $email = strtolower($email);

        if ( $this->is_blocked($email) ) {
            // e-mail is in the list - so remove it
            $blocked = str_replace (' ' . $email, '', explode (' ', get_settings('do_not_mail')));
            update_option('do_not_mail', $blocked);
            return true;
            }
        return false;
    }


    function has_subscribers() {
        if ( count($this->get_unique_subscribers()) > 0 )
            return true;
        return false;
    }


    function get_unique_subscribers() {
        global $comments, $comment, $sg_subscribers;
        if ( isset($sg_subscribers) )
            return $sg_subscribers;

        $sg_subscribers = array();
        $subscriber_emails = array();

        // We run the comment loop, and put each unique subscriber into a new array
        foreach ( (array) $comments as $comment ) {
            if ( comment_subscription_status() && !in_array($comment->comment_author_email, $subscriber_emails) ) {
                $sg_subscribers[] = $comment;
                $subscriber_emails[] = $comment->comment_author_email;
            }
        }
        return $sg_subscribers;
    }


    function hidden_form_fields() { ?>
        <input type="hidden" name="ref" value="<?php echo $this->ref; ?>" />
        <input type="hidden" name="key" value="<?php echo $this->key; ?>" />
        <input type="hidden" name="email" value="<?php echo $this->email; ?>" />
    <?php
    }


    function generate_key($data='') {
        if ( '' == $data )
            return false;
        if ( !$this->settings['salt'] )
            die('fatal error: corrupted salt');
        return md5(md5($this->settings['salt'] . $data));
    }


    function validate_key() {
        if ( $this->key == $this->generate_key($this->email) )
            $this->key_type = 'normal';
        elseif ( $this->key == $this->generate_key($this->email . $this->new_email) )
            $this->key_type = 'change_email';
        elseif ( $this->key == $this->generate_key($this->email . 'blockrequest') )
            $this->key_type = 'block';
        elseif ( current_user_can('manage_options') )
            $this->key_type = 'admin';
        elseif ( $this->key == $this->generate_key($this->email . 'double-opt-in'))
            $this->key_type = 'opt_in';
        else
            return false;
        return true;
    }


    function determine_action() {
        // rather than check it a bunch of times
        $is_email = is_email($this->email);

        if ( is_email($this->new_email) && $is_email && $this->key_type == 'change_email' )
            $this->action = 'change_email';
        elseif ( isset($_POST['removesubscrips']) && $is_email )
            $this->action = 'remove_subscriptions';
        elseif ( isset($_POST['removeBlock']) && $is_email && current_user_can('manage_options') )
            $this->action = 'remove_block';
        elseif ( isset($_POST['changeemailrequest']) && $is_email && is_email($this->new_email) )
            $this->action = 'email_change_request';
        elseif ( $is_email && isset($_POST['blockemail']) )
            $this->action = 'block_request';
        elseif ( isset($_GET['subscribeid']) )
            $this->action = 'solo_subscribe';
        elseif ( $is_email && isset($_GET['blockemailconfirm']) && $this->key == $this->generate_key($this->email . 'blockrequest') )
            $this->action = 'block';
        elseif (($this->key_type == "opt_in" || $this->key_type == "admin") && isset($_REQUEST["opt-in"]) && $_REQUEST["opt-in"] == "1")
            $this->action = 'opt_in';
        elseif (($this->key_type == "opt_in" || $this->key_type == "admin") && isset($_REQUEST["opt-in"]) && $_REQUEST["opt-in"] == "2")
            $this->action = 'solo_opt_in';
        else
            $this->action = 'none';
    }


    function remove_subscriber($email, $postid) {
        global $wpdb;
        $postid = (int) $postid;
        $email = strtolower($email);
        $email_sql = $wpdb->escape($email);

        if ( delete_post_meta($postid, '_sg_subscribe-to-comments', $email) || $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'N' WHERE comment_post_ID  = '$postid' AND LCASE(comment_author_email) ='$email_sql'") )
            return true;
        else
            return false;
        }


    function remove_subscriptions ($postids) {
        global $wpdb;
        $removed = 0;
        for ($i = 0; $i < count($postids); $i++) {
            if ( $this->remove_subscriber($this->email, $postids[$i]) )
                $removed++;
        }
        return $removed;
    }


    function send_notifications($cid) {
        global $wpdb;
        $cid = (int) $cid;
        $comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID='$cid' LIMIT 1");
        $post = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE ID='$comment->comment_post_ID' LIMIT 1");

        if ( $comment->comment_approved == '1' && $comment->comment_type == '' ) {
            // Comment has been approved and isn't a trackback or a pingback, so we should send out notifications

            $message  = sprintf(__("There is a new comment on the post \"%s\"", 'subscribe-to-comments') . ". \n%s\n\n", $post->post_title, get_permalink($comment->comment_post_ID));
            $message .= sprintf(__("Author: %s\n", 'subscribe-to-comments'), $comment->comment_author);
            $message .= __("Comment:\n", 'subscribe-to-comments') . $comment->comment_content . "\n\n";
            $message .= __("See all comments on this post here:\n", 'subscribe-to-comments');
            $message .= get_permalink($comment->comment_post_ID) . "#comments\n\n";
            //add link to manage comment notifications
            $message .= __("To manage your subscriptions or to block all notifications from this site, click the link below:\n", 'subscribe-to-comments');
            $message .= get_settings('home') . '/?wp-subscription-manager=1&email=[email]&key=[key]';

            $subject = sprintf(__('New Comment On: %s', 'subscribe-to-comments'), $post->post_title);

            $subscriptions = $this->subscriptions_from_post($comment->comment_post_ID);
            foreach ( (array) $subscriptions as $email ) {
                if ( !$this->is_blocked($email) && $email != $comment->comment_author_email && is_email($email) ) {
                        $message_final = str_replace('[email]', urlencode($email), $message);
                        $message_final = str_replace('[key]', $this->generate_key($email), $message_final);
                    $this->send_mail($email, $subject, $message_final);
                }
            } // foreach subscription
        } // end if comment approved
        return $cid;
    }


    function change_email_request() {
        if ( $this->is_blocked() )
            return false;

        $subject = __('E-mail change confirmation', 'subscribe-to-comments');
        $message = sprintf(__("You are receiving this message to confirm a change of e-mail address for your subscriptions at \"%s\"\n\n", 'subscribe-to-comments'), get_bloginfo('blogname'));
        $message .= sprintf(__("To change your e-mail address from %s to %s, click this link:\n\n", 'subscribe-to-comments'), $this->email, $this->new_email);
        $message .= get_option('home') . "/?wp-subscription-manager=1&email=" . urlencode($this->email) . "&new_email=" . urlencode($this->new_email) . "&key=" . $this->generate_key($this->email . $this->new_email) . ".\n\n";
        $message .= __('If you did not request this action, please disregard this message.', 'subscribe-to-comments');
        return $this->send_mail($this->new_email, $subject, $message);
    }


    function block_email_request($email) {
        if ( $this->is_blocked($email) )
            return false;
        $subject = __('E-mail block confirmation', 'subscribe-to-comments');
        $message = sprintf(__("You are receiving this message to confirm that you no longer wish to receive e-mail comment notifications from \"%s\"\n\n", 'subscribe-to-comments'), get_bloginfo('name'));
        $message .= __("To cancel all future notifications for this address, click this link:\n\n", 'subscribe-to-comments');
        $message .= get_option('home') . "/?wp-subscription-manager=1&email=" . urlencode($email) . "&key=" . $this->generate_key($email . 'blockrequest') . "&blockemailconfirm=true" . ".\n\n";
        $message .= __("If you did not request this action, please disregard this message.", 'subscribe-to-comments');
        return $this->send_mail($email, $subject, $message);
    }


    function send_mail($to, $subject, $message) {
        $subject = '[' . get_bloginfo('name') . '] ' . $subject;

        // strip out some chars that might cause issues, and assemble vars
        $site_name = str_replace('"', "'", $this->site_name);
        $site_email = str_replace(array('<', '>'), array('', ''), $this->site_email);
        $charset = get_settings('blog_charset');

        $headers  = "From: \"{$site_name}\" <{$site_email}>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/plain; charset=\"{$charset}\"\n";
        return wp_mail($to, $subject, $message, $headers);
    }


    function change_email() {
        global $wpdb;
        $new_email = $wpdb->escape(strtolower($this->new_email));
        $email = $wpdb->escape(strtolower($this->email));
        if ( $wpdb->query("UPDATE $wpdb->comments SET comment_author_email = '$new_email' WHERE comment_author_email = '$email'") )
            $return = true;
        if ( $wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '$new_email' WHERE meta_value = '$email' AND meta_key = '_sg_subscribe-to-comments'") )
            $return = true;
        return $return;
    }


    function entry_link($postid, $uri='') {
        if ( empty($uri) )
            $uri = get_permalink($postid);
        $postid = (int) $postid;
        $title = get_the_title($postid);
        if ( empty($title) )
            $title = __('click here', 'subscribe-to-comments');
        $output = '<a href="'.$uri.'">'.$title.'</a>';
        return $output;
    }

    function sg_wp_head()
    {
        if (isset($this->settings["manager_css_url"]) && $this->settings["manager_css_url"] != "") {
            print '<link rel="stylesheet" type="text/css" href="' . $this->settings["manager_css_url"] . '" />';
        }

        return true;
    }

    function db_upgrade_check () {
        global $wpdb;

        // add the options
        add_option(
            'sg_subscribe_settings',
            array(
                'use_custom_style' => '',
                'email' => get_bloginfo('admin_email'),
                'name' => get_bloginfo('name'),
                'header' => '[theme_path]/header.php',
                'sidebar' => '',
                'footer' => '[theme_path]/footer.php',
                'before_manager' => '<div id="content" class="widecolumn subscription-manager">',
                'after_manager' => '</div>',
                'not_subscribed_text' => __('Notify me of followup comments via e-mail', 'subscribe-to-comments'),
                'subscribed_text' => __('You are subscribed to this entry.  <a href="[manager_link]">Manage your subscriptions</a>.', 'subscribe-to-comments'),
                'author_text' => __('You are the author of this entry.  <a href="[manager_link]">Manage subscriptions</a>.', 'subscribe-to-comments'),
                'manager_css_url' => '',
                'double_opt_in_subject' => __('Please confirm your subscribtion', 'subscribe-to-comments'),
                'double_opt_in' => __('Click on the following link: [link]', 'subscribe-to-comments'),
                'version' => $this->version
            )
        );

        $settings = get_option('sg_subscribe_settings');

        if (!$settings) { // work around WP 2.2/2.2.1 bug
            wp_redirect('http://' . $_SERVER['HTTP_HOST'] . add_query_arg('stcwpbug', '1'));
            exit;
        }

        $update = false;

        if (!$settings['salt']) {
            $settings['salt'] = md5(md5(uniqid(rand() . rand() . rand() . rand() . rand(), true))); // random MD5 hash
            $update = true;
        }

        if (!$settings['clear_both']) {
            $settings['clear_both'] = 'clear_both';
            $update = true;
        }

        if (!$settings['version']) {
            $settings = stripslashes_deep($settings);
            $update = true;
        }

        if (!isset($settings["double_opt_in_enable"])) {
            $settings["double_opt_in_enable"] = 1;
            $update = true;
        }

        if (!$settings['double_opt_in_subject'] || !$settings['double_opt_in']) {
            $subject = __('Please confirm your subscribtion', 'subscribe-to-comments');
            $text    = __('Click on the following link: [link]', 'subscribe-to-comments');

            $file = WP_PLUGIN_DIR . "/gurken-subscribe-to-comments/extras/default_email_text.xml";
            if (function_exists("simplexml_load_file") && file_exists($file) && is_readable($file)) {
                $sxe = @simplexml_load_file($file);
                if ($sxe) {
                    $locale = strtolower(get_locale());

                    foreach ($sxe->lang as $lang) {
                        if (strtolower($lang["code"]) == $locale) {
                            $subject = (string) $lang->subject;
                            $text    = (string) $lang->text;
                            break;
                        }
                    }
                }
            }

            $settings['double_opt_in_subject'] = $subject;
            $settings['double_opt_in'] = $text;
            $update = true;
        }

        if ( $settings['not_subscribed_text'] == '' || $settings['subscribed_text'] == '' ) { // recover from WP 2.2/2.2.1 bug
            delete_option('sg_subscribe_settings');
            wp_redirect('http://' . $_SERVER['HTTP_HOST'] . add_query_arg('stcwpbug', '2'));
            exit;
        }

        if ( $update )
            $this->update_settings($settings);
    }

    function install()
    {
        global $wpdb;

        $result = $wpdb->get_row("DESC " . $wpdb->comments . " comment_subscribe");

        if (!is_object($result)) {
            $wpdb->query("ALTER TABLE $wpdb->comments ADD COLUMN comment_subscribe enum('Y','C','N') NOT NULL default 'N'");
        } else if (strpos($result->Type, "C") === false) {
            $wpdb->query("ALTER TABLE " . $wpdb->comments . " MODIFY `comment_subscribe` enum('Y','C','N') NOT NULL default 'N'");
        }
    }

    function uninstall()
    {
        global $wpdb;

        delete_option("sg_subscribe_settings");

        $wpdb->query("ALTER TABLE " . $wpdb->comments . " DROP `comment_subscribe`");
    }

    function update_settings($settings) {
        $settings['version'] = $this->version;
        update_option('sg_subscribe_settings', $settings);
    }


    function current_viewer_subscription_status(){
        global $wpdb, $post, $user_email;

        $comment_author_email = ( isset($_COOKIE['comment_author_email_'. COOKIEHASH]) ) ? trim($_COOKIE['comment_author_email_'. COOKIEHASH]) : '';
        get_currentuserinfo();

        if ( is_email($user_email) ) {
            $email = strtolower($user_email);
            $loggedin = true;
        } elseif ( is_email($comment_author_email) ) {
            $email = strtolower($comment_author_email);
        } else {
            return false;
        }

        $post_author = get_userdata($post->post_author);
        if ( strtolower($post_author->user_email) == $email && $loggedin )
            return 'admin';

        if ( is_array($this->subscriptions_from_email($email)) )
            if ( in_array($post->ID, (array) $this->email_subscriptions) )
                return $email;
        return false;
    }

    function manage_link($email='', $html=true, $echo=true) {
        $link  = get_option('home') . '/?wp-subscription-manager=1';
        if ( $email != 'admin' ) {
            $link = add_query_arg('email', urlencode($email), $link);
            $link = add_query_arg('key', $this->generate_key($email), $link);
        }
        $link = add_query_arg('ref', rawurlencode('http://' . $_SERVER['HTTP_HOST'] . attribute_escape($_SERVER['REQUEST_URI'])), $link);
        //$link = str_replace('+', '%2B', $link);
        if ( $html )
            $link = htmlentities($link);
        if ( !$echo )
            return $link;
        echo $link;
    }

    function on_edit($cid) {
        global $wpdb;
        $comment = &get_comment($cid);
        if ( !is_email($comment->comment_author_email) && $comment->comment_subscribe == 'Y' )
            $wpdb->query("UPDATE $wpdb->comments SET comment_subscribe = 'N' WHERE comment_ID = '$comment->comment_ID' LIMIT 1");
        return $cid;
    }

    function add_admin_menu() {
        add_management_page(__('Comment Subscription Manager', 'subscribe-to-comments'), __('Subscriptions', 'subscribe-to-comments'), 8, 'stc-management', 'sg_subscribe_admin');

        add_options_page(__('Subscribe to Comments', 'subscribe-to-comments'), __('Subscribe to Comments', 'subscribe-to-comments'), 5, 'stc-options', array('sg_subscribe_settings', 'options_page'));
    }
}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
