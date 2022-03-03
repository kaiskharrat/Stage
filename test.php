<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

function dateTimDiff($date1, $date2)
{
    $dif = array();
    $first = strtotime($date1);
    $second = strtotime($date2);
    $datediff = abs($first - $second);
    $dif['s'] = floor($datediff);
    $dif['m'] = floor($datediff / (60)); //minute
    $dif['h'] = floor($datediff / (60 * 60)); //hour
    $dif['d'] = floor($datediff / (60 * 60 * 24));//day
    $dif['M'] = floor($datediff / (60 * 60 * 24 * 30)); //Months
    $dif['y'] = floor($datediff / (60 * 60 * 24 * 30 * 365));//year

    return $dif;
}

//----------------------------------------------------------------------------------------------------------------------------

$dateDebut = '2022-01-01';
$dateFin = '2022-01-31';

$nbDay = 0;
foreach( new DatePeriod(
             new DateTime($dateDebut),
             new DateInterval('P1D'),
             new DateTime($dateFin)
         ) as $oDT)
{
    $numCurrentDay = $oDT -> format('N');
    if
        ($numCurrentDay != '7')
    {
        ++ $nbDay;
    }
}

//---------------------------------------------------------------------------------------------------------------------------------------------

$inputFileType = 'Xlsx';
$inputFileName = 'input.xlsx';

$reader = \phpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

$spreadsheet = $reader->load($inputFileName);
$sheetData = $spreadsheet->getActiveSheet()->toArray();

foreach ($sheetData as $t) {
    $j = 0;
    if ($t[3] != 'Date/Temps') {
        $datenew = DateTime::createFromFormat("d/m/Y H:i:s", $t[3]);
        $j = ($datenew->format("d"));
    }


    $newSheetData[$t[0]][$t[2]][$j][] = array(
        "départment" => $t[0],
        "nom" => $t[1],
        "matricule" => $t[2],
        "dateTemps" => $t[3],
        "machine" => $t[4],
        "prénom" => $t[5],
    );
}

$somme = array();

foreach ($newSheetData as $key => $departement) {
    echo "<hr> departement :";

    print_r($key);
    echo "<br>";

    foreach ($departement as $departementId => $matricule) {
        echo "<hr> matricule :";

        print_r($departementId);
        echo "<br>";
        $jourDeTravail = 0;
        $jourPourVerifer = 0;
        $heursSupp = 0;
        $heursRetard = 0;

        foreach ($matricule as $matriculeId => $jour) {
            echo "<hr> jour :";

            print_r($matriculeId);
            echo "<br>";

            echo "<br>";
            $nbPointage = count($jour);





            if ($nbPointage == 2 || $nbPointage == 4) {
                print_r($jour["0"]['dateTemps']);
                echo "<br>";
                print_r($jour["1"]['dateTemps']);
                echo "<br>";
                $datenew = DateTime::createFromFormat("d/m/Y H:i:s", $jour["0"]['dateTemps']);
                $heurs = ($datenew->format("H:i:s"));
                $datenew2 = DateTime::createFromFormat("d/m/Y H:i:s", $jour["1"]['dateTemps']);
                $heurs2 = ($datenew2->format("H:i:s"));
                echo "<br>";
                $h1 = strtotime($heurs);
                $h2 = strtotime($heurs2);
                $diffh = date('H', $h2 - $h1);
                echo "Nombre d'heurs travailler : " . $diffh;
                echo "<br>";
                echo "nombre de pointage :" . $nbPointage;
                echo "<br>";
                if ($diffh >= 6) {
                    echo "nombre de jour travail :" . $jourDeTravail = $jourDeTravail + 1;
                    echo "<br>";
                        if($diffh>9){
                          $heursSuppJour = $diffh - 9 ;
                          $heursSupp=$heursSuppJour+$heursSupp ;
                          echo "Nombre d'heures supplémentaire : " . $heursSupp ;
                          echo "<br>";
                    }
                        else{
                            $heursRetardJour = 9-$diffh ;
                            $heursRetard=$heursRetardJour+ $heursRetard ;
                            echo "Nombre d'heures de retard : " . $heursRetard ;
                            echo "<br>";
                        }
                }
                echo "nombre de jour a verifer :" . $jourPourVerifer;
            } else {

                echo "nombre de pointage :" . $nbPointage;
                echo "<br>";
                echo "nombre de jour travail :" . $jourDeTravail;
                echo "<br>";
                echo "nombre de jour a verifer :" . $jourPourVerifer = $jourPourVerifer + 1;
                echo "<br>";
                echo "Nombre d'heures supplémentaire :" . $heursSupp;
                echo "<br>";
                echo "Nombre d'heures de retard :" . $heursRetard;

            }
            $nbJourAbsent= $nbDay - ($jourDeTravail + $jourPourVerifer);
            echo "<br>";
            //echo "nombre de jour dabsent : " . $nbJourAbsent ;

            echo "<tr>";
            echo "<tr>";
        }
        $somme[$departementId][$matriculeId]['Matricule'] = $departementId;
        $somme[$departementId][$matriculeId]['departement'] = $key;
        $somme[$departementId][$matriculeId]['jourDeTravail'] = $jourDeTravail;
        $somme[$departementId][$matriculeId]['jourPourVerifer'] = $jourPourVerifer;
        $somme[$departementId][$matriculeId]['Nombre dheures supplémentaire'] = $heursSupp;
        $somme[$departementId][$matriculeId]['Nombre dheures de retard'] = $heursRetard;
        $somme[$departementId][$matriculeId]['Nombre de jour dabsent '] = $nbJourAbsent;


    }
}

?>
    <table border="1" width="50" align="centre">
    <tr>
    <td>Matricule</td>
    <td>departement</td>
    <td>jourDeTravail</td>
    <td>jourPourVerifer</td>
    <td>Nombre dheures supplémentaire</td>
    <td>Nombre dheures de retard</td>
    <td>Nombre de jour dabsent</td>
 
    </tr>
   
   <?php

foreach ($somme  as $k) { ?>
    <tr>
    <td> <?php echo ($k[31]['Matricule']) ?> </td>
    <td><?php echo  ($k[31]['departement']) ?></td>
    <td><?php echo  ($k[31]['jourDeTravail']) ?></td>
    <td><?php echo  ($k[31]['jourPourVerifer']) ?></td>
    <td><?php echo  ($k[31]['Nombre dheures supplémentaire']) ?></td>
    <td><?php echo  ($k[31]['Nombre dheures de retard']) ?></td>
    <td><?php echo  ($k[31]['Nombre de jour dabsent ']) ?></td>
 
    </tr>
 <?php } ?>
    </table>
    <?php
    echo "<hr>"; 
    
    echo "<br>";








