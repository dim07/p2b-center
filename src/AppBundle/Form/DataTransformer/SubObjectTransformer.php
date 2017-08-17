<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubobjectTransformer
 *
 * @author dima
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\SubObject;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SubObjectTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (sub) to a string (number).
     *
     * @param  SubObject|null $sub
     * @return string
     */
    public function transform($sub)
    {
        if (null === $sub) {
            return '';
        }

        return $sub->getId();
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $subId
     * @return SubObject|null
     * @throws TransformationFailedException if object (sub) is not found.
     */
    public function reverseTransform($subId)
    {
        // no issue number? It's optional, so that's ok
        if (!$subId) {
            return;
        }

        $sub = $this->manager
            ->getRepository('AppBundle:SubObject')
            // query for the issue with this id
            ->find($subId)
        ;

        if (null === $sub) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An subobject with id "%s" does not exist!',
                $subId
            ));
        }

        return $sub;
    }
}

