<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EditImage
{
	private $em;
	private $container;

	function __construct(EntityManagerInterface $em,ContainerInterface $container)
	{
		$this->em = $em;
		$this->container = $container;
	}

	public function edit($form,$image)
	{	
		$newName=$image->getName();
		// dd($newName);

		$imageFile = $form->get('image')->getData();
		// $newName = md5($imageFile) . '.' . $imageFile->guessExtension();
		if ($imageFile!=NULL) {
			# code...
		$newName = $imageFile->getClientOriginalName('');

		$imageFile->move(
				$this->container->getParameter('images_directory'),
				$newName
			);
		}
		$image->setName($newName);
		$this->em->persist($image);
		$this->em->flush();
	}
}