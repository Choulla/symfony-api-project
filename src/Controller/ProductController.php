<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/product",)
     */
    public function index(): Response
    {
        return new JsonResponse(
            [
                "message"=>"ok",
                "test"=>"test"
            ]
        );
    }

    /**
     * @Route("/api/product/insert",methods={"POST"})
     */
    public function insert(Request $request,ManagerRegistry $doctrine){
        $parametre = json_decode($request->getContent(),true);

        $entityManager = $doctrine->getManager();
        $product = new Product();
        $product->setName($parametre['name']);
        $product->setDescription($parametre['description']);
        $product->setPrice($parametre['price']);

        $entityManager->persist($product);
        $entityManager->flush();

        return new JsonResponse(
            [
                "message"=>"created successfully !".$product->getId()
            ]
        );
    }

    /**
     * @Route("/api/product/edit/{id}", methods={"PUT"})
     */
    public function edit(ManagerRegistry $doctrine,$id,Request $request){
        $entityManager = $doctrine->getManager();
        $product = $doctrine->getRepository(Product::class)->find($id);
        if ($product == null) {
            return $this->json('produit n\'existe pas' , 404);
        }
        $parametre = json_decode($request->getContent(),true);

        $product->setName( $parametre["name"] );
        $product->setDescription( $parametre["description"] );
        $product->setPrice( $parametre["price"] );

        $entityManager->flush();
        return new JsonResponse(
            [
                "message"=>"product updated successfully !"
            ]
        );
    }

    /**
     * @Route("/api/product/delete/{id}", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine,$id,Request $request){
        $entityManager = $doctrine->getManager();
        $product = $doctrine->getRepository(Product::class)->find($id);
        if ($product == null) {
            return $this->json('produit n\'existe pas' , 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();
        return new JsonResponse(
            [
                "message"=>"product deleted successfully !"
            ]
        );
    }

    /**
     * @Route("/api/product/show", methods={"GET"})
     */
    public function products(ManagerRegistry $doctrine){
        $products = $doctrine->getRepository(Product::class)->findAll();
        if ($products == null) {
            return $this->json('aucun produit existe' , 404);
        }
        $producttable = array();
        for($i=0;$i<count($products);$i++){
            $producttable[$i] = array(
                'id' => $products[$i]->getId(),
                'name' => $products[$i]->getName(),
                'description' => $products[$i]->getDescription(),
                'price'=>$products[$i]->getPrice()
            );
        }
        return new JsonResponse(
            $producttable
        );
    }
}
