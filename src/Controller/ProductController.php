<?php 

namespace App\Controller;

use App\Dto\FileDto;
use App\Entity\Comment;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\ProductRepository;
use App\Form\CommentFileType;
use App\Form\CommentType;
use Symfony\Component\Form\FormFactory;





class ProductController {
    public function addProduct(
        Environment $twig, 
        FormFactoryInterface $factory, 
        Request $request, 
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator
        ) 
    {   
        $product = new Product();
        $builder = $factory->createBuilder(FormType::class, $product);
        $builder->add('name', TextType::class,
            ['label' => 'FORM.PRODUCT.NAME',
             'attr' => [
                 'placeholder' => 'FORM.PRODUCT.PLACEHOLDER.NAME'
                ]               
            ]
        )
        ->add('description', TextareaType::class,
            ['required' => false,
                'label' => 'FORM.PRODUCT.DESCRIPTION',
                'attr' => [
                    'placeholder' => 'FORM.PRODUCT.PLACEHOLDER.DESCRIPTION',
                    'class' => 'form-control'
                    ]
            ]
        )
        ->add('version', TextType::class,
            ['label' => 'FORM.PRODUCT.VERSION',
                'attr' => [
                    'placeholder' => 'FORM.PRODUCT.PLACEHOLDER.VERSION'
                ]
            ]
            )
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
            
            return new RedirectResponse($urlGenerator->generate('homepage')); //instead of '/'= $urlGenerator etc.... this class is in autowiring
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
    
    public function displayProduct(Environment $twig, ProductRepository $repository)
    {
        return new Response(
            $twig->render(
                'Product/displayProduct.html.twig',
                [
                    'products' => $repository->findAll()
                ]
                )
            );
    }
    public function detailProduct(
        Environment $twig,
        ProductRepository $repository,
        int $product,
        FormFactoryInterface $formFactory
        ) {
            $product = $repository->find($product);
            if (!$product) {
                throw new NotFoundHttpException();
            }
            
            $comment = new Comment();
            $form = $formFactory->create(
                CommentType::class,
                $comment,
                ['stateless' =>true]);
            
            /** $fileDto = new FileDto();
            * 
            * $form = $formFactory->create(CommentFileType::class, $fileDto);
            * 
            **/
            
            return new Response(
                $twig->render(
                    'Product/detailProduct.html.twig',
                    [
                        'product' => $product,
                        'routeAttr' => ['product' => $product->getId()],
                        'form' => $form->createView()
                    ]
                 )
             );
    }

}

?>
