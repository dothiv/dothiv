<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends PageController
{
    /**
     * @var float how many EUR to increment on click
     */
    private $eurIncrement = 0.1;

    /**
     * @var float budget available in current stretch; used to calculate status
     */
    private $eurGoal = 50000.0;

    /**
     * @var float
     */
    private $alreadyDonated = 100000.0;

    public function indexAction(Request $request, $locale)
    {
        // TODO: Cache
        $data = $this->buildPageObject($request, $locale, 'index');
        // Projects
        $data['projects'] = $this->getContent()->buildEntries('Project', $locale);
        usort($data['projects'], function (\stdClass $projectA, \stdClass $projectB) {
            if ($projectA->order == $projectB->order) {
                return 0;
            }
            return ($projectA->order < $projectB->order) ? -1 : 1;
        });
        // Partners
        $data['partners'] = $this->getContent()->buildEntries('Partner', $locale);
        shuffle($data['partners']);
        // Build pinkbar data
        // TODO: Money format
        // FIXME: Remove random once live.
        $already_donated                    = round($this->alreadyDonated * (mt_rand() / mt_getrandmax()), 2);
        $clicks                             = intval(($this->eurGoal * (1 / $this->eurIncrement)) * (mt_rand() / mt_getrandmax()));
        $data['pinkbar']                    = array();
        $data['pinkbar']['donated']         = $already_donated;
        $data['pinkbar']['donated_label']   = $this->moneyFormat($already_donated, $locale);
        $unlocked                           = $clicks * $this->eurIncrement;
        $data['pinkbar']['unlocked']        = $unlocked;
        $data['pinkbar']['unlocked_label']  = $this->moneyFormat($unlocked, $locale);
        $data['pinkbar']['percent']         = $unlocked / $this->eurGoal;
        $data['pinkbar']['clicks']          = $clicks;
        $data['pinkbar']['increment']       = $this->eurIncrement;
        $data['pinkbar']['increment_label'] = $this->moneyFormat($this->eurIncrement, $locale);
        // Build response
        $response = new Response();
        $template = $this->getBundle() . ':Page:index.html.twig';
        return $this->getRenderer()->renderResponse($template, $data, $response);
    }

    protected function moneyFormat($value, $locale)
    {
        switch ($locale) {
            case 'de':
                return sprintf('%s €', number_format($value, 2, ',', '.'));
                break;
            default:
                return sprintf('$%s', number_format($value, 2, '.', ','));
        }
    }
}
