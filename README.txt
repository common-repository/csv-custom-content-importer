=== CSV Custom Content Importer ===
Contributors: lucavicidomini
Donate link: http://example.com/
Tags: csv,import,custom post type,pod,pods
Requires at least: 4.5
Tested up to: 5.0.3
Stable tag: 0.5
Requires PHP: 5.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Import a CSV file as a Custom Post Type, for free!

== Description ==

Please note: this is an early version of the plugin and only works with Custom Post Type defined using the [PODS Framework](https://pods.io/) .

If you are using advanced CMS features from Wordpress, you have problably defined you own custom post types, and have designed your own archive and/or post templates.

CSV Custom Content Importer allows you to import data from a CSV file into your own Custom Post Type.

CSV Custom Content Importer only offers basic import features... but it is a free plugin :)

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/csv-custom-content-importer` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use it via the Tools -> CSV Custom Content Import menu

== Frequently Asked Questions ==

No FAQs yet.

== Screenshots ==

1. Step 1: select the custom post type to import and a CSV file to upload.
2. Step 2: match selected custom post's fields with CSV file columns.
3. Maintenance screen lets you delete previously uploaded CSV files.

== Changelog ==

= 0.5 =
* First version

== Upgrade Notice ==

No upgrade notice yet.

== Known bugs and limitations ==

* Only allows to import to a Custom Post Type defined using the [PODS Framework](https://pods.io/) (maybe I will add support to other frameworks in the future).
* Only supports simple fields (i.e. no relationship fields)
