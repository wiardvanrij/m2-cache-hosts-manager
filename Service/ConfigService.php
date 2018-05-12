<?php
/**
 * Copyright © 2017 Webfixit.nl. All rights reserved.
 * Wiard van Rij - Webfixit
 */
namespace Webfixit\CacheHostsManager\Service;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Setup\Model\ConfigGenerator;

class ConfigService
{

    private $configGenerator;

    private $deploymentConfig;

    private $writer;

    public function __construct(
        ConfigGenerator $configGenerator,
        DeploymentConfig $deploymentConfig,
        DeploymentConfig\Writer $writer
    ) {
        $this->configGenerator = $configGenerator;
        $this->deploymentConfig = $deploymentConfig;
        $this->writer = $writer;
    }

    /**
     * @return array
     */
    public function getCurrentCacheHosts()
    {
        return $this->deploymentConfig->getConfigData(ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS);
    }

    /**
     * @param string $inputData
     *
     * @return array
     */
    public function generateConfig($inputData)
    {
        $fileConfigStorage = [];
        $data[ConfigOptionsListConstants::INPUT_KEY_CACHE_HOSTS] = $inputData;

        $config = $this->configGenerator->createCacheHostsConfig($data);

        if (isset($fileConfigStorage[$config->getFileKey()])) {
            $fileConfigStorage[$config->getFileKey()] = array_replace_recursive(
                $fileConfigStorage[$config->getFileKey()],
                $config->getData()
            );
        } else {
            $fileConfigStorage[$config->getFileKey()] = $config->getData();
        }

        return $fileConfigStorage;
    }

    /**
     * @param array $fileConfigStorage
     * @param array $currentData
     *
     * @return array
     */
    public function mergeConfigData($fileConfigStorage, $currentData)
    {
        foreach ($fileConfigStorage as $env => $fileConfig) {
            $fileConfigStorage[$env][ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS] =
                array_map("unserialize", array_unique(array_map("serialize",
                    array_merge($fileConfig[ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS], $currentData))));
        }

        return $fileConfigStorage;
    }

    /**
     * @param array $fileConfigStorage
     * @param array $currentData
     *
     * @return array
     */
    public function mergeRemoveConfigData($fileConfigStorage, $currentData)
    {
        foreach ($fileConfigStorage as $env => $fileConfig) {
            foreach ($fileConfig[ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS] as $entry) {
                foreach ($currentData as $key => $currentDataEntry) {

                    if ($entry['host'] == $currentDataEntry['host']) {
                        // If current data + new data port exists and is equal
                        if (isset($entry['port']) && isset($currentDataEntry['port'])) {
                            if ($entry['port'] == $currentDataEntry['port']) {
                                unset($currentData[$key]);
                            }
                            // If current data + new data both do not own a port
                        } elseif ( ! isset($entry['port']) && ! isset($currentDataEntry['port'])) {
                            unset($currentData[$key]);
                        }
                    }
                }
            }
            $fileConfigStorage[$env][ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS] = array_values($currentData);
        }

        return $fileConfigStorage;
    }

    /**
     * @param array $fileConfigStorage
     */
    public function writeConfig($fileConfigStorage)
    {
        $this->writer->saveConfig($fileConfigStorage, true);
    }
}