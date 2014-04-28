<?php
class MW_Mcore_Block_Disabled extends Mage_Adminhtml_Block_System_Config_Form_Field
{

 protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mw_mcore/system/config/status.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
//    public function getAjaxCheckUrl()
//    {
//       // return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_atwixtweaks/check');
//    }
// 
    /**
     * Generate button html
     *
     * @return string
     */
    public function getStatusHtml()
    {
//        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
//            ->setData(array(
//            'id'        => 'mcore_button',
//            'label'     => $this->helper('adminhtml')->__('Extend Trial'),
//            'onclick'   => 'javascript:check(); return false;'
//        ));
        return '<font style="color:red; font-weight:bold">'.$this->__("Disabled").'</font>' ;// $button->toHtml();
    }
}