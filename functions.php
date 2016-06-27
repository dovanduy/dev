<?php

if (!function_exists('d')) {

    function d($obj, $exit = false) {
        var_dump($obj);
        if ($exit)
            exit;
    }

}

if (!function_exists('p')) {
   
    function p($obj, $exit = false) {
        echo '<pre>' . print_r($obj, true) . '</pre>';
        if ($exit)
            exit;
    }

}

if (!function_exists('app_array_field')) {
    function app_array_field($arr, $field, $toString = false) {
       $result = array();
       if ($arr) {
           foreach ($arr as $item) {
               $result[] = $item[$field];
           }
       }
       $result = array_unique($result);
       if ($toString) {
           $result = implode(',', $result);
       }
       return $result;
    }
}
    
if (!function_exists('curl_file_create')) {

    function curl_file_create($filename, $mimetype = '', $postname = '') {
        return "@$filename;filename="
                . ($postname ? : basename($filename))
                . ($mimetype ? ";type=$mimetype" : '');
    }

}

if (!function_exists('mongo_id')) {

    function mongo_id() {
        return substr(md5(mt_rand()/mt_getrandmax()) . md5(mt_rand()/ mt_getrandmax()), 1, 24);
    }

}

if (!function_exists('voucher_code')) {

    function voucher_code() {
        return strtoupper(substr(md5(mt_rand()/mt_getrandmax()) . md5(mt_rand()/ mt_getrandmax()), 1, 5));
    }

}

if (!function_exists('generate_token')) {

    function generate_token() {
        return strtoupper(substr(md5(mt_rand()/mt_getrandmax()) . md5(mt_rand()/ mt_getrandmax()), 1, 30));
    }

}

if (!function_exists('app_plan_text')) {

    function app_plan_text($text) {  
         return trim(strip_tags($text));
    }

}
 
     
if (!function_exists('str_no_vi')) {
    function str_no_vi($string, $length = 100, $strSymbol = '-', $isToLower = 1)
	{
		$string = strip_tags_content($string);
		$arrCharFrom = array(
            "ạ","á","à","ả","ã","Ạ","Á","À","Ả","Ã",
            "â","ậ","ấ","ầ","ẩ","ẫ","Â","Ậ","Ấ","Ầ","Ẩ","Ẫ",
            "ă","ặ","ắ","ằ","ẳ","ẫ","Ă","Ắ","Ằ","Ẳ","Ẵ","Ặ",
            "ê","ẹ","é","è","ẻ","ẽ","Ê","Ẹ","É","È","Ẻ","Ẽ",
            "ế","ề","ể","ễ","ệ","Ế","Ề","Ể","Ễ","Ệ",
            "ọ","ộ","ổ","ỗ","ố","ồ","Ọ","Ộ","Ổ","Ỗ","Ố","Ồ","Ô","ô",
            "ó","ò","ỏ","õ","Ó","Ò","Ỏ","Õ",
            "ơ","ợ","ớ","ờ","ở","ỡ",
            "Ơ","Ợ","Ớ","Ờ","Ở","Ỡ",
            "ụ","ư","ứ","ừ","ử","ữ","ự","Ụ","Ư","Ứ","Ừ","Ử","Ữ","Ự",
            "ú","ù","ủ","ũ","Ú","Ù","Ủ","Ũ",
            "ị","í","ì","ỉ","ĩ","Ị","Í","Ì","Ỉ","Ĩ",
            "ỵ","ý","ỳ","ỷ","ỹ","Ỵ","Ý","Ỳ","Ỷ","Ỹ",
            "đ","Đ"
        );
        $arrCharEndNoVn = array(
            "a","a","a","a","a","A","A","A","A","A",
            "a","a","a","a","a","a","A","A","A","A","A","A",
            "a","a","a","a","a","a","A","A","A","A","A","A",
            "e","e","e","e","e","e","E","E","E","E","E","E",
            "e","e","e","e","e","E","E","E","E","E",
            "o","o","o","o","o","o","O","O","O","O","O","O","O","o",
            "o","o","o","o","O","O","O","O",
            "o","o","o","o","o","o",
            "O","O","O","O","O","O",
            "u","u","u","u","u","u","u","U","U","U","U","U","U","U",
            "u","u","u","u","U","U","U","U",
            "i","i","i","i","i","I","I","I","I","I",
            "y","y","y","y","y","Y","Y","Y","Y","Y",
            "d","D"
        );
        $arrCharFilter = str_replace($arrCharFrom,$arrCharEndNoVn,trim($string));
		if (strlen($arrCharFilter) > $length && $length > 0) {
			$arrCharFilter = substr($arrCharFilter, 0, $length);
		}		
		$arrCharFilter = preg_replace('/[\W|_]+/', $strSymbol, $arrCharFilter);
		return $isToLower ? strtolower($arrCharFilter) : $arrCharFilter;
	}
}

if (!function_exists('name_2_url')) { 
    function name_2_url($name, $ext = '') {
        return str_no_vi(trim($name), 150, '-') . $ext;
    }
}

if (!function_exists('mk_dir')) { 
	function mk_dir($target) {
		// from php.net/mkdir user contributed notes
		if (file_exists($target)) {
			if (!@ is_dir($target))
				return false;
			else
				return true;
		}

		// Attempting to create the directory may clutter up our display.
		if (@mkdir($target)) {
			$stat = @stat(dirname($target));
			$dir_perms = $stat ['mode'] & 0007777; // Get the permission bits.
			@chmod($target, $dir_perms);
			return true;
		} else {
			if (is_dir(dirname($target)))
				return false;
		}
		// If the above failed, attempt to create the parent node, then try again.
		if (mk_dir(dirname($target)))
			return mkdir($target);

		return false;
	}
}

function allowIp($ip = null) {	
    return 1;
	if (isset($_SERVER["REMOTE_ADDR"]) && empty($ip)) {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	$allowIp = array(
		'127.0.0.1',
		'10.10.8.25',	
		'42.112.89.165',
		'115.78.209.220',
		'112.213.89.30',
        '1.52.32.16'
	);
	return in_array($ip, $allowIp) ? 1 : 0;
}

function domain() {
    if (!isset($_SERVER['SERVER_NAME'])) {
        return null;
    }
    $domain = 'localhost';
    preg_match("/^([a-zA-Z0-9-.]+)(.com.vn|.vn|.com|.in|.co|.info|.name|.dev)$/", $_SERVER['SERVER_NAME'], $match);   
    if (!empty($match[0])) {
        $domain = $match[0];
    }
    if (strpos($domain, 'www.') !== false) {
        $domain = str_replace('www.', '', $domain);
    }
    return $domain;
}

function strip_tags_content($text, $tags = '', $invert = FALSE) { 
    preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
    $tags = array_unique($tags[1]);
    if (is_array($tags) AND count($tags) > 0) {
        if ($invert == FALSE) {
            return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        } else {
            return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
        }
    } elseif ($invert == FALSE) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
    }
}

function content_by_tag($stringText = "", $tag = "", $isPlanText = false)
{
    if (!empty($stringText) && !empty($tag)) {      
        preg_match("/<$tag>(.*)<\/$tag>/", $stringText, $match);
        if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR || sizeof($match) == 0) {
            return "";
        } else {
            $string = "";
            if ($isPlanText) {
                $string = str_replace("<![CDATA[", "", $match[1]);
                $string = strip_tags(str_replace("]]>", "", $string));
            } else {
                $string = $match[1];
            }
            if (($_index = strpos($string, "</$tag>")) !== false) {
                $string = substr($string, 0, $_index);
            }
            preg_match("/&#[xX][a-fA-F0-9]+/", $string, $match);
            if (preg_last_error() == PREG_BACKTRACK_LIMIT_ERROR || sizeof($match) == 0) {
                return trim($string);
            } else {
                return html_entity_decode(trim($string), ENT_QUOTES);
            }
        }
    }
    return "";
}
   
if (!function_exists('truncate')) { 
    function truncate($text, $length = 100, $options = array())
    {
        mb_internal_encoding('UTF-8');
        $default = array(
            'ending' => '...', 'exact' => false
        );
        $options = array_merge($default, $options);
        extract($options);

        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }

        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;
        return $truncate;
    }
}
    
if (!function_exists('app_money_format')) {
    function app_money_format($value) {        
        if (is_numeric($value)) {
            return number_format($value, 0, ',', '.') . ' VND';
        }
        return $value;
    }
}

function datetime_format($value = '', $format = 'Y-m-d H:i') { 
    if (empty($value)) {
        return date($format);
    }
    if (is_numeric($value)) {
        return date($format, $value);
    }
    return $value;
}

/**
 * Date format for application
 *    
 * @author thailvn
 * @param int $time Input DateTime        
 * @return string Date
 */
function app_datetime_format($time = null, $onlyDate = false) {
	if (empty($time) || !is_numeric($time)) {
		return false;
	}
	if($onlyDate == true){
		return date('Y年m月d日', $time);
	}        
	$minuteAgo = ceil((time() - $time) / 60);
	if ($minuteAgo > 0 && $minuteAgo < 60) {
		return str_pad($minuteAgo, 2, '0', STR_PAD_LEFT) . "分前";
	} elseif ($minuteAgo > 0 && $minuteAgo < 24 * 60) {
		return str_pad(ceil($minuteAgo / 60), 2, '0', STR_PAD_LEFT) . "時間前";
	}
	return date('Y/m/d', $time);
}
	
function db_int($value) {        
    return (int) str_replace(array(',', '.','VND',' '), '', $value); 
}

function db_float($value) {        
    return (float) str_replace(array(',', '.','VND','đ',' '), '', trim($value)); 
}

/**
 * File: SimpleImage.php
 * Author: Simon Jarvis
 * Modified by: Miguel Fermín
 * Based in: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
 * 
 * This program is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU General Public License 
 * as published by the Free Software Foundation; either version 2 
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
 * GNU General Public License for more details: 
 * http://www.gnu.org/licenses/gpl.html
 */
if (!class_exists('SimpleImage')) { 
	class SimpleImage {
	   
		public $image;
		public $image_type;
        
		public function __construct($filename = null){
			if (!empty($filename)) {
				$this->load($filename);
			}
		}
        
		public function load($filename) {
			$image_info = @getimagesize($filename);
            if (!empty($image_info)) {
                $this->image_type = $image_info[2];
                if ($this->image_type == IMAGETYPE_JPEG) {
                    $this->image = imagecreatefromjpeg($filename);
                } elseif ($this->image_type == IMAGETYPE_GIF) {
                    $this->image = imagecreatefromgif($filename);
                } elseif ($this->image_type == IMAGETYPE_PNG) {
                    $this->image = imagecreatefrompng($filename);
                } else {
                    //throw new Exception("The file you're trying to open is not supported");                    
                    return false;
                }
                return true;
            }
            return false;
		}
        
		public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
			if ($image_type == IMAGETYPE_JPEG) {
				imagejpeg($this->image,$filename,$compression);
			} elseif ($image_type == IMAGETYPE_GIF) {
				imagegif($this->image,$filename);         
			} elseif ($image_type == IMAGETYPE_PNG) {
				imagepng($this->image,$filename);
			}
			if ($permissions != null) {
				chmod($filename,$permissions);
			}
		}
        
		public function output($image_type=IMAGETYPE_JPEG, $quality = 80) {
			if ($image_type == IMAGETYPE_JPEG) {
				header("Content-type: image/jpeg");
				imagejpeg($this->image, null, $quality);
			} elseif ($image_type == IMAGETYPE_GIF) {
				header("Content-type: image/gif");
				imagegif($this->image);         
			} elseif ($image_type == IMAGETYPE_PNG) {
				header("Content-type: image/png");
				imagepng($this->image);
			}
		}
        
		public function getWidth() {
			return imagesx($this->image);
		}
        
		public function getHeight() {
			return imagesy($this->image);
		}
        
		public function resizeToHeight($height) {
			$ratio = $height / $this->getHeight();
			$width = round($this->getWidth() * $ratio);
			$this->resize($width,$height);
		}
        
		public function resizeToWidth($width) {
			$ratio = $width / $this->getWidth();
			$height = round($this->getHeight() * $ratio);
			$this->resize($width,$height);
		}
		public function square($size) {
			$new_image = imagecreatetruecolor($size, $size);
			if ($this->getWidth() > $this->getHeight()) {
				$this->resizeToHeight($size);				
				imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
				imagealphablending($new_image, false);
				imagesavealpha($new_image, true);
				imagecopy($new_image, $this->image, 0, 0, ($this->getWidth() - $size) / 2, 0, $size, $size);
			} else {
				$this->resizeToWidth($size);
				
				imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
				imagealphablending($new_image, false);
				imagesavealpha($new_image, true);
				imagecopy($new_image, $this->image, 0, 0, 0, ($this->getHeight() - $size) / 2, $size, $size);
			}
			$this->image = $new_image;
		}
	   
		public function scale($scale) {
			$width = $this->getWidth() * $scale/100;
			$height = $this->getHeight() * $scale/100; 
			$this->resize($width,$height);
		}
	   
		public function resize($width,$height) {
			$new_image = imagecreatetruecolor($width, $height);
			
			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
			$this->image = $new_image;   
		}
        
		public function cut($x, $y, $width, $height) {
			$new_image = imagecreatetruecolor($width, $height);	
			imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			imagecopy($new_image, $this->image, 0, 0, $x, $y, $width, $height);
			$this->image = $new_image;
		}
        
		public function maxarea($width, $height = null)	{
			$height = $height ? $height : $width;
			
			if ($this->getWidth() > $width) {
				$this->resizeToWidth($width);
			}
			if ($this->getHeight() > $height) {
				$this->resizeToheight($height);
			}
		}
		
		public function minarea($width, $height = null)	{
			$height = $height ? $height : $width;
			
			if ($this->getWidth() < $width) {
				$this->resizeToWidth($width);
			}
			if ($this->getHeight() < $height) {
				$this->resizeToheight($height);
			}
		}
        
		public function cutFromCenter($width, $height) {
			
			if ($width < $this->getWidth() && $width > $height) {
				$this->resizeToWidth($width);
			}
			if ($height < $this->getHeight() && $width < $height) {
				$this->resizeToHeight($height);
			}
			
			$x = ($this->getWidth() / 2) - ($width / 2);
			$y = ($this->getHeight() / 2) - ($height / 2);
			
			return $this->cut($x, $y, $width, $height);
		}
        
		public function maxareafill($width, $height, $red = 0, $green = 0, $blue = 0) {
			$this->maxarea($width, $height);
			$new_image = imagecreatetruecolor($width, $height); 
			$color_fill = imagecolorallocate($new_image, $red, $green, $blue);
			imagefill($new_image, 0, 0, $color_fill);        
			imagecopyresampled(	$new_image, 
								$this->image, 
								floor(($width - $this->getWidth())/2), 
								floor(($height-$this->getHeight())/2), 
								0, 0, 
								$this->getWidth(), 
								$this->getHeight(), 
								$this->getWidth(), 
								$this->getHeight()
							); 
			$this->image = $new_image;
		}
        
	}
}

function app_echo($message, $flag = true) {
    if ($flag) {
        echo $message;
    }
}

function app_file_get_contents($url, $retry = true, $echoFlag = false) {        
    $content = @file_get_contents($url);
    if ($content === false) {
       for ($i = 0; $i <= 99; $i++) {           
           app_echo($url . ' Retying' . PHP_EOL, $echoFlag);           
           sleep(3);
           $content = @file_get_contents($url);
           if ($content !== false) {
               app_echo($url . ' Done' . PHP_EOL, $echoFlag);
               return $content;
           }
       }
    } else {
        app_echo($url . ' Done' . PHP_EOL, $echoFlag); 
        return $content;
    }   
    return false;
}

function app_file_put_contents($targetFileName, $content, $echoFlag = false) {
    $retry = 99;
    do {
        $ok = @file_put_contents($targetFileName, $content);        
        app_echo($targetFileName . ' Retrying' . PHP_EOL, $echoFlag);
        $retry--;
        sleep(3);
    } while ($ok === false && $retry > 0);
    return $ok;
}

function app_get_fb_share_content($product) {    
    $price = app_money_format($product['price']);
    if (!empty($product['original_price'])) {
        $price .= ' (giá trước đây ' . app_money_format($product['original_price']) . ')';
    }   
    $short = str_replace(PHP_EOL, ' ', mb_ereg_replace('!\s+!', ' ', $product['short'])); 
    if (in_array(substr($short, -1), ['.', ',', ';', '-', '_'])) {
        $short = substr($short, 0, strlen($short) - 1);
    }
    $short .= " - chi tiết {$product['short_url']}";
    $data = [
        'message' => implode(PHP_EOL, [
            $product['name'],
            "Giá {$price}",             
            "Đặt hàng: 097 443 60 40 - 098 65 60 997",                 
            $short,
            'Giao hàng TOÀN QUỐC',            
        ]),
        'link' => $product['url'],
        'picture' => $product['image_facebook'],
        'caption' => 'vuongquocbalo.com'
    ];
    if (!empty($product['tags'])) {
        $data['tags'] = is_array($product['tags']) ? implode(',', $product['tags']) : $product['tags'];
    }
    return $data;
}

if (!function_exists('app_random_value')) {
    function app_random_value($array, $default=null)
    {
        $k = mt_rand(0, count($array) - 1);
        return isset($array[$k])? $array[$k]: $default;
    }
}

function app_get_comment_message($random = true) {
    // Câu nói hay bất hủ về cuộc sống
    $data1 = [
        'Có nhiều người lạ lắm, mặc dù họ chẳng hề có ý định dành cho bạn một phần nhỏ xíu nào trong cuộc đời họ nhưng lúc nào cũng muốn là một phần rất quan trọng trong cuộc đời bạn.',
        'Thời gian một người bỏ ra cho bạn là tình yêu của người đó dành cho bạn. Không phải ai rảnh sẽ bỏ ra nhiều hơn mà là ai yêu nhiều hơn sẽ cố gắng ở bên bạn nhiều hơn',
        'Không phải vết thương nào chảy máu cũng đều đau. Có đôi khi vết thương không nhìn thấy máu mới thật sự là vết thương đau nhất.',
        'Đừng lập gia đình sớm, dù bất cứ lý do nào đừng vội khi chưa sẵn sàng, chưa từng trải chưa hiểu được chung sống là một thử thách to lớn thế nào.',
        'Đừng mơ trong cuộc sống mà hãy sống trong giấc mơ.',
        'Dù bạn có vấp ngã hàng trăm lần thì cũng đừng bỏ cuộc. Hãy đứng dậy.',
        'Cuộc sống cũng như một cuốn sách. Khi gặp chuyện buồn hãy tự mình bước sang một trang mới chứ đừng gập sách lại.',
        'Lời nói của bạn có sức mạnh làm tan vỡ trái tim, hàn gắn mối quan hệ, khai sáng con người và thay đổi thế giới. Hãy nói có trách nhiệm và đừng quên trách nhiệm với lời nói của bạn.',
        'Con đường đi tới thành công không bao giờ thẳng. Bạn sẽ phải trả giá bằng những ngã rẽ sai lầm nhiều lần trước khi tìm được con đường đúng nhất.',
        'Bạn hãy nhớ sau này bạn sẽ chỉ hối tiếc về những việc bạn đã không làm khi có cơ hội, chứ không phải những việc bạn đã từng làm. Vì thế hãy hành động ngay khi bạn có cơ hội.',
        'Khác biệt giữa một thách thức và một cơ hội chỉ nằm ở thái độ của bạn. Khi niềm tin của bạn lớn hơn nỗi sợ hãi, thách thức sẽ biến thành cơ hội của bạn.',
        'Còn gì đẹp bằng một trái tim đang tan vỡ vẫn có thể tiếp tục tin vào tình yêu. Còn gì cao cả bằng một con người đang trải qua bão tố cuộc đời mình vẫn tiếp tục có thể nâng đỡ những người khác.',
        'Đôi khi nếu bạn chờ đợi quá nhiều thứ cùng lúc, rất có thể bạn sẽ ra về trắng tay.',
        'Nếu bạn còn sợ làm điều gì đó chỉ vì người đời sẽ phán xét thì tin mừng là thời buổi này chả ai buồn nhớ điều bạn làm quá một tuần.',
        'Hãy tự biết cách gây áp lực cho chính bản thân để vươn lên và tỏa sáng. Bởi vì không ai sẽ làm điều đó thay cho bạn.',
        'Một trong vấn đề nghiêm trọng của thế giới này đó là những kẻ khờ và mù quáng thì luôn quá chắc chắn về bản thân, còn những người khôn ngoan thì lại đầy nghi hoặc.',
        'Thành công chỉ có thể đạt được bởi những người biết rõ thất bại là không thể tránh khỏi.',
        'Cuộc sống vẫn vậy nếu nó lấy đi thứ gì của bạn thế nào nó cũng bù lại cho bạn thứ khác chỉ có điều là bạn có chịu đi tìm hay không thôi.',
        'Khi chúng ta mong ước cuộc đời không nghịch cảnh hãy nhớ rằng cây sồi trở nên mạnh mẽ trong gió ngược, và kim cương hình thành dưới áp lực.',
    ];
    
    // Những câu chúc ngày mới hay và ý nghĩa nhất
    $data2 = [
        'Tặng anh một món quà nhỏ bé tên là “Buổi sáng tốt lành!!” được gói bằng sự chân thành, buộc bằng sự quan tâm và dính keo bằng lời cầu nguyện của em để anh được an bình và hạnh phúc cả ngày…',
        'Hoàng tử của em, dậy thôi nào, qua cái thời làm nũng rồi nhé. Dậy đi, Linh tinh là không chơi với anh nữa đâu nhé. Yêu anh! chúc anh ngày mới tràn ngập niềm vui và hạnh phúc! Chụt!',
        'Ông mặt trời mọc rồi kìa, với nụ cười ấm áp biết bao! Ông chúc anh một buổi sáng tốt lành và mong anh sẽ có một ngày thật tuyệt!',
        'Anh dậy chưa? Anh vẫn còn ngủ phải không? Em không muốn ôm một “chú heo con” đâu nhé! Anh dậy chuẩn bị đi làm đi nhé! Anh phải nhớ 3 điều em dặn này: 1. Nhớ em; 2. Thương em; 3. Yêu em. Anh bắt đầu thực hiện kể từ lúc anh nhận được tin nhắn này!',
        'Anh ơi, dậy chưa? Anh đừng nướng kĩ quá, khét rùi! Em nhìn thấy từ phía nhà anh…”ôi! có khói bốc lên cao rồi kìa” , em sợ ko nhận ra anh mất. Hé hé…',
        'Tình yêu là gì? Tình yêu chính là điều khiến điện thoại của em lên tiếng chuông mỗi khi anh gửi tin nhắn. Chúc anh luôn sẵn sàng cho ngày mới nhiều thành công.',
        'Em gửi cho anh 1000 nụ cười, bây giờ anh hãy cười đi nhé. Còn 999 nụ cười anh hãy để dành dưới gối, mỗi sáng thức dậy anh hãy lấy ra 1 nụ cười nhá. Vì em mong muốn anh luôn vui vẻ!^^',
        'Mặt trời đã hé rạng đằng Đông và những con chim đang ca hót vui vẻ. Bươm bướm đang bay lượn quanh những cành hoa. Đã đến lúc dậy và ngáp một cái thật to nào! Chúc buổi sáng tốt lành !!',
        'Tối qua em đi ngủ với một nụ cười vì em biết em sẽ mơ thấy anh… Và sáng nay em thức dậy cùng với một nụ cười vì em biết anh không là một giấc mơ.',
        'Chúc buổi sáng an lành, 1 ngày làm việc may mắn và thành công,chúc bạn luôn vui vẻ tràn ngập tiếng cười.',
        'Khi anh nói với em rằng “Chúc một buổi sáng tốt lành!”. Đó không đơn thuần là 1 SMS mà còn là một thông điệp: “Anh nhớ em ngay khi anh vừa tỉnh giấc”!!!^^',
        'Một vòng tay ban đêm sưởi ấm trái tim, một nụ hôn ban đêm thắp sáng bình minh và một buổi sáng tốt lành để bắt đầu một ngày cho anh!!',
        'Một ngày đối với anh bao giờ cũng vui và trọn vẹn hơn khi có em ở bên. Không phải chỉ có em cần anh, mà anh cũng cần em thật nhiều! Ngày mới vui vẻ em nhé!',
        'Chúc anh buổi sáng tốt lành, thật sự tốt lành đủ để anh có thể mỉm cười được ấy!',
        'Đêm đã kết thúc để bắt đầu ngày mới. Chúc nụ cười của anh như những vệt nắng lấp lánh của bình minh và để âu lo lại với màn đêm.',
        'Trên thiên đường có 10 thiên thần: 5 thiên thần đang chơi đùa, 4 thiên thần đang nói chuyện, 1 thiên thần đang ngủ. Thiên thần đang ngủ là em đó, em đậy đi ăn sáng với anh nhé !!!',
        'Em biết không? Một ngày đối với anh bao giờ cũng vui và trọn vẹn hơn khi anh cầm điện thoại send cho em 1 SMS chúc ngày mới tốt lành và đặc biệt hơn là gửi vòng tay yêu thương của anh đến em nữa!',
        'Ánh trăng bị xóa tan rồi em, sương mù cũng hết rồi cô bé của anh, dậy thôi em, chúng mình đi chơi nhé.',
        'Trước khi chưa yêu em anh luôn làm bạn với chiếc đồng hồ báo thức vì nó giúp anh thức dậy đúng giờ mỗi sáng nhưng từ khi có em rồi thì anh đã quên khuấy nó lúc nào không biết vì anh biết em rất quan trọng với anh. Anh muốn em đánh thức anh mỗi sáng không chỉ hôm nay và mãi mãi cho đến hết đời cơ. Ngày mới hạnh phúc em nhé! hi hi',
        'Tình yêu với em đã khiến mỗi sáng anh thức dậy có ý nghĩa và đẹp hơn. Anh đã biết em quan trọng với anh như thế nào!!!',
    ];
    
    // Câu chúc sinh nhật hay nhất
    $data3 = [
        'Nhân ngày sinh nhật, anh chúc em nhan sắc “quyết liệt” thăng hoa, tiền tài ào ào thăng tiến và tình yêu “tưng bừng” bùng nổ.',
        'Em chúc mừng anh trai mọi điều tốt lành. Mai mốt em có con, anh làm cha đỡ đầu cho nó nha (chứ không phải là cha thiệt nha).',
        'Hãy để những lời chúc sâu lắng của tôi luôn ở bên cạnh cuộc sống tuyệt vời của bạn. Tôi hy vọng trong năm tới bạn luôn khỏe mạnh và thuận buồm xuôi gió trong công việc. Sinh nhật vui vẻ!',
        'Chúc bạn luôn luôn “vui vẻ, tươi trẻ, mạnh khoẻ, tính tình mát mẻ, cuộc đời suôn sẻ” và luôn luôn “tươi cười, yêu đời, ngời ngời sức sống” ^^ Happy Birth Day!',
        'Ước mong anh là con suối trong những ngày nắng gắt, để em… rửa chân cho mát.',
        'Một ngày bình yên, êm ấm bên người mà mình yêu. Nhưng nhớ đừng làm gì đi quá giới hạn “nhạy cảm” nhé bạn',
        'Hãy luôn giữ nét baby và giọng cười trời cho của anh nha. Đừng thay đổi hình tượng của em nha anh, một anh chàng baby đáng… đánh đòn.',
        'Nhân dịp sinh nhật lần thứ… của em, chúc em luôn tươi khỏe, trẻ đẹp. Cầu mong những gì may mắn nhất, tốt đẹp nhất và hạnh phúc nhất sẽ đến với em trong tuổi mới.',
        'Chúc ấy luôn ấm áp, cả bên trong lẫn bên ngoài.',
        'Tiệc sinh nhật có lợi cho sức khoẻ con người lắm nhe. Nghiên cứu cho thấy ai càng nhiều tiệc sinh nhật thì sống càng lâu!',
        'Giữ kín tuổi thật của bạn nhé, bí mật quốc gia đấy!',
        'Tuổi mới ăn no chóng lớn, tiền bạc đầy nhà, gà đầy chuồng nhé bạn.',
        'Hôm nay không như ngày hôm qua, hôm nay là một ngày đặc biệt, là ngày mà một thiên thần đáng yêu đã có mặt trên thế giới cách đây… năm. Luôn mỉm cười và may mắn nhé.',
        'Chúc ấy tuổi mới ngày càng đẹp trai hơn, tiền luôn đầy túi, bụng ngày càng nhiều múi và người yêu chất cao hơn núi.',
        'Chúc mọi điều ước trong ngày sinh nhật của bạn đều trở thành hiện thực, hãy thổi nến trên bánh sinh nhật để ước mơ được nhiệm màu.',
        'Chúc bạn sinh nhật vui vẻ, sang tuổi mới xinh lại càng xinh, duyên lại càng duyên, yêu lại càng yêu.',
        'Xin chúc mừng sinh nhật của một trong những công dân xinh đẹp, mỹ miều, kiêu sa yêu kiều nhất trên quả đất này.',
        'Mừng ngày sinh nhật của em, mừng ngày đó em sinh ra đời cùng ngàn ngôi sao tỏa sáng.',
        'Sinh nhật vui vẻ, 1 ngày lượm được cọc tiền, 1 tuần lượm được túi tiền, 1 tháng lượm được va li tiền, cả năm ôm tiền mà ngủ.',
        'Chúc mừng sinh nhật anh, sang một tuổi mới, thành công mới, nhiều niềm vui mới, nhiều thắng lợi mới, và nếu có thể thì cả người yêu mới nữa nhé. Yêu em đây nè!',
    ];
    
    // Status hay
    $data4 = [
        'Chọn người yêu chỉ cần ba điều này là đủ ✓ Không lừa mình ✓ Không làm mình tổn thương ✓ Bằng lòng ở bên mình',
        'Trong trái tim em đã có anh. Ai tốt hơn anh em cũng không cần.',
        'Thà tỏ tình rồi thất bại còn hơn ăn hại cả đời làm anh trai.',
        'Yêu là bình minh mỗi sớm có anh bên mình, là hoàng hôn mênh mang từng con phố, mình cùng tay trong tay đi giữa cuộc đời, nguyện thề luôn bên nhau mãi.',
        'Khi chia tay mà vẫn muốn là bạn thì chỉ có 2 lý do: Vẫn còn yêu nhau và Không có cái gọi là tình yêu giữa họ',
        'Tình yêu của anh nhẹ nhàng như gió, mỏng manh như nắng, và rồi để lại cho em cay đắng ngút ngàn.',
        'Sống cùng một thành phố, dưới cùng một bầu trời, chưa bao giờ em gặp lại anh – người yêu cũ',
        'Có duyên sẽ gặp lại, có nợ sẽ tìm về, đủ yêu ta sẽ trọn đời bên nhau.',
        'Hãy im lặng anh nhé, vì kể từ giờ, em sẽ không tin vào anh nữa.',
        'Em nói hai ta không chung đường, không sao cả, tôi sẵn sàng vì em thay đổi lộ trình.',
        'Làm gì có ai muốn cô đơn, chỉ là không muốn phải thất vọng mà thôi.',
        'Em mệt lắm khi em nói mà chẳng ai nghe, em buồn mà không ai thấu, em cô đơn mà không thể một người ở bên',
        'Tôi vẫn đợi ai đó đến yêu tôi nghiêm túc.',
        'Bạn trai tâm lý sẽ biết lúc nào nên lắng nghe, lúc nào nên lên tiếng, và đặc biệt là lúc nào nên nắm tay và ôm cô ấy vào lòng.',
        'Anh trốn đâu kỹ quá, em tìm hoài không thấy.',
        'Nhớ nhé! Yêu ít thôi nhưng miễn là dài lâu. Hứa ít thôi nhưng miễn là làm được.',
        'Anh là một, là riêng, là duy nhất đối với em.',
        'Có một người dù thế nào đi nữa tôi cũng không muốn gặp lại vì gặp lại tôi sợ mình sẽ lại rung động.',
        'Ngày em đến, em dạy anh cách yêu thương trọn vẹn một người. Ngày em đi, em chưa dạy anh cách quên đi một người anh từng trọn vẹn yêu thương.',
        'Đừng rời xa tôi vì tôi lỡ yêu người mất rồi…',
    ];
    $data = array_merge($data1, $data2, $data3, $data4);
    if ($random) {
        $value = app_random_value($data);
        if (in_array($value, $data1)) {
            $value = '**CÂU NÓI HAY BẤT HỦ VỀ CUỘC SỐNG**' . PHP_EOL . $value;
        } elseif (in_array($value, $data2)) {
            $value = '**CÂU CHÚC NGÀY MỚI HAY VÀ Ý NGHĨA NHẤT**' . PHP_EOL . $value;
        } elseif (in_array($value, $data3)) {
            $value = '**CÂU CHÚC SINH NHẬT HAY NHẤT**' . PHP_EOL . $value;
        } elseif (in_array($value, $data4)) {
            $value = '**STATUS HAY**' . PHP_EOL . $value;
        }       
        return PHP_EOL . $value;
    }
    return $data;
}

function app_get_comment_icon($random = true) {
    $data = [
        'https://sc.mogicons.com/c/200.jpg',
        'https://sc.mogicons.com/c/363.jpg',
        'https://sc.mogicons.com/c/276.jpg',
        'https://sc.mogicons.com/c/217.jpg',
        'https://sc.mogicons.com/c/164.jpg',
        'https://sc.mogicons.com/c/248.jpg',
        'https://sc.mogicons.com/c/326.jpg',
        'https://sc.mogicons.com/c/396.jpg',
        'https://sc.mogicons.com/c/191.jpg',
        'https://sc.mogicons.com/c/210.jpg',
        'https://sc.mogicons.com/c/283.jpg',
        'https://sc.mogicons.com/c/241.jpg',
        'https://sc.mogicons.com/c/266.jpg',
        'https://sc.mogicons.com/c/350.jpg',
        'https://sc.mogicons.com/c/274.jpg',
    ]; 
    if ($random) {
        return app_random_value($data);
    }
    return $data;
}

if (!allowIp()) {
    exit;
}

if (!function_exists('app_facebook_tags')) {
    function app_facebook_tags($facebookId = null) {
        $ids = [
            //'10206637393356602', // Thai Lai thailvn@gmail.com         
            '125965971158216', // Ken Ken mail.vuongquocbalo.com@gmail.com        
            '835521976592060', // Ngoc Nguyen My myngoc204@yahoo.com
            '1723524741251993', // Duc Tin
            '114752562282451', // fb.khaai@gmail.com
            '116860312071059', // fb.hoaian@gmail.com
            '103432203421638', // kinhdothoitrang@outlook.com
        ];
        if (!empty($facebookId)) {
            $result = array();
            foreach ($ids as $id) {
                if ($id != $facebookId) {
                    $result[] = $id;
                }
            }
            return $result;
        }
        return $ids;
    }
}

if (!function_exists('app_facebook_groups')) {
    function app_facebook_groups() {
        return [
             //'392392084295942', // https://www.facebook.com/groups/donnhahn18899/            
            '1479744482314512', // https://www.facebook.com/groups/Thuducquan2quan9/
            '795251457184853', // https://www.facebook.com/groups/24hmuabanraovat/
            '113462365452492', // https://www.facebook.com/groups/795251457184853/ HỘI MUA BÁN-RAO VẶT-GIAO LƯU KẾT BẠN TOÀN QUỐC           
            '538895742888736', //https://www.facebook.com/groups/baneverything/
            '794951187227341', //https://www.facebook.com/groups/chosaletonghopbmt/
        ];
    }
}
    