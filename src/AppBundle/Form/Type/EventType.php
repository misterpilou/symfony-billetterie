<?php

namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class EventType
 */
class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'admin.form.event.name',
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'admin.form.event.date',
                'widget'   => 'single_text',
                'format'   => 'dd/MM/yyyy HH:mm',
                'attr' => ['class' => 'js-datetimepicker'],
            ])
            ->add('location', TextType::class, [
                'label' => 'admin.form.event.location',
            ])
            ->add('eventType', EntityType::class, [
                'label' => 'admin.form.event.event_type',
                'class' => 'AppBundle:EventType',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('eventType')
                        ->orderBy('eventType.name', 'ASC');
                },
                'choice_label' => 'name'
            ])
            ->add('stocks', CollectionType::class, [
                'entry_type' => StockType::class
            ]);
    }
}
