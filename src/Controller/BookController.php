<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'add_book')]
    public function addBook(Request  $request, ManagerRegistry $managerRegistry)
    {
      $book=  new Book();
      $form= $this->createForm(BookType::class, $book);
     $form->handleRequest($request);
      if($form->isSubmitted()){
          $nbBooks= $book->getAuthor()->getNbBooks();
         // var_dump($nbBooks).die();
          $book->getAuthor()->setNbBooks($nbBooks+1);
         $book->setPublished(true);
         $em = $managerRegistry->getManager();
         $em->persist($book);
         $em->flush();
         return  new Response("Done!");
     }
      //1ere methode
      /*return $this->render('book/add.html.twig',
      array('formBook'=>$form->createView()));*/
        //2eme methode
        return $this->renderForm('book/add.html.twig',
        array('formBook'=>$form));
    }

    #[Route('/listBook', name: 'list_book')]
    public function listBook(BookRepository  $repository)
    {
        return $this->render("book/list.html.twig",
        array('books'=>$repository->findAll()));
      /*  return $this->render("book/list.html.twig",
            array('books'=>$repository->findBy(['published'=>false])));*/
    }


    #[Route('/updateBook/{ref}', name: 'update_book')]
    public function updateBook($ref,BookRepository $repository,Request  $request, ManagerRegistry $managerRegistry)
    {
        $book= $repository->find($ref) ;
        $form= $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $nbBooks= $book->getAuthor()->getNbBooks();
            $book->getAuthor()->setNbBooks($nbBooks+1);
            $book->setPublished(true);
            $em = $managerRegistry->getManager();
            $em->flush();
           // return  new Response("Done!");
            return  $this->redirectToRoute("list_book");
        }
        return $this->renderForm('book/update.html.twig',
            array('formBook'=>$form));
    }

}
