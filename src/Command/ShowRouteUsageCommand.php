<?php

declare(strict_types=1);

namespace Migrify\SymfonyRouteUsage\Command;

use Migrify\SymfonyRouteUsage\EntityRepository\RouteVisitRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ShowRouteUsageCommand extends Command
{
    /**
     * @var string[]
     */
    private const TABLE_HEADLINE = ['Visits', 'Controller', 'Route', 'Last Visit'];

    /**
     * @var RouteVisitRepository
     */
    private $routeVisitRepository;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(RouteVisitRepository $routeVisitRepository, SymfonyStyle $symfonyStyle)
    {
        $this->routeVisitRepository = $routeVisitRepository;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Show usage of routes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tableData = [];
        $this->symfonyStyle->title('Used Routes by Visit Count');
        foreach ($this->routeVisitRepository->fetchAll() as $routeUsageStat) {
            $tableData[] = [
                'visit_count' => $routeUsageStat->getVisitCount(),
                'route' => $routeUsageStat->getRoute(),
                'controller' => $routeUsageStat->getController(),
                'last_visit' => $routeUsageStat->getUpdatedAt()->format('Y-m-d'),
            ];
        }
        $this->symfonyStyle->table(self::TABLE_HEADLINE, $tableData);
        return ShellCode::SUCCESS;
    }
}
