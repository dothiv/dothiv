<?php

/**
 * Controller for the pinkbar api.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Service\MoneyFormatServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class PinkbarController
{
    /**
     * @var float how many EUR to increment on click
     */
    private $eurIncrement = 0.1;

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
     * @param TranslatorInterface         $translator
     * @param MoneyFormatServiceInterface $moneyFormatService
     * @param float                       $eurGoal
     * @param float                       $alreadyDonated
     */
    public function __construct(TranslatorInterface $translator, MoneyFormatServiceInterface $moneyFormatService, $eurGoal, $alreadyDonated)
    {
        $this->translator         = $translator;
        $this->moneyFormatService = $moneyFormatService;
        $this->eurGoal            = floatval($eurGoal);
        $this->alreadyDonated     = floatval($alreadyDonated);
    }

    /**
     * Build pinkbar data
     *
     * @param string $locale
     *
     * @return Response
     */
    public function statsAction($locale)
    {
        // TODO: Connect to live stats.
        $clicks                  = 0;
        $data                    = array();
        $data['enabled']         = false;
        $data['donated']         = $this->alreadyDonated;
        $data['donated_label']   = $this->moneyFormatService->decimalFormat($data['donated'], $locale);
        $unlocked                = $clicks * $this->eurIncrement;
        $data['unlocked']        = $unlocked;
        $data['unlocked_label']  = $this->moneyFormatService->decimalFormat($data['unlocked'], $locale);
        $data['percent']         = $this->eurGoal > 0 ? $unlocked / $this->eurGoal : 0;
        $data['clicks']          = $clicks;
        $data['clicks_label']    = $this->translator->trans('pinkbar.clicks', array($clicks));
        $data['increment']       = $this->eurIncrement;
        $data['increment_label'] = $this->moneyFormatService->format($data['increment'], $locale);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        return $response;
    }
}
