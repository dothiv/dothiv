<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Exception\AccessDeniedHttpException;
use Dothiv\APIBundle\Exception\BadRequestHttpException;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Exception\NotFoundHttpException;
use Dothiv\BusinessBundle\Repository\CRUD;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CRUDControllerInterface
{
    /**
     * Returns the paginated list of items.
     *
     * @param Request $request
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function listItemsAction(Request $request);

    /**
     * Returns a single item
     *
     * @param string $identifier
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     */
    public function getItemAction($identifier);

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
     */
    public function updateItemAction(Request $request, $identifier);

    /**
     * Creates a new item.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedHttpException
     */
    public function createItemAction(Request $request);

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
     */
    public function deleteItemAction(Request $request, $identifier);

    /**
     * Disable storing of entity changes.
     *
     * @return self
     */
    public function disableHistory();

    /**
     * This controller is used in an admin context.
     */
    public function makeAdminController();

    /**
     * @return bool
     */
    public function isAdminController();

    /**
     * @return bool
     */
    public function isUserController();

    /**
     * @param callback $itemCreator
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function setItemCreator($itemCreator);
}
