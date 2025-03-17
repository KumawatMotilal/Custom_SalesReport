<?php
namespace Custom\SalesReport\Model;

use Custom\SalesReport\Api\ReportManagementInterface;
use Custom\SalesReport\Ui\DataProvider\ReportDataProvider;

class ReportManagement implements ReportManagementInterface
{
    protected $dataProvider;

    public function __construct(ReportDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public function getReportData()
    {
        return $this->dataProvider->getData();
    }
}
