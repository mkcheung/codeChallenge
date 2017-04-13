<?php

namespace AppBundle\Form\MeetingFilterType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MeetingFilterType extends AbstractType
{

    /**
     * @codeCoverageIgnore
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $meetingTypes = $options['data'][0];
        $regions = $options['data'][1];

        $meetingOptions = [];
        $weekDays = [];

        $regionOptions = [
            '' => ''
        ];

        $day_start = date( "d", strtotime( "next Sunday" ) ); // get next Sunday
        for ( $i = 0; $i < 7; $i++ ) {
            $weekday = date( "l", mktime( 0, 0, 0, date( "m" ), $day_start + $i, date( "y" ) ) );
            $weekDays[strtolower($weekday)] = $weekday;
        }

        foreach ($meetingTypes as $meetingType) {
            $meetingOptions[$meetingType->getMeetingTypeInitials()] = $meetingType->getMeetingType();
        }

        foreach ($regions as $region) {
            $regionOptions[$region->getRegionAbbrev()] = $region->getRegion();
        }

        $builder
            ->add('street_address', TextType::class, array(
                'required' => false
            ))
            ->add('city', TextType::class, array(
                'required' => false
            ))
            ->add('state', ChoiceType::class, array(
                'choices'  => $regionOptions,
                'required' => false
            ))
            ->add('zip_code', TextType::class, array(
                'required' => false
            ))
            ->add('meeting_type', ChoiceType::class, array(
                'choices'   => $meetingOptions,
                'multiple'  => true,
                'required' => false
            ))
            ->add('day', ChoiceType::class, array(
                'choices'  => $weekDays,
                'required' => false
            ))
            ->add('Submit', SubmitType::class, array(
                'attr' => array('label' => 'Submit'),
            ));
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return null;
    }

}