<?php
/**
 * @author     Martin Spuetz <martin@infogurke.de>
 * @copyright  2009
 * @package    gurken-subscribe-to-comments
 *
 * Based on subscribe to comments 2.1.2 from http://txfx.net/code/wordpress/subscribe-to-comments/
 */

/* $Id: index.php 2315 2008-06-25 13:05:11Z martin $ */

class sg_subscribe_settings {
    function options_page_contents() {
        global $sg_subscribe;
        sg_subscribe_start();
        if ( isset($_POST['sg_subscribe_settings_submit']) ) {
            check_admin_referer('subscribe-to-comments-update_options');
            $update_settings = stripslashes_deep($_POST['sg_subscribe_settings']);

            // checkbox fix
            if (!isset($update_settings["double_opt_in_enable"])) {
                $update_settings["double_opt_in_enable"] = 0;
            }

            $sg_subscribe->update_settings($update_settings);
        }


        echo '<h2>'.__('Subscribe to Comments Options','subscribe-to-comments').'</h2>';
        echo '<ul>';

        echo '<li><label for="name">' . __('"From" name for notifications:', 'subscribe-to-comments') . ' <input type="text" size="40" id="name" name="sg_subscribe_settings[name]" value="' . sg_subscribe_settings::form_setting('name') . '" /></label></li>';
        echo '<li><label for="email">' . __('"From" e-mail addresss for notifications:', 'subscribe-to-comments') . ' <input type="text" size="40" id="email" name="sg_subscribe_settings[email]" value="' . sg_subscribe_settings::form_setting('email') . '" /></label></li>';
        echo '<li><label for="clear_both"><input type="checkbox" id="clear_both" name="sg_subscribe_settings[clear_both]" value="clear_both"' . sg_subscribe_settings::checkflag('clear_both') . ' /> ' . __('Do a CSS "clear" on the subscription checkbox/message (uncheck this if the checkbox/message appears in a strange location in your theme)', 'subscribe-to-comments') . '</label></li>';
        echo '<li><label for="manager_css_url">' . __('Custom CSS url:', 'subscribe-to-comments') . ' <input type="text" size="40" id="name" name="sg_subscribe_settings[manager_css_url]" value="' . sg_subscribe_settings::form_setting('manager_css_url') . '" /> (optional)</label></li>';
        echo '</ul>';

        echo '<fieldset><legend>' . __('Comment Form Text', 'subscribe-to-comments') . '</legend>';

        echo '<p>' . __('Customize the messages shown to different people.  Use <code>[manager_link]</code> to insert the URI to the Subscription Manager.', 'subscribe-to-comments') . '</p>';

        echo '<ul>';

        echo '<li><label for="not_subscribed_text">' . __('Not subscribed', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="not_subscribed_text" name="sg_subscribe_settings[not_subscribed_text]">' . sg_subscribe_settings::textarea_setting('not_subscribed_text') . '</textarea></li>';

        echo '<li><label for="subscribed_text">' . __('Subscribed', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="subscribed_text" name="sg_subscribe_settings[subscribed_text]">' . sg_subscribe_settings::textarea_setting('subscribed_text') . '</textarea></li>';

        echo '<li><label for="author_text">' . __('Entry Author', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="author_text" name="sg_subscribe_settings[author_text]">' . sg_subscribe_settings::textarea_setting('author_text') . '</textarea></li>';

        echo '</ul></fieldset>';

        echo '<fieldset>';

        echo '<ul>';

        // enable double opt-in checkbox
        echo '<li><label for="double_opt_in_enable">'
           . __('Enable Double Opt-In:', 'subscribe-to-comments')
           . ' <input type="checkbox" id="double_opt_in_enable" name="sg_subscribe_settings[double_opt_in_enable]" value="1" ';

        if (sg_subscribe_settings::form_setting('double_opt_in_enable')) {
            echo 'checked="checked" ';
        }

        echo '" /></label></li>';

        echo '<li><label for="double_opt_in_subject">' . __('Double Opt-In Subject:', 'subscribe-to-comments') . ' <input type="text" size="40" id="name" name="sg_subscribe_settings[double_opt_in_subject]" value="' . sg_subscribe_settings::form_setting('double_opt_in_subject') . '" /></label></li>';

        echo '<li><label for="double_opt_in">' . __('Double Opt-In', 'subscribe-to-comments') . '</label><br /><textarea style="width: 98%; font-size: 12px;" rows="10" cols="60" id="double_opt_in" name="sg_subscribe_settings[double_opt_in]">' . sg_subscribe_settings::textarea_setting('double_opt_in') . '</textarea></li>';

        echo '</ul></fieldset>';

        echo '<fieldset>';
        echo '<legend><input type="checkbox" id="use_custom_style" name="sg_subscribe_settings[use_custom_style]" value="use_custom_style"' . sg_subscribe_settings::checkflag('use_custom_style') . ' /> <label for="use_custom_style">' . __('Use custom style for Subscription Manager', 'subscribe-to-comments') . '</label></legend>';

        echo '<p>' . __('These settings only matter if you are using a custom style.  <code>[theme_path]</code> will be replaced with the path to your current theme.', 'subscribe-to-comments') . '</p>';

        echo '<ul>';
        echo '<li><label for="sg_sub_header">' . __('Path to header:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_header" name="sg_subscribe_settings[header]" value="' . sg_subscribe_settings::form_setting('header') . '" /></label></li>';
        echo '<li><label for="sg_sub_sidebar">' . __('Path to sidebar:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_sidebar" name="sg_subscribe_settings[sidebar]" value="' . sg_subscribe_settings::form_setting('sidebar') . '" /></label></li>';
        echo '<li><label for="sg_sub_footer">' . __('Path to footer:', 'subscribe-to-comments') . ' <input type="text" size="40" id="sg_sub_footer" name="sg_subscribe_settings[footer]" value="' . sg_subscribe_settings::form_setting('footer') . '" /></label></li>';


        echo '<li><label for="before_manager">' . __('HTML for before the subscription manager:', 'subscribe-to-comments') . ' </label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="before_manager" name="sg_subscribe_settings[before_manager]">' . sg_subscribe_settings::textarea_setting('before_manager') . '</textarea></li>';
        echo '<li><label for="after_manager">' . __('HTML for after the subscription manager:', 'subscribe-to-comments') . ' </label><br /><textarea style="width: 98%; font-size: 12px;" rows="2" cols="60" id="after_manager" name="sg_subscribe_settings[after_manager]">' . sg_subscribe_settings::textarea_setting('after_manager') . '</textarea></li>';
        echo '</ul>';
        echo '</fieldset>';
    }

    function checkflag($optname) {
        $options = get_settings('sg_subscribe_settings');
        if ( $options[$optname] != $optname )
            return;
        return ' checked="checked"';
    }

    function form_setting($optname) {
        $options = get_settings('sg_subscribe_settings');
        return attribute_escape($options[$optname]);
    }

    function textarea_setting($optname) {
        $options = get_settings('sg_subscribe_settings');
        return wp_specialchars($options[$optname]);
    }

    function options_page() {
        /** Display "saved" notification on post **/
        if ( isset($_POST['sg_subscribe_settings_submit']) )
            echo '<div class="updated"><p><strong>' . __('Options saved.', 'subscribe-to-comments') . '</strong></p></div>';

        echo '<form method="post"><div class="wrap">';

        sg_subscribe_settings::options_page_contents();

      echo '<p class="submit"><input type="submit" name="sg_subscribe_settings_submit" value="';
      _e('Update Options &raquo;', 'subscribe-to-comments');
      echo '" /></p></div>';

        if ( function_exists('wp_nonce_field') )
            wp_nonce_field('subscribe-to-comments-update_options');

        echo '</form>';
    }

}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

