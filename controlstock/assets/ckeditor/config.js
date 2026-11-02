/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
		{ name: 'links' },
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'others' },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
		{ name: 'styles' },
		{ name: 'colors' },
		{ name: 'about' }
	];

	// Remove some buttons provided by the standard plugins, which are
	// not needed in the Standard(s) toolbar.
	//config.removeButtons = 'Underline,Subscript,Superscript';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';
	
	config.extraPlugins = 'panel';
	config.extraPlugins = 'panelbutton';
	config.extraPlugins = 'floatpanel';
	config.extraPlugins = 'colorbutton';
	
	//Para conectar el KCFINDER
    config.filebrowserBrowseUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/browse.php?type=files&dir=files/public';
    config.filebrowserImageBrowseUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/browse.php?type=images&dir=images/public';
    config.filebrowserFlashBrowseUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/browse.php?type=flash&dir=flash/public';
    config.filebrowserUploadUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/upload.php?type=files&dir=files/public';
    config.filebrowserImageUploadUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/kcfinder/upload.php?type=images&dir=images/public';
    config.filebrowserFlashUploadUrl = 'http://www.casarom.com.ar/assets/ckeditor/kcfinder-3.12/kcfinder/upload.php?type=flash&dir=flash/public';
	
};
