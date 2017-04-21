<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/21/17
 * Time: 10:29 AM
 */
namespace AppBundle\Service\Traits;

trait ValidatorTrait
{

    protected $validator;

    protected function validateEntity($entity){

        $validationErrors = $this->validator->validate($entity);
        $errors = [];
        for($i = 0 ; $i < count($validationErrors) ; $i++){
            $error = $validationErrors->get($i);
            $property = $error->getPropertyPath();
            $errors[] = $property.': '.$error;
        }
        return $errors;
    }
}