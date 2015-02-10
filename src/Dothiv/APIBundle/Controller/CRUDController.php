<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Annotation\ApiRequest;
use Dothiv\APIBundle\Exception\AccessDeniedHttpException;
use Dothiv\APIBundle\Exception\BadRequestHttpException;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Exception\NotFoundHttpException;
use Dothiv\APIBundle\Exception\UnprocessableEntityHttpException;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\APIBundle\Transformer\PaginatedListTransformer;
use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\CRUD\OwnerEntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\BusinessBundle\Service\FilterQueryParser;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * FIXME: Replace transformers with serializers
 */
class CRUDController
{
    use CreateJsonResponseTrait;

    /**
     * @var CRUD\EntityRepositoryInterface
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var bool
     */
    protected $storeHistory = true;

    /**
     * @var bool
     */
    protected $isAdminController = false;

    /**
     * @param CRUD\EntityRepositoryInterface  $itemRepo
     * @param EntityTransformerInterface      $itemTransformer
     * @param PaginatedListTransformer        $paginatedListTransformer
     * @param SerializerInterface             $serializer
     * @param EntityChangeRepositoryInterface $entityChangeRepo
     * @param SecurityContextInterface        $securityContext
     * @param EntityManipulatorInterface      $entityManipulator
     * @param EventDispatcherInterface        $eventDispatcher
     */
    public function __construct(
        CRUD\EntityRepositoryInterface $itemRepo,
        EntityTransformerInterface $itemTransformer,
        PaginatedListTransformer $paginatedListTransformer,
        SerializerInterface $serializer,
        EntityChangeRepositoryInterface $entityChangeRepo,
        SecurityContextInterface $securityContext,
        EntityManipulatorInterface $entityManipulator,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->itemRepo                 = $itemRepo;
        $this->itemTransformer          = $itemTransformer;
        $this->paginatedListTransformer = $paginatedListTransformer;
        $this->serializer               = $serializer;
        $this->entityChangeRepo         = $entityChangeRepo;
        $this->securityContext          = $securityContext;
        $this->entityManipulator        = $entityManipulator;
        $this->eventDispatcher          = $eventDispatcher;
    }

    /**
     * Returns the paginated list of items.
     *
     * @param Request $request
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function listItemsAction(Request $request)
    {
        if (!($this->itemRepo instanceof CRUD\PaginatedReadEntityRepositoryInterface)) {
            throw new BadRequestHttpException(sprintf('"%s" items must not be listed!', get_class($this->itemRepo)));
        }
        $options = new CRUD\PaginatedQueryOptions();
        try {
            Option::fromValue($request->query->get('sortField'))->map(function ($sortField) use ($options) {
                $options->setSortField(new IdentValue($sortField));
            });
            Option::fromValue($request->query->get('sortDir'))->map(function ($sortDir) use ($options) {
                $options->setSortDir($sortDir);
            });
            Option::fromValue($request->query->get('offsetKey'))->map(function ($offsetKey) use ($options) {
                $options->setOffsetKey($offsetKey);
            });
        } catch (\Dothiv\BusinessBundle\Exception\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $filterQueryParser = new FilterQueryParser();
        $filterQuery       = $filterQueryParser->parse($request->query->get('q'));
        if ($this->isUserController()) {
            $filterQuery->setUser($this->getUser());
        }
        $paginatedList = $this->createListing(
            $this->itemRepo,
            $this->paginatedListTransformer,
            $this->itemTransformer,
            $options,
            $filterQuery,
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params')
        );

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($paginatedList, 'json'));
        return $response;
    }

    /**
     * @param CRUD\PaginatedReadEntityRepositoryInterface $repo
     * @param PaginatedListTransformer                    $listTransformer
     * @param EntityTransformerInterface                  $itemTransformer
     * @param CRUD\PaginatedQueryOptions                  $options
     * @param FilterQuery                                 $filterQuery
     * @param string                                      $route
     * @param array                                       $routeParams
     *
     * @return \Dothiv\APIBundle\Model\PaginatedList
     */
    protected function createListing(
        CRUD\PaginatedReadEntityRepositoryInterface $repo,
        PaginatedListTransformer $listTransformer,
        EntityTransformerInterface $itemTransformer,
        CRUD\PaginatedQueryOptions $options,
        FilterQuery $filterQuery,
        $route,
        array $routeParams
    )
    {
        $paginatedResult = $repo->getPaginated($options, $filterQuery);
        $paginatedList   = $listTransformer->transform($paginatedResult, $route, $routeParams);
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

        $this->checkPermission($item);

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
     * @throws BadRequestHttpException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @ApiRequest("Dothiv\APIBundle\Request\DefaultUpdateRequest")
     */
    public function updateItemAction(Request $request, $identifier)
    {
        if (!($this->itemRepo instanceof CRUD\UpdateEntityRepositoryInterface)) {
            throw new BadRequestHttpException(sprintf('"%s" items must not be updated!', get_class($this->itemRepo)));
        }
        /** @var CRUD\UpdateEntityRepositoryInterface $repo */
        $repo = $this->itemRepo;
        /** @var EntityInterface $item */
        $item = $repo->getItemByIdentifier($identifier)->getOrCall(function () use ($identifier) {
            throw new NotFoundHttpException(
                sprintf('No item with identifier "%s" found!', $identifier)
            );
        });

        $this->checkPermission($item);
        $change = $this->updateItem($item, $request->attributes->get('model'));
        $repo->persistItem($item)->flush();
        $this->eventDispatcher->dispatch(BusinessEvents::ENTITY_CHANGED, new EntityChangeEvent($change, $item));
        return $this->createNoContentResponse();
    }

    /**
     * @param EntityInterface    $item
     * @param DataModelInterface $data
     *
     * @return EntityChange
     * @throws UnprocessableEntityHttpException
     */
    protected function updateItem(EntityInterface $item, DataModelInterface $data)
    {
        $changes = $this->entityManipulator->manipulate($item, $data);
        if (!$changes) {
            throw new UnprocessableEntityHttpException('Entity unchanged.');
        }
        $change = new EntityChange();
        $change->setAuthor(new EmailValue($this->securityContext->getToken()->getUser()->getEmail()));
        $change->setEntity($this->itemRepo->getItemEntityName($item));
        $change->setIdentifier(new IdentValue($item->getPublicId()));
        $change->setChanges($changes);
        if ($this->storeHistory) {
            $this->entityChangeRepo->persist($change)->flush();
        }
        return $change;
    }

    /**
     * Creates a new item.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     * @ApiRequest("Dothiv\APIBundle\Request\DefaultCreateRequest")
     */
    public function createItemAction(Request $request)
    {
        if (!($this->itemRepo instanceof CRUD\CreateEntityRepositoryInterface)) {
            throw new BadRequestHttpException();
        }
        /** @var CRUD\CreateEntityRepositoryInterface $repo */
        $repo = $this->itemRepo;
        $item = $this->itemRepo->createItem();
        if ($this->isUserController()) {
            if (!($item instanceof OwnerEntityInterface)) {
                throw new AccessDeniedHttpException(sprintf('"%s" items have no owner!', get_class($this->itemRepo)));
            }
            $item->setOwner($this->getUser());
        }

        try {
            $this->entityManipulator->manipulate($item, $request->attributes->get('model'));
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Verify owner
        $this->checkPermission($item);
        $repo->persistItem($item)->flush();
        $this->eventDispatcher->dispatch(BusinessEvents::ENTITY_CREATED, new EntityEvent($item));
        $model    = $this->itemTransformer->transform($item, null, false);
        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->headers->set('Location', $model->getJsonLdId());
        $response->setContent($this->serializer->serialize($model, 'json'));
        return $response;
    }

    /**
     * Deletes item with the identifier $identifier
     *
     * @param Request $request
     * @param string  $identifier
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @ApiRequest("Dothiv\APIBundle\Request\DefaultUpdateRequest")
     */
    public function deleteItemAction(Request $request, $identifier)
    {
        if (!($this->itemRepo instanceof CRUD\DeleteEntityRepositoryInterface)) {
            throw new BadRequestHttpException(sprintf('"%s" items must not be deleted!', get_class($this->itemRepo)));
        }
        /** @var CRUD\DeleteEntityRepositoryInterface $repo */
        $repo = $this->itemRepo;
        /** @var EntityInterface $item */
        $item = $repo->getItemByIdentifier($identifier)->getOrCall(function () use ($identifier) {
            throw new NotFoundHttpException(
                sprintf('No item with identifier "%s" found!', $identifier)
            );
        });

        $this->checkPermission($item);

        $repo->deleteItem($item)->flush();
        $this->eventDispatcher->dispatch(BusinessEvents::ENTITY_DELETED, new EntityEvent($item));
        return $this->createNoContentResponse();
    }

    /**
     * @param EntityInterface $item
     *
     * @throws AccessDeniedHttpException
     */
    protected function checkPermission(EntityInterface $item)
    {
        if ($this->isUserController() || !$this->isAdmin()) {
            if (!($item instanceof OwnerEntityInterface)) {
                throw new AccessDeniedHttpException(sprintf('"%s" items have no owner!', $this->itemRepo->getItemEntityName($item)));
            }
            if ($item->getOwner() !== $this->getUser()) {
                throw new AccessDeniedHttpException(
                    sprintf(
                        'Item "%s" with id "%s" does not belong to user "%s"!',
                        $this->itemRepo->getItemEntityName($item),
                        $item->getPublicId(),
                        $this->getUser()->getHandle()
                    )
                );
            }
        }
    }

    /**
     * Disable storing of entity changes.
     *
     * @return self
     */
    public function disableHistory()
    {
        $this->storeHistory = false;
        return $this;
    }

    /**
     * This controller is used in an admin context.
     */
    public function makeAdminController()
    {
        $this->isAdminController = true;
    }

    /**
     * @return bool
     */
    public function isAdminController()
    {
        return $this->isAdminController;
    }

    /**
     * @return bool
     */
    public function isUserController()
    {
        return !$this->isAdminController();
    }
}
