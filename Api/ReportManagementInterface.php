<?php
namespace Custom\SalesReport\Api;

interface ReportManagementInterface
{
    /**
     * Get sales report data
     *
     * @return array
     */
    public function getReportData();
}
