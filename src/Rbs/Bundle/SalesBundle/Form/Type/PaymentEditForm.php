<?php

namespace Rbs\Bundle\SalesBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Rbs\Bundle\SalesBundle\Entity\AgentBank;
use Rbs\Bundle\SalesBundle\Entity\Order;
use Rbs\Bundle\SalesBundle\Entity\Payment;
use Rbs\Bundle\SalesBundle\Repository\AgentBankRepository;
use Rbs\Bundle\SalesBundle\Repository\AgentRepository;
use Rbs\Bundle\SalesBundle\Repository\OrderRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Rbs\Bundle\CoreBundle\Form\Transformer\BankAccountTransformer;

class PaymentEditForm extends AbstractType
{
    /** @var Request */
    private $request;

    /** @var  EntityManager */
    private $em;
    /** @var  Payment $payment */
    private $payment;
    public function __construct($entityManager, $request = null, Payment $payment)
    {
        $this->em = $entityManager;
        $this->request = $request;
        $this->payment= $payment;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('bankAccount', 'choice', array(
                'required' => false,
                'choices' => $this->getAccountList(),
                'attr' => array('class' => 'select2me')
            ))

            ->add('agentBankBranch', 'entity', array(
                'class' => 'RbsSalesBundle:AgentBank',
                'attr' => array(
                    'class' => 'select2me'
                ),
                'property' => 'getBankBranchName',
                'empty_value' => 'Select Agent Bank',
                'empty_data' => null,
                'query_builder' => function (AgentBankRepository $repository)
                {
                    return $repository->createQueryBuilder('ab')
                        ->where('ab.deletedAt IS NULL')
                        ->andWhere('ab.agent = :agent')
                        ->setParameter('agent',$this->payment->getAgent());
                    }
            ))

        ;

        $builder->get('bankAccount')
            ->addModelTransformer(new BankAccountTransformer($this->em));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Rbs\Bundle\SalesBundle\Entity\Payment',
            'payment'=> null
        ));
    }

    public function getName()
    {
        return 'payment_edit';
    }

    private function getAccountList()
    {
        return $this->em->getRepository('RbsCoreBundle:BankAccount')->getAccountListWithBankBranch();
    }
}
