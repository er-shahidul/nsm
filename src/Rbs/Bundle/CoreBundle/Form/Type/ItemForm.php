<?php

namespace Rbs\Bundle\CoreBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Repository\CategoryRepository;
use Rbs\Bundle\CoreBundle\Repository\ItemTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ItemForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', 'choice', array(
                'choices' => array('Disable', 'Enable'),
                'attr' => array(
                    'class' => 'input-small'
                )
            ))
            ->add('name')
            ->add('sku', null, array(
                'label' => 'Item Code'
            ))
            ->add('itemUnit')
            ->add('itemType', 'entity', array(
                'attr' => array('class' => 'select2me'),
                'class' => 'Rbs\Bundle\CoreBundle\Entity\ItemType',
                'query_builder' => function(ItemTypeRepository $itemTypeRepository) {
                    return $itemTypeRepository->createQueryBuilder('it')
                        ->andWhere("it.deletedAt IS NULL");
                },
                'empty_value' => 'Select itemType',
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'ItemType should not be blank'
                    )),
                ),
                'property' => 'itemType',
                'required' => true
            ))
            ->add('category', 'entity', array(
                'class' => 'Rbs\Bundle\CoreBundle\Entity\Category',
                'query_builder' => function(CategoryRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->andWhere("c.deletedAt IS NULL");
                },
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Category should not be blank'
                    )),
                ),
                'property' => 'name',
                'multiple' => true,
                'required' => true
            ))
            ->add('bundles', null, array(
                'label' => 'Modules'
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\CoreBundle\Entity\Item'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rbs_bundle_corebundle_item';
    }
}
