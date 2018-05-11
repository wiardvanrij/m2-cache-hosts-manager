<?php
/**
 * Copyright © 2017 Webfixit.nl. All rights reserved.
 * Wiard van Rij - Webfixit
 */
namespace Webfixit\CacheHostsManager\Console\Command;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Setup\Model\ConfigGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends Command
{

    private $configGenerator;

    private $deploymentConfig;

    private $writer;

    public function __construct(ConfigGenerator $configGenerator,  DeploymentConfig $deploymentConfig, Writer $writer) {

        $this->configGenerator = $configGenerator;
        $this->deploymentConfig = $deploymentConfig;
        $this->writer = $writer;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('cachehosts:add')->setDescription('Adds a cache host');
        $this->addArgument("http-cache-host");
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentData = $this->deploymentConfig->getConfigData(ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS);

        $inputData = $input->getArgument("http-cache-host");
        if(!isset($inputData)) {
            $output->writeln('Missing argument "http-cache-host"');
            die();
        }

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

        foreach ($fileConfigStorage as $env => $fileConfig) {
            $fileConfigStorage[$env][ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS] =
                array_map("unserialize", array_unique(array_map("serialize",
                    array_merge($fileConfig[ConfigOptionsListConstants::CONFIG_PATH_CACHE_HOSTS], $currentData))));
        }

        $this->writer->saveConfig($fileConfigStorage, true);

//        $output->writeln('Ok works');
    }
}
