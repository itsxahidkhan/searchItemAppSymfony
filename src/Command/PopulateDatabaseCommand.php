<?php
// src/Command/PopulateDatabaseCommand.php
namespace App\Command;

use App\Entity\SearchItem;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this->setName('app:populate-database')
             ->setDescription('Populates the database with dummy data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $item = new SearchItem();
            $item->setName($faker->word);
            $item->setDescription($faker->sentence);
            $item->setCategory($faker->word);
            $item->setCreatedAt($faker->dateTimeThisYear);
            $this->em->persist($item);
        }
        $this->em->flush();
        $output->writeln('Database populated with 100 dummy records.');
    }
}
