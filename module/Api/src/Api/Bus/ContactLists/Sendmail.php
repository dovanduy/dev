<?php

namespace Api\Bus\ContactLists;

use Application\Lib\Arr;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Predicate\Expression;
use Api\Bus\AbstractBus;

/**
 * Add categories
 *
 * @package 	Bus
 * @created 	2015-09-06
 * @version     1.0
 * @author      ThaiLai
 * @copyright   YouGo INC
 */
class SendMail extends AbstractBus {
    
    protected $_required = array(
        'email',
    );
    
    public function operateDB($sm, $param) {
        try {         
            $emailFile = \Application\Module::getConfig('send_email_file');
            $content = file_get_contents($emailFile);
            if (empty($content)) {
                $this->_response = false;
                return $this->result();
            }
            $param['products'] = unserialize($content);
            if (count($param['products']) > 10) {
                $param['products'] = Arr::rand($param['products'], 10);
            }
            if (!isset($param['smtp'])) {
                $param['smtp'] = 1;
            }            
            switch ($param['smtp']) {
                case 2:
                    $mail = $sm->get("Mail2");
                    break;
                case 3:
                    $mail = $sm->get("Mail3");
                    break;
                case 4:
                    $mail = $sm->get("Mail4");
                    break;
                case 5:
                    $mail = $sm->get("Mail5");
                    break;
                case 6:
                    $mail = $sm->get("Mail6");
                    break;
                case 7:
                    $mail = $sm->get("Mail7");
                    break;
                case 8:
                    $mail = $sm->get("Mail8");
                    break;
                case 9:
                    $mail = $sm->get("Mail9");
                    break;
                case 10:
                    $mail = $sm->get("Mail10");
                    break;
                default:
                     $mail = $sm->get("Mail");
            }
            $viewModel = new ViewModel(array('data' => $param));
            $viewModel->setTemplate('email/pr');
            $mail->setFrom('vuongquocbalo@gmail.com', 'BALO Khuyến Mãi');                     
            $mail->setTo($param['email']);                     
            $mail->setSubject('HOT! HOT! HOT!');
            $mail->setBody($viewModel);
            $model = $sm->get('ContactLists'); 
            $mail->send();
            $this->_response = $model->update([
                'table' => 'contact_lists',
                'set' => [
                    'sent_at' => new Expression('UNIX_TIMESTAMP()')
                ],
                'where' => [
                    'email' => $param['email']
                ]
            ]);        
            return $this->result($model->error());
        } catch (\Exception $e) {
            $this->_exception = $e;
        }
        return false;
    }

}
