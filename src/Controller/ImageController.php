<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Upload;
use App\Service\EditImage;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Route("/")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/", name="image_index", methods={"GET"})
     */
    public function index(ImageRepository $imageRepository): Response
    {   
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="image_new", methods={"GET","POST"})
     */
    public function new(Request $request,Upload $upload): Response
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            if ($form->get('image')->getData() ==null) 
            {
                $this->addFlash('selectImage','Select Image!');
                return $this->redirectToRoute('image_new');
            }
            //inject in service
            $image = $upload->imageUpload($form,$image);
            
            return $this->redirectToRoute('image_index');
        }

        return $this->render('image/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_show", methods={"GET"})
     */
    public function show(Image $image): Response
    {
        return $this->render('image/show.html.twig', [
            'image' => $image,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Image $image, EditImage $EditImage): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        $orignalCmt = new ArrayCollection();

        $oldName = $image->getName();
        
        foreach ($image->getComment() as $cmt) 
        {
            $orignalCmt->add($cmt);
        }

        $em = $this->getDoctrine()->getManager();
        
        if ($form->isSubmitted() && $form->isValid()) {

             foreach ($orignalCmt as $cmt) 
            {
       
                if ($image->getComment()->contains($cmt) === false) {
                    $em->remove($cmt);
                }
            }
            $image = $EditImage->edit($form,$image);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_index');
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_delete", methods={"POST"})
     */
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
           
             $imageName = $image->getName();

            unlink($this->getParameter('images_directory').'/'.$imageName);


            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }

        return $this->redirectToRoute('image_index');
    }
}
