<?php


namespace Dothiv\APIBundle\Transformer;

use Doctrine\Common\Util\Debug;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\APIBundle\Model\DomainCollaboratorModel;
use Dothiv\ValueObject\URLValue;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class DomainCollaboratorTransformer extends AbstractTransformer implements EntityTransformerInterface
{

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var DomainTransformer
     */
    private $domainTransformer;

    /**
     * {@inheritdoc}
     */
    public function transform(EntityInterface $entity, $route = null, $listing = false)
    {
        /** @var DomainCollaborator $entity */
        $model = new DomainCollaboratorModel();
        $model->setJsonLdId(new URLValue(
            $this->router->generate(
                Option::fromValue($route)->getOrElse($this->route),
                array(
                    'domain'     => $entity->getDomain()->getName(),
                    'identifier' => $entity->getPublicId(),
                ),
                RouterInterface::ABSOLUTE_URL
            )
        ));
        $model->setUser($this->userTransformer->transform($entity->getUser(), null, true));
        $model->setDomain($this->domainTransformer->transform($entity->getDomain(), null, true));
        return $model;
    }

    /**
     * @param DomainTransformer $domainTransformer
     */
    public function setDomainTransformer($domainTransformer)
    {
        $this->domainTransformer = $domainTransformer;
    }

    /**
     * @param UserTransformer $userTransformer
     */
    public function setUserTransformer($userTransformer)
    {
        $this->userTransformer = $userTransformer;
    }
}
