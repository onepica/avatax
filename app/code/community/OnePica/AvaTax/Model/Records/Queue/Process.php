<?php
/**
 * OnePica_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Queue process model
 *
 * @category   OnePica
 * @package    OnePica_AvaTax
 * @author     OnePica Codemaster <codemaster@onepica.com>
 */
class OnePica_AvaTax_Model_Records_Queue_Process
{
    /**
     * Remove the Failed process
     *
     * @return $this
     */
    public function clear()
    {
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter(
                'status', array(
                    'in' => array(
                        OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED,
                        OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED,
                        OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE
                    )
                )
            );

        /** @var OnePica_AvaTax_Model_Records_Queue $item */
        foreach ($queue as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Run the complete process
     *
     * @throws \Exception
     * @return $this
     */
    public function run()
    {
        $this->_cleanCompleted()
            ->_cleanFailed()
            ->_cleanUnbalanced()
            ->_parseInvoices()
            ->_parseCreditMemos();

        return $this;
    }

    /**
     * Delete any queue items that have been completed. Items stay in queue for some
     * transparency into the process.
     *
     * @return $this
     */
    protected function _cleanCompleted()
    {
        $days = intval(Mage::getStoreConfig('tax/avatax/queue_success_lifetime'));
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)
            ->addFieldToFilter(
                'updated_at',
                array('lt' => $this->_getDateModel()->gmtDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            );

        foreach ($queue as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Delete any queue items that have failed. Items stay in queue for some
     * transparency into the process.
     *
     * @return $this
     */
    protected function _cleanFailed()
    {
        $days = intval(Mage::getStoreConfig('tax/avatax/queue_failed_lifetime'));
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED)
            ->addFieldToFilter(
                'updated_at',
                array('lt' => $this->_getDateModel()->gmtDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            );

        foreach ($queue as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Delete any queue items that have unbalanced status
     *
     * @return $this
     * @throws \Exception
     */
    protected function _cleanUnbalanced()
    {
        $days = (int)Mage::getStoreConfig('tax/avatax/queue_failed_lifetime');
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('status', OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED)
            ->addFieldToFilter(
                'updated_at',
                array('lt' => $this->_getDateModel()->gmtDate('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
            );

        /** @var OnePica_AvaTax_Model_Records_Queue $item */
        foreach ($queue as $item) {
            $item->delete();
        }

        return $this;
    }

    /**
     * Attempt to send any pending invoices to Avalara
     *
     * @return $this
     */
    protected function _parseInvoices()
    {
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('type', OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_INVOICE)
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED))
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE))
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED));

        /** @var OnePica_AvaTax_Model_Action_Invoice $invoiceAction */
        $invoiceAction = Mage::getModel('avatax/action_invoice');
        /** @var OnePica_AvaTax_Model_Records_Queue $item */
        foreach ($queue as $item) {
            $item->setAttempt($item->getAttempt() + 1);
            try {
                /** @var Mage_Sales_Model_Order_Invoice $invoice */
                $invoice = Mage::getModel('sales/order_invoice')->load($item->getEntityId());
                if ($invoice->getId()) {
                    $invoiceAction->process($invoice, $item);
                }
                $item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)->setMessage(null)->save();
            } catch (OnePica_AvaTax_Model_Service_Exception_Unbalanced $e) {
                $item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED)
                    ->setMessage($e->getMessage())
                    ->save();
            } catch (Exception $e) {
                $status = ($item->getAttempt() >= OnePica_AvaTax_Model_Service_Abstract_Config::QUEUE_ATTEMPT_MAX)
                    ? OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED
                    : OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY;
                $item->setStatus($status)
                    ->setMessage($e->getMessage())
                    ->save();
            }
        }

        return $this;
    }

    /**
     * Attempt to send any pending credit memos to Avalara
     *
     * @return $this
     */
    protected function _parseCreditMemos()
    {
        /** @var OnePica_AvaTax_Model_Records_Mysql4_Queue_Collection $queue */
        $queue = Mage::getModel('avatax_records/queue')->getCollection()
            ->addFieldToFilter('type', OnePica_AvaTax_Model_Records_Queue::QUEUE_TYPE_CREDITMEMEO)
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED))
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE))
            ->addFieldToFilter('status', array('neq' => OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED));

        /** @var OnePica_AvaTax_Model_Action_Creditmemo $creditmemoAction */
        $creditmemoAction = Mage::getModel('avatax/action_creditmemo');
        /** @var OnePica_AvaTax_Model_Records_Queue $item */
        foreach ($queue as $item) {
            $item->setAttempt($item->getAttempt() + 1);
            try {
                /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
                $creditmemo = Mage::getModel('sales/order_creditmemo')->load($item->getEntityId());
                if ($creditmemo->getId()) {
                    $creditmemoAction->process($creditmemo, $item);
                }
                $item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_COMPLETE)
                    ->setMessage(null)
                    ->save();
            } catch (OnePica_AvaTax_Model_Service_Exception_Unbalanced $e) {
                $item->setStatus(OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_UNBALANCED)
                    ->setMessage($e->getMessage())
                    ->save();
            } catch (Exception $e) {
                $status = ($item->getAttempt() >= OnePica_AvaTax_Model_Service_Abstract_Config::QUEUE_ATTEMPT_MAX)
                    ? OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_FAILED
                    : OnePica_AvaTax_Model_Records_Queue::QUEUE_STATUS_RETRY;
                $item->setStatus($status)
                    ->setMessage($e->getMessage())
                    ->save();
            }
        }

        return $this;
    }

    /**
     * Get core date model
     *
     * @return Mage_Core_Model_Date
     */
    protected function _getDateModel()
    {
        return Mage::getSingleton('core/date');
    }
}
