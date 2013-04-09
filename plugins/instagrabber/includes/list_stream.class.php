<?php 

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 * Our theme for this list table is going to be movies.
 */
class List_Stream extends WP_List_Table {
    
    private $stream;
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct($stream){
        global $status, $page;
        $this->stream = $stream; 
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'image',     //singular name of the listed records
            'plural'    => 'images',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'title', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'title'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
       return $item[$column_name];
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'title'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_pic_thumbnail($item){
        $stream = $this->stream;
        //Build row actions
        $drafttext = __('Save draft', 'instagrabber');
        $publishtext = __('Publish Image', 'instagrabber');
        $standardtext = $stream->post_status == 'draft' ? $drafttext : $publishtext;

        $actions = array(
            'post'      => sprintf('<a href="?page=%s&action=%s&image=%s">%s</a>',$_REQUEST['page'].'&stream='.$_GET['stream'],'post-instagrammer',$item['id'], $standardtext)
        );

        

        if($stream->post_status == 'draft'){
            $actions['publish'] = sprintf('<a href="?page=%s&action=%s&poststatus=%s&image=%s">%s</a>',$_REQUEST['page'].'&stream='.$_GET['stream'],'post-instagrammer-override', 'published',$item['id'], $publishtext);
        }else{
            $actions['draft'] = sprintf('<a href="?page=%s&action=%s&poststatus=%s&image=%s">%s</a>',$_REQUEST['page'].'&stream='.$_GET['stream'],'post-instagrammer-override','draft',$item['id'], $drafttext);
        }

        if (get_option('instagrabber_allow_save_images') && get_option('instagrabber_allow_save_images') != "false"){
            if($item['media_id'] == 0){
                $actions['saveimage'] = sprintf('<a href="?page=%s&action=%s&image=%s">%s</a>',$_REQUEST['page'].'&stream='.$_GET['stream'],'instagrabber_save_image', $item['id'], __("Save image in media library", 'instagrabber'));
            }
        }
        
        //Return the title contents
        return sprintf('<img src="'.$item['pic_thumbnail'] .'" width="100">%1$s',
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ 'image',  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }

    

    function column_tags($item){
        
        if($item['tags'] != "" || !empty($item['tags'])){
            
                $tags = unserialize(trim($item['tags']));
                if ($tags) {
                    $tags = implode(', ', $tags);
                }
                
            
            
            
            
        }else{
            $tags = '';
        }
        
        return sprintf(
            '%s',
            /*$2%s*/ $tags
        );
    }
    
    function column_pic_link($item){
        
        //Return the title contents
        return sprintf('<a href="'.$item['pic_link'] .'" >'.$item['pic_link'].'</a>'
        );
    }

    function column_published($item){
        
        //Return the title contents
        $text = $item['published'] == 1 ? __('Yes', 'instagrabber') : __('No', 'instagrabber');
        return $text;
    }
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'            => __('id', 'instagrabber'),
            'media_id'      => __('Media ID', 'instagrabber'),
            'pic_thumbnail' => __('Thumbnail', 'instagrabber'),
            'user_name'     => __('Username', 'instagrabber'),
            'pic_link'      => __('Link', 'instagrabber'),
            'pic_timestamp' => __('Posted', 'instagrabber'),
            'time_added'    => __('Added', 'instagrabber'),
            'caption'       => __('Caption', 'instagrabber'),
            'tags'          => __('Tags', 'instagrabber'),
            'comment_count' => __('Comments', 'instagrabber'),
            'like_count'    => __('Likes', 'instagrabber'),
            'published'     => __('Published', 'instagrabber'),
        );
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'time_added'    => array('time_added',true),     //true means its already sorted
            'user_name'     => array('user_name',false),
            'pic_timestamp' => array('pic_timestamp',false),
            'comment_count' => array('comment_count',false),
            'like_count'    => array('like_count',false),
            'published'     => array('published',false)
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $stream = $this->stream;
        
        $standardtext = $stream->post_status == 'draft' ? __('Save draft', 'instagrabber') : __('Publish directly', 'instagrabber');
        $overridetext = $stream->post_status == 'draft' ? __('Publish directly', 'instagrabber') : __('Save draft', 'instagrabber');
        
        $actions = array(
            'post-instagrammer'    => $standardtext,
            'post-instagrammer-override' => $overridetext
        );
         if (get_option('instagrabber_allow_save_images') && get_option('instagrabber_allow_save_images') != "false"){
            $actions['instagrabber_save_image'] = __('Save images to media library', 'instagrabber');
         }

        return $actions;
    }
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
        
    }
    
    function extra_tablenav($which){

        echo '<div class="alignleft actions">';
        echo '<a href="?page='.$_GET['page'].'&stream='. $_GET['stream'] .'&action=updatestream&streamid='. $_GET['stream'] .'" title="" class="button-primary action">'.__('Update stream', 'instagrabber').'</a>';
        echo '</div>';

        if($which == 'bottom'){
           
        }
    }


        /**
     * Generate the table navigation above or below the table
     *
     * @since 3.1.0
     * @access protected
     */
    function display_tablenav( $which ) {
        if ( 'top' == $which )
            
?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

        <div class="alignleft actions">
            <?php $this->bulk_actions( $which ); ?>
        </div>
<?php
        $this->extra_tablenav( $which );
        $this->pagination( $which );
?>

        <br class="clear" />
    </div>
<?php
    }
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        
        $current_user = wp_get_current_user();
        update_user_meta($current_user->ID, 'instagrabber_last_seen_'.$_GET['stream'], date('Y-m-d H:i:s', time()));
        /**
         * First, lets decide how many records per page to show
         */

        $user = get_current_user_id();
        $screen = get_current_screen();

        $option = $screen->get_option('per_page', 'option');

        $per_page = get_user_meta($user, $option, true);
        
        if ( empty ( $per_page ) || $per_page < 1 ) {
 
            $per_page = $screen->get_option( 'per_page', 'default' );
         
        }

        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array('id', 'media_id');
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        $args = array(
                'return' => ARRAY_A,
            );
        if(isset($_GET['s'])){
            $query = '%' . $_GET['s'] . '%';
             $args['where'] = "AND CONCAT(caption, tags, user_name) LIKE '$query'";
        }
        $data = Database::get_images($_GET['stream'], $args);
        
                
        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
        
    }
    
}
 ?>