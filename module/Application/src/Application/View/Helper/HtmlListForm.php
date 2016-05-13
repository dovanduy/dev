<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Exception;
use Zend\View\Helper\AbstractHtmlElement;

/**
 * Helper for ordered and unordered lists
 */
class HtmlListForm extends AbstractHtmlElement
{
    /**
     * Generates a 'Table' element.
     *
     * @param  array $items   Array with the elements of the list
     * @param  bool  $ordered Specifies ordered/unordered list; default unordered
     * @param  array $attribs Attributes for the ol/ul tag.
     * @param  bool  $escape  Escape the items.
     * @throws Exception\InvalidArgumentException
     * @return string The list XHTML.
     */
    public function __invoke($form)
    {        
        return $this->getView()->partial('htmlListForm', array(
            'form' => $form            
        ));
    }
    
}
