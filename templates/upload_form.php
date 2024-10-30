<?php global $custom_types; ?>

<p>
	This plugin allows to import
	<a href="https://it.wordpress.org/plugins/pods/" target="_blank">Pods</a>
	from an arbitrary CSV file.
</p>

<form name="csv_upload" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="upload_form">
    <?php wp_nonce_field( 'upload_csv' ); ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php _e( 'Custom type', 'csv-custom-content-importer' ); ?>
            </th>
            <td>
                <select name="custom_type">
                    <option value="" selected disabled>Select one...</option>
		            <?php if ( count( $custom_types > 0 ) ): ?>
			            <?php foreach ( $custom_types as $group => $list ): ?>
                            <?php /*<optgroup label="<?php echo $group; ?>"> */ ?>
					            <?php foreach ( $list as $value => $label ): ?>
                                    <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
					            <?php endforeach; ?>
                            <?php /* </optgroup> */ ?>
			            <?php endforeach; ?>
		            <?php endif; ?>
                </select>
                <br>
                <span class="description">
                    This is the custom type you want to import from CSV.
                </span>
            </td>
        </tr>

        <tr>
            <th scope="row">
	            <?php _e( 'CSV file', 'csv-custom-content-importer' ); ?>
            </th>
            <td>
                <input type="file" name="csv">
                <br>
                <span class="description">
                    Please note that the first row of the uploaded CSV will be interpreted
                    as header.
                </span>
            </td>
        </tr>
    </table>

    <p>
        The next step will ask you to match selected custom type's fields with
        CSV columns.
    </p>

    <?php submit_button( __( 'Upload CSV', 'csv-custom-content-importer' ) ) ?>
</form>


<p class="description">
    Note: the uploaded CSV file won't show up in media gallery, but will be stored in
    <span style="font-family: monospace"><?php echo wp_upload_dir( 'ccci/uploads', false )['path']; ?></span>.
    If you want to clean this directory, check
    <a href="?page=csv-custom-content-importer&tool=maintenance">here</a>.
</p>