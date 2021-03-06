<?php

namespace Funstaff\Bundle\RedirectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RedirectExportCommand.
 *
 * @author Bertrand Zuchuat <bertrand.zuchuat@gmail.com>
 */
class RedirectExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('funstaff:redirect:export')
            ->setDescription('Export redirect data')
            ->setDefinition(array(
                new InputArgument('filename', InputArgument::OPTIONAL, 'filename', 'redirect.csv')))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getHelperSet()->get('dialog');

        $exportPath = sprintf(
            '%s'.DIRECTORY_SEPARATOR.'%s',
            $container->getParameter('funstaff_redirect.export_path'),
            $input->getArgument('filename')
        );
        $folder = dirname($exportPath);
        if (!is_readable($folder)) {
            if (!$dialog->askConfirmation(
                    $output,
                    sprintf(
                        '<question>The destination folder "%s" doesn\'t exist. Would you like to create?</question>',
                        $folder
                    ),
                    false
                )) {
                return;
            } else {
                $container->get('filesystem')->mkdir($folder, 0777);
                $output->writeln(sprintf(
                    'Create destination folder "<comment>%s</comment>"',
                    $folder
                ));
            }
        }

        $container
            ->get('funstaff_redirect.redirect_manager')
            ->export($exportPath);

        $output->writeln(sprintf(
            'Export data to "<comment>%s</comment>"',
            $exportPath
        ));
    }
}