<?php

$motsvides = array("de","la","des","à","l","du","et","d","les","le","en","par","un","que","aux","pour","une","au","dans","n°","sur","ou","est","sont","qui","qu","-","ces","être","ne","s","pas","son","ayant","ses","ce","leur","n","il","ainsi","vers","cette","avec","leurs","elle","auprès","été","afin","avoir","non","sa","seront","lors","dont","après","m","sous","sera","peut","se","peuvent","entre","soit","ont","mme","y","plus","tout","elles","toute","comme","cet","si","même","r","chaque","autre","lui","deux","pendant","contre","outre","sans","ils","i","ailleurs","aucune","celles","avant","lesquelles","selon","enfin","ni","via","c","toutefois","lorsque","donc","toutes","tous","seules");

function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}
function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}

function cnil2html($fichier){
    $dir = "./dataxml/cnil/";
    //$content = "<H1>Affichage fichier</H1>\n\n<H2>fichier $fichier</H2>\n\n";
    $content = file_get_contents($dir.$fichier);
    $content = str_replace("<TEXTE_CNIL>", "<TABLE border=1 width=80%>", $content);$content = str_replace("</TEXTECNIL>","</TABLE>",$content);
    $content = str_replace("<META>","",$content); $content = str_replace("</META>","",$content);
    $content = str_replace("<META_COMMUN>","",$content); $content = str_replace("</META_COMMUN>","",$content);
    $content = str_replace("<ID>","<TR><TH width=20%>Identifiant CNIL</TH><TD>",$content); $content = str_replace("</ID>","</TD></TR>",$content);
    $content = str_replace("<ANCIEN_ID>","<TR><TH>Ancien Identifiant</TH><TD>",$content); $content = str_replace("</ANCIEN_ID>","</TD></TR>",$content);
    $content = preg_replace("/<ORIGINE.*?<\/ORIGINE>/", "", $content);
    $content = preg_replace("/<URL.*?<\/URL>/", "", $content);
    $content = str_replace("<NATURE>","<TR><TH>Nature document</TH><TD>",$content);$content=str_replace("</NATURE>","</TD></TR>",$content);
    $content = str_replace("<META_SPEC>","",$content); $content = str_replace("</META_SPEC>","",$content);
    $content = str_replace("<TITRE>","<TR><TH>Titre</TH><TD>",$content); $content = str_replace("</TITRE>", "</TD></TR>",$content);
    $content = str_replace("<TITREFULL>","<TR><TH>Titre long</TH><TD>",$content); $content = str_replace("</TITREFULL>", "</TD></TR>",$content);
    $content = str_replace("<NUMERO>","<TR><TH>Numéro</TH><TD>",$content); $content = str_replace("</NUMERO>", "</TD></TR>",$content);
    $content = str_replace("<NATURE_DELIB>","<TR><TH>Nature délibération</TH><TD>",$content); $content = str_replace("</NATURE_DELIB>", "</TD></TR>",$content);
    $content = str_replace("<DATE_TEXTE>","<TR><TH>Date du texte</TH><TD>",$content); $content = str_replace("</DATE_TEXTE>", "</TD></TR>",$content);
    $content = str_replace("<DATE_PUBLI>","<TR><TH>Date de publication</TH><TD>",$content); $content = str_replace("</DATE_PUBLI>", "</TD></TR>",$content);
    $content = str_replace("<ETAT_JURIDIQUE>","<TR><TH>Etat juridique</TH><TD>",$content); $content = str_replace("</ETAT_JURIDIQUE>", "</TD></TR>",$content);
    $content = str_replace("<CONTENU>","<TR><TH>Texte</TH><TD style=\"text-align:justify;\">",$content); $content = str_replace("</CONTENU>", "</TD></TR>",$content);
    $content = str_replace("<br/>","<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$content);
    return($content);

    }

function cnil2txt($fichier){
    $dir = "./dataxml/cnil/";
    $content = file_get_contents($dir.$fichier);

    preg_match('/<CONTENU>(.*?)<\/CONTENU>/s', $content, $matches);

    return isset($matches[1]) ? $matches[1] : 'Contenu not found';
}



function recupnoeud($haystack, $nodename){
    if (!$pos1 = strpos($haystack, "<$nodename>")) return "$nodename start absent";
    if (!$pos2 = strpos($haystack, "</$nodename")) return "$nodename end absent";
    $nodecontent = trim(addslashes(strip_tags(substr($haystack, $pos1, $pos2-$pos1))));
    if (strlen($nodecontent)==0) return "$nodename vide";
    else return $nodecontent;

}

function date2fr($date){
// date anglaise - on vérifie que c'est bien un format AAAA/MM/JJ
    if (preg_match("/(\d{4})-(\d{2})-(\d{2})/",$date, $matches)){
        //print "DATE2FR : $date =>".$matches[2]."/".$matches[1]."/".$matches[0];
        return $matches[3]."/".$matches[2]."/".$matches[1];
    }
    else return $date;
}

function date2en($date){
    if (preg_match("/(\d{2})\/(\d{2})\/(\d{4})/",$date, $matches)){
        print "DATE2EN : $date =>".$matches[3]."/".$matches[2]."/".$matches[1];
        return $matches[3]."/".$matches[2]."/".$matches[1];
    }
    else return $date;
}
?>