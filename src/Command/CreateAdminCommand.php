<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Validation;

#[AsCommand(
    name: 'app:create:admin',
    description: 'Create the first user with admin role',
)]
class CreateAdminCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface      $em;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, string $name = null)
    {
        $this->passwordHasher = $passwordHasher;
        $this->em = $entityManager;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Account username')
            ->addArgument('email', InputArgument::REQUIRED, 'Email of the admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Your high secured password')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $inputUsername = $input->getArgument('username');
        $inputEmail = $input->getArgument('email');
        $inputPassword = $input->getArgument('password');

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

        // validate email and password
        $validator = Validation::createValidator();

        $constraints = [];

        $constraints[] = $validator->validate($inputEmail, [
            new Email([
                'message' => "\"$inputEmail\" is not a valid email address."
            ])
        ]);

        $constraints[] = $validator->validate($inputPassword, [
            new Length([
                'min' => 8,
                'minMessage' => "Your password must contain at least 8 characters."
            ]),
            new NotCompromisedPassword(['message' => "\"$inputPassword\" is compromised, please use a more difficult password."])
        ]);

        $violationCount = 0;

        foreach ($constraints as $violations) {
            if (0 !== count($violations)) {
                foreach ($violations as $violation) {
                    $io->warning($violation->getMessage());
                    $violationCount++;
                }
            }
        }

        // if the fields do not pass the constraints, stop the script
        if (0 !== $violationCount) {
            return Command::FAILURE;
        }

        // check if there is already a registered admin
        $registeredAdmin = $this->em->getRepository(User::class)->findByRole('ROLE_ADMIN');

        if ($registeredAdmin) {
            $io->error('The administrator already exist!');
            return Command::FAILURE;
        }

        $io->writeln(
            [
                "",
                "<fg=yellow>Recap:</>",
                "Your display name: <fg=green>$inputUsername</>",
                "Your email address: <fg=green>$inputEmail</>",
                "Your password: <fg=yellow>$inputPassword</>",
                ""
            ]
        );

        $confirm = new ConfirmationQuestion(
            'Are you ok with the informations above? (y/n)',
            false,
            '/^(y)/i'
        );
        $helper = $this->getHelper('question');
        $confirmHelper = $helper->ask($input, $output, $confirm);

        if (!$confirmHelper) {
            $io->error('Exit, please retry.');
            return Command::FAILURE;
        }

        $user = new User();
        $user
            ->setEmail($inputEmail)
            ->setPassword($this->passwordHasher->hashPassword($user, $inputPassword))
            ->setUsername($inputUsername)
            ->setRoles(['ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Your account has been successfully created!');
        return Command::SUCCESS;
    }
}
