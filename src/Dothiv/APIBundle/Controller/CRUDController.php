<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Exception\AccessDeniedHttpException;
use Dothiv\APIBundle\Exception\BadRequestHttpException;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Exception\NotFoundHttpException;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\APIBundle\Transformer\PaginatedListTransformer;
use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUDRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\BusinessBundle\Repository\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Service\FilterQueryParser;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CRUDController
{
    use CreateJsonResponseTrait;

    /**
     * @var CRUDRepositoryInterface
     */
    protected $itemRepo;

    /**
     * @var EntityTransformerInterface
     */
    protected $itemTransformer;

    /**
     * @var PaginatedListTransformer
     */
    protected $paginatedListTransformer;

    /**
     * @var EntityChangeRepositoryInterface
     */
    protected $entityChangeRepo;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var EntityManipulatorInterface
     */
    protected $entityManipulator;

    /**
     * @param CRUDRepositoryInterface         $itemRepo
     * @param EntityTransformerInterface      $itemTransformer
     * @param PaginatedListTransformer        $paginatedListTransformer
     * @param SerializerInterface             $serializer
     * @param EntityChangeRepositoryInterface $entityChangeRepo
     * @param SecurityContextInterface        $securityContext
     * @param EntityManipulatorInterface      $entityManipulator
     */
    public function __construct(
        CRUDRepositoryInterface $itemRepo,
        EntityTransformerInterface $itemTransformer,
        PaginatedListTransformer $paginatedListTransformer,
        SerializerInterface $serializer,
        EntityChangeRepositoryInterface $entityChangeRepo,
        SecurityContextInterface $securityContext,
        EntityManipulatorInterface $entityManipulator
    )
    {
        $this->itemRepo                 = $itemRepo;
        $this->itemTransformer          = $itemTransformer;
        $this->paginatedListTransformer = $paginatedListTransformer;
        $this->serializer               = $serializer;
        $this->entityChangeRepo         = $entityChangeRepo;
        $this->securityContext          = $securityContext;
        $this->entityManipulator        = $entityManipulator;
    }

    /**
     * Returns the paginated list of items.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listItemsAction(Request $request)
    {
        $options = new PaginatedQueryOptions();
        Option::fromValue($request->query->get('sortField'))->map(function ($sortField) use ($options) {
            $options->setSortField($sortField);
        });
        Option::fromValue($request->query->get('sortDir'))->map(function ($sortDir) use ($options) {
            $options->setSortDir($sortDir);
        });
        Option::fromValue($request->query->get('offsetKey'))->map(function ($offsetKey) use ($options) {
            $options->setOffsetKey($offsetKey);
        });
        $filterQueryParser = new FilterQueryParser();
        $filterQuery       = $filterQueryParser->parse($request->query->get('q'));
        if (!$this->isAdmin()) {
            $filterQuery->setUser($this->getUser());
        }
        $paginatedList = $this->createListing(
            $this->itemRepo,
            $this->paginatedListTransformer,
            $this->itemTransformer,
            $options,
            $filterQuery,
            $request->attributes->get('_route')
        );

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($paginatedList, 'json'));
        return $response;
    }

    /**
     * @param CRUDRepositoryInterface    $repo
     * @param PaginatedListTransformer   $listTransformer
     * @param EntityTransformerInterface $itemTransformer
     * @param PaginatedQueryOptions      $options
     * @param FilterQuery                $filterQuery
     * @param string                     $route
     *
     * @return \Dothiv\APIBundle\Model\PaginatedList
     */
    protected function createListing(
        CRUDRepositoryInterface $repo,
        PaginatedListTransformer $listTransformer,
        EntityTransformerInterface $itemTransformer,
        PaginatedQueryOptions $options,
        FilterQuery $filterQuery,
        $route
    )
    {
        $paginatedResult = $repo->getPaginated($options, $filterQuery);
        $paginatedList   = $listTransformer->transform($paginatedResult, $route);
        foreach ($paginatedResult->getResult() as $reg) {
            $paginatedList->addItem($itemTransformer->transform($reg, null, true));
        }
        return $paginatedList;
    }

    /**
     * Returns a single item
     *
     * @param string $identifier
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     */
    public function getItemAction($identifier)
    {
        $item = $this->itemRepo->getItemByIdentifier($identifier)->getOrCall(function () use ($identifier) {
            throw new NotFoundHttpException(
                sprintf('No item with identifier "%s" found!', $identifier)
            );
        });

        $this->checkPermission($identifier, $item);

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($this->itemTransformer->transform($item), 'json'));
        return $response;
    }

    /**
     * @return bool
     */
    protected function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getUser()->getRoles());
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->securityContext->getToken()->getUser();
    }

    /**
     * Updates the item with the identifier $identifier
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return Response
     */
    public function updateItemAction(Request $request, $identifier)
    {
        /** @var EntityInterface $item */
        $item = $this->itemRepo->getItemByIdentifier($identifier)->getOrCall(function () use ($identifier) {
            throw new NotFoundHttpException(
                sprintf('No item with identifier "%s" found!', $identifier)
            );
        });

        try {
            $newPropertyValues = json_decode($request->getContent());
            $change            = $this->updateItem($item, (array)$newPropertyValues);
            $this->entityChangeRepo->persist($change)->flush();
            return $this->createNoContentResponse();
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * @param EntityInterface $item
     * @param array           $newPropertyValues
     *
     * @return EntityChange
     */
    protected function updateItem(EntityInterface $item, array $newPropertyValues)
    {
        $change = new EntityChange();
        $change->setAuthor(new EmailValue($this->securityContext->getToken()->getUser()->getEmail()));
        $change->setEntity($this->itemRepo->getItemEntityName($item));
        $change->setIdentifier(new IdentValue($item->getPublicId()));

        $changes = $this->entityManipulator->manipulate($item, $newPropertyValues);
        $change->setChanges($changes);
        return $change;
    }

    /**
     * @param string          $identifier
     * @param EntityInterface $item
     *
     * @throws AccessDeniedHttpException
     */
    protected function checkPermission($identifier, EntityInterface $item)
    {
        if (!$this->isAdmin()) {
            if (!method_exists($item, 'getUser')) {
                throw new AccessDeniedHttpException(sprintf('Item "%s" has no user!', $this->itemRepo->getItemEntityName($item)));
            }
            if ($item->getUser() !== $this->getUser()) {
                throw new AccessDeniedHttpException(
                    sprintf('Item "%s" with id "%s" does not belong to user!', $this->itemRepo->getItemEntityName($item), $identifier)
                );
            }
        }
    }
}
