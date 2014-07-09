<?php

namespace Dothiv\APIBundle\Annotation;

use Dothiv\APIBundle\Exception\BadMethodCallException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class ApiRequest
{
    /**
     * @var string
     */
    private $model;

    /**
     * @param array $data
     *
     * @throws \BadMethodCallException
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            $this->model = $data['value'];
            unset($data['value']);
        }
        foreach ($data as $k => $v) {
            $setter = 'set' . ucfirst($k);
            if (!method_exists($this, $setter)) {
                throw new BadMethodCallException(
                    sprintf('Unknown property "%s" on annotation "%s"', $k, get_class($this))
                );
            }
            $this->$setter($v);
        }
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Returns the annotation alias name.
     *
     * @return string
     * @see Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface
     */
    public function getAliasName()
    {
        return 'apirequest';
    }
}
