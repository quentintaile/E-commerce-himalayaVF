<?php

namespace App\Controller;

use App\Class\Cart;
use App\Entity\Order;
use App\Entity\Address;
use App\Form\OrderType;
use App\Entity\OrderDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /**
     * Première étape du tunnel d'achat :
     * Choix de l'adresse de livraison et du transporteur.
     */

      #[Route("/commande/livraison", name:"app_order")]
     
    public function index(): Response
    {
        // Récupère les adresses de l'utilisateur connecté
        $addresses = $this->getUser()->getAddresses();
        
        // Redirige l'utilisateur vers le formulaire de création d'adresse s'il n'a pas d'adresses enregistrées
        if (count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }
        
        // Prépare le formulaire OrderType pour choisir l'adresse et le transporteur
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary'), // Route de soumission du formulaire
        ]);
        
        // Affiche le formulaire de choix d'adresse et transporteur
        return $this->render('order/index.html.twig', [
            'deliveryForm' => $form->createView(),
        ]);
    }

    /**
     * Deuxième étape du tunnel d'achat :
     * Récapitulatif de la commande de l'utilisateur, insertion en base de données et préparation du paiement via Stripe.
     */
     #[Route("/commande/recapitulatif", name:"app_order_summary")]
    
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManagerInterface): Response
    {
        // Vérifie que la méthode de la requête est bien 'POST' pour éviter des accès non autorisés
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        
        // Récupère les produits du panier de l'utilisateur
        $products = $cart->getCart();
        
        // Prépare le formulaire OrderType
        $form = $this->createForm(OrderType::class, null, ['addresses' => $this->getUser()->getAddresses()]);
        $form->handleRequest($request);
        
        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère l'adresse choisie par l'utilisateur
            $addressObject = $form->get('addresses')->getData();
            $address = $addressObject->getFirstname() . ' ' . $addressObject->getLastname() . '<br/>' .
                       $addressObject->getAddress() . '<br/>' .
                       $addressObject->getPostal() . ' ' . $addressObject->getCity() . '<br/>' .
                       $addressObject->getCountry() . '<br/>' .
                       $addressObject->getPhone();
            
            // Crée une nouvelle commande
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);
            
            // Ajoute les détails de chaque produit du panier à la commande
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductImage($product['object']->getImage());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }
            
            // Enregistre la commande et ses détails en base de données
            $entityManagerInterface->persist($order);
            $entityManagerInterface->flush();
        }
        
        // Affiche le récapitulatif de la commande
        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $cart->getCart(),
            'order' => isset($order) ? $order : null,
            'totalWt' => $cart->getTotalWt(),
        ]);
    }
}
