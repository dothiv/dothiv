<?php


namespace Dothiv\APIBundle\Transformer;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\APIBundle\Model\UserProfileChangeModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class UserProfileChangeTransformer extends AbstractTransformer implements EntityTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var UserProfileChange $entity */
        $model = new UserProfileChangeModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'handle'     => $entity->getUser()->getHandle(),
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setProperties($entity->getProperties()->toArray());
        return $model;
    }
}
