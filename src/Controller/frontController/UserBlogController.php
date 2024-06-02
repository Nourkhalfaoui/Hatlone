<?php

namespace App\Controller\frontController;

use App\Entity\Article;
use App\Form\PostFrontType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/user')]
class UserBlogController extends AbstractController
{


    #[Route('/user/blog', name: 'app_user_blog')]
    public function listBlog( ArticleRepository $blogrepo , PaginatorInterface $paginator, Request $request): Response
    {
        $blog = $blogrepo->findAll();
        $blogPaginated = $paginator->paginate(
            $blog,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('user_post/index.html.twig', [
            'blog' => $blogPaginated,

        ]);
    }
   
    #[Route('/user/blog/new', name: 'new_blog', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Article();
        $form = $this->createForm(PostFrontType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            $fileName = uniqid().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $fileName
            );

            $blog->setImage($fileName);

            $entityManager->persist($blog);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre Blog est bien ajouté ');

            return $this->redirectToRoute('app_user_blog', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_post/addBlog.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }
        
        #[Route('/user/blog/{id}', name: 'app_blog_front_show')]
        public function showBlog(int $id,ArticleRepository $articleRepository): Response
        {
            $blog = $articleRepository->find($id);
            return $this->render('user_post/showBlog.html.twig', [
                'blog' => $blog,
            ]);
        }

        #[Route('/user/blog/{id}', name: 'app_blogfront_delete', methods: ['POST'])]
        public function deleteBlog(Request $request, Article $blog, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
                $entityManager->remove($blog);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_user_blog', [], Response::HTTP_SEE_OTHER);
        }

        #[Route('/user/blog/{id}/edit', name: 'app_blog_front_edit', methods: ['GET', 'POST'])]
        public function editBlog(Request $request, Article $blog, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(PostFrontType::class, $blog);
            $form->handleRequest($request);
       
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
       
                return $this->redirectToRoute('app_user_blog', [], Response::HTTP_SEE_OTHER);
            }
       
            return $this->renderForm('user_post/editblog.html.twig', [
                'blog' => $blog,
            ]);
        }





      
}
