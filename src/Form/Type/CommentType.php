<?php


namespace MicroCMS\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('email', EmailType::class, [
//                'label' => 'Votre email',
//                'attr' => ['placeholder' => '(ne sera pas publié)'],
//                'required' => true,
//                'invalid_message' => 'Cet email n\'est pas valide',
//                'constraints' => new Assert\Email(['checkMX' => true])
//            ])
//            ->add('website', UrlType::class, [
//                'label' => 'Your website',
//                'required' => false,
//                'constraints' => new Assert\Url()
//            ])
            ->add('content', TextareaType::class, [
                'label' => 'Your comment',
                'required' => true,
                'constraints' => new Assert\NotBlank()
            ]);
    }

    public function getName()
    {
        return 'comment';
    }
}