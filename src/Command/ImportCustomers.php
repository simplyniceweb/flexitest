<?php

namespace App\Command;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Entity\Customers;
use App\Repository\CustomersRepository;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportCustomers extends Command
{
    private $entityManager;
    private $guzzleClient;
    private $userPassword;
    private $customerRepo;

    protected static $defaultName = 'app:import-customers';

    public function __construct(UserPasswordHasherInterface $userPassword, EntityManagerInterface $entityManager, CustomersRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
        $this->userPassword = $userPassword;
        $this->entityManager = $entityManager;
        $this->guzzleClient = new GuzzleClient(['base_uri' => 'https://randomuser.me/api']);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('swvid', InputArgument::OPTIONAL, 'SWV number?');
        $this->addOption('clientonly', '-co', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->entityManager;

        $parameters = [
            'results' => 100,
            'nat' => 'AU'
        ];

        $response = $this->guzzleClient->request('GET', '?' . http_build_query($parameters), ['http_errors' => false]);
        
        if ($response->getStatusCode() !== 200) {
            $output->writeln('Something wrong with the third party API.');
            return Command::FAILURE;
        }

        $body = $response->getBody()->getContents();
        $results = json_decode($body, true);

        if ($results && $results['info']['results'] > 0) {
            $customers = $results['results'];

            foreach($customers as $customer) {
                $email = $customer['email'];

                $customerObject = $this->customerRepo->findOneBy(['email' => $email]);
                if (!is_object($customerObject)) {
                    $customerObject = new Customers();
                }
                
                $customerObject->setFirstName($customer['name']['first']);
                $customerObject->setLastName($customer['name']['last']);
                $customerObject->setEmail($email);
                $customerObject->setUsername($customer['login']['username']);

                $customerObject->setGender($customer['gender']);
                $customerObject->setCountry($customer['location']['country']);
                $customerObject->setCity($customer['location']['city']);
                $customerObject->setPhone($customer['phone']);
                
                $hashedPassword = $this->userPassword->hashPassword($customerObject, $customer['login']['password']);

                $customerObject->setPassword($hashedPassword);

                $entityManager->persist($customerObject);
                $entityManager->flush();
            }
        }

        $output->writeln('Done importing.');

        return Command::SUCCESS;
    }
}