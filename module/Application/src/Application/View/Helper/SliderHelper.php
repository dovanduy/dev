<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;

/**
 * Helper for ordered and unordered lists
 */
class SliderHelper extends AbstractHtmlElement
{    
    /**
     * Render slider html
     *     
     * @author thailvn
     * @param int $total Total record
     * @param int $limit Page size
     * @param int $displayPage How page link showing
     * @param string $url If empty then get current url     
     * @return string Paging html 
     */
    public function __invoke($slideId = null, $firstInputId = null, $lastInputId = null, $min = 0, $max = 1000, $step = 10, $value = "0,1000")
    {         
		if (empty($slideId)) {
			$rand = rand(1, 9);
			$slideId = 'slider-' . $rand;
			$firstInputId = 'slider-first-' . $rand;
			$lastInputId = 'slider-last-' . $rand;
		}		
		$this->getView()->headScript()->offsetSetScript(100 + rand(1, 1000), " 
			$(function() {    
				$(\"#{$slideId}\").slider({
					range: true,
					step: {$step},
					min: {$min},
					max: {$max},
					values: [{$value}],
					slide: function( event, ui ) {
						$('#{$firstInputId}').val($.number(ui.values[0]));
						$('#{$lastInputId}').val($.number(ui.values[1]));						
					}
				});				
			});
		");		        
        $expValue = explode(',', $value);
        if (!isset($expValue[0]) || !isset($expValue[1])) {
            $expValue[0] = $min;
            $expValue[1] = $max;
        }
		$firstValue = number_format(db_float($expValue[0]));
		$lastValue = number_format(db_float($expValue[1]));
		$html = "			
			<div id=\"{$slideId}\"></div>
			<p class='silde-input'>
				<input readonly class='firstInput' type='text' id='{$firstInputId}' name='{$firstInputId}' value='{$firstValue}'/> 
				<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span> 
				<input readonly class='lastInput number' type='text' id='{$lastInputId}' name='{$lastInputId}' value='{$lastValue}' /> 
			</p>
			<p class='silde-submit'>
				<input type='submit' id='submitSlide' class='submit-button btn btn-default' value='Tìm'>
			</p>
		";
		return $html;
    }
    
}
