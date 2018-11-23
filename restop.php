<?php



// * ================== Calcul du Tableau du probabilité === * //
for ($i=0;$i<$nbrOper;$i++){
	for ($j=0; $j <$nbrOper ; $j++) { 

		$probabilite[$i][$j] = 0;
	}
}
  


// * ================== Debut Reste d'operations =========== * //


foreach ($tFourmis as $Fourmi ) {


	// Tableau d'operations finis
	$tabOperFinis = array();
	$tabOperations = initialisationOperations();
	$tabOperateurs = initialisationOperateurs();
	$operateurStat = initialisationStat();
	$tabOperationsFinisParts = initialiserOperationFinisParts($tabOperations);
	$probababilte = array(); 
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
				//$tacheCourant  = choisirOperProbabilte($ti,$tachesPossibles,$probababilte,$tabOperations) ;
				$tj = $tacheCourant;
			
		
				
				
				
	} 			
				var_dump($tabOperationsFinisParts);
				var_dump($operateurStat);
				// Fin Fourmi 
				// Determination de la tache j @param ICG = Indice Competence Globale , $Q = constatne fixé , $rou = constante fixé
				//$listeICG[] = calculeICG();
				$listeDelta[] = $Q/(1/120);  
				// Mise a jour du tableau du pheromones
							$delta = $Q/(1/120);
							for($i=0;$i<count($tabOperFinis)-1;$i++){
								$tauxPheromones[$tabOperFinis[$i]][$tabOperFinis[$i+1]] = ( 1 - $rou ) * $delta + array_sum ($listeDelta) ;
							} 
							echo "<br>Taux du pher<br>";
							var_dump($listeDelta);
							var_dump($tauxPheromones);
							
				// fin mis a jour
}

// Determination de la tache j @param ICGMax = Indice Competence Globale Max , $Q = constatne fixé , $rou = constante fixé
//$ICGMax = max($listeICG);




// * ================== Fin Reste d'operations =========== * //

?>