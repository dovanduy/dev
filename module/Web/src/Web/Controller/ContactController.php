<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Web\Controller;

use Web\Lib\Api;
use Web\Form\Contact\Form;

class ContactController extends AppController
{    
    /**
     * construct
     * 
     */
    public function __construct()
    {        
        parent::__construct();        
    }    
    
    /**
     * 
     *
     * @return Zend\View\Model
     */
    public function indexAction()
    {   
        $this->setHead(array(
            'title' => $this->translate('Contact')
        ));
        $contactForm = new Form();  
        $contactForm ->setController($this)
                    ->create('post');
        
        // send form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = (array) $request->getPost();         
            $contactForm->setData($post);         
            if ($contactForm->isValid()) {
                $id = Api::call('url_contacts_add', $post);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'web/contact'          
                    );
                }
            }
        }
        
        return $this->getViewModel(array(
               'contactForm' => $contactForm
            )
        );
    }    
    
    
}
