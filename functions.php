<?php 
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
	$tachesps = array();
	foreach ($tachesPossibles as $tache) {
		if (!in_array($tache,$tOperFinis)){
			$tachesps[] = $tache; 
		}
		
	}
	$tachesPossibles = $tachesps;
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
			$satmax = 105;
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
					    7 =>  array( 51 , "Assembler pièce et sangle","Automate programme n°805 (Brother)" , 0 , 0 ),
					    8 =>  array( 20 , "Emballer","Manuel" , 0 , 0 )
					   
					 
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
	$satmax = 105;
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


function initialiserOperationFinisParts($tabOperations){
		$i=0;
	foreach ($tabOperations as $operation) {
		$tabOperationsFinisParts[$i][] = $i;
		$tabOperationsFinisParts[$i][] = array(); // listes des operateurs
		$tabOperationsFinisParts[$i][] = array(); // listes des parts d'operations
		$tabOperationsFinisParts[$i][] = array(); // IQ des operateurs
		$tabOperationsFinisParts[$i][] = array(); // A des Operateurs
		$tabOperationsFinisParts[$i][] = array(); // TP des Operateurs
		$i++;
	}
	return $tabOperationsFinisParts;
}


function calculerOperationFinisParts($tabOperations,$tabOperateursStats){
	
		$i=0;
	foreach ($tabOperations as $operation) {
		$tabOperationsFinisParts[$i][] = $i;
		$tabOperationsFinisParts[$i][] = getListeOperateurs($i,$tabOperateursStats); // listes des operateurs
		$tabOperationsFinisParts[$i][] = getListePartsOperations($i,$tabOperateursStats);
		$gammes =  allGammes();// listes des parts d'operations
		$tabOperationsFinisParts[$i][] = getIQOperateurs($i,$tabOperationsFinisParts[$i],$gammes); // IQ des operateurs
		$tabOperationsFinisParts[$i][] = getAOperateurs($i,$tabOperationsFinisParts[$i],$gammes); // A des Operateurs
		$tabOperationsFinisParts[$i][] = getTPOperateurs($i,$tabOperationsFinisParts[$i],$gammes); // TP des Operateurs
		$i++;
	}
	return $tabOperationsFinisParts;
}



function allGammes(){
	$Gammes = array (
					0 => array ( 0 , 0 , 0.986 , 0.758 , 0.828 ) ,
					1 => array ( 0 , 2 , 0.828 , 0.692 , 0.956 ) ,
					2 => array ( 0 , 3 , 0.833 , 0.667 , 0.965 ) ,
					3 => array ( 1 , 2 , 0.993 , 0.833 , 0.956 ) ,
					4 => array ( 1 , 5 , 1 , 0.767 , 0.989 ) ,
					5 => array ( 1 , 4 , 0.870 , 1 , 0.913 ) ,
					6 => array ( 1 , 3 , 0.930 , 0.800 , 0.965 ) ,
					7 => array ( 1 , 0 , 0.913 , 0.767 , 0.828 ) ,
					8 => array ( 2 , 4 , 0.985 , 0.972 , 0.913 ) ,
					9 => array ( 2 , 3 , 0.907 , 0.972 , 0.965 ) ,
					10 => array ( 2 , 0 , 0.859 , 0.653 , 0.828 ) ,
					11 => array ( 3 , 1 , 0.993 , 0.767 , 0.850 ) ,
					12 => array ( 3 , 2 , 0.964 , 0.972 , 0.950 ) ,
					13 => array ( 3 , 3 , 0.838 , 0.972 , 0.950 ) ,
					14 => array ( 3 , 0 , 0.803 , 0.653 , 0.583 ) ,
					15 => array ( 4 , 1 , 0.804 , 0.972 , 0.828 ) ,
					16 => array ( 4 , 2 , 0.655 , 0.583 , 0.956 ) ,
					17 => array ( 4 , 3 , 0.641 , 0.583 , 0.965 ) ,
					18 => array ( 5 , 0 , 0.926 , 0.875 , 0.828 ) ,
					19 => array ( 5 , 2 , 0.897 , 0.778 , 0.956 ) ,
					20 => array ( 5 , 3 , 0.955 , 0.597 , 0.965 ) ,
					21 => array ( 6 , 5 , 0.979 , 1 , 0.989 ) ,
					22 => array ( 6 , 2 , 0.998 , 0.889 , 0.956 ) ,
					23 => array ( 6 , 3 , 1 , 0.778 , 0.965 ) ,
					24 => array ( 7 , 2 , 0.982 , 0.857 , 0.956 ) ,
					25 => array ( 7 , 3 , 0.886 , 1 , 0.965 ) ,
					26 => array ( 8 , 0 , 1 , 1 , 1 ) ,
					27 => array ( 8 , 1 , 1 , 1 , 1 ) ,
					28 => array ( 8 , 2 , 1 , 1 , 1 ) ,
					29 => array ( 8 , 3 , 1 , 1 , 1 ) ,
					30 => array ( 8 , 4 , 1 , 1 , 1 ) ,
					31 => array ( 8 , 5 , 1 , 1 , 1 ) 
					

				);
	return $Gammes;
}

function choisirOperProbabilté($ti,$tabOper,&$probababilte,$tauxPheromones,$tabOperations){
	// Valeur $alpha
	$alpha = 0.8;
	// Valeur Beta
	$betta = 0.2;

	if (count($tabOper) > 0 ){
			if (count($tabOper) == 1 ){
				return $tabOper[0];
			}else{

				//calcul somme de tauxPheromones des voisinages
				$sumTauxPher = 0 ;
				foreach ($tabOper as $operation) {
					$sumTauxPher = pow(array_sum($tauxPheromones[$ti][$operation]),$alpha); 	
				 }
				// Calcul de probabilite
				 $i = 0;
				foreach ($tabOper as $operation) {
						if ($i == 0 ){
							$probmax = pow($tauxPheromones[$ti][$operation],$alpha) * pow($ni,$betta) / $sumTauxPher * pow($ni, $betta);
						}
						// calcul ni
						$ni = 0;
						if (getMachine($ti,$tabOperations) == getMachine($operation,$tabOperations)){
							$ni = 1 ;
						}

						$prob = pow($tauxPheromones[$ti][$operation],$alpha) * pow($ni,$betta) / $sumTauxPher * pow($ni, $betta) ; 	
						if ($prob > $probmax ){
							$probmax = $prob;
							$tache = $operation;
						}
						$probabilite[$ti][$operation] = $prob;
						$i++;
				 } 
				
			}	
	}
	return $tache;
}


function getListeOperateurs($i,$tab2){
	$listeop = array();
	$idop = 0;
	foreach ($tab2 as $operateur) {
		if (in_array($i,$operateur[5])){
			$listeop[] = $idop;
		}
		$idop++;
	}
return $listeop;
}

function getListePartsOperations($i,$tab2){
	$listepartop = array();
	$idop = 0;
	foreach ($tab2 as $operateur) {
		if (in_array($i,$operateur[5])){
			$counterop = 0;
			foreach($operateur[5] as $op){
				if ($i == $op){
					$listepartop[] = $operateur[6][$counterop];
				}
				$counterop++;
			}
		}
		$idop++;
	}
return $listepartop;
}

// $i : id operation
// $tabOperationsFinisParts : liste des operateurs qui on effectue cette operation $i
// $gammes : Tout la gammes du montages

function getIQOperateurs($i,$tabOperationsFinisParts,$gammes){
	$listeIQ = array();
	foreach($tabOperationsFinisParts[1] as $idoperateur){
		foreach ($gammes as $gamme) {
			if ($gamme[0] == $i && $gamme[1] == $idoperateur ){
				$listeIQ[] = $gamme[2];
			}
		}

	}
	return $listeIQ;
	
}
function getAOperateurs($i,$tabOperationsFinisParts,$gammes){
	$listeA = array();
	foreach($tabOperationsFinisParts[1] as $idoperateur){
		foreach ($gammes as $gamme) {
			if ($gamme[0] == $i && $gamme[1] == $idoperateur ){
				$listeA[] = $gamme[3];
			}
		}

	}
	return $listeA;
	
}
function getTPOperateurs($i,$tabOperationsFinisParts,$gammes){
	$listeTP = array();
	foreach($tabOperationsFinisParts[1] as $idoperateur){
		foreach ($gammes as $gamme) {
			if ($gamme[0] == $i && $gamme[1] == $idoperateur ){
				$listeTP[] = $gamme[4];
			}
		}

	}
	return $listeTP;
	
}




// 
function calculICG($tabOperationsFinisParts,$tabOperationsFinis){
	$N = 0;
	$IQG = 0;
	foreach ($tabOperationsFinisParts as $operation){
		foreach ($operation[3] as $IQ ) {
			$N++;
			$IQG+=$IQ;
			
		}
		foreach ($operation[5] as $TP ) {
			$TPG+=$TP;
			
		}
		$TPG = $TPG/$N;

	}
	$lastidfinis = $tabOperationsFinis[count($tabOperationsFinis)-1];
	$listeA = $tabOperationsFinisParts[$lastidfinis][4];
	if ( $listeA == NULL ){
		echo "NB : L'operation finale n'est pas affecté";
		$A = 0 ;
	}else{
		$A = $listeA[count($listeA)];
	}

	return (0.708*(0.665*(1-($N-$IQG))+0.231*$A+0.104*$TPG));
}

function tabmax($tab){
	$max = $tab[0];
	foreach($tab as $val){
		if ($val > $max){
			$max = $val;

		}
	}
	return $max;
}
function getIDICG($tab){
	$id = 0;
	$counter = 0;
	foreach($tab as $val){
		if ($val > $tab[$id]){
			$id = $counter;

		}
		$counter++;
	}
	return $id;
}

function CalculerProbabilte($tacheCourant,$tachesPossibles,&$probabilite,$tauxPheromones,$tabOperations,$alpha,$betta){
		
		$tachesuivant = -1;
		$probtachesuiv = -100;
	foreach ($tachesPossibles as $tache){
		if (getMachine($tachecourant,$tabOperations) == getMachine($tache,$tabOperations) ){
		 $nitaches = 1;	
		}else{
		 $nitaches = 0.02;
		}
		
		$sumTauxPher += $tauxPheromones[$tacheCourant][$tache];
		$probabilite[$tacheCourant][$tache] = pow($tauxPheromones[$tacheCourant][$tache],$alpha) * pow($nitaches,$betta) / $sumTauxPher * pow($nitaches,$betta);
		if ($probabilite[$tacheCourant][$tache] > $probtachesuiv){
			$tachesuivant = $tache;
			$probtachesuiv = $probabilite[$tacheCourant][$tache];
		}

	}


return $tache;

}
?>