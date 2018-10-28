<?php

// Tableau du precedences des taches
$tPrec =  array(
						0 => array('-'),
						1 => array(0),
						2 => array('-'),
						3 => array(2),
						4 => array(3),
						5 => array(2,1),
						6 => array('-'),
						7 => array(6),
						8 => array(7),
						9 => array(8),
					   10 => array(9)
);
$tabOperations = initialisationOperations();
$tabOperateurs = initialisationOperateurs();

$operateursOperation = array (
							0 =>  array( 0 , 2 , 3  ),
							1 =>  array( 0 , 2 , 3 , 4 , 5 ),
							2 =>  array( 0 , 1 , 3 , 4 ),
							3 =>  array( 0 , 1 , 2 , 3 ),
							4 =>  array( 0 , 2 , 3 ),
							5 =>  array( 0 , 2 , 3 ),
							6 =>  array( 2 , 3 , 5 ),
							7 =>  array( 6 ),
							8 =>  array( 2 , 3  ),
							9 =>  array( 6 ),
							10 => array( 0 , 1 , 2 , 3 , 4 , 5  )


	);
$operateurStat =initialisationStat();

// Nombre des machines max
$MMAX = 2 ;
$TMAX = 2 ;

// Nombre des taches de l'algorithme
$nbrTaches = 50 ;
// Nombre des operations
$nbrOper = 9 ;
// Pheromones 0
$pher0 = 0.2;
// Parametre du tableau du pheromones
$rou = 1 ;
$Q = 1 ;
$ICG = 1 ;

// Taux des pheromones
$tauxPheromones = array();
$tauxPheromones = initialisationPheremones($nbrOper,$pher0);
// Affichage Tableau des pheromones

var_dump($tauxPheromones);

// Tableaux des fourmis
$tFourmis = array();
$tFourmis = chercherFourmis($tPrec,$tFourmis);


// Algorithm core
// 1er iteration
foreach ($tFourmis as $Fourmi ) {


	// Tableau d'operations finis
	$tabOperFinis = array();
	$tabOperations = initialisationOperations();
	$tabOperateurs = initialisationOperateurs();
	$operateurStat = initialisationStat();
	$tacheCourant = $Fourmi;

	// Affectation des operations du controle
	$idoperateur = chercherOperateurControle($tabOperateurs);
	$resultCtr = affecterOperationControle($tabOperations,$operateurStat,$idoperateur);
	$operateurStat = $resultCtr[0];
	$tabOperations = $resultCtr[1];
	
	while (count($tabOperFinis) < count($tPrec)){

		
		$tachesPossibles = array();
		
		
		
		$tachesPossibles = chercherTachesPossibles($tacheCourant,$tPrec,$tabOperFinis,$tachesPossibles);
		/* Attribier la tache a un operateur */
		$tabResult = affecterOperationOperateur($tabOperateurs,$tabOperations,$operateursOperation,$operateurStat,$tacheCourant,$TMAX,$MMAX);
		if ($tabResult != NULL ){
			$idOpt = $tabResult[1];
			$operateurStat[$idOpt] = $tabResult[0];
			$tabOperations[$tacheCourant] = $tabResult[2];
		}
		

		
		// Determination du tache i
		$ti = $tacheCourant;

		
		$tabOperFinis[] = $tacheCourant;
		
		
		$tacheCourant  = choisirOperHazard($tachesPossibles) ;
		
			
		
		// Determination de la tache j @param ICG = Indice Competence Globale , $Q = constatne fixé , $rou = constante fixé
				
				/* Mise a jour du tableau du pheromones
				
				$tj = $tacheCourant;
				$delta = $Q/(1/$ICG);
				$tauxPheromones[$i][$j] = $rou * $tauxPheromones[$i][$j] + $delta ; 
				
				*/
	}
	

}
// * ================== Fin 1er Operation ================== * //








// Liste des fonctions 

/* 
	Fonction qui permet de chercher tout les fourmis a partir de d'un tableau de precedences 
	@param $tPrec
	@param $tFourmis
	@result $tFourmis

*/
function chercherFourmis($tPrec,$tFourmis){
	for($i=0;$i<count($tPrec);$i++) {
		if ($tPrec[$i][0] == '-'){
			$tFourmis[] = $i; 
		}
	}
	return $tFourmis;

}

/* 
	Fonction qui permet de chercher tout les operations possible apres une operation courant 
	@param $operCourant
	@param $tPrec
	@param $tOperFinis
	@result $operPossibles

*/

function chercherTachesPossibles($tacheCourant,$tPrec,$tOperFinis,$ancTachesPossibles){
	$tachesPossibles=array();
	for($i=0;$i<count($tPrec);$i++){
		// Test tacheCourant = la tache parcouri
				if ($i != $tacheCourant){
				if (count($tPrec[$i]) == 1  ){
						if ($tPrec[$i][0] == '-'){
								if (!in_array($i, $tOperFinis)){
									$tachesPossibles[] = $i; 
								}
						} else if ($tPrec[$i][0] == $tacheCourant){
								if (!in_array($i, $tOperFinis)){
									$tachesPossibles[] = $i; 
								} 
						} else {
							if (in_array($tPrec[$i][0],$tOperFinis) && (!in_array($i,$tOperFinis))){
								$tachesPossibles[] = $i;
							}

						}
								
				}else if (in_array($tacheCourant,$tPrec[$i])){
					$p=true;
					for($j=0;$j<count($tPrec[$i]);$j++){
						if ($tPrec[$i][$j] != $tacheCourant && !in_array($tPrec[$i][$j],$tOperFinis)){
							$p=false;
						}
					}
					if ($p){$tachesPossibles[] = $i ;}
				}else{
					$p=true;
					for($j=0;$j<count($tPrec[$i]);$j++){
						if ($tPrec[$i][$j] != $tacheCourant && !in_array($tPrec[$i][$j],$tOperFinis)){
							$p=false;
						}
					}
					if ($p){$tachesPossibles[] = $i ;}
				}

				
			}
	}
	foreach ($ancTachesPossibles as $tache) {
		if (!in_array($tache,$ancTachesPossibles)){
			$tachesPossibles[] = $tache;
		}
	}
	return $tachesPossibles;
}


/* 
	Fonction qui permet de choisir une operation aux hazard a partir d'une liste des operations 
	@param $tabOper
	@result $operChoisie

*/

function choisirOperHazard($tabOper){
	if (count($tabOper) > 0 ){
			if (count($tabOper) == 1 ){
				return $tabOper[0];
			}else{
				$indice = rand(0,count($tabOper)-1);
				return $tabOper[$indice];	
			}	
	}
	
	
}



function initialisationPheremones($nbrTaches,$pher0){
	for($i=0;$i<$nbrTaches;$i++){
				for($j=0;$j<$nbrTaches;$j++){
				$tabPheromones[$i][$j] = $pher0;
				}
	}
	return $tabPheromones;

}

/*
	Fonction qui permet de retourner l'indice de competence globale
*/

function affecterOperationOperateur($tabOperateurs,$tabOperations,$operateursOperation,$operateurStat,$tacheCourant,$nbrTachesMax,$nbrMachinesMax){
			//SATURATION MAX
			$satmax = 120;
			// Determiner l'operation
			$operation = $tabOperations[$tacheCourant];
			// Chercher list des operateurs compatible
			$operateursIds = $operateursOperation[$tacheCourant];

			// Chercher l'operateur Adequoit
			foreach ($operateursIds as $idOpt) {

					$operateur = $operateurStat[$idOpt];

					if ($operation[3] == 0){ // Operation non affecté 
									$nbrmachines = $operateur[7]; 
									$potentiel = $operateur[1] ;
									$nbrtaches =  $operateur[8] ;
									$charge = $operateur[2] + $operation[0];
									$saturation = round(($charge / $potentiel) *100,2); 
									$effectif =  round($charge / $operateur[0] *100 , 2 ) ;

									// Operation non affecté et tt les contraintes d'operateur acceptable
									if ($saturation <= $satmax && $nbrtaches < $nbrTachesMax && $nbrmachines < $nbrMachinesMax ){
										
										// Changer les valeurs d'equilibrage pour l'operateur
										$operateur[2] = $charge  ;
										$operateur[3] = $saturation ;
										$operateur[4] = $effectif ;
										$operateur[5][] = $tacheCourant ; 
										$operateur[6][] = $operation[0] ;  // Ajouter la charge aux table stat
										$operateur[7] = diffMachine($operateur,$operation,$tabOperations);
										$operateur[8]=$nbrtaches+1;
										$operation[3] = 1;
										// Il faut sortir de la boucle 
										// Changer tout les tables
										//$tabOperations[$tacheCourant] = $operation;
										//$operateurStat[$idOpt] = $operateur;
										
										return array($operateur,$idOpt,$operation);
										
									}
									

					}
										

		} if ($operation[3] == 0 ){ // Tache non effecté a aucun operateur
			$nbrdiv = 2;
			$nbrdivtot = count($operateursIds);
			$operationdivise = diviserOperation(2,$operation,$tacheCourant);
			$affect = affecterOperationDivise($operationdivise,$operateursIds,$operateurStat,$nbrTachesMax,$nbrMachinesMax,$tabOperations);
			while (!$affect && ($nbrdiv <= $nbrdivtot)){
			$nbrdiv++;
			$operationdivise = diviserOperation($nbrdiv,$operation,$tacheCourant);
			$affect = affecterOperationDivise($operationdivise,$operateursIds,$operateurStat,$nbrTachesMax,$nbrMachinesMax,$tabOperations);
			}
			
		}


			
			
			return null;

}

/*
	Fonction qui permet de retourner l'indice de competence globale

*/
function getICG(){

}
function diffMachine($operateur,$operation,$tabOperations){
	
	
		if (count($operateur[5]) == 0 ){
			return 1;
		}
		$nbrmachines = $operateur[7];
		$machine = 0;
		foreach($operateur[5] as $oper){
			
			
			if ( getMachine($oper,$tabOperations) == $operation[2] ){
				$machine = 1;
			}
		}
		
		if ($machine){
			
			return $nbrmachines+1;
		}else{return $nbrmachines;}


}
function getMachine($oper,$tabOperations){
	return $tabOperations[$oper][2];

}

function initialisationOperations(){
	$tabOperations = array( 
						0 =>  array( 15 , "Couper Keder" , "lame automatique (SU LEE)" , 0  , 0 ),
					    1 =>  array( 12 , "Exécuter fixation sangle à 9cm" , "PP301 (Durkopp)" , 0  , 0 ),
					    2 =>  array( 50 , "Exécuter cross box sangle" , "Automate programme n°212 (Brother)"  , 0  , 0  ),
					    3 =>  array( 60 , "Assembler filets et bandes" , "PP301 (Durkopp)" , 0 , 0  ),
					    4 =>  array( 20 , "Couper extrémités des bandes","Ciseau à chaud" , 0 , 0 ),
					    5 =>  array( 50 , "Assembler Keder et filet","PP2. 301 (Durkopp)" , 0 , 0 ),
					    6 =>  array( 40 , "Exécuter les surpiqures de sécurité","Automate programme n°800 (Brother)", 0 , 0),
					    7 =>  array( 45 , "Eplucher et Controler (interm)","Manuel" , 0 , 1 ),
					    8 =>  array( 51 , "Assembler pièce et sangle","Automate programme n°805 (Brother)" , 0 , 0 ),
					    9 =>  array( 30 , "Controler","Manuel"  , 0 , 1 ),
					   10 => array( 20 , "Emballer","Manuel" , 0 , 0 )
					 
					 );
		return $tabOperations;
}

function initialisationOperateurs(){
	$tabOperateurs = array(
							0 => array( "Hayet"  , "HA1245" , 0 ),
							1 => array( "Mariem" , "MA1245" , 0 ),
							2 => array( "Roua" , "RA1245" , 0 ),
							3 => array( "Ghada" , "GH1245" , 0 ),
							4 => array( "Sameh" , "SA1245" , 0 ),
							5 => array( "Ameni" , "AM1245" , 0),
							6 => array( "Nouha" , "NO1245", 1)
							

	);
	return $tabOperateurs;
}

function initialisationStat(){
	$operateurStat = array(
						0 => array( 70.635 , 47.34 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						1 => array( 94.360 , 63.25 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						2 => array( 72.846 , 48.83 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						3 => array( 69.816 , 46.80 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						4 => array( 91.383 , 61.25 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						5 => array( 87.285 , 58.51 , 0 , 0 , 0 , array() , array() , 0 , 0 ),
						6 => array( 100.00 , 67.03 , 0 , 0 , 0 , array() , array() , 0 , 0 )

	);
	return $operateurStat;
}
function diviserOperation($nbrdiv,$operation,$idoperation){
	for($i=0;$i < $nbrdiv ;$i++ ){
		$operationdivise[$i][] = $operation[0]/$nbrdiv; // Tranche charge 
		$operationdivise[$i][] = 0; // Etat ( 1 => tranche affectee , 0 => tranche non affecté )
		$operationdivise[$i][] = $idoperation; // L'identifiant d'operation 
		$operationdivise[$i][] = null; // ID  d'operateur effectier cette tranche d'operation  
	}
	return $operationdivise;

}

function affecterOperationDivise($operationdivise,$operateursIds,&$operateurStat,$nbrTachesMax,$nbrMachinesMax,&$tabOperations){
	//SATURATION MAX
	$satmax = 120;
	$affectation = 0;
	$idoperationdivision = 0 ;
	$counter = 0;
	foreach ($operationdivise as $operation){ // Pour chaque tranche d'operation
	$trancheaffected = 0 ;
	while(!$trancheaffected && $counter<count($operateursIds)){
			// Pour chaque operateur
			$idOpt = $operateursIds[$counter];
			$operateur = $operateurStat[$idOpt];
			$tacheCourant = $operation[2];
			if ($operation[3] != $idOpt){
				// Essayer d'affecter cette tranche d'operation pour cette operateur
									$nbrmachines = $operateur[7]; 
									$potentiel = $operateur[1] ;
									$nbrtaches =  $operateur[8] ;
									$charge = $operateur[2] + $operation[0];
									$saturation = round(($charge / $potentiel) *100,2); 
									$effectif =  round($charge / $operateur[0] *100 , 2 ) ;

									// Operation non affecté et tt les contraintes d'operateur acceptable
									if ($saturation <= $satmax && $nbrtaches < $nbrTachesMax && $nbrmachines < $nbrMachinesMax ){
										
										
										$operation[3] = $idOpt;
										$affectation++;
										$trancheaffected = 1;
										
									}

			}

		$counter++;
		}// Fin de parcours de la liste des operateurs
		
		$operationdivise[$idoperationdivision] = $operation;
		$idoperationdivision++;
	}// Fin de parcours de la liste des tranches
	if ($affectation == count($operationdivise)){ // Tache divisé et effecté pour des operateurs
		
		foreach ($operationdivise as $operationtr) {
			$operation = $tabOperations[$operationtr[2]];
			$operateurStat[$operationtr[3]][2] += $operationtr[0];
			$charge = $operateurStat[$operationtr[3]][2];
			$potentiel = $operateur[1] ;
			$saturation = round(($charge / $potentiel) *100,2); 
			$effectif =  round($charge / $operateur[0] *100 , 2 ) ;

			$operateurStat[$operationtr[3]][3] = $saturation;
			$operateurStat[$operationtr[3]][4] = $effectif;
			$operateurStat[$operationtr[3]][5][] = $operationtr[2];
			$operateurStat[$operationtr[3]][6][] = $operationtr[0];
			$operateurStat[$operationtr[3]][7] = diffMachine($operateurStat[$operationtr[3]],$operation,$tabOperations);
			$operateurStat[$operationtr[3]][8] += 1;

		}
		$tabOperations[$operationdivise[0][2]][3] = 1;
		return 1;
		
	}else{ // Tache divisé et non effecté
		return 0;
	}

}

//

function affecterOperationControle($operations,$operateurstat,$idoperateur){

		$idoperation = 0;
		foreach($operations as $operation){
			if ($operation[4] == 1 ){
				$operateurstat[$idoperateur][2] = $operateurstat[$idoperateur][2] + $operation[0];
				$operateurstat[$idoperateur][3] = round($operateurstat[$idoperateur][2] / $operateurstat[$idoperateur][1] * 100 , 2 ) ;
				$operateurstat[$idoperateur][4] = round($operateurstat[$idoperateur][2] / $operateurstat[$idoperateur][0] * 100 , 2 ) ;
				$operateurstat[$idoperateur][5][] = $idoperation ;
				$operateurstat[$idoperateur][6][] = $operation[0] ;
				$operations[$idoperation][3] = 1; 
			}
			$idoperation ++;
		}
		
		return array($operateurstat,$operations);


}
//
function chercherOperateurControle($operateurs){
$idoperateur = 0;
	foreach ($operateurs as $operateur) {
		if ( $operateur[2] == 1 ){
			return $idoperateur;
		}
		$idoperateur++ ;
	}
	return -1;
}

?>