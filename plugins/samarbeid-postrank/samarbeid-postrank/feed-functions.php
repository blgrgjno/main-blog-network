<?php
/**
 * Retrieves a feed from cache
 */
 
function bvt_sfa_get_feed(
        $category_id   = FALSE,
        $order_by      = "post_publish_date",
        $order_desc    = TRUE,
        $record_offset = 0,
        $rows_per_page = 10,
        $search        = FALSE) {

    global $wpdb;

    // Set up sane defaults and sanitise things...

    $allowed_order_fields = array("post_publish_date", "post_postrank");
    $order_dir = $order_desc ? "DESC" : "ASC";

    if (!in_array($order_by, $allowed_order_fields)) {

        $order_by = "post_publish_date";
    }

    $record_offset = abs((int)$record_offset);
    $rows_per_page = abs((int)$rows_per_page);

    $record_offset = ($record_offset > 10000) ? 0 : $record_offset;
    $rows_per_page = ($rows_per_page > 1000) ? 10 : $rows_per_page;
    $rows_per_page = ($rows_per_page < 1) ? 10 : $rows_per_page;

    // Set up filters

    $filters = array();

    $filters[] = "post_publish_status = 1";

    if ($category_id !== FALSE) {

        $filters[] = "category_id = '" . $wpdb->escape($category_id) . "'";
    }

    if ($search) {

        $filters[] = "
            MATCH (post_title, post_author, post_content)
            AGAINST ('" . $wpdb->escape($search) . "')";
    }

    $filters = implode(" AND ", $filters);

    $where_clause = $filters ? "WHERE $filters" : "";

    // Run query

    $sql = "
        SELECT SQL_CALC_FOUND_ROWS
            post_id,
            post_link,
            post_title,
            post_publish_date,
            post_author,
            post_postrank_metrics,
            post_content,
            post_votes_agree,
            post_votes_disagree,
            post_manually_created
        FROM
            " . BVT_SFA_DB_FEED_DATA_TABLE . "
        $where_clause
        ORDER BY
            $order_by $order_dir
        LIMIT
            $record_offset,$rows_per_page";

    $items = $wpdb->get_results($sql);

    if ($items === FALSE) {
        return FALSE;
    }

    $total_records = $wpdb->get_var("SELECT FOUND_ROWS()");

    if ($total_records === FALSE) {
        return FALSE;
    }

    return array(
        "items" => $items,
        "pagination" => array(
            "page" => floor($record_offset / $rows_per_page),
            "record_offset" => $record_offset,
            "rows_per_page" => $rows_per_page,
            "total_records" => $total_records
        )
    );
}


/**
 * Retrieves a single post via its unique ID
 */
function bvt_sfa_get_post($post_id) {

    global $wpdb;

    $sql = $wpdb->prepare("
        SELECT
            post_id,
            post_link,
            post_title,
            post_publish_date,
            post_author,
            post_postrank_metrics,
            post_content,
            post_votes_agree,
            post_votes_disagree,
            post_manually_created
        FROM
            " . BVT_SFA_DB_FEED_DATA_TABLE . "
        WHERE
            post_id = %d",
        $post_id
    );

    return $wpdb->get_row($sql);
}


/**
 * Retrieves the feed URL for a category
 */
function bvt_sfa_get_feed_url($category_id) {

    global $wpdb;

    if (!isset($category_id)) {

        return FALSE;
    }

    $sql = $wpdb->prepare("
        SELECT
            feed_url
        FROM
            " . BVT_SFA_DB_FEED_TABLE . "
        WHERE
            category_id = %d",
        $category_id
    );

    return $wpdb->get_var($sql);
}


/**
 * Decides whether or not a page should be displayed in the paginator
 */
function bvt_sfa_showpage($page, $current_page, $total_pages) {

    $buffer = BVT_SFA_PAGINATOR_BUFFER;

    if ($page < $buffer)
        return true;
    if ($page >= $total_pages - $buffer)
        return true;
    if (($page >= $current_page - $buffer) && ($page <= $current_page + $buffer))
        return true;
    if (($page == $current_page - $buffer - 1) && ($page == $buffer))
        return true;
    if (($page == $current_page + $buffer + 1) && ($page == $total_pages - $buffer - 1))
        return true;

    return false;
}


/**
 * Outputs markup appropriate to this request
 */
function bvt_sfa_postfeed($category_id = FALSE) {

    if (isset($_GET["pr_post_id"])) {

        bvt_sfa_postfeed_output_single(bvt_sfa_get_post($_GET["pr_post_id"]));
    }
    else {

        bvt_sfa_postfeed_output_multiple($category_id);
    }
}


/**
 * Outputs markup for a full feed
 */
function bvt_sfa_postfeed_output_multiple($category_id) {

    // Get request params (and provide sane defaults)

    $order_by      = isset($_GET["pr_order_by"]) ? $_GET["pr_order_by"] : "post_publish_date";
    $order_desc    = isset($_GET["pr_order_desc"]) ? $_GET["pr_order_desc"] : "1";
    $record_offset = isset($_GET["pr_record_offset"]) ? $_GET["pr_record_offset"] : "0";
    $rows_per_page = isset($_GET["pr_rows_per_page"]) ? $_GET["pr_rows_per_page"] : "10";
    $search        = isset($_GET["pr_search"]) ? $_GET["pr_search"] : "";

    $search = stripslashes_deep($search);  // Aaaagh! http://www.satollo.net/wordpress-and-php-magic-quotes-you-want-run-me-crazy

    $order_desc_bool = $order_desc === "0" ? FALSE : TRUE;
    $reverse_order   = $order_desc_bool ? "0" : "1";

    $search_term = $search ? $search : "Søk i debatter";

    // Figure out if we are in a category page or not, or if a category
    // was specified as a parameter

    $cat_id = FALSE;

    if ($category_id && is_numeric($category_id)) {

        $cat_id = $category_id;
    }
    elseif ($GLOBALS['cat']) {

        $cat_id = $GLOBALS['cat'];
    }

    if ($cat_id) {

        $category = get_category($cat_id);
        $cat_id = $category->cat_ID;

        $intro_body = str_replace(
            "[category]",
            strtolower(strtr($category->name, "ÆØÅ", "æøå")),  // ugly...
            get_option("BVT_SFA_FEED_INTRO_BODY")
        );
    }
    else {
        $intro_body = get_option("BVT_SFA_FEED_INTRO_BODY_HOMEPAGE");
		# remove escaping done by WordPress so it's possible to use HTML
		$intro_body = str_replace("\\", "", $intro_body);
    }

    // Prepare sort links

    $sort_links = array(
        "Nyeste" => "post_publish_date",
        "Mest populære" => "post_postrank"
    );

    foreach ($sort_links as $link_text => &$link_order_by) {

        $link_url = add_query_arg(
            array(
                "pr_record_offset" => 0,
                "pr_order_by" => $link_order_by,
                "pr_order_desc" => ($order_by == $link_order_by) ? $reverse_order : "1"
            )
        );

        $link_markup = '<a href="' . htmlspecialchars($link_url) . '"';

        if ($order_by == $link_order_by) {

            $link_markup .= ' class="selected"';
        }

        $link_markup .= '>' . htmlspecialchars($link_text) . '</a>';

        $link_order_by = $link_markup;
    }

    // Fetch feed data

    $feed_data = bvt_sfa_get_feed(
        $cat_id,
        $order_by,
        $order_desc_bool,
        $record_offset,
        $rows_per_page,
        $search
    );

    // Output results

    ?>

    <div class="bvt_sfa_feedlist">
      <div id="bvt_sfa_tooltip">&nbsp;</div>
        <div id="bvt_sfa_feedback">Hei og hå!</div>
      <div class="hgroup">
        <h2>
          <?php echo htmlspecialchars(get_option("BVT_SFA_FEED_INTRO_HEADING")); ?>
        </h2>
		<p><?php echo $intro_body; #echo htmlspecialchars($intro_body); ?></p>

      </div>
      <p><a href="gi-innspill#registrer-artikkel-eller-blogginnlegg">Fortell oss om aktuelle innlegg og artikler</a></p>
       <hr/>
      <?php if ($feed_url = bvt_sfa_get_feed_url($cat_id)) { ?>

        <?php $feed_url = preg_replace('/(\?|\&)n=\d+/', '', $feed_url); ?>

        <a
            class="i16x16 iralign syndication"
            href="<?php echo htmlspecialchars($feed_url); ?>">
          Hold deg oppdatert ved å abonnere på nyhetsstrømmen
        </a>

      <?php } ?>

      <div class="toolbar">
        <ul class="navList">

          <?php foreach ($sort_links as $sort_link) { ?>

            <li><?php echo $sort_link; ?></li>

          <?php } ?>

        </ul>

        <form class="search">
          <fieldset>
            <label for="pr_search" class="accessibilityHidden">
              Søk i datastrøm
            </label>
            <input
                title="Søk i debatter"
                value="<?php echo htmlspecialchars($search_term); ?>"
                type="text"
                class="inputField toggleable"
                name="pr_search"
                id="pr_search" />
            <button title="Send inn søk">Søk</button>
          </fieldset>
        </form>
      </div>      

      <?php if ($feed_data && $feed_data["pagination"]["total_records"]) { ?>
        
        <?php if ($search_term != "Søk i debatter") { ?>
          <div class="bvt_sfa_feedlist_hits">
            <p>Du fikk <?php echo $feed_data["pagination"]["total_records"]?> treff på <strong><?php echo $search_term?></strong></p>
            <p><a href="<?php echo str_replace("pr_search=".$search_term,"",$link_url) ?>">Vis alle debattinnlegg</a></p>
          </div>
        <?php }?>
          
        <ol>

        <?php foreach ($feed_data["items"] as $item) { ?>

          <li><?php bvt_sfa_postfeed_output_single($item); ?></li>

        <?php } ?>

        </ol>

        <?php

        // Output pagination

        $total_records = $feed_data["pagination"]["total_records"];
        $rows_per_page = $feed_data["pagination"]["rows_per_page"];
        $current_page  = $feed_data["pagination"]["page"];
        $total_pages   = ceil($total_records / $rows_per_page);
        $ellipsis      = false;

        ?>

        <?php if ($total_records > $rows_per_page) { ?>

          <ol class="pager">

          <?php for ($i = 0; $i < $total_pages; ++$i) { ?>

            <?php if (bvt_sfa_showpage($i, $current_page, $total_pages)) { ?>

              <?php $ellipsis = false; ?>

              <li<?php echo ($current_page == $i) ? ' class="selected"' : ''; ?>>

                <?php if ($current_page == $i) { ?>

                  <strong><?php echo ($i + 1);?></strong>

                <?php } else { ?>

                  <?php

                  $link_page = add_query_arg(
                      array(
                          "pr_order_by" => $order_by,
                          "pr_order_desc" => $order_desc,
                          "pr_record_offset" => ($i * $rows_per_page)
                      )
                  );

                  ?>

                  <a href="<?php echo htmlspecialchars($link_page); ?>"><?php
                      echo ($i + 1);
                  ?></a>

                <?php } /* end if ($current_page == $i) */ ?>

              </li>

            <?php } elseif (!$ellipsis) { ?>

              <?php $ellipsis = true; ?>

              <li>…</li>

            <?php } /* end if (bvt_sfa_showpage($i, $current_page, $total_pages)) */ ?>

          <?php } /* end for ($i = 0; $i < $total_pages; ++$i) */ ?>

          </ol>

        <?php } /* end if ($total_records > $rows_per_page) */ ?>

      <?php } elseif ($feed_data && $feed_data["pagination"]["total_records"] == 0) { ?>

        <p>Ingen resultater funnet…</p>

      <?php } else /* if ($feed_data...) */ { ?>

        <p>Innholdet er midlertidig utilgjengelig.</p>

      <?php } /* end if ($feed_data...) */ ?>
      
      <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script>
    </div>

    <?php
}


/**
 * Outputs a single post
 */
function bvt_sfa_postfeed_output_single($item) {

    if (!$item) {

        echo "<p>Dette innlegget ble ikke funnet.</p>";
        return;
    }

    // Some "static" data...

    $allowed_tags_head = "<abbr><acronym><del><q>";

    $allowed_tags_body = "<a><abbr><acronym><br><del><dl><li><ol><p><q>" .
        "<table><td><tbody><th><thead><tfoot><tr><ul>";
        
    #for marking special kinds of items with specific classes
    $item_class = "";
    //special handling (hack) for items from google news. Contains a rather odd formatted table.    
    if($item->post_author=="news.google.com"){
        $allowed_tags_body = "<a><abbr><acronym><br><del><dl><li><ol><p><q><ul>";
    }
    // Build output strings

    $post_id  = htmlspecialchars($item->post_id);    
    $link     = htmlspecialchars($item->post_link);
    
    if($item->post_manually_created == 1 || (substr($link, 0, 4) == "md5:")){
      $item_class="manual";
    }    
    
    $date     = htmlspecialchars(strftime("%d.%m.%Y", strtotime($item->post_publish_date)));
    $author   = htmlspecialchars($item->post_author);
    $agree    = htmlspecialchars($item->post_votes_agree);
    $disagree = htmlspecialchars($item->post_votes_disagree);
    

    // Trusting Google Reader for extra markup sanitation...

    $title   = strip_tags($item->post_title, $allowed_tags_head);
    $content = strip_tags($item->post_content, $allowed_tags_body);

    // HACK: items with "md5:*" links are actually user inputs and must NOT
    // have an <a> tag output for them

    // TODO: This markup needs updating
    
    ?>

    <div class="bvt_sfa_item <?php echo($item_class)?>">

      <div class="vote">
        <input type="hidden" class="post_id" value="<?php echo $post_id; ?>" />
        <div class="decision agree" title="Enig? Gi din stemme!">
          <strong><?php echo $agree; ?></strong><br/><span>Enig</span>
        </div>
        <div class="decision disagree" title="Uenig? Gi din stemme!">
          <strong><?php echo $disagree; ?></strong><br/><span>Uenig</span>
        </div>
      </div>
      <div class="text-container">
        <h3>
  
          <?php if (substr($link, 0, 4) == "md5:" || $link=="") { ?>
            
            
            <?php echo $title; ?>
            
  
          <?php } else { ?>
  
            <a class="external" href="<?php echo $link; ?>"><?php echo $title; ?></a>
  
          <?php } ?>
  
        </h3>
        <p class="byline">
          <span class="date"><?php echo $date; ?></span> –
          <span class="blog"><?php echo $author; ?></span>
        </p>
        <div class="description">
          <div class="fade-overlay"></div>
          <?php echo $content; ?>
        </div>
        <?php if (substr($link, 0, 4) == "md5:" || $link == "") { 
          //obs - hardcoded testdata
          $link = htmlspecialchars('http://samarbeidforarbeid.regjeringen.no/takk-for-ditt-bidrag/?pr_post_id='.$post_id);          
        } ?>
        
        <ul class="shared-condensed">        
          <li>
            <a name="fb_share" type="icon_link" share_url="<?php echo $link; ?>">Del</a>
          </li>
          <li class="tweetmeme">      
            <?php /*tweetmeme has a ridicilous button implementation that would require the same js-file to be retrieved for each element. Same goes here, but at least it loads in an iframe*/ ?>
            <!--iframe frameborder="0" width="90" height="20" src="http://api.tweetmeme.com/button.js?url=<?php echo urlencode($link)?>&amp;style=compact"></iframe-->
            
          </li>
        </ul>
        
        <?php echo bvt_sfa_postfeed_output_metrics($item); ?>
      </div>

    </div>

    <?php
}


/**
 * Outputs metrics for a post
 */
function bvt_sfa_postfeed_output_metrics($item) {

    $allowed_metrics = explode(",", get_option("BVT_SFA_METRICS_ITEMS"));

    $metrics_id_titles = array(
        "delicious" => "Bokmerket på delicious",
        "brightkite" => "Nevnt på Brightkite",
        "reddit_comments" => "Reddit kommentarer",
        "reddit_votes" => "Reddit stemmer",
        "views" => "Visninger i webstrømmer",
        "identica" => "Nevnt på Identi.ca",
        "google" => "Lenket til fra andre nettsider",
        "diigo" => "Bokmerket på Diigo",
        "clicks" => "Klikket i webstrømmer",
        "blip" => "Nevnt på Blip",
        "digg" => "Antall Diggs og Digg kommentarer",
        "bookmarks" => "Bokmerket",
        "comments" => "Kommentarer",
        "twitter" => "Kvitret",
        "jaiku" => "Nevnt på Jaiku",
        "tumblr" => "Nevnt på Tumblr",
        "ff_likes" => "Likt på FriendFeed",
        "ff_comments" => "FriendFeed kommentarer"
    );

    $metrics = json_decode($item->post_postrank_metrics, true);

    $metrics_markup = "";

    foreach ($allowed_metrics as $metric_id) {

        if ($metrics && array_key_exists($metric_id, $metrics)) {

            $metrics_markup .=
                '<li';

            if (array_key_exists($metric_id, $metrics_id_titles)) {

                $metrics_markup .=
                    ' title="' . htmlspecialchars($metrics_id_titles[$metric_id]) .'"';
            }

            $metrics_markup .=
                '><span class="socMedia socMedia' .
                htmlspecialchars(ucfirst($metric_id)) . '"></span>' .
                htmlspecialchars($metrics[$metric_id]) . '</li>';
        }
    }
    if($metrics_markup != ""){
      return '<ul class="metrics">'.$metrics_markup.'</ul>';
    }
    else{
      return "";
    }
    
}

?>