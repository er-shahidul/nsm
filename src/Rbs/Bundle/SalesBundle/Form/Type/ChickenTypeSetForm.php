<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Rbs\Bundle\CoreBundle\Entity\ItemType;
use Rbs\Bundle\CoreBundle\Repository\ItemRepository;
use Rbs\Bundle\SalesBundle\RbsSalesBundle;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChickenTypeSetForm extends AbstractType
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('agent', 'entity', array(
                'class' => 'RbsSalesBundle:Agent',
                'property' => 'name',
                'required' => true,
                'attr' => array(
                    'class' => 'select2me'
                ),
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Agent should not be blank'
                    )),
                ),
                'empty_value' => 'Select Agent',
                'query_builder' => function (AgentRepository $repository)
                {
                    return $repository->createQueryBuilder('c')
                        ->join('c.user', 'u')
                        ->join('u.profile', 'p')
                        ->join('c.itemType ', 'it')
                        ->where('u.deletedAt IS NULL')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.zilla = :zilla')
                        ->andWhere('u.userType = :AGENT')
                        ->andWhere('it.itemType = :itemType')
                        ->setParameter('AGENT', User::AGENT)
                        ->setParameter('itemType', ItemType::Chick)
                        ->setParameter('zilla', $this->user->getZilla())
                        ->orderBy('p.fullName','ASC');
                }
            ))
            ->add('item', 'entity', array(
                'class' => 'RbsCoreBundle:Item',
                'property' => 'name',
                'required' => true,
                'empty_value' => 'Select Item',
                'query_builder' => function (ItemRepository $repository)
                {
                    return $repository->createQueryBuilder('i')
                        ->join('i.itemType', 'it')
                        ->where('i.deletedAt IS NULL')
                        ->andWhere('it.itemType = :chicken')->setParameter('chicken', ItemType::Chick)
                        ->orderBy('i.name','ASC')
                        ->join('i.bundles', 'bundles')
                        ->andWhere('bundles.id = :saleBundleId')->setParameter('saleBundleId', RbsSalesBundle::ID);
                },
                'constraints' => array(
                    new NotBlank(array(
                        'message'=>'Item should not be blank'
                    )),
                ),
            ))
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
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\ChickenSetForAgent'
        ));
    }

    public function getName()
    {
        return 'chicken_set_for_agent';
    }
}
