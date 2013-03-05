<?php
class Subscribe2_List_Table extends WP_List_Table {
	function __construct(){
		global $status, $page;

		parent::__construct( array(
			'singular'	=> 'subscriber',
			'plural'	=> 'subscribers',
			'ajax'		=> false
		) );
	}

	function column_default($item, $column_name){
		global $current_tab;
		if ( $current_tab == 'registered' ) {
			switch($column_name){
				case 'email':
					return $item[$column_name];
				default:
					return print_r($item,true);
			}
		} else {
			switch($column_name){
				case 'email':
				case 'date':
					return $item[$column_name];
				default:
					return print_r($item,true);
			}
		}
	}

	function column_email($item){
		global $current_tab;
		if ( $current_tab == 'registered' ) {
			$actions = array('edit' => sprintf('<a href="?page=%s&amp;email=%s">Edit</a>', 's2', $item['email']));
			return sprintf('%1$s %2$s', $item['email'], $this->row_actions($actions));
		} else {
			global $mysubscribe2;
			if ( '0' === $mysubscribe2->is_public($item['email']) ) {
				return sprintf('<span style="color:#FF0000">%1$s</span>', $item['email']);
			} else {
				return sprintf('%1$s', $item['email']);
			}
		}
	}

	function column_cb($item){
		return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $item['email']);
	}

	function get_columns(){
		global $current_tab;
		if ( $current_tab == 'registered' ) {
			$columns = array('email' => 'Email');
		} else {
			$columns = array(
				'cb'		=> '<input type="checkbox" />',
				'email'		=> 'Email',
				'date'		=> 'Date'
			);
		}
		return $columns;
	}

	function get_sortable_columns() {
		global $current_tab;
		if ( $current_tab == 'registered' ) {
			$sortable_columns = array('email' => array('email', true));
		} else {
			$sortable_columns = array(
				'email'	=> array('email', true),
				'date'	=> array('date', false)
			);
		}
		return $sortable_columns;
	}

	function get_bulk_actions() {
		global $current_tab;
		if ( $current_tab == 'registered' ) {
			return array();
		} else {
			$actions = array(
				'delete'	=> __('Delete', 'subscribe2'),
				'toggle'	=> __('Toggle', 'subscribe2')
			);
			return $actions;
		}
	}

	function prepare_items() {

		global $mysubscribe2;
		if ( is_int($mysubscribe2->subscribe2_options['entries']) ) {
			$per_page = $mysubscribe2->subscribe2_options['entries'];
		} else {
			$per_page = 25;
		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		global $mysubscribe2, $subscribers, $current_tab;
		$data = array();
		if ( $current_tab == 'public' ) {
			foreach($subscribers as $email) {
				$data[] = array('email' => $email, 'date' => $mysubscribe2->signup_date($email));
			}
		} else {
			foreach($subscribers as $email) {
				$data[] = array('email' => $email);
			}
		}

		function usort_reorder($a,$b){
			$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'email';
			$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc';
			$result = strcmp($a[$orderby], $b[$orderby]);
			return ($order==='asc') ? $result : -$result;
		}
		usort($data, 'usort_reorder');

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items/$per_page)
		) );
	}
}
?>