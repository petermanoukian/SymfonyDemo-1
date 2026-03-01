<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-users', description: 'Creates Super Admin and Admin users')]
class CreateSuperUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 1. Create the SUPER ADMIN
        $super = new User();
        $super->setEmail('super@admin.com');
        $super->setRoles(['ROLE_SUPER_ADMIN']);
        $super->setPassword($this->passwordHasher->hashPassword($super, 'SuperPass123!'));
        $this->entityManager->persist($super);

        // 2. Create the STANDARD ADMIN
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'AdminPass123!'));
        $this->entityManager->persist($admin);

        $this->entityManager->flush();

        $output->writeln('<info>Success: super@admin.com and admin@admin.com created!</info>');
        
        return Command::SUCCESS;
    }
}