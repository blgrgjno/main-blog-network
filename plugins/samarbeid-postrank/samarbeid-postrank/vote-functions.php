<?php

/**
 * Output the voting JS dependencies on the page footer
 */
function bvt_sfa_voting_js() {

    $src = WP_PLUGIN_URL . "/" .
            str_replace(basename(__FILE__), "", plugin_basename(__FILE__)) .
            "resources/voting.js?v=1";

    $src = htmlspecialchars($src);

    ?>

    <script type="text/javascript">
    //<![CDATA[
        var bvt_sfa_ajax_url = location.protocol+"//"+location.host+"/"+"vote";
    //]]>
    </script>

    <script type="text/javascript" src="<?php echo $src; ?>"></script>

    <?php
}


/**
 * Callback handler for AJAX voting requests
 */
function bvt_sfa_voting_ajax_callback() {

    if (!isset($_POST["bvt_sfa_post_id"]) || !isset($_POST["bvt_sfa_vote_type"])) {

        echo json_encode(array("result" => "error", "debug" => $_POST));
        die();
    }
    elseif (!isset($_COOKIE["bvt_sfa_nonce"])) {

        bvt_sfa_voting_ajax_new_nonce();
    }
    elseif (!$nonce_id = bvt_sfa_voting_get_nonce_id($_COOKIE["bvt_sfa_nonce"])) {

        bvt_sfa_voting_ajax_new_nonce();
    }
    elseif (bvt_sfa_voting_has_voted($nonce_id, $_POST["bvt_sfa_post_id"])) {

        bvt_sfa_voting_ajax_prevent_vote();
    }
    else {

        bvt_sfa_voting_ajax_register_vote(
            $nonce_id,
            $_POST["bvt_sfa_post_id"],
            $_POST["bvt_sfa_vote_type"]);
    }
}


/**
 * AJAX response - provide a new nonce for voting
 */
function bvt_sfa_voting_ajax_new_nonce() {

    global $wpdb;

    $new_nonce = md5(microtime() . mt_rand());

    $sql = $wpdb->prepare("
        INSERT INTO " . BVT_SFA_DB_NONCE_TABLE . "
        (
            nonce_value,
            nonce_expires
        )
        VALUES
        (
            %s,
            DATE_ADD(NOW(), INTERVAL 1 MONTH)
        )",
        $new_nonce
    );

    $wpdb->query($sql);

    echo json_encode(array("result" => "new_nonce", "data" => $new_nonce));
    die();
}


/**
 * AJAX response - nonce already used for voting on this item
 */
function bvt_sfa_voting_ajax_prevent_vote() {

    echo json_encode(array("result" => "already_voted"));
    die();
}


/**
 * AJAX response - accept vote
 * @param $nonce_id int The ID (not the value) of the nonce used for voting
 * @param $post_id int ID of the post being voted on
 * @param $vote_type string One of "up" or "down"
 */
function bvt_sfa_voting_ajax_register_vote($nonce_id, $post_id, $vote_type) {

    global $wpdb;

    if (($vote_type !== "up") && ($vote_type !== "down")) {

        echo json_encode(array("result" => "error", "debug" => $vote_type));
        die();
    }

    $sql = $wpdb->prepare("
        INSERT INTO " . BVT_SFA_DB_NONCE_VOTE_TABLE . "
        (
            nvote_nonce_id,
            nvote_post_id
        )
        VALUES
        (
            %d,
            %d
        )",
        $nonce_id,
        $post_id
    );

    if (!$wpdb->query($sql)) {

        echo json_encode(array("result" => "error", "debug" => "log_vote"));
        die();
    }

    $field = ($vote_type === "up") ? "post_votes_agree" : "post_votes_disagree";

    $sql = $wpdb->prepare("
        UPDATE
            " . BVT_SFA_DB_FEED_DATA_TABLE . "
        SET
            $field = $field + 1
        WHERE
            post_id = %d
        LIMIT 1",
        $post_id
    );

    if ($wpdb->query($sql) === FALSE) {

        echo json_encode(array("result" => "error", "debug" => "count_vote"));
        die();
    }

    echo json_encode(array("result" => "ok"));
    die();
}


/**
 * Get the ID of a nonce
 * @param $nonce_value string The nonce value
 * @return mixed The nonce ID (int) or FALSE if not found
 */
function bvt_sfa_voting_get_nonce_id($nonce_value) {

    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT
            nonce_id
        FROM
            " . BVT_SFA_DB_NONCE_TABLE . "
        WHERE
            nonce_value = %s",
        $nonce_value
    );

    if (!$nonce_id = $wpdb->get_var($sql)) {

        return FALSE;
    }

    return $nonce_id;
}


/**
 * Checks if a nonce has already been used to vote on a given item
 * @param $nonce_id int The ID (not the value) of the nonce
 * @param $post_id int The ID of the post
 * @return bool TRUE if already voted, FALSE if not
 */
function bvt_sfa_voting_has_voted($nonce_id, $post_id) {

    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT
            nvote_post_id
        FROM
            " . BVT_SFA_DB_NONCE_VOTE_TABLE . "
        WHERE
            nvote_nonce_id = %d AND
            nvote_post_id = %d",
        $nonce_id,
        $post_id
    );

    return !!($wpdb->get_row($sql));
}

?>