<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Demande;
use App\Entity\Secretaire;
use App\Form\DemandeEntrepriseType;
use App\Form\DemandeLaureatType;
use App\Form\DemandeSecretaireType;
use App\Repository\DemandeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @Route("/demande")
 * @IsGranted({"ROLE_LAUREAT", "ROLE_ENTREPRISE", "ROLE_SECRETAIRE", "ROLE_ETABLISSEMENT"})
 */
class DemandeController extends AbstractController
{
    const ETAT_PROCESS = 2;
    const ETAT_NOT_VALIDE = 0;
    const ETAT_VALIDE = 1;

    /**
     * @Route("/", name="demande_index", methods={"GET"})
     */
    public function index(DemandeRepository $demandeRepository): Response
    {
        // Setting appropriate user type
        switch ($this->getUser()) {
            case $this->isGranted('ROLE_LAUREAT'):
                $demandes = $demandeRepository->findByLaureat(['laureat_id' => $this->getUser()]);      
                break;
            case $this->isGranted('ROLE_ENTREPRISE'):
                $demandes = $demandeRepository->findByEntreprise(['entreprise_id' => $this->getUser()]);      
                break;
            case $this->isGranted(["ROLE_SECRETAIRE", "ROLE_DIRECTEUR"]):
                $demandes = $demandeRepository->findByEtablissement(['etablissement_id' => $this->getUser()->getEtablissement()]);
                break;                             
            default:
                
                break;
        }
        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes
        ]);
    }

    /**
     * @Route("/new", name="demande_new", methods={"GET","POST"})
     * @IsGranted({"ROLE_LAUREAT", "ROLE_ENTREPRISE"})
     */
    public function new(Request $request): Response
    {
    	// Setting appropriate user type
        switch ($this->getUser()) {
            case $this->isGranted('ROLE_LAUREAT'):
                $demande = new Demande();
		        $form = $this->createForm(DemandeLaureatType::class, $demande);
		        $form->handleRequest($request);

		        if ($form->isSubmitted() && $form->isValid()) {
		            // dd($demande);
		            $demande->setLaureat($this->getUser());
		            $entityManager = $this->getDoctrine()->getManager();
		            $entityManager->persist($demande);
		            $entityManager->flush();

		            return $this->redirectToRoute('demande_index');
		        }

		        return $this->render('demande/laureat_new.html.twig', [
		            'demande' => $demande,
		            'form' => $form->createView(),
		        ]);   
                break;
            case $this->isGranted('ROLE_ENTREPRISE'):
                $demande = new Demande();
		        $form = $this->createForm(DemandeEntrepriseType::class, $demande);
		        $form->handleRequest($request);

		        if ($form->isSubmitted() && $form->isValid()) {
		        	$demande->setEntreprise($this->getUser());
		            $entityManager = $this->getDoctrine()->getManager();
		            $entityManager->persist($demande);
		            $entityManager->flush();

		            return $this->redirectToRoute('demande_index');
		        }

		        return $this->render('demande/entreprise_new.html.twig', [
		            'demande' => $demande,
		            'form' => $form->createView(),
		        ]);      
                break;                            
            default:
                
                break;
        }
    }
    
    /**
     * @Route("/{id}/edit", name="validate_demande", methods={"GET","POST"})
     * @IsGranted({"ROLE_ETABLISSEMENT", "ROLE_SECRETAIRE"})
    */
    public function edit(Request $request, Demande $demande): Response
    {
        if(!$demande){
            $demande = new Demande();
        }

        //$demande = new Demande();
        $form = $this->createForm(DemandeEtablissementType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        	// Setting appropriate user type
	        switch ($this->getUser()) {
	            case $this->isGranted('ROLE_SECRETAIRE'):
	            	$demande->setDateValidationSecretaire(new \DateTime());
	            	$demande->setSecretaire($this->getUser());
	            	break;
	            case $this->isGranted('ROLE_DIRECTEUR'):
	            	$demande->setDateValidationDP(new \DateTime());
	            	$demande->setDirecteurPedagogique($this->getUser());
	            	break;
	            default:
	            	break;
	        }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($demande);
            $entityManager->flush();

            // return $this->redirectToRoute('demande_index');
        }

        return $this->render('demande/secretaire_new.html.twig', [
            'demande' => $demande,
            'form' => $form->createView(),
        ]);
    }

    

    /**
     * @Route("/{id}/edit", name="demande_edit", methods={"GET","POST"})
     */
    // public function edit(Request $request, Demande $demande): Response
    // {
    //     $form = $this->createForm(DemandeType::class, $demande);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->getDoctrine()->getManager()->flush();
    //         return $this->redirectToRoute('demande_index');
    //     }

    //     return $this->render('demande/edit.html.twig', [
    //         'demande' => $demande,
    //         'form' => $form->createView(),
    //     ]);
    // }

    /**
     * @Route("/{id}", name="demande_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Demande $demande): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('demande_index');
    }
}
