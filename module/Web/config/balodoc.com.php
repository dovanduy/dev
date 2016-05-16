<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /web/:controller/:action
            'web' => array(
                'type' => 'Hostname',
                'options' => array(
                    'route' => 'balodoc.com',                    
                ),                
            ),
        ),
    ),
    'website_id' => 1,
    'facebook_admins' => '980808445349037',
    'facebook_app_id' => '980808445349037',
    'facebook_app_secret' => '0b9565ce4b6a9c0bfb15cccbb2d0f8a8',
    'site_name' => 'Balodoc',   
    'head_meta' => array(
        'owner' => 'balodoc',
        'author' => 'balodoc',
        'distribution' => 'Global',
        'placename' => 'Việt Nam',
        'copyright' => 'Copyright © 2015 Balodoc.com. All Rights Reserved',
    ),
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/cache/balodoc',
                'dirPermission' => 0755,
                'filePermission' => 0666,
                'ttl' => 60*60,
                'namespace' => 'web'
            ),
        ),
        'plugins' => array(
            'exception_handler' => array('throw_exceptions' => false),
            'serializer'
        )
    ),
    'limit' => array(
        'products' => 36
    ),
    
    'commitment' => ' 
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p><span>1</span> Giao hàng TOÀN QUỐC</p>
                <p><span>2</span> Thanh toán khi nhận hàng</p>
                <p><span>3</span> Đổi trả trong <strong>7</strong> ngày</p>
                <p><span>4</span> Chất lượng đảm bảo</p>
                <p><span>5</span> Cam kết hàng giống hình</p>           
                <p><span>6</span> Hotline: <strong style="color:#D4232B">098 65 60 943</strong></p>
                <p><span>7</span> Skype: <a href="skype:thailvn?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" align="absmiddle" /></a></p>           
            </div>              
        </div>
    ',
    
    'commitment_and_guide' => ' 
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p><span>1</span> Giao hàng TOÀN QUỐC</p>
                <p><span>2</span> Thanh toán khi nhận hàng</p>
                <p><span>3</span> Đổi trả trong <strong>7</strong> ngày</p>
                <p><span>4</span> Chất lượng đảm bảo</p>
                <p><span>5</span> Cam kết hàng giống hình</p>           
            </div>  
            <div class="guide">  
                <div class="title">Hướng Dẫn Mua Hàng</div>
                <p><span>1</span> Mua hàng trực tiếp tại website balodoc.com</p>
                <p><span>2</span> Gọi điện thoại <strong style="color:#D4232B">098 65 60 943</strong> để mua hàng</p>           
                <p><span>3</span> Chat skype: <a href="skype:thailvn?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" align="absmiddle" /></a></p>
            </div>
        </div>
    '
    
);
