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
                    'route' => 'vuongquocbalo.com',                    
                ),                
            ),
        ),
    ),
    'st_host' => 'http://vuongquocbalo.com/web',
    'website_id' => 1,
    
    'facebook_admins' => '129746714106531',
    //'facebook_app_id' => '1679604478968266',
    //'facebook_app_secret' => '53bbe4bab920c2dd3bb83855a4e63a94',
    
    'facebook_app_id' => '575135192674647',
    'facebook_app_secret' => 'fdeb4ccfb7f28c96365cc6d91bebb6a4',
    
    'facebook_login_file' => [
        23 => getcwd() . '/shell/vuongquocbalo/facebook_login.serialize', // fb.khaai@gmail.com
        25 => getcwd() . '/shell/vuongquocbalo/facebook_login2.serialize', // fb.hoaian@gmail.com
        30 => getcwd() . '/shell/vuongquocbalo/facebook_login3.serialize', // kinhdothoitrang@outlook.com
        20 => getcwd() . '/shell/vuongquocbalo/facebook_login4.serialize', // mail.vuongquocbalo.com@gmail.com
        49 => getcwd() . '/shell/vuongquocbalo/facebook_login5.serialize', // atem.vn@gmail.com
        59 => getcwd() . '/shell/vuongquocbalo/facebook_login6.serialize', // nguyenthimytrang94@yahoo.com.vn
    ],    
    'google_app_id' => '262987808969-jbn8697q26rfdkj48uu71qeirhcpch7q.apps.googleusercontent.com',
    'google_app_secret' => 'dk1V5W0lodxaD1xEXZYEeMfN',
    'google_app_redirect_uri' => 'http://vuongquocbalo.com/glogin',
    
	 // Admin
	'google_app_id2' => '692650781994-mv3unhde1gf92i26s3qajrrbrs9hbsae.apps.googleusercontent.com',
    'google_app_secret2' => 'kEAA8MNKgFbmhL3ZDU9U4eTp',
    'google_app_redirect_uri2' => 'http://vuongquocbalo.com/glogin2',
    'google_login_file' => getcwd() . '/shell/vuongquocbalo/google_login.serialize',
   	
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
                'cacheDir' => getcwd() . '/data/vuongquocbalo/cache/web',
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
                <p>✓ Giao hàng TOÀN QUỐC</p>
                <p>✓ Thanh toán khi nhận hàng</p>                  
                <p>✓ Giao hàng từ <strong>2 - 3</strong> ngày</p> 
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>
            </div>              
        </div>
    ',
    
    'commitment_and_guide' => ' 
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p>✓ Giao hàng TOÀN QUỐC</p>
                <p>✓ Thanh toán khi nhận hàng</p>                                           
                <p>✓ Giao hàng từ <strong>2 - 3</strong> ngày</p>                               
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>                                   
            </div>  
            <div class="guide">  
                <div class="title">Hướng Dẫn Mua Hàng</div>
                <p>✓ Đặt hàng trực tiếp tại website, xem <a target="_blank" href="http://vuongquocbalo.com/huong-dan-mua-hang.html">Hướng dẫn mua hàng</a></p>             
                <p><i class="fa fa-mobile"></i> <strong style="color:#D4232B">097 443 60 40 - 098 65 60 997</strong></p>
                <p><i class="fa fa-envelope"></i> <a itemprop="email" href="mailto:vuongquocbalo@gmail.com">vuongquocbalo@gmail.com</a></p>           
                <p><i class="fa fa-skype"></i> <a href="skype:thailvn?chat"><img src="http://download.skype.com/share/skypebuttons/buttons/chat_green_transparent_97x23.png" style="border: none;" width="97" height="23" alt="Chat with me" align="absmiddle" /></a></p>                                           
            </div>
        </div>
    ',
    
    'bank_account' => '
        <div class="payment-account">
            <p>✓ Thông tin ngân hàng chuyển khoản:</p>
            <p>✓ Sau khi đặt hàng thành công, vui lòng chuyển số tiền phải thanh toán vào tài khoản ngân hàng của chúng tôi, ngay sau khi nhận được tiền, chúng tôi sẽ xác nhận với bạn bằng email và điện thoại</p>
            <p>✓ Hotline: <strong style="color:#D4232B">097 443 60 40 - 098 65 60 997</strong></p>
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
    'chat' => '', //<script>window._sbzq||function(e){e._sbzq=[];var t=e._sbzq;t.push(["_setAccount",41882]);var n=e.location.protocol=="https:"?"https:":"http:";var r=document.createElement("script");r.type="text/javascript";r.async=true;r.src=n+"//static.subiz.com/public/js/loader.js";var i=document.getElementsByTagName("script")[0];i.parentNode.insertBefore(r,i)}(window);</script>',
    'ga' => "<script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        ga('create', 'UA-77904073-1', 'auto');
        ga('send', 'pageview');
      </script>",
    
    'meta_head' => [
        'title' => 'Mua balo trực tuyến giá rẻ, đẹp, chất lượng, với nhiều chương trình khuyến mãi',
        'favicon' => 'favicon.ico'
    ]
);
