<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Application\Console;

use Ixocreate\Application\ApplicationConfig;
use Ixocreate\Contract\Application\BootstrapItemInterface;
use Ixocreate\Contract\Command\CommandInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class BootstrapGenerateCommand extends Command implements CommandInterface
{
    /**
     * @var ApplicationConfig
     */
    private $applicationConfig;

    private $template = <<<'EOD'
<?php
declare(strict_types=1);

namespace App;

/** @var %s %s */

EOD;

    /**
     * BootstrapListCommand constructor.
     */
    public function __construct(ApplicationConfig $applicationConfig)
    {
        parent::__construct(self::getCommandName());
        $this->applicationConfig = $applicationConfig;
    }

    public function configure()
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'Bootstrap file name');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (\file_exists($this->applicationConfig->getBootstrapDirectory() . $input->getArgument("file"))) {
            throw new \Exception("Bootstrap file already exists");
        }

        foreach ($this->applicationConfig->getBootstrapItems() as $bootstrapItem) {
            if ($bootstrapItem->getFileName() === $input->getArgument("file")) {
                $this->generateFile($bootstrapItem);
                $output->writeln(\sprintf("<info>%s generated</info>", $bootstrapItem->getFileName()));
                return;
            }
        }

        throw new \Exception(\sprintf("Bootstrap file %s does not exist", $input->getArgument("file")));
    }

    public static function getCommandName()
    {
        return "bootstrap:generate";
    }

    private function generateFile(BootstrapItemInterface $bootstrapItem): void
    {
        \file_put_contents(
            $this->applicationConfig->getBootstrapDirectory() . $bootstrapItem->getFileName(),
            \sprintf($this->template, '\\' . \get_class($bootstrapItem->getConfigurator()), '$' . $bootstrapItem->getVariableName())
        );
    }
}
