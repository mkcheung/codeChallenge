<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 2:54 PM
 */

namespace AppBundle\Form\RegionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegionCreate extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('region', TextType::class, [
                'required' => false
            ])
            ->add('region_abbrev', TextType::class, [
                'required' => false
            ])
            ->add('Submit', SubmitType::class, array(
                'attr' => array('label' => 'Submit'),
            ));
    }

    public function getName()
    {
        return null;
    }

}