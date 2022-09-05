<?php

namespace Brjupo\RewardPointsReport\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Creates RewardPoints View
 */
class CreateRewardPointsView implements SchemaPatchInterface
{
  /**
   * @var SchemaSetupInterface
   */
  private $schemaSetup;

  /**
   * @param SchemaSetupInterface $schemaSetup
   */
  public function __construct(
    SchemaSetupInterface $schemaSetup
  ) {
    $this->schemaSetup = $schemaSetup;
  }

  /**
   * @inheritdoc
   */
  public function getAliases()
  {
    return [];
  }

  /**
   * @inheritdoc
   */
  public function apply()
  {
    $this->schemaSetup->startSetup();

    $sql = "DROP VIEW IF EXISTS rewardpoints_report;";
    $this->schemaSetup->getConnection()->query($sql);

    $sql = "CREATE SQL SECURITY INVOKER VIEW rewardpoints_report AS
            (
            SELECT 
              reward_points_delta_expiration.history_id,
              customer_entity.firstname,
              customer_entity.lastname,
              customer_entity.email,
              reward_points_delta_expiration.points_delta,
              reward_points_delta_expiration.expired_at_static
            FROM
              customer_entity
            INNER JOIN
              ( SELECT 
                  magento_reward_history.history_id,
                  magento_reward.customer_id,
                  magento_reward_history.points_delta,
                  magento_reward_history.expired_at_static
                FROM
                  magento_reward
                INNER JOIN magento_reward_history 
                  ON magento_reward.reward_id = magento_reward_history.reward_id
              ) AS reward_points_delta_expiration 
            ON customer_entity.entity_id = reward_points_delta_expiration.customer_id
            ORDER BY reward_points_delta_expiration.history_id DESC
            );";

    $this->schemaSetup->getConnection()->query($sql);
    $this->schemaSetup->endSetup();


    return $this;
  }

  /**
   * @inheritdoc
   */
  public static function getDependencies()
  {
    return [];
  }
}
