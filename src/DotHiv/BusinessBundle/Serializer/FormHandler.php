<?php

namespace DotHiv\BusinessBundle\Serializer;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormError;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\GenericSerializationVisitor;
use Symfony\Component\Form\Form;
use Symfony\Component\Locale\Exception\NotImplementedException;
use JMS\Serializer\Handler\FormErrorHandler;

/**
 * Extends the JMS FormHandler to serialize the actual form data for unbound forms.
 * For bound forms, the behavior stays the same.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class FormHandler extends FormErrorHandler {

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        parent::__construct($translator);
    }

    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    private function convertFormToArray(GenericSerializationVisitor $visitor, Form $data, $serializeData = false)
    {
        $isRoot = null === $visitor->getRoot();

        $children = array();
        $form = $errors = array();
        
        if (!$data) {
            return $form;
        }

        if (($isRoot && !$data->isBound()) || $serializeData) {

            foreach ($data->all() as $child) {
                if (count($child->getChildren()) > 0) {
                    $form[$child->getName()] = $this->convertFormToArray($visitor, $child, true);
                } else {
                    $form[$child->getName()] = $child->getData();
                }
            }

        } else {

            foreach ($data->getErrors() as $error) {
                $errors[] = $this->getErrorMessage($error);
            }

            if ($errors) {
                $form['errors'] = $errors;
            }

            foreach ($data->all() as $child) {
                $children[$child->getName()] = $this->convertFormToArray($visitor, $child);
            }

        }

        if ($children) {
            $form['children'] = $children;
        }

        if ($isRoot) {
            $visitor->setRoot($form);
        }

        return $form;
    }

    public function serializeFormToXml(XmlSerializationVisitor $visitor, Form $form, array $type)
    {
        throw new NotImplementedException();
    }

    private function getErrorMessage(FormError $error)
    {
        if (null !== $error->getMessagePluralization()) {
            return $this->translator->transChoice($error->getMessageTemplate(), $error->getMessagePluralization(), $error->getMessageParameters(), 'validators');
        }

        return $this->translator->trans($error->getMessageTemplate(), $error->getMessageParameters(), 'validators');
    }

}
