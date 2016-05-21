<?php

namespace Api\Bus\Users;

use Zend\View\Model\ViewModel;
use Api\Bus\AbstractBus;

/**
 * update categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class ForgotPassword extends AbstractBus {

    protected $_required = array(
        'website_id',      
        'new_password_url',      
        'email',      
    );
    
    public function operateDB($sm, $param) {
        try {          
			$model = $sm->get('UserActivations');
            $data = $model->add($param);
            if (empty($model->error()) && !empty($data)) {                        
                if (!empty($data['email'])) {   
                    if (empty($param['website_url'])) {
                        $websiteModel = $sm->get('Websites');
                        $website = $websiteModel->getDetail($param);
                        if (!empty($website['url'])) {
                            $param['website_url'] = $website['url'];
                        }
                    }					
                    $mail = $sm->get("Mail");        
                    $viewModel = new ViewModel(array(
						'new_password_url' => $param['new_password_url'] . '/' . $data['token'],
						'data' => $data,
						'website' => $website,
					));
                    $viewModel->setTemplate('email/forgotpassword');
                    $mail->setTo($data['email']);                                         
                    $mail->setSubject(sprintf('MAT KHAU MOI TREN WEBSITE %s', $param['website_url']));
                    $mail->setBody($viewModel);
                    $mail->send();
                }                
            }
            $this->_response = $data;
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
