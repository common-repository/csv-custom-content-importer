<?php global $csv_file, $csv_columns, $custom_type, $custom_name, $custom_fields; ?>

<form method="post">
	<input type="hidden" name="action" value="matching_form">
	<input type="hidden" name="csv_file" value="<?php echo $csv_file; ?>">
	<input type="hidden" name="custom_type" value="<?php echo $custom_type; ?>">
	<input type="hidden" name="custom_name" value="<?php echo $custom_name; ?>">
	<?php wp_nonce_field( 'match_fields' ); ?>

	<table class="form-table">
		<?php foreach ( $custom_fields as $field_name => $field_label ) : ?>
            <tr valign="top">
				<th scope="row">
					<?php echo $field_label; ?>
				</th>
				<td>
					<select name="custom_field_<?php echo $field_name; ?>">
						<option value=""></option>
						<?php foreach ( $csv_columns as $i => $csv_header_label ) : ?>
							<option value="<?php echo $i; ?>">
								<?php echo $csv_header_label; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		<?php endforeach; ?>
        <?php /*
        <tr valign="top">
            <th scope="row">
                <?php _e( 'Import options', 'csv-custom-content-importer' ); ?>
            </th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text">
                        <?php _e('Import options', 'csv-custom-content-importer' ); ?>
                    </legend>
                    <label for="option_publish">
                        <input
                                type="checkbox"
                                checked="checked"
                                value="1"
                                id="option_publish"
                                name="option_publish">
	                    <?php _e('Automatically publish imported items', 'csv-custom-content-importer' ); ?>
                    </label>
                </fieldset>
            </td>
            */ ?>
        </tr>
	</table>

    <p>
        If you go ahead, the plugin will parse the CSV file and
        create a <?php echo $custom_name; ?> for each row of the CSV file.
    </p>

	<?php submit_button( __( 'Import now', 'csv-custom-content-importer' ) ) ?>
</form>