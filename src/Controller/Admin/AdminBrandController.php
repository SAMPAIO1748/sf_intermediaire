<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Repository\BrandRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminBrandController extends AbstractController
{
    /**
     * @Route("admin/brands", name="admin_brand_list")
     */
    public function adminListBrand(BrandRepository $brandRepository)
    {
        $brands = $brandRepository->findAll();

        return $this->render("admin/brands.html.twig", ['brands' => $brands]);
    }

    /**
     * @Route("admin/brand/{id}", name="admin_brand_show")
     */
    public function adminShowBrand($id, BrandRepository $brandRepository)
    {
        $brand = $brandRepository->find($id);

        return $this->render("admin/brand.html.twig", ['brand' => $brand]);
    }

    /**
     * @Route("admin/update/brand/{id}", name="admin_update_brand")
     */
    public function adminUpdateBrand(
        $id,
        BrandRepository $brandRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $brand = $brandRepository->find($id);

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {

            $mediaFile = $brandForm->get('media')->getData();

            if ($mediaFile) {

                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $brand->setMedia($newFilename);
            }

            $entityManagerInterface->persist($brand);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_brand_list");
        }


        return $this->render("admin/brandform.html.twig", ['brandForm' => $brandForm->createView()]);
    }

    /**
     * @Route("admin/create/brand/", name="admin_brand_create")
     */
    public function adminBrandCreate(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {
        $brand = new Brand();

        $brandForm = $this->createForm(BrandType::class, $brand);

        $brandForm->handleRequest($request);

        if ($brandForm->isSubmitted() && $brandForm->isValid()) {


            $mediaFile = $brandForm->get('media')->getData();

            if ($mediaFile) {

                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $brand->setMedia($newFilename);
            }

            $entityManagerInterface->persist($brand);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_brand_list");
        }


        return $this->render("admin/brandform.html.twig", ['brandForm' => $brandForm->createView()]);
    }

    /**
     * @Route("admin/delete/brand/{id}", name="admin_delete_brand")
     */
    public function adminDeleteBrand(
        $id,
        BrandRepository $brandRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $brand = $brandRepository->find($id);

        $entityManagerInterface->remove($brand);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_brand_list");
    }
}
