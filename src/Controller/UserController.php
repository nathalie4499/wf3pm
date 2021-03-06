<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController {
    
    public function register (
        Environment $twig,
        FormFactoryInterface $factory,
        Request $request,
        ObjectManager $manager,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        \Swift_Mailer $mailer,
        EncoderFactoryInterface $encoderFactory,
        RoleRepository $roleRepository
        ) 
    {
        $user = new User();
        $builder = $factory->createBuilder(FormType::class, $user);
        $builder->add('username', TextType::class,
            [
                'required' => true,
                'label' => 'FORM.USER.NAME'
            ]
            )
            ->add('firstname', TextType::class,
                [
                 'required' => true,
                 'label' => 'FORM.USER.FIRST'
            ]
            )
            ->add('lastname', TextType::class,
               [
                   'required' => true,
                   'label' => 'FORM.USER.LAST'
               ]
            )
            ->add('email', EmailType::class,
                [
                    'required' => true,
                    'label' => 'FORM.USER.EMAIL'
                ]
             )

            ->add('password', RepeatedType::class, array(
                  'type' => PasswordType::class,
                  'required' => true,
                  'invalid_message' => 'The password fields must match.',
                  'first_options' => array('label' => 'FORM.USER.PASS'),
                  'second_options' => array('label' => 'Repeat Password')
                  
              ))
            ->add('submit', SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn-lbock btn-success'
                    ],
                    'label' => 'FORM.USER.SUBMIT'
                ]    
            );
            
        $form = $builder->getForm();    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $salt = md5($user->getUsername());
            $user->setSalt($salt);
            
            $encoder = $encoderFactory->getEncoder(User::class);
            $password = $encoder->encodePassword($user->getPassword(), $salt);
            
            $user->setPassword($password);
            
            $user->addRole($roleRepository->findOneByLabel('ROLE_USER'));
            
            $manager->persist($user);
            $manager->flush();
            
            $message = new \Swift_Message();
            $message->setFrom('wf3pm@localhost.com')
                ->setTo($user->getEmail())
                ->setSubject('Validate your account')
                ->setContentType('text/html')
                ->setBody(
                    $twig->render('mail/account_creation.html.twig',
                        //['token' => $user->getEmailToken()]
                        ['user' => $user] //now the token is inside the user
                        )
                )->addPart(
                    $twig->render(
                        'mail/account_creation.txt.twig',
                        ['user' => $user]
                    )
                    , 'text/plain' // or text/html if we reverse
                );
            $mailer->send($message);
            
            $session->getFlashBag()->add('info', 'Your account has been created');
            
            return new RedirectResponse($urlGenerator->generate('homepage')); //instead of '/'= $urlGenerator etc.... this class is in autowiring
        }
           return new Response(
               $twig->render('Form/addUser.html.twig',
               [
                   'formular' => $form->createView()
               ]
               ) 
             );  
    }
    public function activateUser($token, ObjectManager $manager, SessionInterface $session, UrlGeneratorInterface $urlGenerator, RoleRepository $roleRepository) //repo to retrieve info (getREpository $repository//manager to make an action 
    {
        $userRepository = $manager->getRepository(User::class);
        $user = $userRepository->findOneBy(['emailToken' => $token]); //or $userRepository->findOneByEmailToken($token);
        
        if(!$user) {
            throw new NotFoundHttpException('User not found for given token');
        }
        
        $user->setActive(true);
        $user->setEmailToken(null); //to remove token when user is activated (someone could reactive the token if admin deactivate it)
        $user->addRole($roleRepository->findOneByLabel('ROLE_ACTIVE'));
        
        $manager->flush();
        
        $session->getFlashBag()->add('info', 'Your account has been validated');
        
        return new RedirectResponse($urlGenerator->generate('homepage'));
        
        return new Response('hello guys : '.$token); //debug purpose
    }
    public function usernameAvailable(
        Request $request, 
        UserRepository $repository
        )
    {
        $username = $request->request->get('username');
        
        $unavailable = false;
        if(!empty($username)) {
            $unavailable = $repository->usernameExist($username);
        }
        return new JsonResponse([
            'available' => !$unavailable
        ]
      );

    }
    
    public function login(AuthenticationUtils $authUtils, Environment $twig) {
        $errors = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();
        
        return new Response(
            $twig->render('Security/login.html.twig',
                [
                    'last_username' => $authUtils->getLastUsername(),
                    'error' => $authUtils->getLastAuthenticationError()
                ]
            )
        );
        
    }
}




