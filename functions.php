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
        print_r($obj);
        if ($exit)
            exit;
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
		if (@ mkdir($target)) {
			$stat = @ stat(dirname($target));
			$dir_perms = $stat ['mode'] & 0007777; // Get the permission bits.
			@ chmod($target, $dir_perms);
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
	if (empty($ip)) {
		$ip = $_SERVER["REMOTE_ADDR"];
	}
	$allowIp = array(
		'127.0.0.1',
		'10.10.8.25',		
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
        return $match[0];
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
    return str_replace(array(',', '.','VND',' '), '', $value); 
}

function db_float($value) {        
    return str_replace(array(',', '.','VND',' '), '', $value); 
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

function app_file_get_contents($url, $retry = true) {        
    $content = @file_get_contents($url);
    if ($content === false) {
       for ($i = 0; $i <= 99; $i++) {
           echo $url . ' Retying' . PHP_EOL;
           sleep(3);
           $content = @file_get_contents($url);
           if ($content !== false) {
               echo $url . ' Done' . PHP_EOL;
               return $content;
           }
       }
    } else {
        echo $url . ' Done' . PHP_EOL;    
        return $content;
    }       
    return false;
}

function app_file_put_contents($targetFileName, $content) {
    $retry = 99;
    do {
        $ok = @file_put_contents($targetFileName, $content);        
        echo $targetFileName . ' Retrying' . PHP_EOL;
        $retry--;
        sleep(3);
    } while ($ok === false && $retry > 0);
    return $ok;
}

if (!allowIp()) {
    exit;
}