<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;

/**
 * This controller lists the redirects active in the browser-plugin
 */
class BrowserPluginController extends PageController
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    public function redirectsAction(Request $request, $locale)
    {
        $response = $this->createResponse();

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        $response->setLastModified($uriLastModified);
        if ($response->isNotModified($request)) {
            return $response;
        }

        try {
            $data = $this->buildPageObject($request, $locale, 'browserplugin');
        } catch (InvalidArgumentException $e) {
            return $this->createNotFoundResponse($e->getMessage());
        }

        // Projects
        $data['redirects'] = [];
        $sort              = array();

        $filterQuery = new FilterQuery();
        $filterQuery->setProperty('clickcounterconfig', '1');
        $options = new PaginatedQueryOptions();

        do {
            $result = $this->domainRepo->getPaginated($options, $filterQuery);
            foreach ($result->getResult() as $domain) {
                /** @var Domain $domain */
                $clickCounterConfig = $domain->getActiveBanner();
                if (Option::fromValue($clickCounterConfig->getRedirectUrl())->isDefined()) {
                    $d                   = HivDomainValue::create($domain->getName())->toUTF8();
                    $r                   = parse_url($clickCounterConfig->getRedirectUrl());
                    $data['redirects'][] = [
                        'hivdomain' => $d,
                        'domain'    => $r['host']
                    ];
                    $sort[]              = $d;
                }
            }
        } while ($result->getNextPageKey()->isDefined() && $options->setOffsetKey($result->getNextPageKey()->get()));
        array_multisort($sort, SORT_ASC, $data['redirects']);

        // Build response
        $template = $this->getBundle() . ':Page:browserplugin.html.twig';
        $response = $this->getRenderer()->renderResponse($template, $data, $response);

        // Store last modified.
        $lastModifiedDate = $lmc->getLastModifiedContent();
        $response->setLastModified($lastModifiedDate);
        $lmc->setLastModified($request, $lastModifiedDate);

        return $response;
    }

    public function setDomainRepository(DomainRepositoryInterface $domainRepo)
    {
        $this->domainRepo = $domainRepo;
    }
}
