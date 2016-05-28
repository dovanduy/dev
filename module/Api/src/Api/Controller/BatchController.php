<?php

namespace Api\Controller;
use Application\Lib\Log;
use Application\Lib\Util;
use Application\Lib\Arr;
use Application\Lib\Cache;
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
        $category = $request->getParam('category', 0);
        $verbose = $request->getParam('verbose');
     
      
        echo $this->$website($category);
        
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
    
    public function categories($import = false) {   
        if ($import == false) {
            return false;
        }
        $categories = array(
            array(                
                'name' => 'Ba lô',
                'meta_keyword' => 'Ba lô, Ba lô giả da, Ba lô giây rút, Ba lô in 3D, Bao lô hộp 2 màu',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                
            ),
            array(                
                'name' => 'Túi chéo',
                'meta_keyword' => 'Túi chéo, Túi chéo hộp, Túi chéo du lịch',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                
            ),
            
            array(           
                'name' => 'Ba lô giả da',
                'parent_name' => 'Ba lô',
                'meta_keyword' => 'Ba lô, Ba lô giả da',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                 
            ),
            array(               
                'name' => 'Ba lô giây rút',
                'parent_name' => 'Ba lô', 
                'meta_keyword' => 'Ba lô, Ba lô giây rút',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                  
            ),
            array(               
                'name' => 'Ba lô in 3D',
                'parent_name' => 'Ba lô', 
                'meta_keyword' => 'Ba lô, Ba lô in 3D',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                   
            ),
            array(               
                'name' => 'Ba lô laptop',
                'parent_name' => 'Ba lô', 
                'meta_keyword' => 'Ba lô, Ba lô laptop',                
                'meta_description' => 'Mua ba lô laptop chính hãng chất lượng tại Balodoc.com',                   
            ),
            array(               
                'name' => 'Bao lô hộp 2 màu',
                'parent_name' => 'Ba lô', 
                'meta_keyword' => 'Ba lô, Bao lô hộp 2 màu',                
                'meta_description' => 'Mua ba lô chính hãng chất lượng tại Balodoc.com',                   
            ),            
            array(               
                'name' => 'Túi chéo hộp',
                'parent_name' => 'Túi chéo', 
                'meta_keyword' => 'Túi chéo, Túi chéo hộp',                
                'meta_description' => 'Mua túi chéo chính hãng chất lượng tại Balodoc.com',                   
            ),
            array(               
                'name' => 'Túi chéo du lịch',
                'parent_name' => 'Túi chéo', 
                'meta_keyword' => 'Túi chéo, Túi chéo du lịch',                
                'meta_description' => 'Mua túi chéo chính hãng chất lượng tại Balodoc.com',                   
            ),
        );
        $categoryModel = $this->getServiceLocator()->get('ProductCategories');
        foreach($categories as $category) {            
            $parentId = 0;
            if (!empty($category['parent_name'])) {
                $parent = $categoryModel->getDetail(array(
                    'name' => $category['parent_name']
                ));
                if (empty($parent)) {
                    echo $category['parent_name'] . ' does not exists' . PHP_EOL;
                    exit;
                }
                $parentId = $parent['category_id'];
            }
            $categoryModel->add(
                array(
                    'website_id' => $this->_website_id,                    
                    'name' => $category['name'],
                    'parent_id' => $parentId,
                    'meta_keyword' => $category['meta_keyword'],
                    'meta_description' => $category['meta_description'],
                )
            );     
        }
    }
        
    public function nguonhangtot_attr($import = false) {       
        
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
            $fields = array(
                array(                
                    'name' => 'Phong cách',
                    'type' => 'radio',
                    'input_options' => array()
                ),
                array(                
                    'name' => 'Phù hợp',
                    'type' => 'checkbox',
                    'input_options' => array(
                        array(
                            'name' => 'Cho nam'                                                    
                        ),
                        array(
                            'name' => 'Cho nữ'                                                    
                        ),
                        array(
                            'name' => 'Cho teen'                                                    
                        ),
                        array(
                            'name' => 'Cho bé trai'                                                    
                        ),
                        array(
                            'name' => 'Cho bé gái'                                                    
                        ),
                    ),
                ),                                
            );
            $inputFieldModel = $this->getServiceLocator()->get('InputFields');
            $inputOptionModel = $this->getServiceLocator()->get('InputOptions');            
            foreach($fields as $field) { 
                $inputFieldModel->add(
                    array(
                        'website_id' => $this->_website_id,
                        'name' => $field['name'],
                        'type' => $field['type'],
                        'input_options' => $field['input_options'],
                    ),
                    $fieldId
                );         
                if (!empty($fieldId) 
                    && $field['name'] == 'Phong cách' 
                    && empty($field['input_options'])) {
                    foreach($attrs as &$attr) {
                        $inputOptionModel->add(
                            array(
                                'website_id' => $this->_website_id,
                                'field_id' => $fieldId,
                                'name' => $attr['name'],
                            ),
                            $attr['option_id']
                        );
                    }
                    unset($attr);
                }
            }            
        }
        return $attrs;
    }
    
    // php index.php import products --verbose nguonhangtot 3
    // php index.php import products --verbose nguonhangtot 4
    // php index.php import products --verbose nguonhangtot 7
    public function nguonhangtot($category = 0) {        
        include_once getcwd() . '/include/simple_html_dom.php';
        $domain = 'http://nguonhangtot.com';
        $productList = array(
            array(   
                'disable' => 0,
                'category_id' => 3,
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
                'disable' => 0,
                'category_id' => 4,
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
                    <p>- Size lớn có kích thước 42x37 (cm)</p>
                    <p>- Size nhỏ có kích thước 32x37 (cm)</p>
                    <p>- Ba lô dây rút hàng Việt Nam xuất khẩu, chất lượng đảm bảo.</p>
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng</p>
                ",
                'detail_url' => array(),
                'size_id' => array(5, 6),
                'max_images' => 2
            ),
            array(               
                'disable' => 0,
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
                'category_id' => 7,
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
                    <p>- Ba lô hàng Việt Nam xuất khẩu, chất lượng đảm bảo</p>
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>
                ",
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 4
            ),
            array(        
                'disable' => 0,
                'category_id' => 8,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/tui-cheo-chi-tiet',
                'id' => '1000317492',
                'short' => "✓ Chất liệu simili 100%
                    ✓ Không thấm nước, không bong tróc
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Chất liệu simili 100% không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn</p>                    
                    <p>- Túi hộp lớn kích thước 32x23x9 (cm), có ngăn để vừa giấy A4, phù hợp cho đi học thêm hoặc đi chơi.</p>                    
                    <p>- Túi hộp nhỏ kích thước 25x17x2 (cm), có ngăn để vừa giấy A5, ipad mini, điện thoại di động. Phù hợp đi chơi.</p>                    
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>
                ",
                'detail_url' => array(),
                'size_id' => array(7, 8),
                'max_images' => 3
            ),
            array(        
                'disable' => 0,
                'category_id' => 9,
                'warranty' => '',
                'made_in' => 'VN',
                'vat' => '1',                
                'url' => 'http://nguonhangtot.com/collections/tui-du-lich',
                'id' => '1000195308',
                'short' => "✓ Chất liệu vải dù
                    ✓ Không thấm nước, không bong tróc
                    ✓ Công nghệ in Nhật Bản cho hình in đẹp
                ",
                'content' => "
                    <p>- Chất liệu vải dù dày dặn có dây đeo tháo rời</p> 
                    <p>- Kích thước 47x26x20 (cm)</p>                    
                    <p>- Không thấm nước, không bong tróc. Bạn sẽ yên tâm đi mưa & dễ lau chùi khi bị bẩn.</p>
                    <p>- Công nghệ in Nhật Bản cho hình in đẹp, đặc biệt mặt in còn được phủ lên lớp màng chống trầy xước và phai màu nên bạn hoàn toàn yên tâm khi sử dụng.</p>
                ",
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 3
            ),
        );
       
        $importList = array();
        foreach ($productList as $item) {    
            if ($item['disable'] === 0 && $item['category_id'] == $category) {
                $importList[] = $item;
            }
        }
        
        if (empty($importList)) {
            echo 'List is empty!';
            exit;
        }
        
        $this->categories(true);
        $attrs = $this->nguonhangtot_attr(true);      
        
        $inputFieldModel = $this->getServiceLocator()->get('InputFields');
        $field = $inputFieldModel->getDetail(
            array(
                'website_id' => $this->_website_id,
                'name' => 'Phong cách',
            )
        );             
        if (empty($field['field_id'])) {
            echo 'Field Phong cach does not exists' . PHP_EOL;
            exit;
        }
        $fieldId = $field['field_id'];
        
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
                        $product['name'] = $this->planText($element->innertext);
                        break;
                    }
                }
                foreach($html->find('span[class=p-model]') as $element) {                    
                    if (!empty($element->innertext)) {                       
                        $code = $this->planText($element->innertext);
                        if ($code != 'Mã hàng:') {
                            $product['code'] = $code;
                            break;
                        }
                    }
                }
                foreach($html->find('span[class=product-price]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['price'] = str_replace(array('VND','.',','), '', $this->planText($element->innertext));
                        break;
                    }
                }                
                $product['images'] = array();
                $product['content'] = $item['content'];
                foreach($html->find('img[id=image]') as $element) {                
                    if (!empty($element->src)) {
                        $imageUrl = 'http:' . $this->planText($element->src);
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
        
        $count = 1;
        foreach ($products as $product) {
            unset($product['id']); 
            unset($product['detail_url']);
            $product['website_id'] = $this->_website_id;
            $productModel = $this->getServiceLocator()->get('Products');
            $product['name'] = str_replace('Balô', 'Ba lô', $product['name']);
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
    
     public function planText($text) {  
         return trim(strip_tags($text));
     }
         
    // php index.php import products --verbose ctt 8
    // php index.php import products --verbose ctt 17
    // php index.php import products --verbose ctt 18
    // php index.php import products --verbose ctt 19
    public function ctt($category = 0) {  
     
        include_once getcwd() . '/include/simple_html_dom.php';        
        $productList = array(
            // Túi Xách Nữ
            array(   
                'disable' => 0,
                'category_id' => 8,                         
                'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=166&sortlist=product_new_desc',                               
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 10
            ),
            // Ba Lô Nữ
            array(   
                'disable' => 0,
                'category_id' => 17,                         
                'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=167&sortlist=product_new_desc',                               
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 10
            ),   
            // Túi Xách Nam
            array(   
                'disable' => 0,
                'category_id' => 19,                         
                'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=164&sortlist=product_new_desc',                               
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 10
            ),
            // Ba Lô Nam
            array(   
                'disable' => 0,
                'category_id' => 18,                         
                'url' => 'http://chothoitrang.com/home/index/ajaxcathome?id=165&sortlist=product_new_desc',                               
                'detail_url' => array(),
                'size_id' => array(),
                'max_images' => 10
            ), 
        );
        $importList = array();
        foreach ($productList as $item) {    
            if ($item['disable'] === 0 && $item['category_id'] == $category) {
                $importList[] = $item;
            }
        }
        
        if (empty($importList)) {
            echo 'List is empty!';
            exit;
        }
        
//        $productModel = $this->getServiceLocator()->get('Products');
//        $productModel->setPriorityAfterImported(array(
//            'website_id' => $this->_website_id,
//            'category_id' => $category,            
//        ));        
//        exit;
        
        // get detail url list
        foreach ($importList as &$item) {
            $page = 1;
            $stop = false;
            do {
                $content = app_file_get_contents($item['url'] . '&p=' . $page);
                if ($content == false) {
                    echo $item['url'] . ' Failed';
                    continue;
                }
                $html = str_get_html($content);
                foreach($html->find('a[class=product-image]') as $element) { 
                    if (!empty($element->href)) {
                        $detailUrl = trim($element->href); 
                        if (!in_array($detailUrl, $item['detail_url'])) {                         
                            $item['detail_url'][] = $detailUrl;
                        } else {
                            $stop = true;
                        }
                    }                    
                }                
                $page++;               
            } while ($stop == false);
        } 
        unset($item);         
        // end get detail url list
        
        $products = array();        
        foreach ($importList as $item) {           
            // get product detail                     
            foreach ($item['detail_url'] as $url) {
                $product = array(
                    'category_id' => $item['category_id']
                );                
                $content = app_file_get_contents($url);
                if ($content == false) {
                    echo $url . ' Failed' . PHP_EOL;
                    continue;
                }
                $content = strip_tags_content($content, '<script><style>', true);
                $html = str_get_html($content);                 
                foreach($html->find('div[class=product-name]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['name'] = $this->planText($element->innertext);
                        break;
                    }
                }               
                foreach($html->find('div[class=std]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['short'] = $this->planText($element->innertext);
                        break;
                    }
                }               
                foreach($html->find('div[class=overview]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['content'] = $element->innertext;
                        break;
                    }
                }
                foreach($html->find('span[class=price]') as $element) {                
                    if (!empty($element->innertext)) {
                        $product['price'] = db_float($element->innertext);
                        break;
                    }
                }
                $product['images'] = array();                 
                foreach($html->find('img[class=img-thumb]') as $element) {  
                    if (count($product['images']) >= $item['max_images']) {
                        break;
                    }
                    if (!empty($element->src)) {                        
                        $imageUrl = str_replace('360x420', '700x817', $this->planText($element->src));
                        if (empty($product['url_image'])) {
                            $product['url_image'] = $imageUrl;
                        }
                        if (!in_array($imageUrl, $product['images'])) {
                            $product['images'][] = $imageUrl;
                        }
                    }
                }  
                $product['import_colors'] = array();
                foreach($html->find('div[class=attributeconf-text attributeconf-color]') as $element) {                
                    if (!empty($element->innertext)) {                        
                        $subHtml = str_get_html($element->innertext);                 
                        foreach($subHtml->find('img') as $element1) {                
                            if (!empty($element1->src)) {
                                $imageUrl = str_replace('360x420', '700x817', $this->planText($element1->src));
                                $product['import_colors'][] = array(
                                    'name' => $this->planText($element1->title),                                    
                                    'url_image' => $this->planText($imageUrl)
                                );                              
                            }
                        }
                        break;
                    }
                } 
                
                $product['import_sizes'] = array();
                foreach($html->find('label[class=option-size]') as $element) {                
                    if (!empty($element->innertext)) {                        
                        $product['import_sizes'][] = array(
                            'name' => $this->planText($element->innertext),
                            'short' => $this->planText($element->innertext)
                        );
                    }
                }
               
                $product['import_attributes'] = array(); 
                foreach($html->find('div[class=product-attribute]') as $element) {
                    if (!empty($element->innertext)) {
                        $subHtml = str_get_html($element->innertext);                 
                        foreach($subHtml->find('div[class=attribute-title]') as $element1) {                
                            if (!empty($element1->innertext)) {
                                $attrName = $this->planText($element1->innertext);
                                break;
                            }
                        }
                        foreach($subHtml->find('div[class=attribute-text]') as $element1) {                
                            if (!empty($element1->innertext)) {
                                $attrValue = $this->planText($element1->innertext);
                                break;
                            }
                        }                       
                        if (!empty($attrName) && !empty($attrValue)) {
                            switch ($attrName) {
                                case 'Mã SP':
                                    $product['code'] = $attrValue;
                                    break;
                                case 'Thương hiệu':
                                    $product['brand_name'] = $attrValue;
                                    break;                                   
                                case 'Tình trạng':                              
                                    break;                                
                                /*
                                case 'Màu Sắc':                                                               
                                case 'Chất liệu':                             
                                case 'Kiểu dáng':                            
                                case 'Mục đích SD':                              
                                case 'Mùa phù hợp':  
                                 * 
                                 */                        
                                default:                                    
                                    $product['import_attributes'][] = array(
                                        'name' => $attrName,
                                        'value' => $attrValue,
                                    );
                                    break;
                            }
                        }                       
                    }
                }               
                $products[] = $product;               
            } 
            // end get product detail
        }
        $count = 1;
        $productModel = $this->getServiceLocator()->get('Products');      
        foreach ($products as $product) { 
            $product['website_id'] = $this->_website_id;
            $product['add_image_to_content'] = 1;
            $_id = $productModel->add($product);
            if ($_id) {  
                echo '[' . $count . '] ' . $product['code'] . ' Done' . PHP_EOL;
            } else {
                echo $product['name'] . ' Failed' . PHP_EOL;
            }
            $count++;
        }
        $productModel->setPriorityAfterImported(array(
            'website_id' => $this->_website_id,
            'category_id' => $category,            
        ));
    }

}