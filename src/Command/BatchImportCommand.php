<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Icon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name       : 'app:batch:import',
    description: 'Import ',
)]
class BatchImportCommand extends Command
{
    private EntityManagerInterface $em;
    private ParameterBagInterface  $params;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface  $parameterBag,
        string                 $name = null
    )
    {
        $this->params = $parameterBag;
        $this->em     = $entityManager;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('category', InputArgument::REQUIRED, 'The category for the icons (separated by , coma)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln([
            '',
            '<fg=cyan>888       888</> <fg=white> .d8888b.  888     888  .d8888b.   .d8888b. </>',
            '<fg=cyan>888   o   888</> <fg=white>d88P  Y88b 888     888 d88P  Y88b d88P  Y88b</>',
            '<fg=cyan>888  d8b  888</> <fg=white>Y88b.      888     888 888    888 Y88b.     </>',
            '<fg=cyan>888 d888b 888</> <fg=white> "Y888b.   Y88b   d88P 888         "Y888b.  </>',
            '<fg=cyan>888d88888b888</> <fg=white>    "Y88b.  Y88b d88P  888  88888     "Y88b.</>',
            '<fg=cyan>88888P Y88888</> <fg=white>      "888   Y88o88P   888    888       "888</>',
            '<fg=cyan>8888P   Y8888</> <fg=white>Y88b  d88P    Y888P    Y88b  d88P Y88b  d88P</>',
            '<fg=cyan>888P     Y888</> <fg=white> "Y8888P"      Y8P      "Y8888P88  "Y8888P" </>',
            '',
        ]);

        // Get files from import folder
        $finder        = new Finder();
        $svgFilesFound = $finder->files()->name('*.svg')->in($this->params->get('app.iconimport_dir'));

        // Obtain the categories entered by the user and explode to an array
        $inputCategories      = $input->getArgument('category');
        $inputCategoriesArray = explode(',', $inputCategories);

        // Some counters
        $inputCategoriesCount = count($inputCategoriesArray);
        $svgFilesFoundCount   = count($svgFilesFound);

        if (0 === $svgFilesFoundCount) {
            $io->error("There is no icons in the import folder.");
            return Command::FAILURE;
        }

        $io->writeln("Importing... please wait.\r\n");

        $io->progressStart();

        $this->addIconAndCategories($svgFilesFound, $inputCategoriesArray, $io);

        $io->progressFinish();

        // Returning success message with counters
        $returnSuccess = "Successfully added $svgFilesFoundCount icon";
        $returnSuccess .= ($svgFilesFoundCount > 1 ? 's' : '');
        $returnSuccess .= " in $inputCategoriesCount categor";
        $returnSuccess .= $inputCategoriesCount > 1 ? 'ies' : 'y';

        $io->success($returnSuccess);

        return Command::SUCCESS;
    }

    /**
     * @param Finder $svgFilesFound
     * @param array $inputCategoriesArray
     * @param SymfonyStyle $io
     * @return void
     */
    public function addIconAndCategories(Finder $svgFilesFound, array $inputCategoriesArray, SymfonyStyle $io): void
    {
        $filesystem = new Filesystem();
        // Get all existing categories
        $categories = $this->em->getRepository(Category::class);

        foreach ($svgFilesFound as $file) {
            $icon = new Icon();

            // Definition of the icon name according to the file name without extension.
            $icon->setName(str_replace('-', ' ', mb_substr($file->getFilename(), 0, -4)));

            // Filling the svg property with the content of the svg file
            $svgContent = $file->getContents();

            $icon->setSvg($svgContent);

            /**
             * For each category entered on the terminal, we check if the category already exists.
             * If the category exists, it is added to the Icon::class.
             * If not, we create a new category and add it to the Icon::class.
             */
            foreach ($inputCategoriesArray as $inputCategory) {
                $existingCategory = $categories->findOneBy(['name' => $inputCategory]);

                if ($existingCategory) {
                    $icon->addCategory($existingCategory);
                } else {
                    $category = new Category();
                    $category->setName($inputCategory);
                    $this->em->persist($category);

                    $icon->addCategory($category);
                }
            }

            // Adding to the database
            $this->em->persist($icon);
            $this->em->flush();

            $io->progressAdvance(1);

            // Delete file from the import directory
            $filesystem->remove($file->getRealPath());
        }
    }
}
