<?php


namespace Dothiv\HivDomainStatusBundle\Transformer;

use Dothiv\AdminBundle\Transformer\DomainTransformer;
use Dothiv\APIBundle\Transformer\AbstractTransformer;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Model\DomainCheckModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class DomainCheckTransformer extends AbstractTransformer implements EntityTransformerInterface
{

    /**
     * @var DomainTransformer
     */
    private $domainTransformer;

    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var HivDomainCheck $entity */
        $model = new DomainCheckModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->domain         = $this->domainTransformer->transform($entity->getDomain(), null, true);
        $model->addresses      = $entity->getAddresses();
        $model->created        = $entity->getCreated();
        $model->dnsOk          = $entity->getDnsOk();
        $model->iframePresent  = $entity->getIframePresent();
        $model->iframeTarget   = $entity->getIframeTarget();
        $model->iframeTargetOk = $entity->getIframeTargetOk();
        $model->scriptPresent  = $entity->getScriptPresent();
        $model->statusCode     = $entity->getStatusCode();
        $model->url            = $entity->getUrl();
        $model->valid          = $entity->getValid();
        return $model;
    }

    /**
     * @param DomainTransformer $domainTransformer
     */
    public function setDomainTransformer($domainTransformer)
    {
        $this->domainTransformer = $domainTransformer;
    }
}
