<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {  /*LES ETAPES DU TEST : 
        * 1. creer un faux client (comme navigateur) de pointer vers une URL
        * 2. Remplir les champs de mon formulaire d'inscription 
        * 3. Est-ce que tu peux regarder si dans ma page si j'ai le message d'alerte suivant. (le flash message)
        */
        // 1. 
        $client = static::createClient(); // c'est le faux navigateur c'est le client qui va requêter sur adresse
        $crawler = $client->request('GET', '/inscription'); // requeter une URL particulier : la methode / et le chemin(url)
        // 2 remplir les champs du formulaire // il va falloir chercher le nom des champs (avec l'inspecteur)
        // on demande a notre client de soumettre un formulaire avec le du boutton(ici 'Valider' on aurait pu choisir l'd)
        // et second parametre de la methode submitform() on passe un tableau des nom des input
        $client->submitForm('Valider', [
            'register_user[email]'=> 'bassechomar@hotmail.com' ,
            'register_user[plainPassword][first]'=>'12345',
            'register_user[plainPassword][second]'=>'12345',
            'register_user[firstname]'=>'omar',
            'register_user[lastname]'=>'BASSE',
        ]);
        $this->assertResponseRedirects('/connexion');
        // il manque la redirection vers la page login  // une etape intermediaire 
        // 2.1 . // FOLLOW ==> est-ce qe tu peux suivre la redirection vers la page login 
        // accompagner le client 
        $client->followRedirect(); //=> se rendre dans le terminal=> php bin/phpunit 
        // 3. test du message flash. 
        // la methode assertSelectorExists() va chercher un élemlent dans le DOM HTML et on lui donne en paramettre l'element qu'on veut aller cherher 
        $this->assertSelectorExists('div:contains("Vous etes maintenant inscrit ! Vous pouvez vous connecter à votre espace membre")');
        



        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h1', 'Hello World');
    }
}
