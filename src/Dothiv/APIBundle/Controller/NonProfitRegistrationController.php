<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Request\NonProfitRegistrationRequest;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Dothiv\APIBundle\Annotation\ApiRequest;

class NonProfitRegistrationController extends BaseController
{

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var NonProfitRegistrationRepositoryInterface
     */
    private $nonProfitRegistrationRepo;

    /**
     * @var AttachmentRepositoryInterface
     */
    private $attachmentRepo;

    public function __construct(
        NonProfitRegistrationRepositoryInterface $nonProfitRegistrationRepo,
        AttachmentRepositoryInterface $attachmentRepo,
        SecurityContext $securityContext,
        Serializer $serializer)
    {
        $this->nonProfitRegistrationRepo = $nonProfitRegistrationRepo;
        $this->attachmentRepo            = $attachmentRepo;
        $this->securityContext           = $securityContext;
        $this->serializer                = $serializer;
    }

    /**
     * Loads a request
     *
     * @param Request $request
     * @param string  $name
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    public function loadAction(Request $request, $name)
    {
        /* @var User $user */
        /* @var NonProfitRegistration $registration */
        $user = $this->securityContext->getToken()->getUser();
        if (empty($user)) {
            throw new AccessDeniedHttpException();
        }

        $registration = $this->nonProfitRegistrationRepo->getNonProfitRegistrationByDomainName($name)->getOrCall(function () {
            throw new NotFoundHttpException();
        });

        if ($registration->getUser()->getHandle() != $user->getHandle()) {
            throw new AccessDeniedHttpException();
        }

        $response = $this->createResponse();
        $response->setStatusCode(200);
        $response->setContent($this->serializer->serialize($registration, 'json'));
        return $response;
    }

    /**
     * Creates a new request
     *
     * @param Request $request
     * @param string  $name
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     * @throws AccessDeniedHttpException
     *
     * @ApiRequest("Dothiv\APIBundle\Request\NonProfitRegistrationRequest")
     */
    public function updateAction(Request $request, $name)
    {
        /* @var User $user */
        /* @var NonProfitRegistrationRequest $model */
        /* @var Attachment $proof */
        /* @var NonProfitRegistration $registration */
        $user = $this->securityContext->getToken()->getUser();
        if (empty($user)) {
            throw new AccessDeniedHttpException();
        }

        $optionalRegistration = $this->nonProfitRegistrationRepo->getNonProfitRegistrationByDomainName($name);

        if ($optionalRegistration->isDefined() && $optionalRegistration->get()->getUser()->getHandle() != $user->getHandle()) {
            throw new AccessDeniedHttpException();
        }

        $model = $request->attributes->get('model');

        $proof = $this->attachmentRepo->getAttachmentByHandle($model->proof)->getOrCall(function () {
            throw new BadRequestHttpException();
        });

        $registration = $optionalRegistration->getOrElse(new NonProfitRegistration());
        $registration->setUser($user);
        $registration->setDomain($name);
        $registration->setPersonFirstname($model->personFirstname);
        $registration->setPersonSurname($model->personSurname);
        $registration->setPersonEmail($model->personEmail);
        $registration->setPersonPhone($model->personPhone);
        $registration->setPersonFax($model->personFax);
        $registration->setOrganization($model->organization);
        $registration->setOrgPhone($model->orgPhone);
        $registration->setOrgFax($model->orgFax);
        $registration->setProof($proof);
        $registration->setAbout($model->about);
        $registration->setField($model->field);
        $registration->setPostcode($model->postcode);
        $registration->setLocality($model->locality);
        $registration->setCountry($model->country);
        $registration->setWebsite($model->website);
        $registration->setForward($model->forward);

        try {
            $this->nonProfitRegistrationRepo->persist($registration)->flush();
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $response = $this->createResponse();
        $response->setStatusCode($optionalRegistration->isEmpty() ? 201 : 200);
        $response->setContent($this->serializer->serialize($registration, 'json'));
        return $response;
    }
}
