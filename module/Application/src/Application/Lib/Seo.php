<?php
namespace Application\Lib;

class Seo
{	
	var $title  		= null;
	var $keywords  		= null;
	var $description	= null;
	var $_aMeta	= null;

	public function __construct($objCache = null, $objRequest = null)
	{
		$arrCompany			= Zend_Registry::get('arrCompany');
		$AppUI 				= Zend_Registry::get('AppUI');
		$objView			= Zend_Registry::get('objView');
		$objTranslate		= Zend_Registry::get('objTranslate');
		$strLanguage		= Zend_Registry::get('strLanguage');
		$strController 		= $objRequest->getControllerName();
		$strAction 			= $objRequest->getActionName();
		$strCompanyName		= $strLanguage == 'vi' ? $arrCompany['name_vi'] : $arrCompany['name_en'];
		$this->title		= str_replace('[company_name]', $strCompanyName, $arrCompany['meta_title']);
		$this->keywords		= str_replace('[company_name]', $strCompanyName, $arrCompany['meta_keywords']);
		$this->description	= str_replace('[company_name]', $strCompanyName, $arrCompany['meta_desc']);
		$strTitle			= '';		
		$objView->assign('sCompanyName', $strCompanyName);
		$objView->assign('sUrl', $sUrl = HOST . $objRequest->getRequestUri());
		
		switch ($strController) {
			case 'index':
				switch ($strAction) {
					case 'index':
						$strTitle = $objTranslate->_('homepage');				
						break;
					case 'home':
						$strTitle = $objTranslate->_('homepage');				
						break;
					case 'introduce':
						$strTitle = $objTranslate->_('introduce');				
						break;
					case 'shopping':
						$strTitle = $objTranslate->_('shopping');				
						break;			
					case 'content':
						$strTitle = '';				
						break;
					/* MLT */	
					case 'foreword':
						$strTitle = $objTranslate->_('foreword');				
						break;
					case 'commitment':
						$strTitle = $objTranslate->_('commitment');				
						break;
						
					case 'pricelist':
						$strTitle = $objTranslate->_('pricelist');				
						break;
					case 'contract':
						$strTitle = $objTranslate->_('contract');				
						break;
					case 'trainning':
						$strTitle = $objTranslate->_('trainning');				
						break;
					/* End MLT */	
						
					case 'helpshopping':
						$strTitle = $objTranslate->_('help_shopping');				
						break;
					case 'helpusing':
						$strTitle = $objTranslate->_('help_using');				
						break;
					case 'registeragent':
						$strTitle = $objTranslate->_('register_agent');				
						break;
					case 'recruitment':
						$strTitle = $objTranslate->_('recruitment');				
						break;
					case 'contact':
						$strTitle = $objTranslate->_('contact');				
						break;
					case 'video':
						$strTitle = $objTranslate->_('video');				
						break;
					case 'photo':
						$strTitle = $objTranslate->_('picture');				
						break;
					case 'forgetpassword':
						$strTitle = $objTranslate->_('forgetpassword');				
						break;
					case 'register':
						$strTitle = $objTranslate->_('register');				
						break;
				}
				break;
			case 'product':
				/*
				$objCate = new Cates();				
				$intCateId = $objRequest->getParam('pcateid', 0);
				if ($intCateId) {
					$arrCate = $objCate->getById($intCateId);
					$strTitle = $arrCate['name_vi'];			
				} else {
					$strTitle = $objTranslate->_('product');
					if ($strAction == 'detail') {
						$strTitle = '[title]';
					}
				}
				*/
				break;
			case 'service':
				$intCateId = $objRequest->getParam('pcateid', 0);
				if ($intCateId > 0 || $strAction == 'detail') {
					$strTitle = '';
					break;
				}
				$strTitle = $objTranslate->_('services');				
				break;
			case 'news':				
				$intCateId = $objRequest->getParam('pcateid', 0);
				if ($intCateId > 0 || $strAction == 'detail') {
					$strTitle = '';
					break;
				}				
				//$strTitle = $objTranslate->_('news');				
				break;
			case 'faq':	
				break;
				$intCateId = $objRequest->getParam('pcateid', 0);
				if ($intCateId > 0 || $strAction == 'detail') {
					$strTitle = '';
					break;
				}				
				$strTitle = $objTranslate->_('faqs');				
				break;
		}
		if ($strTitle) {
			$this->title = str_replace('[title]', $strTitle, $this->title);
			$this->title = htmlspecialchars($this->title);
		}
		$this->description = htmlspecialchars($this->description);
		$this->keywords = htmlspecialchars($this->keywords);		
	}
	
	public function setMeta($strTitle = '', $strDescription = '', $strKeywords = '')
	{		
		$objView			= Zend_Registry::get('objView');
		$strTitle 			= Functions::mbtruncate($strTitle, 100);
		$strDescription 	= Functions::mbtruncate($strDescription, 500);
		$strKeywords 		= Functions::mbtruncate($strKeywords, 500);	
		if ($strDescription == '') {
			$strDescription = $strTitle;
		}
		if ($strKeywords == '') {
			$strKeywords = $strTitle;
		}			
		$this->title 		= htmlspecialchars(str_replace('[title]', $strTitle, $this->title));
		$this->title 		= htmlspecialchars($this->title);
		$this->description 	= htmlspecialchars(str_replace('[description]', $strDescription, $this->description));
		$this->keywords 	= htmlspecialchars(str_replace('[keywords]', $strKeywords, $this->keywords));
		$objView->assign('objSeo', $this);
	}
	
	public function setMeta2($mMeta, $sValue = null)
	{
		$objView = Zend_Registry::get('objView');		
		if (!is_array($mMeta))
		{
			$mMeta = array($mMeta => $sValue);
		}		
		foreach ($mMeta as $sKey => $sValue)
		{			
			if ($sKey != 'og:image') {
				$sValue = Functions::mbtruncate($sValue, 200);			
				$sValue = strip_tags($sValue);						
			}
			if (isset($this->_aMeta[$sKey]))
			{
				$this->_aMeta[$sKey] .= ($sKey == 'keywords' ? ', ' : ' ') . $sValue;
			}
			else 
			{
				$this->_aMeta[$sKey] = $sValue;
			}
		}		
		$objView->assign('objSeo', $this);
	}
}