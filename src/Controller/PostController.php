<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post', name: 'app_post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts'=>$postRepository->findAll()
        ]);
    }

    #[Route("/{id}",name: "app_show")]
    public function show(Post $post): Response{


        return  $this->render('post/show.html.twig',[
            "controller_name"=>"Post",
           "post"=>$post
        ]);
    }

    #[Route("/create", name:"app_create", priority: 2)]
    #[Route("/edit/{id}", name:"app_edit", priority: 2)]

    public function create(Request $request, EntityManagerInterface $manager, Post $post=null):Response
    {
        $edit = false;
        if($post){
            $edit = true;
        }
        if(!$edit){
            $post = new Post();

        }
        $formPost = $this->createForm(PostType::class,$post);

        $formPost->handleRequest($request);
        if($formPost->isSubmitted() && $formPost->isValid())
        {
            if(!$edit){
                $post->setCreatedAt(new \DateTime());
            }

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('app_postapp_show', ['id'=>$post->getId()]);

        }


        return $this->renderForm('post/create.html.twig', [
            'formPost'=>$formPost,
            'edit'=>$edit
        ]);
    }


    #[Route("/delete/{id}",name: "app_delete")]
    public function delete(Post $post, PostRepository $postRepository, EntityManagerInterface $manager): Response{

        if ($post){
            $manager->remove($post);
            $manager->flush();
        }


        return $this->redirectToRoute("app_postapp_index");
    }
}
