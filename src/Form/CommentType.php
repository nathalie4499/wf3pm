<?php
namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('comment', TextareaType::class)
               ->add(
                   'files',
                   CollectionType::class,
                   ['entry_type' => CommentFileType::class,
                    'allow_add' => true
                       
                   ]
                );
        
        if ($options['stateless']) {
            $builder->add('submit', SubmitType::class);
        }      
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::configureOptions()
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setdefault('data_type', Comment::class);
        $resolver->setdefault('stateless', false); //not nested into another form     
        
    }

    
}

