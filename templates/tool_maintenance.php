<?php global $ccci_upload_dir, $ccci_upload_files; ?>

<h3>Maintenance</h3>

<p>
    This form lists files uploaded to directory
    <span style="font-family: monospace"><?php echo $ccci_upload_dir; ?></span>.
    Select files you want to delete, then click
    <?php _e( 'Delete now', 'csv-custom-content-importer' ) ?>.
</p>

<p>
    <button class="button" onclick="javascript:jQuery('#maintenance_delete input').prop('checked', true);">
	    <?php _e( 'Select all', 'csv-custom-content-importer' ) ?>
    </button>
    <button class="button" onclick="javascript:jQuery('#maintenance_delete input').prop('checked', false);">
		<?php _e( 'Select none', 'csv-custom-content-importer' ) ?>
    </button>
</p>

<form method="post" id="maintenance_delete">
    <input type="hidden" name="action" value="maintenance_delete">
	<?php wp_nonce_field( 'delete_uploads' ); ?>

    <table>
        <tr>
            <th>Delete?</th>
            <th>File name</th>
        </tr>
    <?php foreach ( $ccci_upload_files as $i => $file ): ?>
        <tr>
            <td style="text-align: center">
                <input
                    type="checkbox"
                    id="files_<?php echo $i; ?>"
                    name="files[]"
                    value="<?php echo $file; ?>">
            </td>
            <td>
                <label for="files_<?php echo $i; ?>">
	                <?php echo $file; ?>
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>

    <?php submit_button( __( 'Delete now', 'csv-custom-content-importer' ), 'primary' ); ?>
</form>

