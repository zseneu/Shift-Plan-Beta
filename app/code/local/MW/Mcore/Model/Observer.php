<?php
class MW_Mcore_Model_Observer
{	
	public function adloginsuccess($o)
	{	
		Mage::app()->getCookie()->set('hide','no');		 	
		Mage::getSingleton('core/session')->unsNotification();
		Mage::helper('mcore')->changeRemind();	
		Mage::helper('mcore')->updatestatus();
	}	
	
	public function logoutupdate($o){		
		Mage::app()->getCookie()->set('hide','no');
		Mage::getSingleton('core/session')->unsNotification();	
		Mage::helper('mcore')->updatestatus();
	}
	
	public function reloadAdmin($o)
	{
			Mage::getSingleton('core/session')->unsNotification();
			
						
	}
}