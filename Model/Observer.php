<?php

class Cammino_Clearabandonedcart_Model_Observer extends Varien_Object
{

    public function clearAbandonedCarts(Varien_Event_Observer $observer)
    {
        $lastQuoteId = Mage::getSingleton('checkout/session')->getQuoteId();
        if ($lastQuoteId) {
            $customerQuote = Mage::getModel('sales/quote')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
            $customerQuote->setQuoteId($lastQuoteId);            
            $customerQuote->removePayment();
            $this->_removeAllItems($customerQuote);
        } else {
            $quote = Mage::getModel('checkout/session')->getQuote();
            $quote->removePayment();
            $this->_removeAllItems($quote);
        }
    }
 
    protected function _removeAllItems($quote){
        foreach ($quote->getAllItems() as $item) {
            $item->isDeleted(true);
            if ($item->getHasChildren()) {
                foreach ($item->getChildren() as $child) {
                    $child->isDeleted(true);
                }
            }
        }
        $quote->collectTotals()->save();
    }

}
