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
                    'route' => 'thoitrang1.net',                    
                ),                
            ),
        ),
    ),
    'st_host' => 'http://thoitrang1.net/web',
    'website_id' => 2,
    
    'facebook_admins' => '129746714106531',
    'facebook_app_id' => '1017869161653955',
    'facebook_app_secret' => 'e9f6b56a1a0de0210e3266625b327743',
    'facebook_login_file' => getcwd() . '/shell/thoitrang1/facebook_login.serialize',
    
    'google_app_id' => '805351782449-ivf6n3p88791eh80rp3j8tasqg7l06s4.apps.googleusercontent.com',
    'google_app_secret' => 'ioV9e4N7WiiFN9sKbRUgCEOM',
    'google_app_redirect_uri' => 'http://thoitrang1.net/glogin',
    
    'google_app_id2' => '57520396243-61uormtrqgdjpa42nt98vb6de1q6nqar.apps.googleusercontent.com',
    'google_app_secret2' => 'sWYrv92ElITxEL8rmC9AVApe',
    'google_app_redirect_uri2' => 'http://thoitrang1.net/glogin2',
    'google_login_file' => getcwd() . '/shell/thoitrang1/google_login.serialize',
        
    'site_name' => 'thoitrang1.net',   
    'head_meta' => array(
        'owner' => 'thoitrang1.net',
        'author' => 'thoitrang1.net',
        'distribution' => 'Global',
        'placename' => 'Việt Nam',
        'copyright' => 'Copyright © 2016 thoitrang1.net. All Rights Reserved',
    ),
    'cache' => array(
        'adapter' => array(
            'name' => 'filesystem',
            'options' => array(
                'dirLevel' => 2,
                'cacheDir' => getcwd() . '/data/thoitrang1/cache/web',
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
        'products' => 24
    ),
    
    'display_page' => 10,
    
    'commitment' => '
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p>✓ Giao hàng TOÀN QUỐC. Free ship ở khu vực nội thành TP HCM (các quận 1, 2, 3, 4 ,5 ,6 ,7 ,8 ,10, 11, Bình Thạnh, Gò Vấp, Phú Nhuận, Tân Bình, Tân Phú), <a target="_blank" href="http://thoitrang1.net/chinh-sach-ban-hang.html">Xem chính sách bán hàng</a></p>
                <p>✓ Thanh toán khi nhận hàng</p>                
                <p>✓ Giao hàng từ <strong>1 - 3</strong> ngày</p> 
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>
            </div>              
        </div>
    ',
    
    'commitment_and_guide' => ' 
        <div id="commitment_and_guide">    
            <div class="commitment">   
                <div class="title">Chính sách bán hàng</div>
                <p>✓ Giao hàng TOÀN QUỐC. Free ship cho đơn hàng có giá trị từ 150.000 VNĐ ở khu vực nội thành TP HCM</p>
                <p>✓ Thanh toán khi nhận hàng</p>                                         
                <p>✓ Giao hàng từ <strong>1 - 3</strong> ngày</p>                               
                <p>✓ Cam kết hàng giống hình</p>                                               
                <p>✓ Hàng chính hãng, giá luôn thấp hơn thị trường</p>                                   
            </div>  
            <div class="guide">  
                <div class="title">Hướng Dẫn Mua Hàng</div>
                <p>✓ Đặt hàng trực tiếp tại website, xem <a target="_blank" href="http://thoitrang1.net/huong-dan-mua-hang.html">Hướng dẫn mua hàng</a></p>             
                <p><i class="fa fa-mobile"></i> <strong style="color:#D4232B">097 443 60 40 - 098 65 60 997</strong></p>
                <p><i class="fa fa-envelope"></i> <a itemprop="email" href="mailto:thoitrang1@gmail.com">thoitrang1@gmail.com</a></p>           
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
        ga('create', 'UA-80707773-1', 'auto');
        ga('send', 'pageview');
      </script>
    ",
    
    'meta_head' => [
        'title' => 'Mua thời trang trực tuyến giá rẻ, đẹp, chất lượng, với nhiều chương trình khuyến mãi',        
    ],
    
    'view_manager' => array(        
        'template_map' => array(  
            'web/layout' => __DIR__ . '/../view/layout/thoitrang1/layout.phtml', 
            'web/header' => __DIR__ . '/../view/partial/thoitrang1/header.phtml',           
            'web/footer' => __DIR__ . '/../view/partial/thoitrang1/footer.phtml'         
        ),        
    ),
	
	'meta_head' => [
        'title' => 'Mua thời trang trực tuyến giá rẻ, đẹp, chất lượng, với nhiều chương trình khuyến mãi',
    ]
    
);
