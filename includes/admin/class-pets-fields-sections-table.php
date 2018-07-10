<?php
/**
 * List Table for Fields
 */

namespace Pets\Admin;

use Pets\Pets_Cache;

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Fields_Sections_Table extends \WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'Section', 'pets' ), //singular name of the listed records
			'plural'   => __( 'Sections', 'pets' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );

	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'title' => __( 'Title', 'pets' ),
			'icon'  => __( 'Icon', 'pets' ),
		);

		return $columns;
	}

	/**
	 * Get values
	 * @param object $item
	 * @param string $column
	 */
	function column_default( $item, $column ) {
		return $item[ $column ];
	}

	/**
	 * @param $item
	 * @param $column
	 */
	function column_icon( $item ) {
		$icon = $item['icon'];

		if ( is_numeric( $icon ) ) {
			$output = '<img width="50" src="' . wp_get_attachment_thumb_url( $icon ) . '">';
		} else {
			$output = '<span class="' . $icon . '">';
		}

		return $output;
	}

	/**
	 * @param $item
	 * @param $column
	 */
	function column_title( $item ) {
		$output = '';

		$output .= $item['title'];

		$delete_nonce = wp_create_nonce( 'pets_delete_field' );

		$actions = array(
			'delete' => sprintf( '<a href="' . admin_url( 'edit.php?post_type=pets&page=pets-fields&tab=sections' ) . '&action=%s&field=%s&_wpnonce=%s">Delete</a>','delete', absint( $item['id'] ), $delete_nonce ),
			'edit'   => sprintf( '<a href="' . admin_url( 'edit.php?post_type=pets&page=pets-fields&tab=sections' ) . '&action=%s&field=%s">Edit</a>','edit', absint( $item['id'] ) )
		);

		$output .= $this->row_actions( $actions );

		return $output;
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'title', true ),
		);

		return $sortable_columns;
	}

	/**
	 * Retrieve field's data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_sections( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}pets_fields_sections";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => __( 'Delete', 'pets' )
		];

		return $actions;
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_section( $id ) {
		global $wpdb;


		$wpdb->delete(
			"{$wpdb->prefix}pets_fields_sections",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}pets_fields_sections";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No Fields avaliable.', 'pets' );
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'sections_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_sections( $per_page, $current_page );
	}

	/**
	 * Process Bulk Actions
	 */
	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'pets_delete_field' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_section( absint( $_GET['field'] ) );
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_section( $id );

			}
		}
	}
}