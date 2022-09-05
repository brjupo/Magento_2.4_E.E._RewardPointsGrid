<?php
namespace Brjupo\RewardPointsReport\Model\ResourceModel;

class RewardPointsReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    function _construct() {
        $this->_init('rewardpoints_report', 'history_id');
    }
}