<?php 

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;



class ProductController {
    public function addProduct(
        Environment $twig, 
        FormFactoryInterface $factory, 
        Request $request, 
        ObjectManager $manager,
        SessionInterface $session
        ) 
    {   
        $product = new Product();
        $builder = $factory->createBuilder(FormType::class, $product);
        $builder->add('name', TextType::class)
        ->add('description', TextareaType::class,
            ['required' => false,
                'label' => 'this label has been changed',
                'attr' => [
                    'placeholder' => 'blabla',
                    'class' => 'form-control'
                    ]
            ]
        )
        ->add('version', TextType::class)
        ->add(
            'submit',
            SubmitType::class,
            [
                'attr' => [
                    'class' => 'btn-lbock btn-success'
                ]
            ]);	
        
        $form = $builder->getForm();
        //if the form is submitted
            //then, if the form is valid
                //then var_dum the data
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($product);
            $manager->flush();
            /* var_dump($product); => to display in the page add/product */
            
            //to validate the creation of product
            
            $session->getFlashBag()->add('info', 'your product was created');
            
            return new RedirectResponse('/');
        }
        
        
        //create a template
        return new Response(
            $twig->render('Product/addProduct.html.twig',
                [
                    'formular' => $form->createView(),
                    'isTrue' => true
                ]
            )
        );
    }
}



?>
