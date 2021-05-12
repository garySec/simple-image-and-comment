<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload
{
	private $em;
	private $container;
	private $upload;

	function __construct(EntityManagerInterface $em,ContainerInterface $container)
	{
		$this->em = $em;
		$this->container = $container;
	}

	public function imageUpload($form,$image)
	{
		$imageFile = $form->get('image')->getData();
		// $newName = md5($imageFile) . '.' . $imageFile->guessExtension();
		$newName = $imageFile->getClientOriginalName('');

		// dd($image->setName($newName));

		$imageFile->move(
				$this->container->getParameter('images_directory'),
				$newName
			);

		$image->setName($newName);
		$this->em->persist($image);
		$this->em->flush();
	}
}