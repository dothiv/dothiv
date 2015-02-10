<?php

namespace Dothiv\LandingpageBundle\Transformer;

use Dothiv\APIBundle\Transformer\AbstractTransformer;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Model\LandingpageConfigurationModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class LandingpageConfigurationTransformer extends AbstractTransformer implements EntityTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var LandingpageConfiguration $entity */
        $model = new LandingpageConfigurationModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setClickCounter($entity->getClickCounter());
        $model->setName($entity->getName());
        $model->setLanguage($entity->getLanguage());
        $model->setText($entity->getText()->getOrElse(null));
        return $model;
    }
}
