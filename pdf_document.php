<?php

  # project: eBrigade
  # homepage: http://sourceforge.net/projects/ebrigade/
  # version: 5.2
  # Copyright (C) 2004, 2020 Nicolas MARCHE
  
	# Correction module FFSS pour prélevement covid > Vervion 3.0 > Copyright (C) 2020 Mathias GUIGUEN  / Mathieu BOUCHER

include_once ("config.php");
check_all(0);
$id=$_SESSION['id'];
$mode=intval($_GET["mode"]);

if ( isset($_GET["P_ID"])) $pid=intval($_GET["P_ID"]);
else $pid=0;

if ( isset($_GET["note"])) $note=intval($_GET["note"]);
else $note=0;

if ( isset($_GET["victime"])) $victime=intval($_GET["victime"]);
else $victime=0;

if ( isset($_GET["numinter"])) $numinter=intval($_GET["numinter"]);
else $numinter=0;

if ( isset($_GET["signed"])) $signed=intval($_GET["signed"]);
else $signed=1;

if ( isset($_GET["evenement"])) $evenement=intval($_GET["evenement"]);
else $evenement=0;

if ( isset($_GET["print"])) $evenement=intval($_GET["print"]);
else $print=0;

/*
 * http://www.fpdf.org/
 * v1.85
 * a dezip et placer dans un dossier nommé : " fpdf "
 *
 * */
require_once("lib/fpdf/fpdf.php");
require_once("lib/fpdf/fpdi.php");
require_once("lib/fpdf/ebrigade.php");


// Appel de la librairie FPDF
//require("fpdf/fpdf.php");


class PDF_JavaScript extends FPDF {

    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8=false) {
        if(!$isUTF8)
            $script=utf8_encode($script);
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS '.$this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }
}

// Création de la class PDF
class PDF extends PDF_JavaScript {


	// Header
	function Header() {

		// Logo : 8 >position à gauche du document (en mm), 2 >position en haut du document, 80 >largeur de l'image en mm). La hauteur est calculée automatiquement.
		$this->Image('images/uploaded_ban.png',8,2);

		// Saut de ligne 20 mm

		$this->SetFont('Helvetica','',9);
		$this->SetX(125);
		$this->Cell(76,8,iconv('UTF-8', 'ISO-8859-1', "Fédération Française de Sauvetage et de Secourisme"),0,'L',1, false);

		$this->Ln(5);

		$this->SetFont('Helvetica','',9);
		$this->SetX(125);
		$this->Cell(76,8,iconv('UTF-8', 'ISO-8859-1', "Centre de Dépistage COVID-19"),0,'L', 'R');
		// Saut de ligne 10 mm
		$this->Ln(10);
		$this->SetDrawColor(29, 45, 111);
		$this->Line(0, 30, 220, 30);
		$this->Ln(10);

		// Titre gras (B) police Helbetica de 11
		$this->SetFont('Helvetica','B',11);
		// fond de couleur gris (valeurs en RGB)

 		// position du coin supérieur gauche par rapport à la marge gauche (mm)
		$this->SetX(70);
		// Texte : 60 >largeur ligne, 8 >hauteur ligne. Premier 0 >pas de bordure, 1 > retour à la ligne ensuite, C >centrer texte, 1> couleur de fond ok
		$this->Cell(60,8,'',0,1,'C',false);
		// Saut de ligne 10 mm
		$this->Ln(10);


	}
	 function AutoPrint($printer='')
    {
        // Ouvre la boîte d'impression
            $script = 'print(true);';
        $this->IncludeJS($script);
    }
    



}


$pdf = new PDF('P','mm','A4');


//$link = mysqli_connect('localhost','root','','test');

// extraction des données à afficher dans le sous-titre (nom du voyageur et dates de son voyage)
$requete = "
				SELECT
					cav.CAV_NAME provenance,
					vi.CAV_ENTREE date_prise_en_charge,
					vi.VI_NOM nom,
					vi.VI_PRENOM prenom,
					vi.VI_SEXE sexe,
					vi.VI_BIRTHDATE date_naiss,
					vi.VI_TEL tel,
					vi.VI_MAIL mail
				FROM victime vi, centre_accueil_victime cav
				WHERE cav.CAV_ID = vi.CAV_ID
				AND vi.VI_ID = $victime
			";
$result = mysqli_query($link, $requete);

/*---------------   POUR AFFICHER SI CEST PAS UNE DONNÉE GET   ---------------*/

$data_user = mysqli_fetch_array($result);
mysqli_free_result($result);

/* APPEL DES VARIABLES */

$provenance = $_data_user['provenance'];
$date_prise_en_charge = $_data_user['date_prise_en_charge'];
$idpass = $_data_user['id'];
$nom = $_data_user['nom'];
$prenom = $_data_user['prenom'];
$date_naiss = $_data_user['date_naiss'];
$tel = $_data_user['tel'];
$mail = $_data_user['mail'];

/* A RAJOUTER */

//$nir = $_data_user['nir'];
//$lot_fab = $_data_user['lot-fab'];



$query = "
				SELECT
					cav.CAV_NAME provenance,
					vi.CAV_ENTREE date_prise_en_charge,
					vi.VI_NOM nom,
					vi.VI_PRENOM prenom,
					vi.VI_SEXE sexe,
					vi.VI_BIRTHDATE date_naiss,
					vi.VI_TEL tel,
					vi.VI_MAIL mail
				FROM victime vi, centre_accueil_victime cav
				WHERE cav.CAV_ID = vi.CAV_ID
				AND vi.VI_ID = $victime
			";
        $result=mysqli_query($dbc,$query);
        custom_fetch_array($result);
        $VI_NOM=strtoupper($VI_NOM);
        $VI_PRENOM=my_ucfirst($VI_PRENOM);

/*
echo "VI NOM", $VI_NOM, "\n";
echo "VI mail", $VI_MAIL, "\n";

echo "\n";
echo "\n";

echo "VI mail", $provenance, "\n";
echo "VI mail", $date_prise_en_charge, "\n";
echo "VI mail", $nom, "\n";
echo "VI mail", $prenom, "\n";
echo "VI mail", $date_naiss, "\n";
echo "VI mail", $tel, "\n";
echo "VI mail", $mail, "\n";
*/

 /*---------------   POUR AFFICHER EN DONNÉE GET   ---------------

$provenance = $_GET['provenance'];
$date_prise_en_charge = $_GET['date_prise_en_charge'];
$id = $_GET['id'];
$nom = $_GET['nom'];
$prenom = $_GET['prenom'];
$date_naiss = $_GET['date_naiss'];
$tel = $_GET['tel'];
$mail = $_GET['mail'];
*/




// Nouvelle page A4 (incluant ici logo, titre et pied de page)
$pdf->AddPage();

// Polices par défaut : Helvetica taille 9
$pdf->SetFont('Helvetica','',9);

// Couleur par défaut : noir
$pdf->SetTextColor(0);



// Sous-titre calées à gauche, texte gras (Bold), police de caractère 11

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Nom: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(43);
$pdf->SetX(21);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($nom)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(43);
$pdf->SetX(70);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Prénom: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(43);
$pdf->SetX(86);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($prenom)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(43);
$pdf->SetX(130);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Date de naissance: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(43);
$pdf->SetX(166);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($date_naiss)),0,1,'L',false);
/* -------------------------------------------- */


$pdf->Ln(2);
$pdf->SetY(51);

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "N I R:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(51);
$pdf->SetX(21);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($nir)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(2);
$pdf->SetY(51);
$pdf->SetX(70);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Téléphone:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(51);
$pdf->SetX(92);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($tel)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(51);
$pdf->SetX(130);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Mail: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(51);
$pdf->SetX(142);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($mail)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(2);

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Date de prise en charge: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(59);
$pdf->SetX(56);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($date_prise_en_charge)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(59);
$pdf->SetX(100);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Provenance:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(59);
$pdf->SetX(125);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($provenance)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Résultat: "),0,1,'L',false);

$pdf->SetY(66);
$pdf->SetX(100);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "N° lot / fabrication:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(66);
$pdf->SetX(136);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($lot_fab)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(5); // saut de ligne 10mm



$pdf->SetFont('Helvetica','',11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(85,25, iconv('UTF-8', 'ISO-8859-1', "  "),0,1,'L', true);
/* -------------------------------------------- */
$pdf->SetY(80);
$pdf->SetX(13);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(5,6, iconv('UTF-8', 'ISO-8859-1', " "),0,1,'L',true);
/* -------------------------------------------- */
/* -------------------------------------------- */
$pdf->SetY(80);
$pdf->SetX(20);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "C    négatif"),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(77);
$pdf->SetX(116);
$pdf->SetFont('Helvetica','',11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(85,25, iconv('UTF-8', 'ISO-8859-1', "  "),0,1,'L', true);
/* -------------------------------------------- */
$pdf->SetY(80);
$pdf->SetX(120);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(5,6, iconv('UTF-8', 'ISO-8859-1', " "),0,1,'L',true);
/* -------------------------------------------- */
/* -------------------------------------------- */
$pdf->SetY(80);
$pdf->SetX(127);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "CT   positif"),0,1,'L',false);
/* -------------------------------------------- */



$pdf->SetY(105);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "IDE: "),0,1,'L',false);


$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Signature: "),0,1,'L',false);



$pdf->SetDrawColor(29, 45, 111);
$pdf->Line(0, 120, 220, 120);


/*   PARTIE DEUX  */

$pdf->Ln(7); // saut de ligne 10mm







$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Nom: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(124);
$pdf->SetX(21);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($nom)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(124);
$pdf->SetX(70);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Prénom: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(124);
$pdf->SetX(86);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($prenom)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(124);
$pdf->SetX(130);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Date de naissance: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(124);
$pdf->SetX(166);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($date_naiss)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(2);

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "N I R:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(132);
$pdf->SetX(21);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($nir)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(2);
$pdf->SetY(132);
$pdf->SetX(70);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Téléphone:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(132);
$pdf->SetX(92);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($tel)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(132);
$pdf->SetX(130);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Mail: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(132);
$pdf->SetX(142);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($mail)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(2);

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Date de prise en charge: "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(140);
$pdf->SetX(56);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($date_prise_en_charge)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(140);
$pdf->SetX(100);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Provenance:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(140);
$pdf->SetX(125);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($provenance)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Résultat: "),0,1,'L',false);

$pdf->SetY(146);
$pdf->SetX(100);
$pdf->SetFont('Helvetica','B',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "N° lot / fabrication:  "),0,1,'L',false);
/* -------------------------------------------- */
$pdf->SetY(146);
$pdf->SetX(136);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', ucfirst($lot_fab)),0,1,'L',false);
/* -------------------------------------------- */

$pdf->Ln(5); // saut de ligne 10mm


$pdf->SetFont('Helvetica','',11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(85,25, iconv('UTF-8', 'ISO-8859-1', "  "),0,1,'L', true);
/* -------------------------------------------- */
$pdf->SetY(160);
$pdf->SetX(13);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(5,6, iconv('UTF-8', 'ISO-8859-1', " "),0,1,'L',true);
/* -------------------------------------------- */
/* -------------------------------------------- */
$pdf->SetY(160);
$pdf->SetX(20);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "C    négatif"),0,1,'L',false);
/* -------------------------------------------- */

$pdf->SetY(157);
$pdf->SetX(116);
$pdf->SetFont('Helvetica','',11);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(85,25, iconv('UTF-8', 'ISO-8859-1', "  "),0,1,'L', true);
/* -------------------------------------------- */
$pdf->SetY(160);
$pdf->SetX(120);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(5,6, iconv('UTF-8', 'ISO-8859-1', " "),0,1,'L',true);
/* -------------------------------------------- */
/* -------------------------------------------- */
$pdf->SetY(160);
$pdf->SetX(127);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "CT   positif"),0,1,'L',false);
/* -------------------------------------------- */



$pdf->SetY(185);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "IDE: "),0,1,'L',false);


$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6, iconv('UTF-8', 'ISO-8859-1', "Signature: "),0,1,'L',false);








/* ETIQUETTE */



$pdf->SetY(220);
$pdf->SetX(10);
$pdf->SetFont('Helvetica','',11);
/* GESTION COULEUR ETIQUETTE POUR POSITIONNEMENT */
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(86,54, iconv('UTF-8', 'ISO-8859-1', "  "),0,1,'L', true);






$pdf->SetY(222);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "N° PAX: " . $victime),0,1,'L',false);

$pdf->SetY(232);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Nom: " . $nom),0,1,'L',false);

$pdf->SetY(244);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Prénom: " . $prenom),0,1,'L',false);

$pdf->SetY(255);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Date de naissance: " . $date_naiss),0,1,'L',false);

$pdf->SetY(266);
$pdf->SetFont('Helvetica','',11);
$pdf->Cell(50,6,iconv('UTF-8', 'ISO-8859-1', "Provenance: " . $provenance),0,1,'L',false);







if($print==1){
	$pdf->AutoPrint();
}

$pdf->Output('test.pdf','I'); // affichage à l'écran


// export  .pdf sur serveur

//$pdf->Output('F', 'test.pdf');
?>
