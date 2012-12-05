<?php
/*
Plugin Name: Samarbeid for Arbeid PostRank
Plugin URI: http://www.bouvet.no
Description: Manages and outputs feed listings with PostRank data.
Version: 1.5.2
Author: Bouvet ASA
Author URI: http://www.bouvet.no
*/
/*  Copyright 2010  Bouvet ASA  (email : sam@bouvet.no)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


global $wpdb;

define("BVT_SFA_DB_VERSION", 24);
define("BVT_SFA_DB_FEED_TABLE", $wpdb->prefix . "samarbeid_feeds");
define("BVT_SFA_DB_FEED_DATA_TABLE", $wpdb->prefix . "samarbeid_feed_data");
define("BVT_SFA_DB_NONCE_TABLE", $wpdb->prefix . "samarbeid_nonce");
define("BVT_SFA_DB_NONCE_VOTE_TABLE", $wpdb->prefix . "samarbeid_nonce_vote");
define("BVT_SFA_TOPICS_CATEGORY_ID", 4);
define("BVT_SFA_FEED_CACHE_TIME", 300);
define("BVT_SFA_FEED_TIMEOUT", 2);
define("BVT_SFA_POSTRANK_TIMEOUT", 3);
define("BVT_SFA_PAGINATOR_BUFFER", 2);

// Register plugin hooks

register_activation_hook(__FILE__, 'bvt_sfa_install');
add_action('wp_footer', 'bvt_sfa_voting_js');
add_action('wp_ajax_bvt_sfa_vote', 'bvt_sfa_voting_ajax_callback');
add_action('wp_ajax_nopriv_bvt_sfa_vote', 'bvt_sfa_voting_ajax_callback');


// Load dependencies

$path = dirname(__FILE__) . '/samarbeid-postrank';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("samarbeid-postrank/feed-functions.php");
require_once("samarbeid-postrank/vote-functions.php");
require_once("samarbeid-postrank/admin-functions.php");


/**
 * Handler for plugin installation
 */
function bvt_sfa_install() {

    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    /*

    // This block is meant to be commented; it is here for convenience while
    // developing, but should NOT be enabled in production since it will wipe
    // all feed settings from the database!

    if ($wpdb->get_var("SHOW TABLES LIKE '" . BVT_SFA_DB_FEED_TABLE . "'")
            == BVT_SFA_DB_FEED_TABLE) {

        $wpdb->query("DROP TABLE " . BVT_SFA_DB_FEED_TABLE);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '" . BVT_SFA_DB_FEED_DATA_TABLE . "'")
            == BVT_SFA_DB_FEED_DATA_TABLE) {

        $wpdb->query("DROP TABLE " . BVT_SFA_DB_FEED_DATA_TABLE);
    }

    */

    if (get_option("BVT_SFA_DB_VERSION") < 16) {

        // dbDelta doesn't seem to cope with adding auto_increment fields

        $wpdb->query("
            ALTER TABLE " . BVT_SFA_DB_FEED_DATA_TABLE . "
                ADD COLUMN post_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                ADD KEY key_post_id (post_id)");
    }

    $sql =
        "CREATE TABLE " . BVT_SFA_DB_FEED_TABLE . " (
            category_id INT(11) NOT NULL,
            feed_url CHAR(150) NOT NULL,
            postrank_url CHAR(150) NOT NULL,
            last_update DATETIME NOT NULL,
            cache_time INT(11),
            PRIMARY KEY  category_id (category_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $sql .=
        "CREATE TABLE " . BVT_SFA_DB_FEED_DATA_TABLE . " (
            category_id INT(11) NOT NULL,
            post_link CHAR(255) NOT NULL,
            post_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_title CHAR(140) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            post_selected_date DATETIME NOT NULL,
            post_publish_date DATETIME NOT NULL,
            post_author CHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
            post_postrank TINYINT(3) UNSIGNED NOT NULL DEFAULT 10,
            post_postrank_metrics TEXT NOT NULL,
            post_content TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci,
            post_publish_status TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            post_votes_agree INT(11) UNSIGNED NOT NULL DEFAULT 0,
            post_votes_disagree INT(11) UNSIGNED NOT NULL DEFAULT 0,
            post_manually_created TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
            PRIMARY KEY  key_primary (category_id,post_link),
            KEY key_post_id (post_id),
            KEY key_category_id_date (category_id,post_selected_date),
            KEY key_category_postrank (category_id,post_postrank),
            KEY key_date (post_selected_date),
            KEY key_postrank (post_postrank),
            FULLTEXT KEY key_fulltext (post_title,post_author,post_content)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $sql .=
        "CREATE TABLE " . BVT_SFA_DB_NONCE_TABLE . " (
            nonce_id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            nonce_value CHAR(32) NOT NULL,
            nonce_expires DATETIME NOT NULL,
            PRIMARY KEY  key_primary (nonce_value),
            KEY key_nonce_id (nonce_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    $sql .=
        "CREATE TABLE " . BVT_SFA_DB_NONCE_VOTE_TABLE . " (
            nvote_nonce_id INT(11) UNSIGNED NOT NULL,
            nvote_post_id INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY  key_primary (nvote_nonce_id,nvote_post_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

    dbDelta($sql);

    update_option("BVT_SFA_DB_VERSION", BVT_SFA_DB_VERSION);
    add_option("BVT_SFA_TOPICS_CATEGORY_ID", BVT_SFA_TOPICS_CATEGORY_ID);
    add_option("BVT_SFA_FEED_INTRO_HEADING", "Heading");
    add_option("BVT_SFA_FEED_INTRO_BODY", "Lorem ipsum... [category]");
    add_option("BVT_SFA_FEED_INTRO_BODY_HOMEPAGE", "Lorem ipsum... (home page)");
    add_option("BVT_SFA_METRICS_ITEMS", "twitter,delicious,bookmarks,clicks");
}

?>