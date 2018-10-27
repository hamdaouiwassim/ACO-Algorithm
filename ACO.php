<?php

// Tableau du precedences des taches
$tPrec = array(
			0 => array ('-') ,
			1 => array ( 0 ) ,
			2 => array ( 3 ) ,
			3 => array ( 0 ),
			4 => array ( '-' ) ,
			5 => array ( 1 ),
			
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
	echo "<br> ======= pour la fourmi ".$Fourmi."  ====== <br>";
	$tabOperations = initialisationOperations();
	$tabOperateurs = initialisationOperateurs();
	$operateurStat = initialisationStat();
	$tacheCourant = $Fourmi;
	while (count($tabOperFinis) < count($tPrec)){

		echo "<br> ======= Tache courant ".$tacheCourant."  ====== <br>";
		$tachesPossibles = array();
		echo "============ Affichage des operations finis ===========<br>";
		var_dump($tabOperFinis);
		echo "<br>============= Affichage des taches possibles pour ".$tacheCourant." ====================== <br>";
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

		var_dump($tachesPossibles);
		$tabOperFinis[] = $tacheCourant;
		echo "<br>========= changement tache ========= <br>";
		
		$tacheCourant  = choisirOperHazard($tachesPossibles) ;
		echo "nouvel tache choisie ==> ".$tacheCourant;
			
		
		// Determination de la tache j @param ICG = Indice Competence Globale , $Q = constatne fixé , $rou = constante fixé
				
				/* Mise a jour du tableau du pheromones
				
				$tj = $tacheCourant;
				$delta = $Q/(1/$ICG);
				$tauxPheromones[$i][$j] = $rou * $tauxPheromones[$i][$j] + $delta ; 
				
				*/
	}
	var_dump($tabOperFinis);
	var_dump($operateurStat);
	var_dump($tabOperations);

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
				echo $indice;
				return $tabOper[$indice];	
			}	
	}
	else {echo "la tableau est vide";}
	
	
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
			$operateursId = $operateursOperation[$tacheCourant];
			// Chercher l'operateur Adequoit
			foreach ($operateursId as $idOpt) {

					$operateur = $operateurStat[$idOpt];
					if ($operation[3] == 0){
									$nbrmachines = $operateur[7]; 
									$potentiel = $operateur[1] ;
									$nbrtaches =  $operateur[8] ;
									$charge = $operateur[2] + $operation[0];
									$saturation = round(($charge / $potentiel) *100,2); 
									$effectif =  round($charge / $operateur[0] *100 , 2 ) ;

									
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
										
										echo "<br>========= Affichage d'operation ===========</br>";
										var_dump($operation);
										echo "<br>========= Affichage d'operation ===========</br>";
										return array($operateur,$idOpt,$operation);
										
									}
									

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
	echo "<br>============== diffMachine function ==================<br>";
	echo "=== Nombre d'operation == ".count($operateur[5]);
		if (count($operateur[5]) == 0 ){
			return 0;
		}
		$nbrmachines = $operateur[7];
		$machine = 0;
		foreach($operateur[5] as $oper){
			echo "<br>====== id operation ====== ".$oper;
			echo "<br>======= String GetMachine ===".getMachine($oper,$tabOperations);
			echo "<br>======= String operation courant ===".$operation[2];
			
			if ( getMachine($oper,$tabOperations) == $operation[2] ){
				$machine = 1;
			}
		}
		echo "<br>======== bool : ====== ".$machine;
		echo "<br>==================== fin diff machine ===================<br>";
		if ($machine){
			echo "<br>**********changement ressie**************<br>";
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

?>