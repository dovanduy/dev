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
                'id' => $product['product_id']
            )
        ); 
        
        $product['url_add_to_wishlist'] = '#';
        $product['original_price'] = !empty($product['original_price']) ? app_money_format($product['original_price']) : '';
        $product['price'] = app_money_format($product['price']);
        $product['name'] = truncate($product['name'], 60);
        $product['short'] = nl2br(truncate($product['short'], 80));
        $isMobile = Util::isMobile();
        
        $btn = "
            <a  itemprop=\"url\" href=\"{$product['url']}\" 
                class=\"pull-right margin-clear btn btn-sm btn-default-transparent btn-animated\"                                                           
                \">{$view->translate('View Details')} <i class=\"fa fa-link\"></i>
            </a>
        ";
        $AppUI = $view->viewModel()->getRoot()->getVariable('AppUI');
        $website = $view->viewModel()->getRoot()->getVariable('website');
        if (!empty($AppUI) && in_array($AppUI->id, \Web\Module::getConfig('admin_user_id'))) {
            if (!empty($product['block_id'])) {
                $removeUrl = $view->url(
                    'web/ajax', 
                    array(
                        'action' => 'removeproductfromblock'
                    ),
                    array(
                        'query' => array(
                            'block_id' => $product['block_id'],
                            'product_id' => $product['product_id'],
                        )
                    )
                );
                $btn = "
                    <a  itemprop=\"url\" href=\"#\" 
                        class=\"pull-right margin-clear btn btn-sm btn-default-transparent btn-animated ajax-submit\"                                                           
                        data-url=\"{$removeUrl}\"
                        data-callback=\"
                            var item = btn.closest('.masonry-grid-item'); 
                            item.remove();
                        \">{$view->translate('Remove')} <i class=\"fa fa-remove\"></i>
                    </a>
                ";
            } else {                
                $addToBlockUrl = $view->url(
                    'web/ajax', 
                    array(
                        'action' => 'addproducttoblock'
                    ),
                    array(
                        'query' => array(
                            'product_id' => $product['product_id']
                        )
                    )
                );
                
                $addToCategoryUrl = $view->url(
                    'web/ajax', 
                    array(
                        'action' => 'addproducttocategory'
                    ),
                    array(
                        'query' => array(
                            'product_id' => $product['product_id']
                        )
                    )
                );
                
                if (is_array($product['category_id']) && !empty($product['category_id'])) {
                    $categoryId = $product['category_id'][0];
                } else {
                    $categoryId = $product['category_id'];
                }
                $removeFromCategoryUrl = $view->url(
                    'web/ajax', 
                    array(
                        'action' => 'removeproductfromcategory'
                    ),
                    array(
                        'query' => array(
                            'category_id' => $categoryId,
                            'product_id' => $product['product_id'],
                        )
                    )
                );
                
                $blockOption = array("<option value=\"\">+B</option>");
                foreach ($website['blocks'] as $block) {
                    $blockOption[] = "<option value=\"{$block['block_id']}\">{$block['name']}</option>";
                }
                $blockOption = implode('', $blockOption);
                
                $categoryOption = array("<option value=\"\">+C</option>");
                foreach ($website['last_categories'] as $categoryId => $name) {
                    $categoryOption[] = "<option value=\"{$categoryId}\">{$name}</option>";
                }
                $categoryOption = implode('', $categoryOption);
                
                $btn = "
                    <form method=\"post\" style=\"margin:-26px 0px 0px 0px\">
                    <a  itemprop=\"url\" href=\"#\" 
                        style=\"width:25px;padding:4px 2px;float:right;margin-left:2px;\"
                        class=\"pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                        data-url=\"{$removeFromCategoryUrl}\"
                        data-callback=\"
                            var item = btn.closest('.masonry-grid-item'); 
                            item.remove();
                        \"><i class=\"fa fa-remove\"></i>
                    </a>
                    </form>
                    
                    <form method=\"post\" style=\"margin:-26px 0px 0px 0px\">
                    <select style=\"width:43px;padding:4px 2px;float:right;margin-left:2px;\"
                    name=\"add_block_id\" 
                    class=\"ajax-change\"
                    data-url=\"{$addToBlockUrl}\"
                    data-callback=\"alert('Added');\" 
                    >{$blockOption}</select>
                    </form>
                    
                    <form method=\"post\" style=\"margin:-26px 0px 0px 0px\">
                    <select style=\"width:43px;padding:4px 2px;float:right;\"
                    name=\"category_id\" 
                    class=\"ajax-change\"
                    data-url=\"{$addToCategoryUrl}\"
                    data-callback=\"alert('Added');\" 
                    >{$categoryOption}</select>
                    </form>
                    
                ";
            }
        }
        /*
        elseif (empty($product['size_id'])) {
            $btn = "
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
        }
        */
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
                            <img itemprop=\"image\" 
                                class=\"lazy lazy-hidden\"                                 
                                style=\"width:100%;height:100%;\"                                 
                                alt=\"{$product['name']}\" 
                                data-original=\"{$product['url_image']}\">
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
                            {$btn}                                                               
                        </div>
                    </div>
                </div>
            </div>
        ";
        return $html;
    }
    
}
