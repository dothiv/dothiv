<?php


namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Request\RecoverPasswordRequest;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Model\FilterQuery;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedQueryOptions;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\ValueObject\ClockValue;
use Symfony\Component\HttpFoundation\Request;
use Dothiv\APIBundle\Annotation\ApiRequest;

/**
 * This controller manages the lost password functionality.
 */
class RecoverPasswordController extends AbstractCRUDController implements CRUDControllerInterface
{
    use Traits\CreateJsonResponseTrait;

    /**
     * @param UserRepositoryInterface              $userRepo
     * @param UserTokenRepositoryInterface         $tokenRepo
     * @param UserProfileChangeRepositoryInterface $userChangeRepo
     * @param UserServiceInterface                 $userService
     * @param ClockValue                           $clock
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        UserTokenRepositoryInterface $tokenRepo,
        UserProfileChangeRepositoryInterface $userChangeRepo,
        UserServiceInterface $userService,
        ClockValue $clock
    )
    {
        $this->userRepo       = $userRepo;
        $this->tokenRepo      = $tokenRepo;
        $this->userChangeRepo = $userChangeRepo;
        $this->clock          = $clock;
        $this->userService    = $userService;
    }

    /**
     * SECURITY NOTICE: This action must always returns this response, even if the user is not found to prevent fishing
     * for existing user accounts.
     *
     * {@inheritdoc}
     * @ApiRequest("Dothiv\APIBundle\Request\RecoverPasswordRequest")
     */
    public function createItemAction(Request $request)
    {
        $response = $this->createNoContentResponse();
        /** @var RecoverPasswordRequest $model */
        $model        = $request->attributes->get('model');
        $userOptional = $this->userRepo->getUserByEmail($model->email);
        if ($userOptional->isDefined()) {
            /** @var User $user */
            $user        = $userOptional->get();
            $filterQuery = new FilterQuery();
            $filterQuery->setUser($user);
            $filterQuery->setProperty('confirmed', false);
            $filterQuery->setProperty('property', 'password');
            $after = $this->clock->getNow()->modify('-24hours');
            $filterQuery->setProperty('created', $after, '>');
            $passwordChangeRequests = $this->userChangeRepo->getPaginated(new PaginatedQueryOptions(), $filterQuery);
            if ($passwordChangeRequests->getTotal() == 0) {
                $user->setPassword($model->password);
                $this->userService->updateUser($user, $request);
            }
        }
        return $response;
    }
}
