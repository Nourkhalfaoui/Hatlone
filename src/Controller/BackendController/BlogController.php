<?php

namespace App\Controller\BackendController;
use App\Entity\Article;
use App\Form\BlogType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class BlogController extends AbstractController
{

    #[Route('/admin', name: 'app_blog_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $blogs = $entityManager
            ->getRepository(Article::class)
            ->findAll();

        return $this->render('blog/components-blog.html.twig', [
            'blogs' => $blogs,
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Article();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blog);
            $entityManager->flush();
            $this->addFlash('success', 'le blog a été ajouté avec succès.');

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_blog_shows', methods: ['GET'])]
    public function show(int   $id, ArticleRepository $articleRepository): Response
    {
        $blog = $articleRepository->find($id);
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager,int $id): Response
    {
        $blog = $entityManager->getRepository(Article::class)->find($id);

        $form = $this->createForm(BlogType::class, $blog);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_index', ['id' => $blog->getId()]);
        }

        return $this->renderForm('blog/edit.html.twig', [
            'form' => $form,
            'blog'=>$blog,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_blog_delete', methods: ['POST'])]
    public function delete(int $id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = $entityManager->getRepository(Article::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/recherche_ajax', name: 'recherche_ajax')]
    public function rechercheAjax(Request $request, SerializerInterface $serializer,ArticleRepository $productRepository): JsonResponse
    {
        $requestString = $request->query->get('searchValue');


        if (empty($resultats)) {
            return new JsonResponse(['message' => 'No reclamations found.'], Response::HTTP_OK);
        }

        $data = [];

        foreach ($resultats as $res) {
            $data[] = [
                'description' => $res->getDescription(),
                'nbCom' => $res->getNbCom(),
                'date' => $res->getDate(),
            ];

        }

        $json = $serializer->serialize($data, 'json', ['groups' => 'reclamations', 'max_depth' => 1]);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

}

