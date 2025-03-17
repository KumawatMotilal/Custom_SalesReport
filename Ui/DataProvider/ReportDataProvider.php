<?php
namespace Custom\SalesReport\Ui\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class ReportDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    protected $collection;
    protected $collectionFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->collectionFactory = $collectionFactory;
    }

    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->getSelect()->distinct(true);
            $this->collection->join(
                ['order' => 'sales_order'],
                'main_table.order_id = order.entity_id',
                ['increment_id']
            );
            
            $this->collection->addFieldToSelect([
                'item_id',
                'order_id',
                'name',
                'qty_ordered',
                'row_total',
                'created_at'
            ]);
        }
        return $this->collection;
    }

    public function addFilter(Filter $filter)
    {
        $field = $filter->getField();
        $condition = $filter->getConditionType();
        $value = $filter->getValue();
        
        $collection = $this->getCollection();
        
        switch ($field) {
            case 'product_name':
                $field = 'name';
                break;
            case 'total_revenue':
                $field = 'row_total';
                break;
            case 'created_at':
                if (is_array($value)) {
                    if (isset($value['from'])) {
                        $value['from'] = date('Y-m-d H:i:s', strtotime($value['from']));
                    }
                    if (isset($value['to'])) {
                        $value['to'] = date('Y-m-d H:i:s', strtotime($value['to']));
                    }
                    $condition = $value;
                }
                break;
        }
    
        if (is_array($condition)) {
            $collection->addFieldToFilter($field, $condition);
        } else {
            $collection->addFieldToFilter($field, [$condition => $value]);
        }
    }
    

    public function getData()
    {
        $collection = $this->getCollection();
        $items = [];
        foreach ($collection as $item) {
            $data = $item->getData();
            $items[] = [
                'item_id' => $data['item_id'],
                'order_id' => $data['order_id'],
                'product_name' => $data['name'],
                'qty_ordered' => $data['qty_ordered'],
                'total_revenue' => $data['row_total'],
                'created_at' => $data['created_at']
            ];
        }
        return [
            'totalRecords' => $collection->getSize(),
            'items' => $items
        ];
    }
}
