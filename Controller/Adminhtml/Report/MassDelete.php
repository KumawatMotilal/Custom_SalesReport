<?php
namespace Custom\SalesReport\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class MassDelete extends Action
{
    protected $collectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $itemIds = $this->getRequest()->getParam('selected');
        
        if (!is_array($itemIds)) {
            $this->messageManager->addErrorMessage(__('Please select item(s).'));
        } else {
            try {
                $collection->addFieldToFilter('item_id', ['in' => $itemIds]);
                $itemsDeleted = 0;
                foreach ($collection as $item) {
                    $item->delete();
                    $itemsDeleted++;
                }
                $this->messageManager->addSuccessMessage(
                    __('A total of %1 record(s) have been deleted.', $itemsDeleted)
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('An error occurred while deleting the items.'));
            }
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
