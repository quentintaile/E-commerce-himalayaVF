<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/produit/{slug}', name: 'app_product')]
    public function index($slug , ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBySlug($slug);
        // dd($product); 
        if (!$product){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
//----nouveautés Symfony -----------------------//
//
/*   public function index(#[MapEntity(slug: 'slug')] Product $product ): Response
*
*
*/