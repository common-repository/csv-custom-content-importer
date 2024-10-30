<?php

/**
 * Class CcciPodHandler
 *
 * Adds compatibility with Pods Framework.
 * @see https://pods.io
 */
class CcciPodHandler extends CcciCustomTypeHandler {

	protected $custom_name;
	protected $custom_fields;

	/**
	 * CcciPodHandler constructor.
	 *
	 * @param $custom_name string The pod name
	 */
	public function __construct( $custom_name ) {
		$this->custom_name = $custom_name;
	}

	public function add ( $content ) {
		$pod = pods( $this->custom_name );
		if ( ! $pod ) {
			throw new Exception( "Pod &quot;{$this->custom_name}&quot; not found." );
		}
		$post_id = $pod->add( $content );
		wp_publish_post( $post_id );
		return $post_id;
    }

    public function get_custom_name() {
	    return $this->custom_name;
    }

	public function get_fields () {
	    $pod = pods( $this->custom_name );
	    if ( ! $pod ) {
	    	throw new Exception( "Pod &quot;{$this->custom_name}&quot; not found." );
	    }

	    // WordPress fields
		$this->custom_fields['title'] = 'Title';
		$this->custom_fields['content'] = 'Content';

		// Pods fields
	    foreach ( $pod->fields() as $field ) {
		    $this->custom_fields[$field['name']] = $field['label'];
	    }

	    return $this->custom_fields;
    }

}