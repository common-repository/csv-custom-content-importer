<?php
/**
 * CSV Custom Content Importer
 *
 * @package     CsvCustomContentImporter
 * @author      Luca Vicidomini
 * @copyright   2019 Luca Vicidomini
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: CSV Custom Content Importer
 * Plugin URI:  https://lucavicidomini.com/projects/ccci
 * Description: Imports data from a CSV file to Pods..
 * Version:     0.5
 * Author:      Luca Vicidomini
 * Author URI:  https://lucavicidomini.com
 * Text Domain: csv-custom-content-importer
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Add sub menu page to the Tools admin menu.
require_once ( 'src/CcciController.php' );
function ccci_setup () {
	add_management_page(
		'CSV Custom Content Importer',
		'CSV Custom Content Importer',
		'import',
		'csv-custom-content-importer',
		array( new CcciController(), 'action' ) );
}
add_action( 'admin_menu', 'ccci_setup' );
