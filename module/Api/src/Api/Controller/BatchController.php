<?php

namespace Api\Controller;
use Application\Lib\Log;
use Application\Lib\Util;
use Application\Lib\Arr;
use Zend\Http\PhpEnvironment\Request;
use Zend\Console\Request as ConsoleRequest;
use Api\Model\Products;
use Api\Model\InputOptions;

class BatchController extends AppController {   

    private $_website_id = 1;
    
    public function indexAction() {        
       
    }
    
    public function importAction()
    {
        $request = $this->getRequest();
 
        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }
 
        // Get system service name  from console and check if the user used --verbose or -v flag
        $website = $request->getParam('website', false);
        $verbose = $request->getParam('verbose');
        
        
        echo $this->$website();
        
        /*
        $shell = "ps aux";
        if ($doname){
            $shell .= " |grep -i $doname ";
        }
        $shell .= " > /Users/abdulmalikikhsan/www/success.txt ";
        //execute...
        system($shell, $val);
 
        if(!$verbose){
            echo "Process listed in /Users/abdulmalikikhsan/www/success.txt \r\n";
        }else{
            $file = fopen('/Users/abdulmalikikhsan/www/success.txt',"r");
 
            while(! feof($file)){
                $listprocess = trim( fgets($file) );
 
                echo $listprocess."\r\n";
            }
            fclose($file);
        }
         * 
         */
        
    }
    
    public function nguonhangtot_attr($import = false) {        
        $fieldId = 1;
        include_once getcwd() . '/include/simple_html_dom.php';
        $getAttrUrl = 'http://nguonhangtot.com/collections/balo-in';
        $content = app_file_get_contents($getAttrUrl);
        if ($content == false) {
            echo $getAttrUrl . ' Failed' . PHP_EOL;  
            exit;
        }
        $content = strip_tags_content($content, '<script><style>', true);
        $html = str_get_html($content);  
        $attrs = array();
        foreach($html->find('div[class=box-content filter_box]') as $element) {
            $attr = array();
            $divHtml = str_get_html($element->innertext);            
            foreach($divHtml->find('input') as $inputElement) {    
                $attr['id'] = $inputElement->value;
                break;
            }
            foreach($divHtml->find('span') as $spanElement) {    
                $attr['name'] = $spanElement->innertext;
                break;
            }
            if (!empty($attr)) {                
                $attrs[] = $attr;
            }
        }
       
        if ($import == true) {
            $inputOptionsModel = $this->getServiceLocator()->get('InputOptions');
            foreach($attrs as &$attr) {    
                $inputOptionsModel->add(
                    array(
                        'website_id' => $this->_website_id,
                        'field_id' => $fieldId,
                        'name' => $attr['name'],
                    ),
                    $optionId
                );
                $attr['option_id'] = $optionId;
            }
            unset($attr);
        }
        return $attrs;
    }
    
    // php index.php import products --verbose nguonhangtot
    public function nguonhangtot() {
        include_once getcwd() . '/include/simple_html_dom.php';
        $domain = 'http://nguonhangtot.com';
        $productList = array(            
            array(               
                'disable' => 1,
                'category_id' => 5,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/balo-3d',
                'id' => '1000195298',
                'short' => "✓ Chất liệu vải dù
                    ✓ Không thấm nước, không bong tróc
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Có 2 ngăn, vải dù dày dặn, không thắm nước, dễ bảo quản</p>                    
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>
                ",
                'detail_url' => array(),
                'size_id' => array(1, 2),
                'max_images' => 2
            ),          
            array(        
                'disable' => 0,
                'category_id' => 6,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/mo-ta-chi-tiet-balo-simili-2-mau',
                'id' => '1000416589',
                'short' => "✓ Chất liệu simili 100%
                    ✓ Không thấm nước, không bong tróc
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Balô hàng Việt Nam xuất khẩu, chất lượng đảm bảo</p>
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>
                ",
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 4
            ),            
            array(   
                'disable' => 1,
                'category_id' => 3,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/balo-day-rut',
                'id' => '1000153440',
                'short' => "✓ Chất liệu simili 100%
                    ✓ Không thấm nước, không bong tróc
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Balô dây rút size nhỏ 79k. Kích thước: 32x37 (cm)</p>
                    <p>- Balô dây rút size lớn 99k. Kích thước: 42x37 (cm)</p>
                    <p>- Balô dây rút hàng Việt Nam xuất khẩu, chất lượng đảm bảo.</p>
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>
                ",
                'detail_url' => array(),
                'size_id' => array(5, 6),
                'max_images' => 2
            ),
            array(   
                'disable' => 1,
                'category_id' => 2,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/balo-in',
                'id' => '1000151735',
                'short' => "✓ Chất liệu: simili giả da chống thấm tốt                   
                    ✓ Dây đeo tháo rời
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Size lớn có kích thước 42x31x14, có 2 ngăn đựng laptop 14\", có chổ để bình nước</p>
                    <p>- Size nhỏ có kích thước 32x27x10, có 1 ngăn để vừa giấy A4</p>
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>                    
                ",
                'detail_url' => array(),
                'size_id' => array(3, 4),
                'max_images' => 4
            ),
            array(   
                'disable' => 1,
                'category_id' => 4,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/balo-tre-em-3d',
                'id' => '1000211047',
                'short' => "✓ Chất liệu: vãi dù mềm, siêu nhẹ
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Kích thước 32x27x10</p>
                    <p>- Có 2 ngăn, mặt mút in nổi, than vãi dù mềm, siêu nhẹ</p>                   
                    <p>- Chất liệu vãi dù mềm, siêu nhẹ</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>                    
                ",
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 2
            )
        ); 
       
        $importList = array();
        foreach ($productList as $item) {    
            if ($item['disable'] === 0) {
                $importList[] = $item;
            }
        }
        
        if (empty($importList)) {
            echo 'List is empty!';
            exit;
        }
        
        $attrs = $this->nguonhangtot_attr(true);
        
        // get detail url list
        foreach ($importList as &$item) {             
            $content = app_file_get_contents($item['url']);
            if ($content == false) {
                echo $item['url'] . ' Failed' . PHP_EOL;
                continue;
            }
            $content = strip_tags_content($content, '<script><style>', true);
            $html = str_get_html($content);  
            $totalRecord = 0;
            foreach($html->find('div[class=results]') as $element) { 
                $spanHtml = str_get_html($element->innertext);
                foreach($spanHtml->find('span') as $spanElement) {    
                    $totalRecord = db_int($spanElement->innertext);
                    break;
                }
            }
            $totalPage = ceil($totalRecord/18);        
            if ($totalPage > 1) {
                for ($page = 1; $page <= $totalPage; $page++) {
                    $content = @file_get_contents($item['url'] . '?page=' . $page);
                    if ($content == false) {
                        echo $item['url'] . ' Failed';
                        continue;
                    }
                    $subHtml = str_get_html($content);
                    foreach($subHtml->find('div[class=product-details]') as $element) { 
                        $aHtml = str_get_html($element->innertext);
                        foreach($aHtml->find('a') as $aElement) {    
                            if (!empty($aElement->href) && strpos($aElement->href, 'collections') !== false) {
                                $item['detail_url'][] = $domain . trim(strip_tags($aElement->href));
                                break;
                            }
                        }
                    } 
                }                
            } else {            
                foreach($html->find('div[class=product-details]') as $element) { 
                    $aHtml = str_get_html($element->innertext);
                    foreach($aHtml->find('a') as $aElement) {    
                        if (!empty($aElement->href) && strpos($aElement->href, 'collections') !== false) {
                            $item['detail_url'][] = $domain . trim(strip_tags($aElement->href));
                            break;
                        }
                    }
                }     
            }    
        } 
        unset($item); 
        // end get detail url list
      
        $products = array();        
        foreach ($importList as $item) {
            $product = $item;
            $productAttr = array();
            // get product attr list            
            foreach ($attrs as &$attr) {               
                $url = "http://nguonhangtot.com/search?q=filter=((collectionid:product={$item['id']})&&(collectionid:product={$attr['id']}))";
                // find total page
                $totalPage = app_file_get_contents($url . '&view=pagesize');
                if (empty($totalPage)) {
                    echo $url . '&view=pagesize' . ' Failed' . PHP_EOL;
                    continue;
                }
                // find records
                for ($page = 1; $page <= $totalPage; $page++) {                
                    $content = app_file_get_contents($url . '&view=ajax&page='.$page);
                    if ($content == false) {
                        echo $url . '&view=ajax&page='.$page . ' Failed' . PHP_EOL;
                        continue;
                    }
                    $content = strip_tags_content($content, '<script><style>', true);
                    $subHtml = str_get_html($content);
                    foreach($subHtml->find('div[class=product-details]') as $element) { 
                        $aHtml = str_get_html($element->innertext);
                        foreach($aHtml->find('a') as $aElement) {    
                            if (!empty($aElement->innertext) && strpos($aElement->href, 'products') !== false) {
                                $productName = trim(strip_tags($aElement->innertext));                                
                                $productAttr[$productName] = $attr['option_id'];
                                break;
                            }
                        }
                    }
                }               
            }
            unset($attr);           
            // end get product attr list
              
            // get product detail
            if (!isset($product['max_images'])) {
                $product['max_images'] = \Admin\Module::getConfig('products.max_images');
            }          
            foreach ($item['detail_url'] as $url) {
                $content = app_file_get_contents($url);
                if ($content == false) {
                    echo $url . ' Failed' . PHP_EOL;
                    continue;
                }
                $content = strip_tags_content($content, '<script><style>', true);
                $html = str_get_html($content);                 
                foreach($html->find('h1[class=heading-title]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['name'] = trim(strip_tags($element->innertext));
                        break;
                    }
                }
                foreach($html->find('span[class=p-model]') as $element) {                    
                    if (!empty($element->innertext)) {                       
                        $code = trim(strip_tags($element->innertext));
                        if ($code != 'Mã hàng:') {
                            $product['code'] = $code;
                            break;
                        }
                    }
                }
                foreach($html->find('span[class=product-price]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['price'] = str_replace(array('VND','.',','), '', trim(strip_tags($element->innertext)));
                        break;
                    }
                }                
                $product['images'] = array();
                $product['content'] = $item['content'];
                foreach($html->find('img[id=image]') as $element) {                
                    if (!empty($element->src)) {
                        $imageUrl = 'http:' . trim(strip_tags($element->src));
                        if (empty($product['images'])) {
                            $product['url_image'] = $imageUrl;
                        }
                        $product['images'][] = $imageUrl;
                        //$product['content'] .= "<center><p><img style=\"width:80%\" src=\"{$imageUrl}\"/></p></center>";
                        if (count($product['images']) >= $product['max_images']) {
                            break;
                        }
                    }
                }
                if (isset($productAttr[$product['name']])) {
                    $product['option_id'] = $productAttr[$product['name']];
                }
                $products[] = $product;
            }    
            // end get product detail
        }
        
        $fieldId = 1;
        $count = 1;
        foreach ($products as $product) {
            unset($product['id']); 
            unset($product['detail_url']);
            $product['website_id'] = $this->_website_id;
            $productModel = $this->getServiceLocator()->get('Products');
            $product['add_image_to_content'] = 1;
            $_id = $productModel->add($product, $productId);
            if ($_id) {
                $productHasFieldsModel = $this->getServiceLocator()->get('ProductHasFields');
                if (!empty($product['option_id'])) {
                    $productHasFieldsModel->addUpdate(
                        array(
                            'product_id' => $productId,
                            'field' => array(
                                $fieldId => $product['option_id']
                            ),
                        )
                    );
                }                
                echo '[' . $count . '] ' . $product['code'] . ' Done' . PHP_EOL;
            } else {
                echo $product['name'] . ' Failed' . PHP_EOL;
            }    
            $count++;
        }
    }

}