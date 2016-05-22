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
class FbLogin extends AbstractBus {

    protected $_required = array(
        'facebook_email',       
        'facebook_id',       
    );
	
	protected $_email_format = array(
        'facebook_email',
    );
    
    public function operateDB($sm, $param) {
        try {          
            $model = $sm->get('Users');
            $this->_response = $model->fbLogin($param);           
            if (empty($model->error()) 
                && isset($param['generate_voucher'])
                && !empty($this->_response['is_first_login'])) {               
                $userId = $this->_response['user_id'];
                if (empty($param['voucher_min_total_money'])) {
                    $param['voucher_min_total_money'] = 0;
                }
                if (empty($param['voucher_type'])) {
                    $param['voucher_type'] = 0;
                }
                if (empty($param['expired'])) {
                    $param['expired'] = strtotime('+1 week');
                }
                $voucher = $sm->get('Vouchers');
                $_id = $voucher->add(array(
                    'website_id' => $param['website_id'],
                    'amount' => $param['voucher_amount'],
                    'type' => $param['voucher_type'],
                    'expired' => $param['voucher_expired'],
                    'min_total_money' => $param['voucher_min_total_money'],
                    'user_id' => $userId
                ));
                if (!empty($voucher->error())) {
                    return $this->result($voucher->error());
                }
                if (!empty($param['send_email'])) { 
                    $data = $voucher->getDetail(array(
                        'website_id' => $param['website_id'],
                        '_id' => $_id,
                    ));                   
                    if (!empty($data['user_email'])) {                  
                        $mail = $sm->get("Mail");        
                        $viewModel = new ViewModel(array('data' => $data));
                        $viewModel->setTemplate('email/register');
                        $mail->setTo($data['user_email']);                     
                        $mail->setSubject(sprintf('%s TANG BAN MA GIAM GIA %s', $data['website_url'], $data['code']));
                        $mail->setBody($viewModel);
                        $mail->send();
                    }
                }                
            }           
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
