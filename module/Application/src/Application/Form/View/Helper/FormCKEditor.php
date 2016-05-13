<?php

namespace Application\Form\View\Helper;
use Zend\Form\View\Helper\FormTextarea as OriginalFormFormTextarea;
use Zend\Form\ElementInterface;

class FormCKEditor extends OriginalFormFormTextarea {
    
    public function __invoke(ElementInterface $element = null)
    {
        // invoke parent and get form text
        include_once getcwd() . '/public/ckeditor/ckeditor_custom.php';
        include_once getcwd() . '/public/ckfinder/ckfinder.php';    
        $name = $element->getName();
        $value = $element->getValue();
        $CKEditor = new \CKEditor();
        $CKFinder = new \CKFinder();
        $attributes = $element->getAttributes();
        foreach ($attributes as $attrKey => $attrValue) {
            switch ($attrKey) {
                case 'width':
                case 'height':
                    $config[$attrKey] = $attrValue;
            }            
        }
        $config['toolbar'] = array(
            array( 'Source'),
            array( 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord'),	  
            array( 'Image', 'Smiley', 'Table', 'Link', 'Unlink'),	  
            array( 'Font','FontSize', 'Bold', 'Italic', 'Underline', 'Strike', '-', 'Subscript', 'Superscript', '-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', '-', 'TextColor', 'BGColor')
        );
        $config['filebrowserBrowseUrl'] = '/ckfinder/ckfinder.html';
        $config['filebrowserImageBrowseUrl'] = '/ckfinder/ckfinder.html?type=Images';
        $config['filebrowserFlashBrowseUrl'] = '/ckfinder/ckfinder.html?type=Flash';
        $config['filebrowserUploadUrl'] = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
        $config['filebrowserImageUploadUrl'] = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
        $config['filebrowserFlashUploadUrl'] = '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
        $CKEditor->basePath = '/ckeditor/';      
        $CKFinder->SetupCKEditorObject($CKEditor);
        return $CKEditor->editor(parent::__invoke($element), $name, $value, $config, null);        
    }
    
}
