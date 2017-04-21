<?php
/**
 * Created by PhpStorm.
 * User: marscheung
 * Date: 4/13/17
 * Time: 2:54 PM
 */

namespace AppBundle\Form\RegionType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegionEdit extends AbstractType
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
            ->add('id', HiddenType::class, [])
            ->add('Submit', SubmitType::class, array(
                'attr' => array('label' => 'Submit'),
            ));
    }

    public function getName()
    {
        return null;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Region'
        ]);
    }
}