<?php

namespace Dothiv\BusinessBundle\Command;

use Dothiv\AdminBundle\Entity\EntityChange;
use Dothiv\AdminBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A command to manipulate configuration settings
 */
class ConfigCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:config')
            ->setDescription('Manipulate configurations settings.')
            ->addOption('user', 'u', InputOption::VALUE_OPTIONAL, 'Email of the user making the change')
            ->addArgument('name', InputArgument::OPTIONAL, 'Name of the configuration setting')
            ->addArgument('value', InputArgument::OPTIONAL, 'Value of the configuration setting to set');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name  = $input->getArgument('name');
        $value = $input->getArgument('value');
        if (Option::fromValue($name)->isEmpty()) {
            $this->listConfigSettings($output);
            return;
        }
        if (Option::fromValue($value)->isEmpty()) {
            $this->listConfig($output, $name);
            return;
        }
        $this->updateConfig($input, $name, $value);
        $this->listConfig($output, $name);
    }

    protected function listConfigSettings(OutputInterface $output)
    {
        $configs = $this->getConfigRepo()->findAll();
        $this->showTable($output, $configs);
    }

    protected function listConfig(OutputInterface $output, $name)
    {
        $this->showTable($output, array($this->getConfigRepo()->get($name)));
    }

    protected function updateConfig(InputInterface $input, $name, $value)
    {
        $config = $this->getConfigRepo()->get($name);
        $change = new EntityChange();
        $change->setEntity($this->getConfigRepo()->getItemEntityName($config));
        $change->setIdentifier(new IdentValue($config->getId()));
        $change->addChange(new IdentValue('value'), $config->getValue(), $value);
        $change->setAuthor(new EmailValue(Option::fromValue($input->getOption('user'))->getOrElse('console@' . $this->getContainer()->getParameter('charitydomain'))));
        $this->getEntityChangeRepo()->persist($change)->flush();
        $config->setValue($value);
        $this->getConfigRepo()->persist($config)->flush();
    }

    /**
     * @return ConfigRepositoryInterface
     */
    protected function getConfigRepo()
    {
        return $this->getContainer()->get('dothiv.repository.config');
    }

    /**
     * @return EntityChangeRepositoryInterface
     */
    protected function getEntityChangeRepo()
    {
        return $this->getContainer()->get('dothiv.admin.repository.entity_change');
    }

    /**
     * @param OutputInterface $output
     * @param                 $configs
     */
    protected function showTable(OutputInterface $output, $configs)
    {
        $table = new TableHelper();
        $table->setHeaders(array('Name', 'Value'));
        foreach ($configs as $config) {
            /** Config */
            $table->addRow(array($config->getName(), $config->getValue()));
        }
        $table->render($output);
    }
}
