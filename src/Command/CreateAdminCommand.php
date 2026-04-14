<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-admin', description: 'Cria o usuário admin padrão')]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => 'admin@admin.com.br']);
        if ($existing) {
            $io->warning('Usuário admin@admin.com.br já existe.');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setNome('Administrador');
        $user->setEmail('admin@admin.com.br');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->hasher->hashPassword($user, 'admin'));

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Usuário admin@admin.com.br criado com sucesso!');
        return Command::SUCCESS;
    }
}
