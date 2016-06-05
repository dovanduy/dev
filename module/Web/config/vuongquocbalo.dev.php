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
                    'route' => 'vuongquocbalo.dev',                    
                ),                
            ),
        ),
    ),
    'st_host' => 'http://vuongquocbalo.dev/web',
    'website_id' => 1,
    
    'facebook_admins' => '261013080913491',
    'facebook_app_id' => '261013080913491',
    'facebook_app_secret' => '0eb33476da975933077a4d4ad094479b',
    'facebook_tag_ids' => array(),
    
    'google_app_id' => '1035781535742-6a2584sfhodr6hbgq6227aektflm27rf.apps.googleusercontent.com',
    'google_app_secret' => '60KJBrtGo7dlJcBsypcYJPGS',
    'google_app_redirect_uri' => 'http://vuongquocbalo.dev/glogin',
    
    'admin_user_id' => array(1, 86, 102),
    
    'site_name' => 'vuongquocbalo',   
    'head_meta' => array(
        'owner' => 'vuongquocbalo',
        'author' => 'vuongquocbalo',
        'distribution' => 'Global',
        'placename' => 'Việt Nam',
        'copyright' => 'Copyright © 2016 vuongquocbalo.com. All Rights Reserved',
    ),
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/cache/vuongquocbalo',
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
        'products' => 18
    ),
    
    'display_page' => 10,
    
    'commitment' => '
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p>✓ Giao hàng TOÀN QUỐC. Free ship cho đơn hàng có giá trị từ 100.000 VNĐ ở khu vực nội thành TP HCM</p>
                <p>✓ Thanh toán khi nhận hàng</p>
                <p>✓ Đổi trả trong <strong>7</strong> ngày</p>    
                <p>✓ Giao hàng từ <strong>1 - 3</strong> ngày</p> 
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>                 
                <!--<p class="text-center"><a href="/signup"><img src="/web/images/signup.png"/></a></p> -->
            </div>              
        </div>
    ',
    
    'commitment_and_guide' => ' 
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p>✓ Giao hàng TOÀN QUỐC. Free ship cho đơn hàng có giá trị từ 100.000 VNĐ ở khu vực nội thành TP HCM</p>
                <p>✓ Thanh toán khi nhận hàng</p>
                <p>✓ Đổi trả trong <strong>7</strong> ngày</p>                               
                <p>✓ Giao hàng từ <strong>1 - 3</strong> ngày</p>                               
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>                                   
            </div>  
            <div class="guide">  
                <div class="title">Hướng Dẫn Mua Hàng</div>
                <p>✓ Đặt hàng trực tiếp tại website, xem <a target="_blank" href="http://vuongquocbalo.com/huong-dan-mua-hang.html">Hướng dẫn mua hàng</a></p>
                <p>✓ Đặt hàng trực tiếp qua chat trên website</p>
                <p><i class="fa fa-mobile"></i> <strong style="color:#D4232B">098 65 60 943 - 098 65 60 997</strong></p>
                <p><i class="fa fa-envelope"></i> <a itemprop="email" href="mailto:vuongquocbalo@gmail.com">vuongquocbalo@gmail.com</a></p>           
                <p><i class="fa fa-skype"></i> <a href="skype:thailvn?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" align="absmiddle" /></a></p>                                           
            </div>
        </div>
    ',
    
    'bank_account' => '
        <div class="payment-account">
            <p>✓ Thông tin ngân hàng chuyển khoản:</p>
            <p>✓ Sau khi đặt hàng thành công, vui lòng chuyển số tiền phải thanh toán vào tài khoản ngân hàng của chúng tôi, ngay sau khi nhận được tiền, chúng tôi sẽ xác nhận với bạn bằng email và điện thoại</p>
            <p>✓ Hotline: <strong style="color:#D4232B">098 65 60 943  - 098 65 60 997</strong></p>
            <p>✓ Chat skype: <a href="skype:thailvn?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" align="absmiddle" /></a></p>           
        </div>
    ',
    /*
    'vouchers' => array(
        'register' => array(
            'amount' => 10,
            'type' => 0,
            'expired' => strtotime(date('Y-m-d')) + 7*24*60*60,
            'send_email' => 1,
        )
    ),
    */
    //'chat' => '<script>window._sbzq||function(e){e._sbzq=[];var t=e._sbzq;t.push(["_setAccount",41882]);var n=e.location.protocol=="https:"?"https:":"http:";var r=document.createElement("script");r.type="text/javascript";r.async=true;r.src=n+"//static.subiz.com/public/js/loader.js";var i=document.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)}(window);</script>',
    'ga' => ""
    
);
