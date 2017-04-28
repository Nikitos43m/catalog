<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name = "list_authors")
     */
    public function indexAction() 
    {
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->createQueryBuilder();
         
        $authors = $this->getDoctrine()
                        ->getRepository(\AppBundle\Entity\Author::class)
                        ->findAll();
       
        return $this->render('default/view.html',[
             'authors' => $authors
             ]);   
    }
    
    
    /**
     * @Route("/list/{id}", name="books_catalog")
     */
    public function listAction($id)
    { 
        $authors = $this->getDoctrine()
                        ->getRepository(\AppBundle\Entity\Author::class)
                        ->find($id);
        
        return $this->render('default/books.html',[
                  'books'=>$authors->getBooks()
               ]);
    }  
    
    
    /**
     * @Route("/auth_update/{id}", name="author_update")
     */
    public function authAction(Request $request, $id)
    {
        $auth = $this->getDoctrine()
                     ->getRepository(\AppBundle\Entity\Author::class)
                     ->find($id);
         
        $form = $this->createFormBuilder($auth)
                     ->add('name', TextType::class)
                     ->add('save', SubmitType::class, array('label' => 'Обновить'))
                     ->getForm();
        
        $form->handleRequest($request);
         
       
         if ($form->isSubmitted() && $form->isValid()) {
           
             $auth = $form->getData();            
             $em = $this->getDoctrine()->getManager();
             $em->persist($auth);
             $em->flush();

             return $this->redirectToRoute('list_authors');
         }
        

        return $this->render('default/author_form.html', [
            'form' => $form->createView(),
        ]);   
    }
        
    /**
     * @Route("/auth_create", name="author_create")
     */
    public function createauthAction(Request $request)
    {
        $auth = new \AppBundle\Entity\Author(); 
         
        $form = $this->createFormBuilder($auth)
                     ->add('name', TextType::class)
                     ->add('save', SubmitType::class, array('label' => 'Добавить'))
                     ->getForm();
        
        $form->handleRequest($request);
         
            if ($form->isSubmitted() && $form->isValid()) 
            {
                $auth = $form->getData();
        
                $em = $this->getDoctrine()->getManager();
                $em->persist($auth);
                $em->flush();
  
                return $this->redirectToRoute('list_authors');
            }
        
        return $this->render('default/create_auth_form.html', [
            'form' => $form->createView(),
        ]);    
    }
    
    /**
     * @Route("/book_update/{id}", name="book_update")
     */
    public function bookAction(Request $request, $id)
    {
        $book = $this->getDoctrine()
                     ->getRepository(\AppBundle\Entity\Book::class)
                     ->find($id);
         
        $form = $this->createFormBuilder($book)
                     ->add('title', TextType::class)
                     ->add('save', SubmitType::class, array('label' => 'Обновить'))
                     ->getForm();
        
        $form->handleRequest($request);
         
       
        if ($form->isSubmitted() && $form->isValid())
        {       
             $book = $form->getData();
             $id_auth = $book->getAuthor()->getId();
         
             $em = $this->getDoctrine()->getManager();
             $em->persist($book);
             $em->flush();
             
             return $this->redirectToRoute('books_catalog', ['id'=>$id_auth]);
        }
        
        return $this->render('default/book_form.html', [
            'form' => $form->createView(),
        ]);   
    }
    
     /**
     * @Route("/book_delete/{id}", name="book_delete")
     */
    public function book_deleteAction($id)
    {       
         $book = $this->getDoctrine()
                      ->getRepository(\AppBundle\Entity\Book::class)
                      ->find($id);

         $em = $this->getDoctrine()->getManager();
         $em->remove($book);
         $em->flush();
         
         $id_auth = $book->getAuthor()->getId();
         
         return $this->redirectToRoute('books_catalog', ['id'=>$id_auth]);   
    }
}
