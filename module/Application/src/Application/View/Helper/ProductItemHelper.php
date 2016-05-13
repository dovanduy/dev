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
use Application\Lib\Util;

/**
 * Helper for ordered and unordered lists
 */
class ProductItemHelper extends AbstractHtmlElement
{    
    /**
     * Render Product Item html
     *     
     * @author thailvn
     * @param array $product Product info  
     * @return string Product Item html 
     */
    public function __invoke(array $product, $columns = 3)
    {   
        $view = $this->getView();
        $request = $view->requestHelper();        
        $product['url'] = $view->url(
            'web/products', 
            array(
                'name' => name_2_url($product['name'])
            )
        );
        $product['url_add_to_cart'] = $view->url(
            'web/carts', 
            array(
                'action' => 'additem',
                'id' => $product['_id']
            )
        );     
        $product['url_add_to_wishlist'] = '#';
        $product['original_price'] = !empty($product['original_price']) ? money_format($product['original_price']) : '';
        $product['price'] = money_format($product['price']);
        $product['name'] = truncate($product['name'], 60);
        $product['short'] = nl2br(truncate($product['short'], 80));
        $isMobile = Util::isMobile();
        if (empty($product['size_id'])) {
            $addToCartBtn = "
                <a  itemprop=\"url\" href=\"#\" 
                    class=\"pull-right margin-clear btn btn-sm btn-default-transparent btn-animated ajax-submit\"                                                           
                    data-url=\"{$product['url_add_to_cart']}\"
                    data-callback=\"
                        var isMobile={$isMobile};
                        if (isMobile == 0) {
                            loadCart(1);        
                        } else {                          
                            $('#mCart .cart-count').html(parseInt($('#mCart .cart-count').html())+1);
                            $('html, body').animate({scrollTop: 0}, 'slow');
                        }
                        showMessage('{$view->translate('Added to cart')}')
                    \">{$view->translate('Add to cart')} <i class=\"fa fa-shopping-cart\"></i>
                </a>
            ";
        } else {
            $addToCartBtn = "
                <a  itemprop=\"url\" href=\"{$product['url']}\" 
                    class=\"pull-right margin-clear btn btn-sm btn-default-transparent btn-animated\"                                                           
                    \">{$view->translate('View Details')} <i class=\"fa fa-link\"></i>
                </a>
            ";
        }
        
        $class = '';
        switch ($columns) {
            case 3:
                $class = 'col-sm-6 col-lg-4 masonry-grid-item';
                break;
            case 4:   
                $class = 'col-md-3 col-sm-6 masonry-grid-item';            
                break;
        }       
        $html = "
            <div class=\"{$class}\">
                <div itemscope itemtype=\"http://schema.org/Product\" class=\"listing-item white-bg bordered mb-20\">
                    <div class=\"overlay-container\">
                        <div class=\"image\">
                            <img itemprop=\"image\" alt=\"{$product['name']}\" src=\"{$product['url_image']}\" alt=\"\">
                        </div>
                        <a class=\"overlay-link popup-img-single\" alt=\"{$product['name']}\" href=\"{$product['url_image']}\"><i class=\"fa fa-search-plus\"></i></a>
                        <div class=\"overlay-to-top links\">
                            <span class=\"small\">
                                <!--<a href=\"{$product['url_add_to_wishlist']}#\" class=\"btn-sm-link\"><i class=\"fa fa-heart-o pr-10\"></i>{$view->translate('Add to Wishlist')}</a>-->
                                <a itemprop=\"url\" href=\"{$product['url']}\" class=\"btn-sm-link\"><i class=\"icon-link pr-5\"></i>{$view->translate('View Details')}</a>
                            </span>
                        </div>
                    </div>
                    <div class=\"body\">
                        <h3>
                            <a itemprop=\"url\" href=\"{$product['url']}\">
                                <span itemprop=\"name\">{$product['name']}</span>
                            </a>
                        </h3>
                        <div class=\"elements-list clearfix\">
                            <span itemprop=\"price\" class=\"price\">{$product['price']}</span>
                            {$addToCartBtn}                                                               
                        </div>
                    </div>
                </div>
            </div>
        ";
        return $html;
    }
    
}
