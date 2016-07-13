<?php

namespace Web\Form\My;

use Application\Form\AbstractForm;

/**
 * List Form
 *
 * @package Web\Form
 * @created    2015-08-25
 * @version     1.0
 * @author      thailh
 * @copyright   YouGo INC
 */
class AllOrderListForm extends AbstractForm
{
    
    /**
     * Table construct
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Element array to create form
     *
     * @return array Array to create elements for form
     */
    public function elements()
    {
        return array(
            
        );
    }

    /**
     * Column array to create table
     *
     * @return array Array to create columns for table
     */
    public function columns()
    {
        return array( 
            array(
                'name' => 'invoice',
                'title' => 'Order information',               
                'type' => 'html',
                'innerHtml' => "                    
                    <table id='order_{code}' class='table cart table-bordered'>                        
                        <tbody>  
                            <tr>
                                <td colspan='2'>
                                    <strong>#{code}</strong>
                                    <span class='fr'>
                                        <a  href='#'          
                                            data-url='{$this->getController()->url()->fromRoute('web/my', array('action' => 'orderdetail', 'id' => '{_id}'))}'
                                            data-modelid='#dialog-modal'
                                            class='show-model'>{$this->getController()->translate('Order detail')}</a>
                                    </span>
                                    </td>                                    
                                </td>                                
                            </tr>                            
                            <tr>
                                <td>{$this->getController()->translate('Order date')}</td>
                                <td class='information'>{created}</td>
                            </tr>                                                       
                            <tr>
                                <td>{$this->getController()->translate('Total money')}</td>
                                <td class='information'>{total_money}</td>
                            </tr>
                            <tr>
                                <td>{$this->getController()->translate('Full name')}</td>
                                <td class='information'>{user_name}</td>
                            </tr>
                            <tr>
                                <td>{$this->getController()->translate('Mobile')}</td>
                                <td class='information'>{user_mobile}</td>
                            </tr>
                            <tr>
                                <td>{$this->getController()->translate('Email')}</td>
                                <td class='information'>{user_email}</td>
                            </tr> 
                            <tr>
                                <td>{$this->getController()->translate('Delivery address')}</td>
                                <td class='information'>{address}</td>
                            </tr>
                            <tr>
                                <td>{$this->getController()->translate('Status')}</td>
                                <td class='information invoice-col'>
                                    {status_name}
                                </td>
                            </tr>
                            <tr>
                                <td>{$this->getController()->translate('Memo')}</td>
                                <td class='information'>{note}</td>
                            </tr>                            
                        </tbody>
                    </table>      
                ",                      
            ),                                
        );
    }

}