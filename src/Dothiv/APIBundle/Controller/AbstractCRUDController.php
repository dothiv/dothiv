<?php


namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Exception\NotImplementedHttpException;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCRUDController implements CRUDControllerInterface
{

    /**
     * @var bool
     */
    protected $storeHistory = true;

    /**
     * @var bool
     */
    protected $isAdminController = false;

    /**
     * @var callable
     */
    protected $itemCreator;

    /**
     * {@inheritdoc}
     */
    public function listItemsAction(Request $request)
    {
        throw new NotImplementedHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function getItemAction($identifier)
    {
        throw new NotImplementedHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function updateItemAction(Request $request, $identifier)
    {
        throw new NotImplementedHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function createItemAction(Request $request)
    {
        throw new NotImplementedHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItemAction(Request $request, $identifier)
    {
        throw new NotImplementedHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function disableHistory()
    {
        $this->storeHistory = false;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function makeAdminController()
    {
        $this->isAdminController = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAdminController()
    {
        return $this->isAdminController;
    }

    /**
     * {@inheritdoc}
     */
    public function isUserController()
    {
        return !$this->isAdminController();
    }

    /**
     * {@inheritdoc}
     */
    public function setItemCreator($itemCreator)
    {
        if (!is_callable($itemCreator)) {
            throw new InvalidArgumentException('Argument passed to setItemCreator() is not callable.');
        }
        $this->itemCreator = $itemCreator;
        return $this;
    }

}
