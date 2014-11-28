<?php

namespace Dothiv\APIBundle\Transformer;

use Dothiv\APIBundle\Model\DomainModel;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\URLValue;
use Dothiv\ValueObject\W3CDateTimeValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class DomainTransformer extends AbstractTransformer implements EntityTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var Domain $entity */
        $model = new DomainModel();
        $model->setDomain(new HivDomainValue($entity->getName()));
        $model->setCreated(new W3CDateTimeValue($entity->getCreated()));
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array('name' => $entity->getName()),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        return $model;
    }
}
