=== WP-SARAHSPELL ===
Contributors: arabspellchecker
Donate link:
Tags: rtl, ltr, wysiwyg, formatting, tinymce, write, edit, post, posts, arabic, spell checking, speller
License: GPLv2 or later
Requires at least: 3.9
Tested up to: 5.5.3
Stable tag: 1.0

Enables Arabic Spell Checking in the TinyMCE editor.

== Description ==
Enables Arabic Spell Checking in the TinyMCE editor.
The plugin relies on our spell checking API server to check words and generate suggestions. For that a single API request is done, and the response will contain all the presumably misspelled words and their correction suggestions. The plugin also uses an API endpoint to check the validity of the API keys used for spell checking. No other info is sent or exchanged with the API.

This plugin relies on Classic Editor plugin (https://wordpress.org/plugins/classic-editor/) and must be installed before enabling this plugin.

Service website (In Arabic only): https://arabicspellchecker.com/
Service's Terms of use link (In Arabic only) : https://arabicspellchecker.com/terms.html
 

== Installation ==

Search for the plugin "wp-sarahspell" from the plugin page inside your WordPress dashboard, or to manually install it do the following:

1. Upload the folder (wp-sarahspell) to the plugins directory (wp-content/plugins).
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Clear your browser cache (This is important otherwise you will see the cached TinyMCE).

== Screenshots ==

1. The post editor showing the new direction buttons.
2. Switching to the text editor shows the added direction to the RTL line.
3. A test post showing the correct alignment for the languages.

== Changelog ==

* 1.0 First release
