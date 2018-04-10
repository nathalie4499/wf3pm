<?php
namespace App\Form;

use App\Entity\CommentFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Dto\FileDto;

class CommentFileType extends AbstractType
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Form\AbstractType::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class);
        
        if ($options['stateless']) {
        $builder->add('submit', SubmitType::class);
            
        }

        
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setdefault('data_type', FileDto::class);
        $resolver->setdefault('stateless', false); //not nested into another form       
        
    }
    
}



