<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Application\Lib\Cache;
use Application\Lib\Log;
use Application\Lib\Api;
use Application\Lib\Arr;
use Admin\Form\Category\CategorySearchForm;
use Admin\Form\Category\CategoryListForm;
use Admin\Form\Category\CategoryAddForm;
use Admin\Form\Category\CategoryUpdateForm;
use Admin\Form\Category\CategoryUpdateLocaleForm;
use Admin\Model\Categories;

class CategoriesController extends AppController
{    
    public function __construct()
    {        
        parent::__construct();        
    }
    
    public function indexAction()
    {
        $param = $this->getParams(array(
            'limit' => \Application\Module::getConfig('general.default_limit')
        ));
        
        // create search form
        $searchForm = new CategorySearchForm();
        $searchForm ->setController($this)
                    ->create('get')
                    ->bindData($param);

        // process update on list form
        $request = $this->getRequest();


        $data = Api::call('url_categories_lists', $param);
        $listForm = new CategoryListForm();
        $listForm   ->setController($this)
                    ->setDataset($data)
                    ->create();

        return $this->getViewModel(array(
                'searchForm' => $searchForm,
                'listForm' => $listForm,
            )
        );
    }

    /**
     * Add a Category
     *
     * @return Zend\View\Model
     */

    public function addAction()
    {
        $param = $this->getParams();

        // create add/edit form
        $form = new CategoryAddForm();
        $form->setAttribute('enctype','multipart/form-data')
            ->setController($this);
        $form->create();

        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $id = Api::call('url_categories_add', $data);
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/categories',
                        array(
                            'action' => 'update',
                            'id' => $id
                        ),
                        array(
                            'query' => array(
                                'locale' => \Application\Module::getConfig('general.default_locale')
                            )
                        )
                    );
                }
            }
        }

        return $this->getViewModel(array(
                'form' => $form,
            )
        );
    }
    /**
     * Update a Category
     *
     * @return Zend\View\Model
     */
    public function updateAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        $locale = $this->params()->fromQuery('locale', '');

        // invalid parameters
        if (empty($id) || empty($locale)) {
            return $this->notFoundAction();
        }

        // get place detail
        $data = Api::call(
            'url_categories_detail',
            array(
                '_id' => $id,
                'locale' => $locale
            )
        );
        // not found data
        if (empty($data)) {
            return $this->notFoundAction();
        }
        if (!empty($data['image_id'])) {
            $data['url_image'] = Images::getUrl($data['image_id'], 'categories');
        }

        // create add/edit form
        $form = new CategoryUpdateForm();
        $form->setAttribute('enctype','multipart/form-data')
            ->setController($this);
        $form->create();

        // create add/edit locale form
        $localeForm = new CategoryUpdateLocaleForm();
        $localeForm ->setController($this)
            ->create();

        // init form data
        if (!empty($data)) {
            $data['locale'] = $locale;
            $form->bindData($data);
            $localeForm->bindData($data);
        }

        // save form
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = (array) $request->getPost();
            $submitedForm = !isset($data['locale']) ? $form : $localeForm;
            $submitedForm->setData($data);
            if ($submitedForm->isValid()) {
                if (isset($data['locale'])) {
                    Api::call('url_categories_addupdatelocale', $data);
                } else {
                    if (isset($data['remove']['url_image'])) {
                        $data['image_id'] = '';
                    }
                    Api::call('url_categories_update', $data);
                }
                if (Api::error()) {
                    $this->addErrorMessage($this->getErrorMessage());
                } else {
                    $this->addSuccessMessage('Data saved successfully');
                    return $this->redirect()->toRoute(
                        'admin/categories',
                        array(
                            'action' => 'update',
                            'id' => $id
                        ),
                        array(
                            'query' => array(
                                'locale' => $locale
                            )
                        )
                    );
                }
            }
        }

        return $this->getViewModel(array(
                'form' => $form,
                'localeForm' => $localeForm,
            )
        );
    }
    
    public function deleteAction()
    {  

    }
    
}
