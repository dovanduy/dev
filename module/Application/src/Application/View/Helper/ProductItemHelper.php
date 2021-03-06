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
        $product['name'] = truncate($product['name'], 60);
        $btn = "
            <a  itemprop=\"url\" href=\"{$product['url']}\" 
                class=\"pull-right margin-clear btn btn-sm btn-default-transparent btn-animated\"                                                           
                \">{$view->translate('View Details')} <i class=\"fa fa-link\"></i>
            </a>
        ";
        $AppUI = $view->viewModel()->getRoot()->getVariable('AppUI');
        $website = $view->viewModel()->getRoot()->getVariable('website');
        if (is_array($product['category_id']) && !empty($product['category_id'])) {
            $categoryId = $product['category_id'][0];
        } else {
            $categoryId = $product['category_id'];
        }
        if (!empty($AppUI) && in_array($AppUI->id, \Application\Module::getConfig('admin_user_id'))) {
            $setPriorityUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'setpriorityproduct'
                ),
                array(
                    'query' => array(
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );

            $emailShareUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'emailshare'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );
            
            $fbShareUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'fbshare'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );
              
            $shareUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'share'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );
            
            $shareBloggerUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'shareblogger'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );
            
            $sharePlusUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'shareplus'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );            
           
            $sendoShareUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'sdshare'
                ),
                array(
                    'query' => array(
                        'url' => $product['url'],                       
                        'product_id' => $product['product_id'],
                    )
                )
            );            
            
            $deleteDbUrl = $view->url(
                'web/ajax', 
                array(
                    'action' => 'deleteproductdb'
                ),
                array(
                    'query' => array(
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            
            $lazadaCopyUrl1 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproductlzd'
                ),
                array(
                    'query' => array(                        
                        'lzd' => 1,
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );            
            $lazadaCopyUrl2 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproductlzd'
                ),
                array(
                    'query' => array(
                        'small_size' => 1,
                        'lzd' => 1,
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            
            $sendoCopyUrl1 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            $sendoCopyUrl2 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(
                        'small_size' => 1,
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            $sendoCopyUrl3 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(                        
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            $sendoCopyUrl4 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(
                        'small_size' => 1,
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            $sendoCopyUrl5 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(                        
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );
            $sendoCopyUrl6 = $view->url(
                'web/ajax', 
                array(
                    'action' => 'copyproduct'
                ),
                array(
                    'query' => array(
                        'small_size' => 1,
                        'category_id' => $categoryId,
                        'product_id' => $product['product_id'],
                    )
                )
            );            
            
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
                $adminBtn = "
                    <div class=\"admin-action\">
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$copyUrl}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$emailShareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-envelope-o\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$fbShareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-facebook\"></i>
                            </a>
                        </form>

                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$shareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-share\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$shareBloggerUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-tag\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$setPriorityUrl}\"
                                data-callback=\"   
                                    showMessage('Updated');
                                \"><i class=\"fa fa-map-pin\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$removeUrl}\"
                                data-confirmmessage=\"Are you sure?\"
                                data-callback=\"
                                    var item = btn.closest('.masonry-grid-item'); 
                                    item.remove();
                                \"><i class=\"fa fa-remove\"></i>
                            </a>
                        </form>
                    </div>
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
                
                $adminBtn = "<div class=\"admin-action\">";
                $link = [];
                if (empty($product['url_lazada'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$lazadaCopyUrl1}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +L
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_lazada']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +L
                            </a>
                        </form>
                    ";                                                    
                }
                if (empty($product['url_lazada2'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"background:#eee;width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$lazadaCopyUrl2}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +L
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_lazada2']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +L
                            </a>
                        </form>
                    "; 
                }
                if (empty($product['url_sendo1'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl1}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +V
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo1']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +V
                            </a>
                        </form>
                    ";
                }
                if (empty($product['url_sendo2'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"background:#eee;width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl2}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +V
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo2']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +V
                            </a>
                        </form>
                    ";
                }
                if (empty($product['url_sendo3'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl3}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +S
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo3']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +S
                            </a>
                        </form>
                    ";
                }
                if (empty($product['url_sendo4'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"background:#eee;width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl4}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +S
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo4']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +S
                            </a>
                        </form>
                    ";
                }
                if (empty($product['url_sendo5'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl5}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +Z
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo5']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +Z
                            </a>
                        </form>
                    ";
                }
                if (empty($product['url_sendo6'])) {
                    $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"background:#eee;width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"show-model btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                data-url=\"{$sendoCopyUrl6}\"  
                                data-modelid=\"#dialog-modal\"><i class=\"fa fa-copy\"></i> +Z
                            </a>
                        </form>
                        ";
                } else {
                    $adminBtn .= "
                        <form method=\"post\">
                            <a  target=\"_blank\" itemprop=\"url\" href=\"{$product['url_sendo6']}\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent\"                                                           
                                ><i class=\"fa fa-eye\"></i> +Z
                            </a>
                        </form>
                    ";
                }
                $adminBtn .= "                    
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$sendoShareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-shirtsinbulk\"></i>
                            </a>
                        </form>
                        <!--
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$emailShareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-envelope-o\"></i>
                            </a>
                        </form>                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$fbShareUrl}\"    
                                data-showloading=\"1\"
                                data-callback=\"                                        
                                    showMessage(result.message);
                                \"><i class=\"fa fa-facebook\"></i>
                            </a>
                        </form>                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$shareUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-share\"></i>
                            </a>
                        </form>
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$shareBloggerUrl}\"      
                                data-showloading=\"1\"  
                                data-callback=\"                                       
                                    showMessage(result.message);
                                \"><i class=\"fa fa-tag\"></i>
                            </a>
                        </form>
                        -->                        
                        <form method=\"post\">
                            <a  itemprop=\"url\" href=\"#\" 
                                style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                                class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                                data-url=\"{$setPriorityUrl}\"
                                data-callback=\"   
                                    showMessage('Updated');
                                \"><i class=\"fa fa-map-pin\"></i>
                            </a>
                        </form>
                        
                        <form method=\"post\">
                        <a  itemprop=\"url\" href=\"#\" 
                            style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                            class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                            data-url=\"{$removeFromCategoryUrl}\"
                            data-confirmmessage=\"Are you sure?\"
                            data-callback=\"
                                var item = btn.closest('.masonry-grid-item'); 
                                item.remove();
                            \"><i class=\"fa fa-remove\"></i>
                        </a>
                        </form>
                        
                        <form method=\"post\">
                        <a  itemprop=\"url\" href=\"#\" 
                            style=\"width:24px;padding:4px 2px;float:right;margin-left:2px;\"
                            class=\"btn-adm pull-right margin-clear btn btn-sm btn-default-transparent ajax-submit\"                                                           
                            data-url=\"{$deleteDbUrl}\"
                            data-confirmmessage=\"Are you sure?\"
                            data-callback=\"
                                var item = btn.closest('.masonry-grid-item'); 
                                item.remove();
                            \"><i class=\"fa fa-trash-o\"></i>
                        </a>
                        </form>

                        <form method=\"post\">
                        <select style=\"width:44px;padding:4px 2px;float:right;margin-left:2px;\"
                        name=\"add_block_id\" 
                        class=\"ajax-change\"
                        data-url=\"{$addToBlockUrl}\"
                        data-callback=\"showMessage('Added');\" 
                        >{$blockOption}</select>
                        </form>

                        <form method=\"post\">
                        <select style=\"width:44px;padding:4px 2px;float:right;\"
                        name=\"category_id\" 
                        class=\"ajax-change\"
                        data-url=\"{$addToCategoryUrl}\"
                        data-callback=\"showMessage('Added');\" 
                        >{$categoryOption}</select>
                        </form>                    
                    </div>                    
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
        $discount = '';   //p($product, 1);   
        if (!empty($product['discount_percent'])) {
            $discount = "<div class=\"discount-text\">-{$product['discount_percent']}%</div>";                        
        } elseif (!empty($product['discount_amount'])) {            
            $amount = app_money_format($product['discount_amount']);
            $discount = "<div class=\"discount-text\">-{$amount}%</div>";            
        }
        if ($product['price'] < $product['original_price']) {
            $product['price'] = app_money_format($product['price'], false);        
            $product['original_price'] = app_money_format($product['original_price']);
        } else {
            $product['price'] = app_money_format($product['price'], false);   
            $product['original_price'] = '';
        }
        $html = "
            <div class=\"{$class}\">
                <div itemscope itemtype=\"http://schema.org/Product\" class=\"mb-20 listing-item white-bg bordered\">
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
                        {$discount}
                    </div>
                    <div class=\"body\">
                        <h3>
                            <a itemprop=\"url\" href=\"{$product['url']}\">
                                <span itemprop=\"name\">{$product['name']}</span>
                            </a>
                        </h3>
                        <div class=\"elements-list clearfix\">
                            <div itemprop=\"offers\" itemscope itemtype=\"http://schema.org/Offer\" class=\"price-block\">
                                <meta itemprop=\"priceCurrency\" content=\"VND\" />
								<span itemprop=\"price\" class=\"price\">{$product['price']}</span><span class=\"price currency\">VND</span>
                                <span class=\"original-price\">{$product['original_price']}</span>                                
                            </div>
                            {$btn}                                                          
                        </div>
                    </div>
                    {$adminBtn}
                </div>
            </div>
        ";
        return $html;
    }
    
}
