/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.addStylesSet( 'my_styles',
    [
        // Block Styles
        { name : 'Title', element : 'h2', attributes : {'class':'titleSection'} },
        { name : 'Subtitle', element : 'h3', attributes : {'class':'subTitleSection'} },
        { name: 'Styled Image (left)', element: 'img', attributes: { 'class': 'left' } },
        { name: 'Styled Image (right)', element: 'img', attributes: { 'class': 'right' } }
    ]);

CKEDITOR.editorConfig = function( config )
{
    // Define changes to default configuration here.
    config.uiColor = '#66AB16';
    config.enableContextMenu = true;
    config.extraPlugins = 'image2, widget';
    config.removePlugins = 'image, elementspath';
    config.removeFormatTags = 'b,big,code,del,dfn,em,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,va,h1,h2,h3,h4';
    config.resize_enabled = false;
    config.language = 'en';
    config.skin = 'kama';
    config.stylesCombo_stylesSet = 'my_styles';
    config.enterMode = CKEDITOR.ENTER_BR;
    config.entities = false;
    config.allowedContent = true;

    config.toolbar_Contenido =
        [
            ['Cut','Copy','Paste','PasteText','PasteWord','-','Print'],
            ['Undo','Redo'],
            ['FontSize'],
            '/',
            ['Bold','Italic','Underline','StrikeThrough','-'],
            ['OrderedList','UnorderedList','-','Outdent','Indent'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyFull'],
            ['Link','Unlink'],
            ['TextColor','BGColor']
        ];

    config.toolbar_Basico = [
        ['Bold','Italic']
    ];

    config.toolbar_Basic =     [
        ['FontSize','TextColor','Bold','Italic','Underline','Subscript','Superscript','Styles','RemoveFormat'],
        ['Find', 'Replace', 'SelectAll','Undo','Redo'],
        ['Cut','Copy','Paste','PasteText','PasteWord'],
        ['NumberedList','BulletedList','Outdent','Indent'],
        ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
        ['Link','Unlink','Anchor','Blockquote','CreateDiv'],
        ['Image','Table','HorizontalRule','SpecialChar','UniversalKey','Maximize','Source']
    ];

    config.toolbar_Basic_New = [
        ['TextColor','Bold','Italic','Underline','Strike','PasteText','-','Link','Unlink','Table','Maximize','Source'],
        ['BulletedList','-','Outdent','Indent'],'/',['Image','FooterImage','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']
    ];

    config.toolbar_Minimo = [
        ['TextColor','Bold','Italic','Underline','Strike','PasteText','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Link','Unlink']
    ];

    config.toolbar_Vacio = [
        []
    ];

    config.toolbar_Newsletter = [
        ['Bold','Italic','Underline','Strike','Font','FontSize'],
        ['OrderedList','UnorderedList','-','Link','Unlink','Table','Maximize'],
        ['BulletedList','-','Outdent','Indent'],['Image'],'/',
        ['TextColor','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Source','RemoveFormat','/','PasteText','Paste']
    ];

};
