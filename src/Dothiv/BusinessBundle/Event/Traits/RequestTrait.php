<?php


namespace Dothiv\BusinessBundle\Event\Traits;

use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;

trait RequestTrait
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @return Option of Request
     */
    public function getRequest()
    {
        return Option::fromValue($this->request);
    }

    /**
     * @param Request $request
     *
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Returns the preferred language.
     *
     * @param array $locales An array of ordered available locales
     *
     * @return string|null The preferred locale
     */
    public function getPreferredLanguage(array $locales)
    {
        if ($this->getRequest()->isEmpty()) {
            return $locales[0];
        }
        return $this->getRequest()->get()->getPreferredLanguage($locales);
    }
}
