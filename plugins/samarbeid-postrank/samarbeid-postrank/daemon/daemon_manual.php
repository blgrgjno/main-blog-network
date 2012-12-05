<?php

require_once("nm_daemon.class.php");
require_once("nm_http.php");
require_once("Zend/Feed.php");
require_once("Zend/Feed/Atom.php");

// NOTE: Required PEAR packages are included with the daemon files, under
// the "PEAR/" subdirectory. These will be used in place of any local
// installation of PEAR.

set_include_path("PEAR" . PATH_SEPARATOR . get_include_path());

require_once("Services/Trackback.php");  // PEAR::Services_Trackback
require_once("Services/Pingback.php");  // PEAR::Services_Pingback


/**
 * Configuration file for the daemon
 */
define("BVT_SFA_DAEMON_INIFILE", "daemon.ini");

/**
 * String provided in the RSS feed when the author is unknown
 */
define("BVT_SFA_DAEMON_AUTHOR_UNKNOWN", "(author unknown)");

/**
 * Regular expression to extract the domain name from a URL; retrieve it with $1
 */
define("BVT_SFA_DAEMON_RE_DOMAIN", '/^.*?:\/\/(.*?)(\/.*|$)/');

/**
 * Author name to use for anonymous user input
 */
define("BVT_SFA_DAEMON_AUTHOR_ANONYMOUS", "Anonym");

/**
 * PostRank Data API URL (including the app key): where to fetch the data stats
 * for each feed item.
 */
define("BVT_SFA_DAEMON_POSTRANK_METRICS_API_URL",
    "http://api.postrank.com/v2/entry/metrics?appkey=EMc84VtM8ZkCKto1Qdr3");

/**
 * Daemon for updating Samarbeid For Arbeid feeds
 */
class samarbeidPostRankDaemon {

    protected $_settings;

    protected $_db;

    /**
     * Establishes database connection
     */
    protected function _connectDb() {

        $this->_db = new mysqli(
            $this->_settings["db_host"],
            $this->_settings["db_user"],
            $this->_settings["db_password"],
            $this->_settings["db_name"]
        );

        // Confirm connection

        if (mysqli_connect_errno()) {

            $this->_log_error(sprintf(
                "Database connection failed: (%d) %s",
                mysqli_connect_errno(),
                mysqli_connect_error()
            ));

            return FALSE;
        }

        if (!$this->_db->set_charset("utf8")) {

            $this->_log_error("Could not set DB connection charset to UTF8");
            return FALSE;
        }

        return TRUE;
    }


    /**
     * Retrieves categories (and category feed metadata) which have expired
     */
    protected function _get_expired_categories() {

        $sql = "
            SELECT
                category_id,
                feed_url,
                postrank_url,
                last_update,
                cache_time
            FROM
                " . $this->_db->escape_string($this->_settings["db_table_feeds"]) . "
            WHERE
                DATE_ADD(last_update, INTERVAL cache_time SECOND) < NOW()";

        if (!$result = $this->_db->query($sql)) {

            $this->_log_error(sprintf(
                "Database query (%s) failed: (%d) %s",
                $sql,
                $this->_db->errno,
                $this->_db->error
            ));

            return;
        }

        $expired_categories = array();

        while ($row = $result->fetch_assoc()) {

            $expired_categories[] = $row;
        }

        return $expired_categories;
    }

    /**
     * Fetches an Atom feed
     * @param $feed_url The URL to fetch
     * @see http://framework.zend.com/manual/en/zend.feed.consuming-atom.html
     * @return An iterable Zend_Feed_Atom object
     */
    protected function _get_feed($feed_url) {

        $http_client = Zend_Feed::getHttpClient();

        $http_client->setConfig(
            array("timeout" => $this->_settings["timeout_feed"])
        );

        try {
            $feed = new Zend_Feed_Atom($feed_url);
        }
        catch (Exception $e) {

            $this->_log_error(sprintf(
                "Error fetching feed: %s (%s)",
                $feed_url,
                $e->getMessage()
            ));

            return FALSE;
        }

        return $feed;
    }

    /**
     * Fetches PostRank scores from a preset PostRank URL
     * Sample JSON data from PostRank: http://api.postrank.com/v2/feed/03af7c719faa758917b4e1838409fbc4?appkey=EMc84VtM8ZkCKto1Qdr3&num=30
     * @param $postrank_url The URL to fetch
     * @return An array of "naked" objects, each one containing data about a feed item
     */
    protected function _get_postrank_data($postrank_url) {

        $req_context = stream_context_create(array(
            "http" => array("timeout" => $this->_settings["timeout_postrank"]))
        );

        $postrank = @file_get_contents($postrank_url, 0, $req_context);

        if ($postrank === FALSE) {

            $this->_log_error(sprintf(
                "Error fetching PostRank data: %s",
                $postrank_url
            ));

            return FALSE;
        }

        $decoded_postrank = json_decode($postrank);

        if (is_null($decoded_postrank)) {

            $this->_log_error(sprintf(
                "PostRank data is not JSON: %s",
                $postrank_url
            ));

            return FALSE;
        }

        return $decoded_postrank;
    }

    /**
     * Fetches PostRank metrics data for all items in a feed
     * Metrics API: http://apidocs.postrank.com/Metrics-API
     * @param $feed An array of feed items, as returned by $this->_get_feed()
     * @return An array of [url] => [JSON of metric data]
     */
    protected function _get_postrank_metrics($feed) {

        $md5_urls = array();

        foreach ($feed as $entry) {

            $url = $entry->link("alternate");
            $md5_urls[md5($url)] = $url;
        }

        $req_context = stream_context_create(array(
            "http" => array(
                "method" => "POST",
                "timeout" => $this->_settings["timeout_postrank"],
                "content" => "url[]=" . implode('&url[]=', array_keys($md5_urls))
            ))
        );

        $postrank_metrics =
            @file_get_contents(BVT_SFA_DAEMON_POSTRANK_METRICS_API_URL, 0, $req_context);

        if ($postrank_metrics === FALSE) {

            $this->_log_error(sprintf("Error fetching PostRank Metrics"));
            return FALSE;
        }

        $decoded_postrank_metrics = json_decode($postrank_metrics, TRUE);

        if (is_null($decoded_postrank_metrics)) {

            $this->_log_error(sprintf("PostRank Metrics data is not JSON"));
            return FALSE;
        }

        $metrics_data = array();

        foreach ($decoded_postrank_metrics as $md5 => $data) {

            $metrics_data[$md5_urls[$md5]] = json_encode($data);
        }

        return $metrics_data;
    }

    /**
     * Fetches live data from feed source and PostRank
     */
    protected function _fetch_data($feed_url, $postrank_url) {

        if (!$feed_url || !$postrank_url) {

            return FALSE;
        }

        // Fetch feed

        if (!$feed = $this->_get_feed($feed_url)) {

            return FALSE;
        }

        // Fetch PostRank data

        if (!$postrank_data = $this->_get_postrank_data($postrank_url)) {

            return FALSE;
        }

        // Fetch individual post metrics via PostRank API

        if (!$postrank_metrics = $this->_get_postrank_metrics($feed)) {

            return FALSE;
        }

        // Merge the feed and PostRank data

        $entries = array();

        foreach ($feed as $entry) {

            $post_postrank = 10;  // Default PostRank value of 1
            $post_link = $entry->link("alternate");

            foreach ($postrank_data->items as $postrank_item) {

                if ($postrank_item->original_link == $post_link) {
                    $post_postrank = round($postrank_item->postrank * 10);
                    break;
                }
            }

            $post_selected_date =
                $entry->getDOM()->getAttribute('gr:crawl-timestamp-msec');

            // Post selection date is provided in miliseconds

            $post_selected_date = (int)($post_selected_date / 1000);

            $entries[] = array(
                "post_title" => $entry->title(),
                "post_link" => $post_link,
                "post_selected_date"=> date("Y-m-d H:i:s", $post_selected_date),
                "post_publish_date" => $entry->published(),
                "post_author" => $entry->author(),
                "post_postrank" => $post_postrank,
                "post_content" => $entry->summary() ? $entry->summary() : $entry->content(),
                "post_postrank_metrics" => $postrank_metrics[$post_link]
            );
        }

        return $entries;
    }

    /**
     * Stores feed data in the cache
     */
    protected function _update_feed_cache($category_id, $feed) {

        $entries_to_keep = array();
        $linkbacks_to_generate = array();

        $table_name = $this->_db->escape_string(
            $this->_settings["db_table_feed_data"]
        );

        $stmt = $this->_db->prepare("
            INSERT INTO $table_name (
                category_id,
                post_link,
                post_title,
                post_publish_date,
                post_selected_date,
                post_author,
                post_postrank,
                post_postrank_metrics,
                post_content,
                post_publish_status
            )
            VALUES (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                1
            )
            ON DUPLICATE KEY UPDATE
                post_selected_date = ?,
                post_postrank = ?,
                post_postrank_metrics = ?"
        );

        $stmt->bind_param(
            "isssssisssis",
            $category_id,
            $entry_post_link,
            $entry_post_title,
            $entry_post_publish_date,
            $entry_post_selected_date,
            $entry_post_author,
            $entry_post_postrank,
            $entry_post_postrank_metrics,
            $entry_post_content,
            $entry_post_selected_date,
            $entry_post_postrank,
            $entry_post_postrank_metrics
        );

        foreach ($feed as $entry) {

            $entries_to_keep[] = $this->_db->escape_string($entry["post_link"]);

            $entry_post_link             = $entry["post_link"];
            $entry_post_title            = $entry["post_title"];
            $entry_post_publish_date     = $entry["post_publish_date"];
            $entry_post_selected_date    = $entry["post_selected_date"];
            $entry_post_postrank         = $entry["post_postrank"];
            $entry_post_content          = $entry["post_content"];
            $entry_post_postrank_metrics = $entry["post_postrank_metrics"];

            // Replace "author unknown" with feed domain name

            if ($entry["post_author"] == BVT_SFA_DAEMON_AUTHOR_UNKNOWN) {

                $entry_post_author = preg_replace(
                    BVT_SFA_DAEMON_RE_DOMAIN, '$1', $entry["post_link"]
                );

                $entry_post_author =
                    $entry_post_author ? $entry_post_author : "";
            }
            else {

                $entry_post_author = $entry["post_author"];
            }

            $stmt->execute();

            if ($stmt->affected_rows === 1) {

                // With ON DUPLICATE KEY UPDATE, the affected-rows value per
                // row is 1 if the row is inserted as a new row and 2 if an
                // existing row is updated.
                // See http://dev.mysql.com/doc/refman/5.1/en/insert-on-duplicate.html

                $entry["post_id"] = $stmt->insert_id;
                $entry["category_id"] = $category_id;

                $linkbacks_to_generate[] = $entry;
            }
        }

        $stmt->close();

        // Remove items which are no longer in the feed
        // HACK: items with "md5:*" links are actually user inputs and must NOT
        // be removed from the table

        $sql = "
            DELETE FROM $table_name
            WHERE
                category_id = '" . $this->_db->escape_string($category_id) . "' AND
                post_manually_created <> 1 AND
                post_link NOT IN (\"" . implode('", "', $entries_to_keep) . "\") AND
                post_link NOT LIKE 'md5:%'";

        $this->_db->query($sql);

        // Update cache timestamp

        $sql = "
            UPDATE " . $this->_db->escape_string($this->_settings["db_table_feeds"]) . " SET
                last_update = '" . $this->_db->escape_string(date("Y-m-d H:i:s")) . "'
            WHERE
                category_id = '" . $this->_db->escape_string($category_id) . "'";

        $this->_db->query($sql);

        // Send linkbacks

        $this->_generate_linkbacks($linkbacks_to_generate);
    }


    /**
     * Generates and sends trackbacks and pingbacks to a list of feed entries
     */
    protected function _generate_linkbacks($items) {

        // First try to send trackbacks

        foreach ($items as $idx => $item) {

            $trackback = new Services_Trackback();

            $trackback->setOptions(array(
                "fetchlines" => $this->_settings["trackback_lines"],
                "timeout" => $this->_settings["trackback_timeout"]
            ));

            $trackback->set("title", $this->_settings["trackback_title"]);
            $trackback->set("excerpt", $this->_settings["trackback_excerpt"]);
            $trackback->set("blog_name", $this->_settings["trackback_blog_name"]);
            $trackback->set("url", $item["post_link"]);

            if (PEAR::isError($trackback->autodiscover())) {

                continue;  // Ignore; site does not support trackbacks
            }

            $trackback->set("url", sprintf(
                $this->_settings["linkback_url"],
                $item["category_id"],
                $item["post_id"]
            ));

            if (PEAR::isError($res = $trackback->send())) {

                $this->_log_error(sprintf(
                    "Could not send trackback for '%s': %s",
                    $item["post_link"],
                    $res->getMessage()
                ));

                continue;
            }

            unset($items[$idx]);  // Trackback succeeded; remove from to-do list
        }

        // Then try pingbacks for those that didn't have trackbacks

        foreach ($items as $idx => $item) {

            $pingback = new Services_Pingback();

            $pingback->setOptions(array(
                "fetchsize" => $this->_settings["pingback_size"],
                "timeout" => $this->_settings["pingback_timeout"]
            ));

            $pingback->set("targetURI", $item["post_link"]);
            $pingback->set("sourceURI", sprintf(
                $this->_settings["linkback_url"],
                $item["category_id"],
                $item["post_id"]
            ));

            if (PEAR::isError($pingback->autodiscover($item["post_link"]))) {

                continue;  // Ignore; site does not support pingbacks
            }

            if (PEAR::isError($res = $pingback->send())) {

                $this->_log_error(sprintf(
                    "Could not send pingback for '%s': %s",
                    $item["post_link"],
                    $res->getMessage()
                ));

                continue;
            }

            unset($items[$idx]);  // Pingback succeeded; remove from to-do list
        }
    }


    /**
     * Fetches new user input/feedback and inserts it into the feed cache
     * table. This is a bit of a kludge since the DB schema was
     * not designed to handle this situation...
     */
    protected function _update_user_input($table_name) {

        $input_table_name = $table_name;

        $feed_data_table_name = $this->_db->escape_string(
            $this->_settings["db_table_feed_data"]
        );

        // Isolate the rows we are working with

        $sql = "UPDATE $input_table_name
                SET    `status` = 'processing'
                WHERE  `status` = 'new'";

        if (!$this->_db->query($sql)) {

            $this->_log_error(sprintf(
                "Could not update user input status (Query: %s) - (%d) %s",
                $sql,
                $this->_db->errno,
                $this->_db->error
            ));

            return FALSE;
        }

        // Select the workset

        $sql = "SELECT `alias`, `subject`, `date`, `category_id`, `input`
                FROM   $input_table_name
                WHERE  `status` = 'processing'";

        if (!$result = $this->_db->query($sql)) {

            $this->_log_error(sprintf(
                "Could not fetch user input data (Query: %s) - (%d) %s",
                $sql,
                $this->_db->errno,
                $this->_db->error
            ));

            return FALSE;
        }

        $new_inputs = array();

        while ($row = $result->fetch_assoc()) {

            $new_inputs[] = $row;
        }

        // Place the new input in the feed table

        $stmt = $this->_db->prepare("
            INSERT IGNORE INTO $feed_data_table_name (
                category_id,
                post_link,
                post_title,
                post_publish_date,
                post_selected_date,
                post_author,
                post_content,
                post_publish_status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, 0)
        ");

        $stmt->bind_param(
            "issssss",
            $input_category_id,
            $input_post_link,
            $input_post_title,
            $input_post_publish_date,
            $input_post_selected_date,
            $input_post_author,
            $input_post_content
        );

        foreach ($new_inputs as $input) {

            $input_category_id        = $input["category_id"];
            $input_post_title         = $input["subject"];
            $input_post_publish_date  = $input["date"];
            $input_post_selected_date = $input["date"];
            $input_post_content       = $input["input"];

            // If there is no alias defined, use "anonymous" as the author name

            $input_post_author = $input["alias"] ?
                $input["alias"] :
                BVT_SFA_DAEMON_AUTHOR_ANONYMOUS;

            // HACK: The DB table uses the post_link field as part of the
            // primary key. We need to fake a unique link for each user input
            // for this to work. The front-end must then ignore all links that
            // start with "md5:"

            $input_post_link = "md5:" . md5($input["date"] . $input["subject"]);

            if (!$stmt->execute()) {

                $this->_log_error(sprintf(
                    "Could not store new user input: (%d) %s - %s",
                    $this->_db->errno,
                    $this->_db->error,
                    print_r($input, true)
                ));

                continue;  // Continue despite error with this entry
            }
        }

        // Mark workset rows as done

        $sql = "UPDATE $input_table_name
                SET    `status` = 'finished'
                WHERE  `status` = 'processing'";

        if (!$this->_db->query($sql)) {

            $this->_log_error(sprintf(
                "Could not finalise user input status (Query: %s) - (%d) %s",
                $sql,
                $this->_db->errno,
                $this->_db->error
            ));

            return FALSE;
        }

        return TRUE;
    }


    /**
     * Clears expired nonces (used for voting) from the database
     */
    protected function _cleanup_nonces() {

        $nonce_table_name = $this->_db->escape_string(
            $this->_settings["db_table_nonce"]
        );

        $nonce_vote_table_name = $this->_db->escape_string(
            $this->_settings["db_table_nonce_vote"]
        );

        $sql = "
            DELETE LOW_PRIORITY
                $nonce_table_name, $nonce_vote_table_name
            FROM
                $nonce_table_name LEFT JOIN $nonce_vote_table_name ON
                (nonce_id = nvote_nonce_id)
            WHERE
                nonce_expires < NOW()";

        if (!$this->_db->query($sql)) {

            $this->_log_error(sprintf(
                "Could not clear expired nonces (Query: %s) - (%d) %s",
                $sql,
                $this->_db->errno,
                $this->_db->error
            ));
        }
    }


    /**
     * Output error to STDERR
     */
    protected function _log_error($message) {

        fwrite(STDERR, date("c") . ": $message\n");
    }


    /**
     * Constructor; set up daemon
     */
    public function __construct($unique_name) {

        global $argv;

        // Validate INI file

        if (!file_exists(BVT_SFA_DAEMON_INIFILE)) {

            echo "Settings file (" . BVT_SFA_DAEMON_INIFILE . ") not found.\n";
            exit(1);
        }

        if (!is_readable(BVT_SFA_DAEMON_INIFILE)) {

            echo "Settings file (" . BVT_SFA_DAEMON_INIFILE . ") not readable.\n";
            exit(1);
        }

        if (!$this->_settings = parse_ini_file(BVT_SFA_DAEMON_INIFILE)) {

            echo "Error parsing " . BVT_SFA_DAEMON_INIFILE . ".\n";
            exit(1);
        }

        // Validate DB connection on startup

        if ((@$argv[1] == "start") || (@$argv[1] == "restart")) {

            if (!$this->_connectDb()) {

                echo "Cannot connect to DB, not starting.\n";
                exit(1);
            }
        }   
    }


    /**
     * Main work loop
     */
    public function do_work() {

        if (!$this->_connectDb()) {

            // Could not get DB connection, give up on this run.
            return;
        }

        if (!$expired_categories = $this->_get_expired_categories()) {

            // Could not get any expired categories, give up on this run.
            return;
        }

        foreach ($expired_categories as $category) {

            $feed = $this->_fetch_data(
                $category["feed_url"],
                $category["postrank_url"]
            );

            if ($feed) {
                $this->_update_feed_cache($category["category_id"], $feed);
            }
        }

        $this->_update_user_input($this->_db->escape_string($this->_settings["db_table_user_input"]));
        $this->_update_user_input($this->_db->escape_string($this->_settings["db_table_user_example"]));
        $this->_cleanup_nonces();
    }
}

// Start the ball rolling...

$daemon = new samarbeidPostRankDaemon("samarbeidPostRankDaemon");
$daemon->do_work()

?>
