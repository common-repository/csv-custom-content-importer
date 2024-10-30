<?php

/**
 * Class CcciCsvHandler
 *
 * An utility class for handling uploaded CSV files.
 */
class CcciCsvHandler {

	/** @var Path of uploaded CSV file */
	protected $csv_path;

	/** @var Columns read from the uploaded CSV file */
	protected $columns = [];

	/** @var File handle */
	protected $handle;

	public function __construct ( $csv_path = null ) {
		if ( $csv_path ) {
			$this->open( $csv_path );
		}
	}

	public function __destruct () {
		$this->close();
	}

	/**
	 * Open a CSV file for reading.
	 * @param $csv_path
	 *
	 * @throws Exception
	 */
	protected function open ( $csv_path ) {
		$this->csv_path = $csv_path;
        $this->handle = fopen( $csv_path, 'r' );
        $this->columns = $this->fetch_row();

        // First row will contain the columns
        if ( ! $this->columns || count( $this->columns ) === 0) {
        	throw new Exception('Could not read header from CSV' );
        }
	}

	/**
	 * Close the file handle.
	 */
    public function close () {
    	if ( $this->handle ) {
    		fclose( $this->handle );
	    }
    }

	/**
	 * Return an array containing the CSV header.
	 *
	 * @return array
	 */
    public function get_columns () {
        return $this->columns;
    }

	/**
	 * Read a line from CSV and returns it as array.
	 *
	 * @return array|false|null
	 */
    public function fetch_row () {
        if ( ! feof( $this->handle ) ) {
        	return fgetcsv( $this->handle );
        }
        return null;
    }

}