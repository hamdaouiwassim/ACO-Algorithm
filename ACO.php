<?php
require_once "functions.php";

// Tableau des operations total

$tabOperationsTotal = array(
						0 =>  array( 15 , "Couper Keder" , "lame automatique (SU LEE)" , 0  , 0 ),
					    1 =>  array( 12 , "Exécuter fixation sangle à 9cm" , "PP301 (Durkopp)" , 0  , 0 ),
					    2 =>  array( 50 , "Exécuter cross box sangle" , "Automate programme n°212 (Brother)"  , 0  , 0  ),
					    3 =>  array( 60 , "Assembler filets et bandes" , "PP301 (Durkopp)" , 0 , 0  ),
					    4 =>  array( 20 , "Couper extrémités des bandes","Ciseau à chaud" , 0 , 0 ),
					    5 =>  array( 50 , "Assembler Keder et filet","PP2. 301 (Durkopp)" , 0 , 0 ),
					    6 =>  array( 40 , "Exécuter les surpiqures de sécurité","Automate programme n°800 (Brother)", 0 , 0),
					    7 =>  array( 51 , "Assembler pièce et sangle","Automate programme n°805 (Brother)" , 0 , 0 ),
					   	8 =>  array( 20 , "Emballer","Manuel" , 0 , 0 ),
					   	9 =>  array( 30 , "Controler","Manuel" , 0 , 1 ),
					   	10 =>  array( 45 , "Controler (interm ) ","Manuel" , 0 , 1 )
					   	
					   
					);


// Tableau du precedences des taches
$tPrec =  array(
						0 => array('-'),
						1 => array('-'),
						2 => array(1),
						3 => array('-'),
						4 => array(3),
						5 => array(4,0),
						6 => array(5),
						7 => array(6),
						8 => array(7),
						
);
$tabOperations = initialisationOperations();
$tabOperateurs = initialisationOperateurs();

$operateursOperation = array (
							0 =>  array( 0 , 2 , 3  ),
							1 =>  array( 0 , 2 , 3 , 4 , 5 ),
							2 =>  array( 0  , 3 , 4 ),
							3 =>  array( 0 , 1 , 2 , 3 ),
							4 =>  array( 0 , 2 , 3 ),
							5 =>  array( 0 , 2 , 3 ),
							6 =>  array( 2 , 3 , 5 ),
							7 =>  array( 2 , 3  ),
							8 =>  array( 0 , 1 , 2 , 3 , 4 , 5  ),
							


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
$pher0 = 0.02;
// Parametre du tableau du pheromones
$rou = 0.7 ;
$Q = 100 ;
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
echo "==== liste des fourmis ====<br>";
var_dump($tFourmis);
foreach ($tFourmis as $Fourmi ) {


	// Tableau d'operations finis
	$tabOperFinis = array();
	$tabOperations = initialisationOperations();
	$tabOperateurs = initialisationOperateurs();
	$operateurStat = initialisationStat();
	$tabOperationsFinisParts = initialiserOperationFinisParts($tabOperations);
	$tacheCourant = $Fourmi;

	// Affectation des operations du controle
	$idoperateur = chercherOperateurControle($tabOperateurs);
	$resultCtr = affecterOperationControle($tabOperationsTotal,$operateurStat,$idoperateur);
	$operateurStat = $resultCtr[0];
	$tabOperations = $resultCtr[1];
	echo "============ avant les fourmis ========";
	echo "===== liste des operations finis =====";
	var_dump($tabOperFinis);
	while (count($tabOperFinis) < count($tPrec)){
		echo "======= pour tache ".$tacheCourant."============";
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
				$tj = $tacheCourant;
				echo "===== liste des taches possibles =====";
				var_dump($tachesPossibles);
				echo "===== liste des operations finis =====";
				var_dump($tabOperFinis);
				
		
				
				
				
	} 			echo "======= Affichage du part des operations =======<br>";
				$tabOperationsFinisParts = calculerOperationFinisParts($tabOperations,$operateurStat);
				var_dump($tabOperationsFinisParts);
				echo "======= Affichage du part des operateurs =======<br>";
				var_dump($operateurStat);
				// Fin Fourmi 
				// Determination de la tache j @param ICG = Indice Competence Globale , $Q = constatne fixé , $rou = constante fixé
				$courantICG = calculICG($tabOperationsFinisParts,$tabOperFinis);
				$listeICG[] = $courantICG;
 				$EquilibrageData[] = array($tabOperationsFinisParts,$tabOperFinis,$courantICG,$operateurStat);
 				 
				// Deposer une poste entre 2 taches successives
							$delta = $Q/(1/$courantICG);
							for($i=0;$i<count($tabOperFinis)-1;$i++){
								$listeDelta[$tabOperFinis[$i]][$tabOperFinis[$i+1]][] = $delta;
							} 
							echo "<br>Liste of Delta<br>";
							var_dump($listeDelta);
							
							
				// fin mis a jour

				// Mise a jour du tableau du pheromones
							$delta = $Q/(1/$courantICG);
							for($i=0;$i<count($tabOperFinis)-1;$i++){
								$tauxPheromones[$tabOperFinis[$i]][$tabOperFinis[$i+1]] = ( 1 - $rou ) * $delta + array_sum ($listeDelta[$tabOperFinis[$i]][$tabOperFinis[$i+1]]) ;
							} 
							echo "<br>Taux du pher<br>";
							var_dump($tauxPheromones);
							
				// fin mis a jour
}
echo "======= listes des operations apres l'equilibrage =======<br>";
var_dump($tabOperations);
var_dump($listeICG);
 $courantICGMax = array(tabmax($listeICG),getIDICG($listeICG));
$listeICGMax[] = $courantICGMax; 
$tabOperFinisMax = $EquilibrageData[$courantICGMax[1]][1] ;
var_dump($listeICGMax);
var_dump($EquilibrageData);
echo "<br>====== liste operateur finis max =======<br>";
var_dump($tabOperFinisMax);
// Determination de la tache j @param ICGMax = Indice Competence Globale Max , $Q = constatne fixé , $rou = constante fixé
//$ICGMax = max($listeICG);

						// Deposer une poste entre 2 taches successives
							$deltaMax = $Q/(1/$courantICGMax[0]);
							for($i=0;$i<count($tabOperFinisMax)-1;$i++){
								$listeDeltaMax[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]][] = $deltaMax;
							} 
							echo "<br>Liste of Delta Max <br>";
							var_dump($listeDeltaMax);
							
							
							// fin mis a jour

							// Mise a jour du tableau du pheromones
										$deltaMax = $Q/(1/$courantICGMax[0]);
										for($i=0;$i<count($tabOperFinisMax)-1;$i++){
											$tauxPheromones[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]] = ( 1 - $rou ) * $delta + array_sum ($listeDeltaMax[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]]) ;
										} 
										echo "<br>Taux du pher Max<br>";
										var_dump($tauxPheromones);
										
							// fin mis a jour
// * ================== Fin 1er Iteration ================== * //

// * ================== Debut Reste d'operations =========== * //

// * ================== Calcul du Tableau du probabilité === * //
for ($i=0;$i<$nbrOper;$i++){
	for ($j=0; $j <$nbrOper ; $j++) { 

		$probabilite[$i][$j] = 0;
	}
}
// Affichage de tableau du probabilité  
var_dump($probabilite);

for ($ntaches=0;$ntaches < 1 ; $ntaches++){

					// * ================== Debut Reste d'operations =========== * //


						foreach ($tFourmis as $Fourmi ) {


											// Tableau d'operations finis
											$tabOperFinis = array();
											$tabOperations = initialisationOperations();
											$tabOperateurs = initialisationOperateurs();
											$operateurStat = initialisationStat();
											$tabOperationsFinisParts = initialiserOperationFinisParts($tabOperations);
											$tacheCourant = $Fourmi;

											// Affectation des operations du controle
											$idoperateur = chercherOperateurControle($tabOperateurs);
											$resultCtr = affecterOperationControle($tabOperationsTotal,$operateurStat,$idoperateur);
											$operateurStat = $resultCtr[0];
											$tabOperations = $resultCtr[1];
											echo "============ avant les fourmis ========";
											echo "===== liste des operations finis =====";
											var_dump($tabOperFinis);
											while (count($tabOperFinis) < count($tPrec)){
												echo "======= pour tache ".$tacheCourant."============";
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

														$tacheCourant  = CalculerProbabilte($ti,$tachesPossibles,$probabilite,$tauxPheromones,$tabOperations,0.8,0.2) ;
														$tj = $tacheCourant;
														echo "===== liste des taches possibles =====";
														var_dump($tachesPossibles);
														echo "===== liste des operations finis =====";
														var_dump($tabOperFinis);
														
												
														
														
														
											} 			echo "======= Affichage du part des operations =======<br>";
														$tabOperationsFinisParts = calculerOperationFinisParts($tabOperations,$operateurStat);
														var_dump($tabOperationsFinisParts);
														echo "======= Affichage du part des operateurs =======<br>";
														var_dump($operateurStat);
														// Fin Fourmi 
														// Determination de la tache j @param ICG = Indice Competence Globale , $Q = constatne fixé , $rou = constante fixé
														$courantICG = calculICG($tabOperationsFinisParts,$tabOperFinis);
														$listeICG[] = $courantICG;
										 				$EquilibrageData[] = array($tabOperationsFinisParts,$tabOperFinis,$courantICG,$operateurStat);
										 				 
														// Deposer une poste entre 2 taches successives
																	$delta = $Q/(1/$courantICG);
																	for($i=0;$i<count($tabOperFinis)-1;$i++){
																		$listeDelta[$tabOperFinis[$i]][$tabOperFinis[$i+1]][] = $delta;
																	} 
																	echo "<br>Liste of Delta<br>";
																	var_dump($listeDelta);
																	
																	
														// fin mis a jour

														// Mise a jour du tableau du pheromones
																	$delta = $Q/(1/$courantICG);
																	for($i=0;$i<count($tabOperFinis)-1;$i++){
																		$tauxPheromones[$tabOperFinis[$i]][$tabOperFinis[$i+1]] = ( 1 - $rou ) * $delta + array_sum ($listeDelta[$tabOperFinis[$i]][$tabOperFinis[$i+1]]) ;
																	} 
																	echo "<br>Taux du pher<br>";
																	var_dump($tauxPheromones);
																	
														// fin mis a jour
										
										echo "======= listes des operations apres l'equilibrage =======<br>";
										var_dump($tabOperations);
										var_dump($listeICG);
										 $courantICGMax = array(tabmax($listeICG),getIDICG($listeICG));
										$listeICGMax[] = $courantICGMax; 
										$tabOperFinisMax = $EquilibrageData[$courantICGMax[1]][1] ;
										var_dump($listeICGMax);
										var_dump($EquilibrageData);
										echo "<br>====== liste operateur finis max =======<br>";
										var_dump($tabOperFinisMax);
										// Determination de la tache j @param ICGMax = Indice Competence Globale Max , $Q = constatne fixé , $rou = constante fixé
										//$ICGMax = max($listeICG);
										// Deposer une poste entre 2 taches successives
										$deltaMax = $Q/(1/$courantICGMax[0]);
										for($i=0;$i<count($tabOperFinisMax)-1;$i++){
												$listeDeltaMax[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]][] = $deltaMax;
																	} 
										echo "<br>Liste of Delta Max <br>";
										var_dump($listeDeltaMax);
																	
																	
										// fin mis a jour							
								
																
										// Mise a jour du tableau du pheromones
										$deltaMax = $Q/(1/$courantICGMax[0]);
										for($i=0;$i<count($tabOperFinisMax)-1;$i++){
											$tauxPheromones[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]] = ( 1 - $rou ) * $delta + array_sum ($listeDeltaMax[$tabOperFinisMax[$i]][$tabOperFinisMax[$i+1]]) ;
										} 
										echo "<br>Taux du pher Max<br>";
										var_dump($tauxPheromones);
										
										// fin mis a jour
						

						// Determination de la tache j @param ICGMax = Indice Competence Globale Max , $Q = constatne fixé , $rou = constante fixé
						//$ICGMax = max($listeICG);
echo "======= probabilite finale ========";
var_dump($probabilite);
echo "======= tout les equilibrages ========";
var_dump($EquilibrageData);
} // Fin pour nombre des taches


// * ================== Fin Reste d'operations =========== * //


// * ================== Resultat Finale ================== * //

$ICGTotMax = $EquilibrageData[0];
foreach($EquilibrageData as $equilibrage){
	if ($equilibrage[2] > $ICGTotMax[2] ){
		$ICGTotMax = $equilibrage;
	}
}

echo "=======  equilibrage Total Max ========";
var_dump($ICGTotMax[0]);
//$ICGTotMax[1];
//$ICGTotMax[2];

}

echo "Nombre d'equilibrage effecté";
echo count($EquilibrageData);


?>


<!-- Bootstrap Include -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<!-- Bootstrap Include -->


<?php $nbrequi= 0 ; foreach($EquilibrageData as $Data){ 
	$nbrequi++;
//echo "====== Data =======";
//var_dump($Data);	
$DataInfo = $Data[0];
$Stat = $Data[3];

?>  	
<div style="padding:20px;">

			<div class="alert alert-info" role="alert">
			  Equilibrage <?php echo $nbrequi; ?> !
					  <br>
					  <div class="alert alert-success" role="alert">
					  Indice Competence Globale => " <?php echo $Data[2]; ?> "!
					</div>
			</div>

<table class="table">
  <thead>
    <tr>
      <th scope="col" >Les Operations | Les Operateurs</th>
      <?php foreach ($tabOperateurs as $operateur) {
      echo '<th scope="col" >'.$operateur[1].'</th>';	
      } ?>
    </tr>
   
  </thead>
  <tbody>
  		<?php foreach ($DataInfo as $Info) {//echo "Data";var_dump($DataInfo); ?>
  	    <tr>
      <td><?php echo $tabOperationsTotal[$Info[0]][1]." ( ".$tabOperationsTotal[$Info[0]][0]." ) ";  ?></td>
      		<?php
      		for ($i=0 ;$i<7 ;$i++){
      		 	$tabvalue[$i]="-";
      		 } 
      		 $j=0;
      		foreach($Info[1] as $op ){
      			$tabvalue[$op] = $Info[2][$j]; 
      			$j++;
      		} 
  		foreach($tabvalue as $value){
      		echo "<td>".$value."</td>";
      		} ?>
      
     
    </tr>
    <?php } ?>
    <tr class="table-primary"> 
      <th>Potentiel</th>
      <td><?php echo $Stat[0][1];?></td>
      <td><?php echo $Stat[1][1];?></td>
      <td><?php echo $Stat[2][1];?></td>
      <td><?php echo $Stat[3][1];?></td>
      <td><?php echo $Stat[4][1];?></td>
      <td><?php echo $Stat[5][1];?></td>
      <td><?php echo $Stat[6][1];?></td>
    </tr>
    <tr class="table-primary">
      <th>Charge</th>
      <td><?php echo $Stat[0][2];?></td>
      <td><?php echo $Stat[1][2];?></td>
      <td><?php echo $Stat[2][2];?></td>
      <td><?php echo $Stat[3][2];?></td>
      <td><?php echo $Stat[4][2];?></td>
      <td><?php echo $Stat[5][2];?></td>
      <td><?php echo $Stat[6][2];?></td>
    </tr>
    <tr class="table-primary">  
     <th>Saturation</th>
      <td><?php echo $Stat[0][3];?></td>
      <td><?php echo $Stat[1][3];?></td>
      <td><?php echo $Stat[2][3];?></td>
      <td><?php echo $Stat[3][3];?></td>
      <td><?php echo $Stat[4][3];?></td>
      <td><?php echo $Stat[5][3];?></td>
      <td><?php echo $Stat[6][3];?></td>
    </tr>
  	<tr class="table-primary">
      
      <th>Temps Effectif</th>
      <td><?php echo $Stat[0][4];?></td>
      <td><?php echo $Stat[1][4];?></td>
      <td><?php echo $Stat[2][4];?></td>
      <td><?php echo $Stat[3][4];?></td>
      <td><?php echo $Stat[4][4];?></td>
      <td><?php echo $Stat[5][4];?></td>
      <td><?php echo $Stat[6][4];?></td>
    </tr>
  
  
  </tbody>
</table>
</div>
<?php
 } ?>


<?php $Data = $ICGTotMax;
$DataInfo = $Data[0];
$Stat = $Data[3];
 ?>
 <div style="padding:20px;">

			<div class="alert alert-danger" role="alert">
			  Equilibrage avec ICG Max  !
					  <br>
					  <div class="alert alert-success" role="alert">
					  Indice Competence Globale => " <?php echo $Data[2]; ?> "!
					</div>
			</div>

<table class="table">
  <thead>
    <tr>
      <th scope="col" >Les Operations | Les Operateurs</th>
      <?php foreach ($tabOperateurs as $operateur) {
      echo '<th scope="col" >'.$operateur[1].'</th>';	
      } ?>

    </tr>
   
  </thead>
  <tbody>
  		<?php foreach ($DataInfo as $Info) {//echo "Data";var_dump($DataInfo); ?>
  	    <tr>
      <td><?php echo $tabOperationsTotal[$Info[0]][1]." ( ".$tabOperationsTotal[$Info[0]][0]." ) ";  ?></td>
      		<?php
      		for ($i=0 ;$i<count($tabOperateurs);$i++){
      		 	$tabvalue[$i]="-";
      		 } 
      		 $j=0;
      		foreach($Info[1] as $op ){
      			$tabvalue[$op] = $Info[2][$j]; 
      			$j++;
      		} 
      		foreach($tabvalue as $value){
      		echo "<td>".$value."</td>";
      		} ?>
      
     
    </tr>
    <?php } ?>
    <tr class="table-primary"> 
      <th>Potentiel</th>
      <td><?php echo $Stat[0][1];?></td>
      <td><?php echo $Stat[1][1];?></td>
      <td><?php echo $Stat[2][1];?></td>
      <td><?php echo $Stat[3][1];?></td>
      <td><?php echo $Stat[4][1];?></td>
      <td><?php echo $Stat[5][1];?></td>
      <td><?php echo $Stat[6][1];?></td>
    </tr>
    <tr class="table-primary">
      <th>Charge</th>
      <td><?php echo $Stat[0][2];?></td>
      <td><?php echo $Stat[1][2];?></td>
      <td><?php echo $Stat[2][2];?></td>
      <td><?php echo $Stat[3][2];?></td>
      <td><?php echo $Stat[4][2];?></td>
      <td><?php echo $Stat[5][2];?></td>
      <td><?php echo $Stat[6][2];?></td>
    </tr>
    <tr class="table-primary">  
     <th>Saturation</th>
      <td><?php echo $Stat[0][3];?></td>
      <td><?php echo $Stat[1][3];?></td>
      <td><?php echo $Stat[2][3];?></td>
      <td><?php echo $Stat[3][3];?></td>
      <td><?php echo $Stat[4][3];?></td>
      <td><?php echo $Stat[5][3];?></td>
      <td><?php echo $Stat[6][3];?></td>
    </tr>
  	<tr class="table-primary">
      
      <th>Temps Effectif</th>
      <td><?php echo $Stat[0][4];?></td>
      <td><?php echo $Stat[1][4];?></td>
      <td><?php echo $Stat[2][4];?></td>
      <td><?php echo $Stat[3][4];?></td>
      <td><?php echo $Stat[4][4];?></td>
      <td><?php echo $Stat[5][4];?></td>
      <td><?php echo $Stat[6][4];?></td>
    </tr>
  
  
  </tbody>
</table>
</div>
















