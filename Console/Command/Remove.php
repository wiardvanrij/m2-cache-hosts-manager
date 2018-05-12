<?php
/**
 * Copyright © 2017 Webfixit.nl. All rights reserved.
 * Wiard van Rij - Webfixit
 */
namespace Webfixit\CacheHostsManager\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webfixit\CacheHostsManager\Service\ConfigService;

class Remove extends Command
{

    /** @var ConfigService  */
    private $configService;

    /**
     * Add constructor.
     *
     * @param ConfigService $configService
     */
    public function __construct(
        ConfigService $configService
    ) {
        $this->configService = $configService;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cachehosts:remove')->setDescription('Removes a cache host');
        $this->addArgument("http-cache-host");
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inputData = $input->getArgument("http-cache-host");
        if ( ! isset($inputData)) {
            $output->writeln('Missing argument "http-cache-host"');
        } else {

            // Current configuration
            $currentData = $this->configService->getCurrentCacheHosts();
            // Input configuration
            $fileConfigStorage = $this->configService->generateConfig($inputData);

            // Merge configs while removing current input
            $mergedFileConfigStorage = $this->configService->mergeRemoveConfigData($fileConfigStorage, $currentData);

            // And write config to file
            $this->configService->writeConfig($mergedFileConfigStorage, true);

            $output->writeln('Wrote to config file');
        }
    }
}
