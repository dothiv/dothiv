<?php

namespace Dothiv\APIBundle\Listener;

use Doctrine\Common\Annotations\Reader;
use Dothiv\APIBundle\Annotation\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ValidatorInterface;

class ViewRequestListener
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Constructor
     *
     * @param Reader             $reader
     * @param ValidatorInterface $validator
     */
    public function __construct(Reader $reader, ValidatorInterface $validator)
    {
        $this->reader    = $reader;
        $this->validator = $validator;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $object     = new \ReflectionObject($controller[0]);
        $method     = $object->getMethod($controller[1]);
        $request    = $event->getRequest();

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if (!($annotation instanceof ApiRequest)) continue;
            /* @var ApiRequest $annotation */

            $modelClass = $annotation->getModel();
            $model      = new $modelClass;

            $this->setModelData($request, $model);

            $errors = $this->validator->validate($model);

            if (count($errors) == 0) {
                $request->attributes->set('model', $model);
                return;
            }

            throw new BadRequestHttpException(
                (string)$errors
            );
        }
    }

    protected function setModelData(Request $request, $model)
    {
        if (stristr($request->headers->get('content-type'), 'application/json') !== false) {
            $this->setModelDataFromJson($request, $model);
        } else {
            $this->setModelDataFromForm($request, $model);
        }
    }

    protected function setModelDataFromJson(Request $request, $model)
    {
        $this->setModelDataFromArray(json_decode($request->getContent()), $model);
    }

    protected function setModelDataFromForm(Request $request, $model)
    {
        $this->setModelDataFromArray($request->query->all(), $model);
    }

    protected function setModelDataFromArray($data, $model)
    {
        foreach ($data as $k => $v) {
            if (property_exists($model, $k)) {
                $model->$k = $v;
            }
        }
    }
}
