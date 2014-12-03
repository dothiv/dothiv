<?php


namespace Dothiv\APIBundle\Transformer;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\APIBundle\Model\UserNotificationModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class UserNotificationTransformer extends AbstractTransformer implements EntityTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var UserNotification $entity */
        $model = new UserNotificationModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setProperties($entity->getProperties());
        $model->setDismissed($entity->getDismissed());
        return $model;
    }
}
