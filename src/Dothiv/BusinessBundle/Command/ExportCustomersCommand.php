<?php

namespace Dothiv\BusinessBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\DomainWhois;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\ShopBundle\Entity\Order;
use MyProject\Proxies\__CG__\stdClass;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;

/**
 * Export a list of all domains with extended information
 */
class ExportCustomersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dothiv:export:customers')
            ->setDescription('Export a list of all domains with extended information');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Registry $doctrine */
        /** @var EntityManager $em */
        $doctrine = $this->getContainer()->get('doctrine');
        $em       = $doctrine->getManager();

        $domains = $em->createQueryBuilder()
            ->select('d')->from('DothivBusinessBundle:Domain', 'd')
            ->getQuery()
            ->getResult();

        // Whois
        $whois = [];
        foreach ($em->createQueryBuilder()
                     ->select('w')->from('DothivBusinessBundle:DomainWhois', 'w')
                     ->getQuery()
                     ->getResult() as $w) {
            /** @var DomainWhois $w */
            $whois[$w->getDomain()->toScalar()] = $w->getWhois();
        }

        // Shop orders
        $orders = [];
        foreach ($em->createQueryBuilder()
                     ->select('o')->from('DothivShopBundle:Order', 'o')
                     ->getQuery()
                     ->getResult() as $o) {
            /** @var Order $o */
            $orders[$o->getDomain()->toScalar()] = $o;
        }

        $fp           = fopen('php://memory', 'w');
        $bytesWritten = 0;

        $bytesWritten += fputcsv($fp, [
            'Domain',
            'First name (dotHIV)',
            'Last name (dotHIV)',
            'Name (WHOIS)',
            'Created (dotHIV)',
            'Updated (dotHIV)',
            'Created (WHOIS)',
            'Updated (WHOIS)',
            'Registry Expiry Date (WHOIS)',
            'Non-Profit',
            'Language',
            'Country (WHOIS)',
            'Email (dotHIV)',
            'Email (WHOIS)',
            'Click-Counter set up?',
            'Click count',
            'Live?',
            'Shop Order?'
        ]);

        foreach ($domains as $domain) {
            /** @var Domain $domain */

            $whoisOption = Option::fromValue(isset($whois[$domain->getName()]) ? $whois[$domain->getName()] : null);
            $whoisData   = function ($prop) use ($whoisOption) {
                return $whoisOption->map(function (ArrayCollection $w) use ($prop) {
                    return $w->get($prop);
                })->getOrElse(null);
            };

            $orderOption = Option::fromValue(isset($orders[$domain->getName()]) ? $orders[$domain->getName()] : null);
            $orderData   = function ($prop) use ($orderOption) {
                return $orderOption->map(function (Order $o) use ($prop) {
                    return $o->$prop();
                })->getOrElse(null);
            };

            $bytesWritten += fputcsv($fp,
                [
                    $domain->getName(),
                    Option::fromValue($domain->getOwner())->map(function (User $u) {
                        return $u->getFirstname();
                    })->getOrElse(null),
                    Option::fromValue($domain->getOwner())->map(function (User $u) {
                        return $u->getSurname();
                    })->getOrElse(null),
                    $domain->getCreated()->format(DATE_W3C),
                    $domain->getUpdated()->format(DATE_W3C),
                    $whoisData('Registrant Name'),
                    $whoisData('Creation Date'),
                    $whoisData('Updated Date'),
                    $whoisData('Registry Expiry Date'),
                    $domain->getNonprofit() ? 'Y' : 'N',
                    in_array($whoisData('Registrant Country'), ['DE', 'CH', 'AT']) || in_array($orderData('getCountry'), ['DE', 'CH', 'AT']) ? 'DE' : 'EN',
                    $whoisData('Registrant Country'),
                    $domain->getOwnerEmail(),
                    $whoisData('Registrant Email'),
                    $domain->getActiveBanner() !== null ? 'Y' : 'N',
                    $domain->getClickcount(),
                    $domain->getLiveSince() !== null ? 'Y' : 'N',
                    $orderOption->isDefined() ? 'Y' : 'N',
                ]
            );
        }
        fseek($fp, 0);
        $csv = fread($fp, $bytesWritten);
        fclose($fp);
        $output->write($csv);
    }

    /**
     * @return DomainRepositoryInterface
     */
    protected function getDomainRepo()
    {
        return $this->getContainer()->get('dothiv.repository.domain');
    }
}
