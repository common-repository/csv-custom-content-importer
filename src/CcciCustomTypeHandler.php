<?php

/**
 * Class CcciCustomTypeHandler
 *
 * A common interface between the plugin and the supported custom types plugin.
 */
abstract class CcciCustomTypeHandler {

	/**
	 * Type for Pods Framework.
	 * @see https://pods.io
	 */
	const TYPE_POD = 'pod';

	/**
	 * Type for ACF.
	 * @see https://www.advancedcustomfields.com/
	 */
	const TYPE_ACF = 'acf';

	/**
	 * @param $custom_type One of the type constants.
	 * @param $custom_name The custom type name.
	 *
	 * @return CcciPodHandler A concrete class that implements the CcciCustomTypeHandler abstract class and that handles custom posts of type $custom_type
	 */
	public static function instantiate ( $custom_type, $custom_name ) {
		switch ( $custom_type ) {
			case self::TYPE_POD:
				require_once 'CcciPodHandler.php';
				return new CcciPodHandler( $custom_name );
				break;
		}
	}

	/**
	 * Builds an array of all available (and supported) custom post types. It is a two-level array:
	 * first level will contain type constants; second level will contain related custom posts type.
	 * @return array
	 */
	public static function get_lists() {
		$custom_types = [];

		if ( function_exists( 'pods_api' ) ) {
			$pods = pods_api()->load_pods();
			foreach ( $pods as $pod ) {
				$key = self::TYPE_POD . '_' .$pod['name'];
				$custom_types[self::TYPE_POD][ $key ] = self::get_label_for_pod( $pod );
			}
		}

		return $custom_types;
	}

	/**
	 * Retrieve the label for a Pod.
	 *
	 * @param $pod
	 *
	 * @return string
	 */
	public static function get_label_for_pod( $pod ) {
		return isset( $pod['options']['label_singular'] )
			? $pod['options']['label_singular']
			: $pod['label'];
	}

	/**
	 * Imports custom post types from a csv file.
	 *
	 * @param CcciCsvHandler $csv_handler
	 * @param $fields_matching
	 */
    public function import ( CcciCsvHandler $csv_handler, $fields_matching ) {
        while ( $row = $csv_handler->fetch_row() ) {
        	$content = [];
        	foreach ( $fields_matching as $field => $column ) {
        		if ( $column !== '' ) {
			        $content[ $field ] = $row[ $column ];
		        }
	        }
        	$this->add( $content );
        }
    }

	/**
	 * Add a new item of current custom type to WordPress.
	 *
	 * @param $content array An associative arrays of field_name => field_value
	 *
	 * @return mixed
	 */
    public abstract function add ( $content );

	/**
	 * @return mixed The current custom type
	 */
	public abstract function get_custom_name ();

	/**
	 * Read fields available for current custom type.
	 *
	 * @return mixed
	 */
    public abstract function get_fields ();

}