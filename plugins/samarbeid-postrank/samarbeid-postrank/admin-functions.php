<?php

add_action('admin_menu', 'bvt_sfa_feed_admin_menu');

/**
 * Register feed admin menu and screen
 */
function bvt_sfa_feed_admin_menu () {

    add_menu_page(
        'PostRank feeds settings',
        'PostRank feeds',
        'manage_categories',
        'bvt_sfa_feed_admin',
        'bvt_sfa_feed_admin_screen',
        'http://www.postrank.com/favicon.ico'
    );
}


/**
 * Display and process postback of feed admin screen
 */
function bvt_sfa_feed_admin_screen() {

    // TODO: Break this function down into smaller bits?

    global $wpdb;

    $submit_flag = "bvt_sfa_feed_admin_form";

    $categories = bvt_sfa_feed_admin_get_categories();

    if (isset($_POST[$submit_flag]) && $_POST[$submit_flag] == 'Y') {

        $errors = false;

        // Postback; save feed settings...

        foreach ($categories as $category) {

            if ($_POST['bvt_sfa_catfeed'][$category->cat_ID]) {

                // ...only for categories actually submitted

                $new_values = $_POST['bvt_sfa_catfeed'][$category->cat_ID];

                $sql = $wpdb->prepare("
                    INSERT INTO " . BVT_SFA_DB_FEED_TABLE . " (
                        category_id,
                        feed_url,
                        postrank_url,
                        cache_time
                    )
                    VALUES (
                        %d,
                        %s,
                        %s,
                        %d
                    )
                    ON DUPLICATE KEY UPDATE
                        feed_url = %s,
                        postrank_url = %s,
                        cache_time = %d",
                    $category->cat_ID,
                    $new_values['feed_url'],
                    $new_values['postrank_url'],
                    $new_values['cache_time'],
                    $new_values['feed_url'],
                    $new_values['postrank_url'],
                    $new_values['cache_time']
                );
            }

            if ($wpdb->query($sql) === FALSE) {

                echo '
                    <div class="error">
                      <p>
                        <strong>Error saving settings!</strong>
                        It was not possible to save feed settings for feed ID
                        ' . htmlspecialchars($category->cat_ID) . '. Error: ';
                $wpdb->print_error();
                echo '</p></div>';

                $errors = true;
            }
        }

        // Save static content

        update_option(
            "BVT_SFA_FEED_INTRO_HEADING",
            $_POST["bvt_sfa_feed_intro_heading"]
        );

        update_option(
            "BVT_SFA_FEED_INTRO_BODY",
            $_POST["bvt_sfa_feed_intro_body"]
        );

        update_option(
            "BVT_SFA_FEED_INTRO_BODY_HOMEPAGE",
            $_POST["bvt_sfa_feed_intro_body_homepage"]
        );

        // Save advanced settings

        update_option(
            "BVT_SFA_TOPICS_CATEGORY_ID",
            $_POST["bvt_sfa_topics_category_id"]
        );

        update_option(
            "BVT_SFA_METRICS_ITEMS",
            str_replace(" ", "", strtolower($_POST["bvt_sfa_metrics_items"]))
            // remove spaces, set to lowercase
        );

        if (!$errors) {
            echo '<div class="updated"><p><strong>Success.</strong>
                  New settings saved.</p></div>';
        }

        // Re-get categories, since the parent category might have changed

        $categories = bvt_sfa_feed_admin_get_categories();
    }

    // Read config from DB

    foreach ($categories as $category) {

        $sql = $wpdb->prepare("
            SELECT
                category_id,
                feed_url,
                postrank_url,
                last_update,
                cache_time
            FROM
                " . BVT_SFA_DB_FEED_TABLE . "
            WHERE
                category_id = %d",
            $category->cat_ID
        );

        // Merge config with list of categories

        if ($result = $wpdb->get_row($sql)) {

            $category->feed_url     = $result->feed_url;
            $category->postrank_url = $result->postrank_url;
            $category->cache_time   = $result->cache_time;
            $category->last_update  =
                ($result->last_update == "0000-00-00 00:00:00") ?
                "Never" :
                $result->last_update;
        }
        else {

            $category->feed_url     = "";
            $category->postrank_url = "";
            $category->cache_time   = BVT_SFA_FEED_CACHE_TIME;
            $category->last_update  = "Never";
        }
    }

    // Output form

    ?>

    <div class="wrap">
    <form method="post" action="">

      <input type="hidden" name="<?php echo $submit_flag; ?>" value="Y" />

      <h2>PostRank Feed Seetings</h2>

      <h3>Subject feeds</h3>

      <table class="widefat">
        <thead>
          <tr>
            <th>Subject title</th>
            <th>Feed URL</th>
            <th>PostRank URL</th>
            <th>Cache time (seconds)</th>
            <th>Last updated</th>
          </tr>
        </thead>

        <tbody>

        <?php foreach ($categories as $category) { ?>

          <tr>

            <td><?php echo htmlspecialchars($category->name) ?></td>
            <td>
              <input
                  type="text"
                  style="width: 100%;"
                  value="<?php echo htmlspecialchars($category->feed_url) ?>"
                  name="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][feed_url]"
                  id="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][feed_url]" />
            </td>
            <td>
              <input
                  type="text"
                  style="width: 100%;"
                  value="<?php echo htmlspecialchars($category->postrank_url) ?>"
                  name="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][postrank_url]"
                  id="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][postrank_url]" />
            </td>
            <td>
              <input
                  type="text"
                  value="<?php echo htmlspecialchars($category->cache_time) ?>"
                  name="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][cache_time]"
                  id="bvt_sfa_catfeed[<?php echo $category->cat_ID ?>][cache_time]" />
            </td>
            <td>
               <?php echo htmlspecialchars($category->last_update) ?>
            </td>

          </tr>

        <?php } ?>

        </tbody>
      </table>


      <h3>Static content</h3>

      <p>Please note that HTML is not allowed in these fields.</p>

      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="bvt_sfa_feed_intro_heading">Intro heading</label>
            </th>
            <td>
              <input
                  style="width: 47.5%;"
                  type="text"
                  value="<?php echo htmlspecialchars(get_option("BVT_SFA_FEED_INTRO_HEADING")); ?>"
                  name="bvt_sfa_feed_intro_heading"
                  id="bvt_sfa_feed_intro_heading" />
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="bvt_sfa_feed_intro_body_homepage">
                Intro text (home page)
              </label>
            </th>
            <td>
              <textarea
                  style="width: 95%; height: 7em;"
                  name="bvt_sfa_feed_intro_body_homepage"
                  id="bvt_sfa_feed_intro_body_homepage"><?php
                echo htmlspecialchars(get_option("BVT_SFA_FEED_INTRO_BODY_HOMEPAGE"));
              ?></textarea>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="bvt_sfa_feed_intro_body">
                Intro text (category pages)
              </label>
              <p><em>Note</em>: The text "<strong>[category]</strong>" will be
                 replaced with the current category name.</p>
            </th>
            <td>
              <textarea
                  style="width: 95%; height: 7em;"
                  name="bvt_sfa_feed_intro_body"
                  id="bvt_sfa_feed_intro_body"><?php
                echo htmlspecialchars(get_option("BVT_SFA_FEED_INTRO_BODY"));
              ?></textarea>
            </td>
          </tr>
        </tbody>
      </table>

      <h3>Advanced settings</h3>

      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">
              <label for="bvt_sfa_topics_category_id">
                Parent category for subjects
              </label>
            </th>
            <td>
              <?php
                  wp_dropdown_categories(array(
                      "exclude" => 1,  /* 1 is "Uncategorized" */
                      "hide_empty" => false,
                      "hierarchical" => 1,
                      "name" => "bvt_sfa_topics_category_id",
                      "selected" => get_option('bvt_sfa_topics_category_id')
                  ));
              ?>
            </td>
          </tr>
          <tr>
            <th scope="row">
              <label for="bvt_sfa_metrics_items">
                PostRank metrics to display
              </label>
            </th>
            <td>
              <input
                  style="width: 47.5%;"
                  type="text"
                  value="<?php echo htmlspecialchars(get_option("BVT_SFA_METRICS_ITEMS")); ?>"
                  name="bvt_sfa_metrics_items"
                  id="bvt_sfa_metrics_items" />
              <p>Comma-separated list of data IDs. Available IDs include:
                delicious, brightkite, reddit_comments, reddit_votes, views,
                identica, google, diigo, clicks, blip, digg, bookmarks,
                twitter, jaiku, tumblr, ff_likes, ff_comments, comments. These are
                <a href="http://www.postrank.com/postrank#sources">described by
                PostRank</a>.</p>
            </td>
          </tr>
        </tbody>
      </table>

      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>

    </form>
    </div>

    <?php
}


/**
 * Returns list of categories for feed admin
 */
function bvt_sfa_feed_admin_get_categories() {

    $categories = get_categories(
        array(
            "child_of"   => get_option("BVT_SFA_TOPICS_CATEGORY_ID"),
            "hide_empty" => false
        )
    );

    return $categories;
}

?>