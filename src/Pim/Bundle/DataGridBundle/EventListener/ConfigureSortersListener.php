<?php

namespace Pim\Bundle\DataGridBundle\EventListener;

use Oro\Bundle\DataGridBundle\Datagrid\Builder;
use Pim\Bundle\CatalogBundle\DependencyInjection\PimCatalogExtension;
use Pim\Bundle\DataGridBundle\Datasource\DatasourceInterface;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\DataGridBundle\Extension\Formatter\Configuration;
use Oro\Bundle\DataGridBundle\Extension\Sorter\Configuration as SorterConfiguration;

/**
 * Configure the sorters of the datagrids
 * TODO: find a way to override or merge grids' configurations to remove this listener
 *
 * @author    Julien Janvier <julien.janvier@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ConfigureSortersListener
{
    /** @var string */
    protected $storageDriver;

    /**
     * @param $storageDriver
     */
    public function __construct($storageDriver)
    {
        $this->storageDriver = $storageDriver;
    }

    /**
     * Reconfigure sorters
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $sortersPath = sprintf('%s[%s]', SorterConfiguration::SORTERS_PATH, Configuration::COLUMNS_KEY);
        $sorters = $config->offsetGetByPath($sortersPath);

        $datasource = $config->offsetGetByPath(Builder::DATASOURCE_TYPE_PATH);
        $sorterType = null;
        if (DatasourceInterface::DATASOURCE_PRODUCT === $datasource) {
            $sorterType = 'product_field';
        } elseif (DatasourceInterface::DATASOURCE_SMART === $datasource &&
            PimCatalogExtension::DOCTRINE_MONGODB_ODM === $this->storageDriver) {
            $sorterType = 'mongodb_field';
        }

        if (null === $sorterType) {
            return;
        }

        foreach ($sorters as $sorterName => $sorterConfig) {
            if (!isset($sorterConfig['sorter'])) {
                $config->offsetSetByPath(sprintf('%s[%s][sorter]', $sortersPath, $sorterName), $sorterType);
            }
        }
    }
}