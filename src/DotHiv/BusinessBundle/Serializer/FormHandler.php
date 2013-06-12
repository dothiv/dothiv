<?php

namespace DotHiv\BusinessBundle\Serializer;

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

    public function serializeFormToJson(JsonSerializationVisitor $visitor, Form $form, array $type)
    {
        return $this->convertFormToArray($visitor, $form);
    }

    private function convertFormToArray(GenericSerializationVisitor $visitor, Form $data, $serializeData = false)
    {
        $isRoot = null === $visitor->getRoot();

        $children = array();

        if (($isRoot && !$data->isBound()) || $serializeData) {

            foreach ($data->all() as $child) {
                if (count($child->getChildren()) > 0) {
                    $children[$child->getName()] = $this->convertFormToArray($visitor, $child, true);
                } else {
                    $form[$child->getName()] = $child->getData();
                }
            }

        } else {

            $form = $errors = array();
            foreach ($data->getErrors() as $error) {
                $errors[] = $this->getErrorMessage($error);
            }

            if ($errors) {
                $form['errors'] = $errors;
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

}
