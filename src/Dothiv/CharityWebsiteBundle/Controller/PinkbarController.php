<?php

/**
 * Controller for the pinkbar api.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Service\Clock;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class PinkbarController
{
    /**
     * @var float how many EUR to increment on click
     */
    private $eurIncrement = 0.001;

    /**
     * @var float budget available in current stretch; used to calculate status
     */
    private $eurGoal = 0.0;

    /**
     * @var float
     */
    private $alreadyDonated = 0.0;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var MoneyFormatServiceInterface
     */
    private $moneyFormatService;

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var int
     */
    private $pageLifetime;

    /**
     * @param TranslatorInterface         $translator
     * @param MoneyFormatServiceInterface $moneyFormatService
     * @param ConfigRepositoryInterface   $configRepo
     * @param SerializerInterface         $serializer
     * @param float                       $eurGoal
     * @param float                       $alreadyDonated
     * @param float                       $eurIncrement
     * @param Clock                       $clock
     * @param int                         $pageLifetime In seconds
     */
    public function __construct(
        TranslatorInterface $translator,
        MoneyFormatServiceInterface $moneyFormatService,
        ConfigRepositoryInterface $configRepo,
        SerializerInterface $serializer,
        $eurGoal,
        $alreadyDonated,
        $eurIncrement,
        Clock $clock,
        $pageLifetime)
    {
        $this->translator         = $translator;
        $this->moneyFormatService = $moneyFormatService;
        $this->configRepo         = $configRepo;
        $this->serializer         = $serializer;
        $this->eurGoal            = floatval($eurGoal);
        $this->alreadyDonated     = floatval($alreadyDonated);
        $this->eurIncrement       = floatval($eurIncrement);
        $this->clock              = $clock;
        $this->pageLifetime       = (int)$pageLifetime;
    }

    /**
     * Build pinkbar data
     *
     * @param Request $request
     * @param string  $locale
     *
     * @return Response
     */
    public function statsAction(Request $request, $locale)
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge($this->pageLifetime);
        $response->setExpires($this->clock->getNow()->modify(sprintf('+%d seconds', $this->pageLifetime)));
        $config = $this->configRepo->get('clickcount');
        if (Option::fromValue($config->getUpdated())->isDefined()) {
            $response->setLastModified($config->getUpdated());
        }
        if ($response->isNotModified($request)) {
            return $response;
        }

        $clicks                  = (int)$config->getValue();
        $data                    = array();
        $data['donated']         = $this->alreadyDonated;
        $data['donated_label']   = $this->moneyFormatService->decimalFormat($data['donated'], $locale);
        $unlocked                = $clicks * $this->eurIncrement;
        $data['unlocked']        = $unlocked;
        $data['unlocked_label']  = $this->moneyFormatService->decimalFormat($data['unlocked'], $locale);
        $data['goal']            = $this->eurGoal;
        $data['goal_label']      = $this->moneyFormatService->decimalFormat($this->eurGoal, $locale);
        $data['percent']         = $this->eurGoal > 0 ? round($unlocked / $this->eurGoal, 3) : 0;
        $data['clicks']          = $clicks;
        $data['clicks_label']    = $this->translator->trans('pinkbar.clicks', array($clicks));
        $data['increment']       = $this->eurIncrement;
        $data['increment_label'] = $this->moneyFormatService->format($data['increment'], $locale);

        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($this->serializer->serialize($data, 'json'));
        return $response;
    }
}
