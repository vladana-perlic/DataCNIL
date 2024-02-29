<?php
session_start();
if (isset($_SESSION['post'])) $_POST = $_SESSION['post'];
include_once("trace.php");
$ref = $_GET['ref'];
if ($ref == "all") {
    $contraintetemp = "1";
} else if (strlen($ref) > 4) {
    $mois = substr($ref, 0, 2);
    $annee = substr($ref, 3, 4);
    $contraintetemp = "AnneeTexte LIKE \"$annee\" AND MoisTexte LIKE \"$mois\"";
} else {
    $contraintetemp = "AnneeTexte LIKE \"$ref\"";
}
include_once("connexion.php");
include_once("fonctions.php");

$rqid = "SELECT NomFichier, DateTexte FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
$rsid = mysqli_query($connexion, $rqid);


$wordsArray = [];

while ($lgid = mysqli_fetch_row($rsid)) {
    $userText = cnil2txt($lgid[0]);
    // print cnil2txt($lgid[0]);
    $wordsArray[] = $userText;
}

function calculateWordFrequency($wordsArray) {
    $wordFrequency = array();
    foreach ($wordsArray as $word) {
        $word = strtolower($word);
        $wordFrequency[$word] = true; 
    }
    return array_keys($wordFrequency);
}

$wordFrequencyObj = calculateWordFrequency($wordsArray);

$jsWordFrequencyObj = json_encode($wordFrequencyObj);
$jsWordsArray = json_encode($wordsArray);
$motsvides = array("de","a","la","des","à","l","du","et","d","les","le","en","par","un","que","aux","pour","une","au","dans","n°","sur","ou","est","sont","qui","qu","-","ces","être","ne","s","pas","son","ayant","ses","ce","leur","n","il","ainsi","vers","cette","avec","leurs","elle","auprès","été","afin","avoir","non","sa","seront","lors","dont","après","m","sous","sera","peut","se","peuvent","entre","soit","ont","mme","y","plus","tout","elles","toute","comme","cet","si","même","r","chaque","autre","lui","deux","pendant","contre","outre","sans","ils","i","je","x","X","ailleurs","aucune","celles","avant","lesquelles","selon","enfin","ni","via","c","toutefois","lorsque","donc","toutes","tous","seules","<xml",">","<br","br/","/p","<br/>","</p>","<p","</p><p");

?>

<html>
    <head>
        <title>DATA CNIL Selection</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
        <script src="http://d3js.org/d3.v3.min.js"></script>
        <script src="https://rawgit.com/jasondavies/d3-cloud/master/build/d3.layout.cloud.js"></script>
        <script>

//Simple animated example of d3-cloud - https://github.com/jasondavies/d3-cloud
//Based on https://github.com/jasondavies/d3-cloud/blob/master/examples/simple.html

// Encapsulate the word cloud functionality
function wordCloud(selector) {

    var fill = d3.scale.category20();

    //Construct the word cloud's SVG element
    var svg = d3.select(selector).append("svg")
        .attr("width", 500)
        .attr("height", 500)
        .append("g")
        .attr("transform", "translate(250,250)");


    //Draw the word cloud
    function draw(words) {
        var cloud = svg.selectAll("g text")
                        .data(words, function(d) { return d.text; })

        //Entering words
        cloud.enter()
            .append("text")
            .style("font-family", "Impact")
            .style("fill", function(d, i) { return fill(i); })
            .attr("text-anchor", "middle")
            .attr('font-size', 1)
            .text(function(d) { return d.text; });

        //Entering and existing words
        cloud
            .transition()
                .duration(600)
                .style("font-size", function(d) { return d.size + "px"; })
                .attr("transform", function(d) {
                    return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                })
                .style("fill-opacity", 1);

        //Exiting words
        cloud.exit()
            .transition()
                .duration(200)
                .style('fill-opacity', 1e-6)
                .attr('font-size', 1)
                .remove();
    }


    //Use the module pattern to encapsulate the visualisation code. We'll
    // expose only the parts that need to be public.
    return {

        //Recompute the word cloud for a new set of words. This method will
        // asycnhronously call draw when the layout has been computed.
        //The outside world will need to call this function, so make it part
        // of the wordCloud return value.
        update: function(words) {
            d3.layout.cloud().size([500, 500])
                .words(words)
                .padding(5)
                .rotate(function() { return ~~(Math.random() * 2) * 90; })
                .font("Impact")
                .fontSize(function(d) { return d.size; })
                .on("end", draw)
                .start();
        }
    }

}

var words = <?php echo $jsWordFrequencyObj; ?>;
var motsvides = <?php echo json_encode($motsvides); ?>;



// creating an array of words and computing a random size attribute.
function getWords(i) {
    return words[i]
            .replace(/[!\.,:;\?"]/g, '')
            .split(' ')
            .filter(function (d) {
                return motsvides.indexOf(d.toLowerCase()) === -1; 
            })
            .filter(function (d) {
                return isNaN(d) && motsvides.indexOf(d.toLowerCase()) === -1;
            })
            .map(function(d) {
                return {text: d, size: 10 + Math.random() * 60};
            })
}

//This method tells the word cloud to redraw with a new set of words.
//In reality the new words would probably come from a server request,
// user input or some other source.
function showNewWords(vis, i) {
    i = i || 0;

    vis.update(getWords(i ++ % words.length))
    setTimeout(function() { showNewWords(vis, i + 1)}, 5000)
}

//Create a new instance of the word cloud visualisation.
var myWordCloud = wordCloud('body');

//Start cycling through the demo data
showNewWords(myWordCloud);


</script>
        

