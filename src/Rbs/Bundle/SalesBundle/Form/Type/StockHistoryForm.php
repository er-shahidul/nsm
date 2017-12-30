<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\SalesBundle\Repository\FeedMillRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class StockHistoryForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feedMill', 'entity', array(
                'class' => 'Rbs\Bundle\SalesBundle\Entity\FeedMill',
                'property' => 'name',
                'required' => false,
                'empty_value' => 'Select Feed Mill',
                'empty_data' => null,
                'query_builder' => function (FeedMillRepository $repository)
                {
                    return $repository->createQueryBuilder('i')
                        ->where('i.deletedAt IS NULL')
                        ->orderBy('i.name','ASC');
                }
            ))
            ->add('quantity', 'text', array(
                'constraints' => array(
                    new NotBlank(array('message'=>'Name should not be blank'))
                )
            ))
            ->add('stockID', 'hidden', array(
                'mapped' => false
            ))
            ->add($builder->create('created_at', 'text', array(
                'label' => 'Date',
                'attr' => array(
                    'class' => 'date-picker',
                    'placeholder' => 'date-month-Year'
                ),
                'empty_data' => new \DateTime(),
                'required' => true
            ))->addViewTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y')))
            ->add('description')
            ->add('submit', 'submit', array(
                'attr'     => array('class' => 'btn green')
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\StockHistory'
        ));
    }

    public function getName()
    {
        return 'stock';
    }
}
