<?php

require_once 'CcciCsvHandler.php';
require_once 'CcciCustomTypeHandler.php';

/**
 * Class CcciController
 *
 * This implements the 'C' of a simple MVC-like pattern.
 * Unfortunately, WordPress makes it difficult to implement a get-post-redirect pattern
 * without using output caching, so we are using a more basic pattern.
 */
class CcciController {

	/** @var array Error messages that must be shown to user */
    protected $errors = [];

	/** @var Path of uploaded CSV file */
	protected $csv_file;

	/** @var Columns read from the uploaded CSV file */
    protected $csv_columns;

    /** @var Identifies the framework used to build the custom post type. */
    protected $custom_type;

    /** @var The custom type name */
    protected $custom_name;

    /** @var Matches custom type's fields and CSV columns */
    protected $fields_matching;

	/**
	 * Handle web requests.
	 */
    public function action () {
        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';

        if ( ! $action && isset( $_GET['tool'] ) && $_GET['tool'] === 'maintenance' ) {
        	$action = 'maintenance';
        }

        switch ( $action ) {
	        case 'maintenance':
	        	$this->show_maintenance();
	        	break;

	        case 'maintenance_delete':
		        $this->handle_maintenance();
		        $this->show_maintenance_completed();
	        	break;

	        case 'matching_form':
	        	// Step 3
	        	$this->handle_matching_form();
	        	$this->show_completed_screen();
	        	$this->delete_csv();
	        	break;

	        case 'upload_form':
	        	// Step 2
                $this->handle_upload_form();
                $this->show_matching_form();
                break;

            default:
            	// Step 1
                $this->show_upload_form();
        }
    }

	/**
	 * Step 1: show the upload form.
	 */
    public function show_upload_form () {
		global $custom_types;
		$custom_types = CcciCustomTypeHandler::get_lists();
        $this->render( 'upload_form' );
    }

	/**
	 * Step 2/a: handle a POST request from Step 1.
	 */
    public function handle_upload_form () {
    	check_admin_referer( 'upload_csv' );

	    // $_POST['custom_type'] will be something like pod_mytype or acf_mytype.
	    // The part before the underscore (_) defines the custom post framework and will be one
	    // of the TYPE_* constants of CcciCustomTypeHandler. The part after the underscore
	    // will be the custom type name.
	    $custom_type_info = isset( $_POST['custom_type'] )
		    ? explode( '_', sanitize_key( $_POST['custom_type'] ) )
		    : '';

	    if ( count( $custom_type_info ) !== 2 ) {
		    $this->error( 'Please select a custom type to import' );
	    }

    	if ( ! isset( $_FILES['csv'] ) ) {
    		$this->error( 'No file uploaded' );
    		return;
	    }

    	// wp_handle_upload will manage the upload, but won't add the file to media gallery.
	    $file = wp_handle_upload( $_FILES['csv'], array( 'action' => 'upload_form' ), 'ccci/uploads' );

	    if ( ! $file ) {
		    $this->error( 'No file uploaded' );
		    return;
	    }

	    if ( isset( $file['error'] ) ) {
		    $this->error( $file['error'] );
		    return;
	    }

	    if ( $file['type'] != 'text/csv' ) {
		    $this->error( 'File must be a CSV' );
		    return;
	    }

	    // Everything OK, update state
	    $this->csv_file = $file['file'];
	    $this->custom_type = $custom_type_info[0];
	    $this->custom_name = $custom_type_info[1];
    }

	/**
	 * Step 2/b: show the matching form.
	 * @throws Exception
	 */
    public function show_matching_form () {
    	if ( $this->has_errors() ) {
    		$this->render();
    		return;
	    }

	    global $csv_file, $csv_columns, $custom_type, $custom_name, $custom_fields;

	    $csv_file = $this->csv_file;
		$custom_type = $this->custom_type;
		$custom_name = $this->custom_name;
		$csv_columns = (new CcciCsvHandler( $this->csv_file ))->get_columns();
		$custom_fields = CcciCustomTypeHandler::instantiate( $this->custom_type, $this->custom_name )->get_fields();

	    $this->render( 'matching_form' );
    }

	/**
	 * Step 3/a: handle a POST request from Step 2.
	 */
    public function handle_matching_form () {
	    check_admin_referer( 'match_fields' );

        if ( ! isset( $_POST['csv_file'] )
				|| ! isset( $_POST['custom_type' ] )
                || ! isset( $_POST['custom_name'] ) ) {
        	$this->errors( 'Invalid form submission' );
        }

        // Read state
        $this->csv_file = sanitize_text_field( $_POST['csv_file'] );
        $this->custom_type = sanitize_key( $_POST['custom_type'] );
        $this->custom_name = sanitize_key( $_POST['custom_name'] );

        // Build the $fields_matching array.
	    foreach ( $_POST as $key => $value ) {
	    	$key = sanitize_key( $key );
	    	$value = sanitize_key( $value );
		    if ( substr( $key,0, 13) == 'custom_field_' ) {
		    	$field = substr( $key, 13 );
			    $this->fields_matching[ $field ] = $value;
		    }
	    }

	    // Import
	    $csv_handler = new CcciCsvHandler( $this->csv_file );
	    $custom_type_handler = CcciCustomTypeHandler::instantiate( $this->custom_type, $this->custom_name );
	    $custom_type_handler->import( $csv_handler, $this->fields_matching );
    }

	/**
	 * Step 3/b: show process outcome.
	 */
    public function show_completed_screen () {
        $this->render( 'completed_screen' );
    }

	/**
	 * Tool for cleaning upload directory.
	 */
    public function show_maintenance () {
    	global $ccci_upload_dir, $ccci_upload_files;
    	$ccci_upload_dir = wp_upload_dir( 'ccci/uploads', false )['path'];
	    $ccci_upload_files = array_filter(
	    	scandir( $ccci_upload_dir ),
	        function ( $item ) { return substr( $item, 0, 1 ) !== '.'; }
	    );
    	$this->render( count( $ccci_upload_files )
    		? 'tool_maintenance'
	        : 'tool_maintenance_empty'
	    );
    }

	/**
	 * Clean the upload directory.
	 */
    public function handle_maintenance () {
	    check_admin_referer( 'delete_uploads' );

    	$selected_files = isset( $_POST['files'] ) ? $_POST['files'] : [];
    	$upload_path = wp_upload_dir( 'ccci/uploads', false )['path'];
	    $uploaded_files = array_filter(
		    scandir( $upload_path ),
		    function ( $item ) { return substr( $item, 0, 1 ) !== '.'; }
	    );
	    foreach ( $uploaded_files as $file ) {
	    	// We assume that file names uploaded via wp_handle_upload are sanitized
		    $file = sanitize_file_name( $file );
	    	// For files currently in the upload directory, delete those who are
		    // in the selected files array. It is a bit of an overkill, but an extra
		    // check before calling 'unlink' won't do harm ;)
	    	if ( array_search( $file, $selected_files, true ) !== false ) {
	    		@unlink( "{$upload_path}/{$file}" );
		    }
	    }
    }

	/**
	 * Show a confirmation screen after cleaning.
	 */
    public function show_maintenance_completed () {
    	// For now, we just recycle the "All done!" screen.
    	$this->show_completed_screen();
    }

	/**
	 * Display a template.
	 * @param $view
	 */
    protected function render( $view = null ) {
	    $path = dirname( dirname(__FILE__) ) . '/templates';
	    include ( "{$path}/components/header.php" );
        $this->render_messages();
	    if ($view) {
	    	include( "{$path}/{$view}.php" );
	    }
	    include ( "{$path}/components/footer.php" );
    }

	/**
	 * Show messages (if any).
	 */
    public function render_messages () {
	    if ( $this->has_errors() ) {
		    foreach ( $this->errors as $error ) {
		    	printf(
		    		'<div class="notice notice-error"><p>%s</p></div>',
				    esc_html__( $error )
			    );
		    }
	    }
    }

	/**
	 * Add an error message to current state. If the controller has errors,
	 * will prevent user to advance in the process.
	 * @param $error
	 */
    protected function error ( $error ) {
        $this->errors[] = $error;
    }

    protected function has_errors () {
    	return count( $this->errors ) > 0;
    }

	/**
	 * Delete the uploaded CSV file.
	 */
    protected function delete_csv () {
    	if ( $this->csv_file && file_exists( $this->csv_file ) ) {
    		@unlink( $this->csv_file );
	    }
    }

}