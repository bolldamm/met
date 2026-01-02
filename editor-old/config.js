/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
    config.skin = 'bootstrapck';
    config.resize_enabled = false;
    config.language = 'en';

    config.toolbar_Minimo = [
        ['Bold','Italic','Underline','PasteText','OrderedList','UnorderedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Link','Unlink'],
        ['BulletedList','NumberedList','-','Outdent','Indent']
    ];

};
