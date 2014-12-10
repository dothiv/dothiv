<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\CharityWebsiteBundle\Exception\RuntimeException;
use Dothiv\ValueObject\ClockValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once __DIR__ . '/../../../../vendor/abraham/twitteroauth/twitteroauth/twitteroauth.php';

/**
 * This controller provides the twitter stream for the social board.
 */
class SocialBoardController
{
    use CreateJsonResponseTrait;

    /**
     * @var string
     */
    private $consumer_key;

    /**
     * @var string
     */
    private $consumer_secret;

    /**
     * @var string
     */
    private $oauth_access_token;

    /**
     * @var string
     */
    private $oauth_access_token_secret;

    /**
     * @var int
     */
    private $pageLifetime;

    /**
     * @var ClockValue
     */
    private $clock;

    /**
     * @param string     $consumer_key
     * @param string     $consumer_secret
     * @param string     $oauth_access_token
     * @param string     $oauth_access_token_secret
     * @param ClockValue $clock
     * @param int        $pageLifetime
     */
    public function __construct(
        $consumer_key,
        $consumer_secret,
        $oauth_access_token,
        $oauth_access_token_secret,
        ClockValue $clock,
        $pageLifetime
    )
    {
        $this->consumer_key              = $consumer_key;
        $this->consumer_secret           = $consumer_secret;
        $this->oauth_access_token        = $oauth_access_token;
        $this->oauth_access_token_secret = $oauth_access_token_secret;
        $this->clock                     = $clock;
        $this->pageLifetime              = $pageLifetime;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function twitterAction(Request $request)
    {
        switch ($request->get('url')) {
            case 'timeline':
                $rest   = 'statuses/user_timeline';
                $params = Array('count' => $request->get('count'), 'include_rts' => $request->get('include_rts'), 'exclude_replies' => $request->get('exclude_replies'), 'screen_name' => $request->get('screen_name'));
                break;
            case 'search':
                $rest = "search/tweets";
                $q    = $request->get('q');
                if ($q == 'dotHIV') {
                    $q = '%40dotHIV%20OR%20%23dotHIV';
                }
                $params = Array('q' => $q, 'count' => $request->get('count'), 'include_rts' => $request->get('include_rts'));
                break;
            case 'list':
                $rest   = "lists/statuses";
                $params = Array('list_id' => $request->get('list_id'), 'count' => $request->get('count'), 'include_rts' => $request->get('include_rts'));
                break;
            default:
                $rest   = 'statuses/user_timeline';
                $params = Array('count' => '20');
                break;
        }

        $auth              = new \TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_access_token, $this->oauth_access_token_secret);
        $auth->host        = "https://api.twitter.com/1.1/";
        $auth->decode_json = false;
        $stream            = $auth->get($rest, $params);

        if (!$stream) {
            throw new RuntimeException('Failed to fetch Twitter stream.');
        }

        $response = $this->createResponse();
        $response->setPublic();
        $response->setSharedMaxAge($this->pageLifetime);
        $response->setExpires($this->clock->getNow()->modify(sprintf('+%d seconds', $this->pageLifetime)));
        $response->setContent($stream);
        return $response;
    }
}
