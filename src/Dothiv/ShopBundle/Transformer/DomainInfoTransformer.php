<?php


namespace Dothiv\ShopBundle\Transformer;

use Dothiv\APIBundle\Transformer\AbstractTransformer;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\ShopBundle\Entity\DomainInfo;
use Dothiv\ShopBundle\Model\DomainInfoModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class DomainInfoTransformer extends AbstractTransformer implements EntityTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var DomainInfo $entity */
        $model = new DomainInfoModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setName($entity->getName());
        $model->setRegistered($entity->getRegistered());
        $model->setPremium($entity->getPremium());
        $model->setBlocked($entity->getBlocked());
        $model->setTrademark($entity->getTrademark());
        $model->setAvailable($entity->getAvailable());
        return $model;
    }
}
