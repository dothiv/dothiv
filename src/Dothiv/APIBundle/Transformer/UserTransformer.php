<?php

namespace Dothiv\APIBundle\Transformer;

use Dothiv\APIBundle\Model\UserModel;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class UserTransformer extends AbstractTransformer
{
    /**
     * @param User    $entity
     * @param string  $route
     * @param boolean $listing
     *
     * @return UserModel
     */
    public function transform(User $entity, $route = null, $listing = false)
    {
        $model = new UserModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array('handle' => $entity->getHandle()),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setFirstname($entity->getFirstname());
        $model->setSurname($entity->getSurname());
        $model->setEmail(new EmailValue($entity->getEmail()));
        return $model;
    }
}
