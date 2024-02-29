<table width="80%">
    <tr>
        <td width="100%">
            <h1 align="center">Avertissement</h1>
        </td>
    </tr>
        <td>
            <ul>
                <li><b>Sur les données</b>
                <ul>
                    <li>Les données sont <a href="https://www.data.gouv.fr/fr/datasets/les-deliberations-de-la-cnil/" target="new">"Les 
                        délibérations de la CNIL"</a> (mise à jour automatique hebdomadaire)
                    <li>Les fichiers XML répondent à la <a href="https://www.data.gouv.fr/fr/datasets/les-deliberations-de-la-cnil/" 
                        target="new">DTD CNIL</a> mais comportent certaines imperfections :
                        <ul>
                            <li>le champ "nature des délibérations" est rempli manuellement et n'est pas régulier
                            <li>les dates prises en compte sont celles des textes, les dates de publication correspondant à la 
                                publication sur la plateforme - cependant la date du texte peut sembler surprenante (nb 3 délibérations 
                                sont datées au 31/12/2999)
                            <li>Selon les délibérations, le champ "titre long" ou le champ "texte délibération" peut ne pas être documenté par la CNIL
                        </ul>
                </ul>
                <li><b>Sur le moteur de recherche</b>
                    <ul>
						<li>Le moteur de recherche a été mis à jour avec de nouvelles fonctionnalités. Il est désormais possible d'utiliser des opérateurs 
						de recherche tels que la virgule (ET logique), AND, OR, % (joker). </li>
						<li>Si plusieurs mots sont séparés par des espaces, ils sont considérés comme une expression complète.</li>
						<li>La recherche se fait en texte plein, sans distinction majuscules/minuscules.</li>
                        <li>Si un seul terme est recherché dans le texte, un concordancier sur ce terme est proposé.</li>
						<li>Le critère de date :
                        <ul>
                            <li>Il est inclusif de la date donnée
                            <li>La date (JJ/MM/AAAA) doit exister dans le calendrier
                        </ul>
					<br>
					<b># Explications des opérateurs :</b>
						<ul>
							<li> Virgule (,) : Opérateur de recherche AND logique. Il recherche les délibérations qui contiennent tous les termes spécifiés.</li>
							<li> AND : Opérateur de recherche AND logique. Il recherche les délibérations qui contiennent tous les termes spécifiés.</li>
							<li> OR : Opérateur de recherche OR logique. Il recherche les délibérations qui contiennent au moins l'un des termes spécifiés.</li>
							<li> % : Joker de correspondance avec 0 ou plusieurs mots. Par exemple, "Centre national % recherche scientifique" recherche tous les 
							délibérations qui commencent par "centre national" suivies (ou non) de n'importe quel(s) mot(s) et se terminant par "recherche".</li>
						</ul>
					<br>	
					<b># Exemples :</b>
						<ul>
							<li>CNRS, C.N.R.S. :
							Cette requête recherchera les résultats qui contiennent soit "CNRS" soit "C.N.R.S".</li>
							<li>CNRS AND C.N.R.S. :
							Cette requête recherchera les résultats qui contiennent à la fois "CNRS" et "C.N.R.S".</li>
							<li>CNRS OR C.N.R.S. :
							Cette requête recherchera les résultats qui contiennent soit "CNRS" soit "C.N.R.S".</li>
							<li>Centre national de% recherche scientifique :
							Cette requête recherchera les résultats qui commencent par "centre national de" suivi (ou non) de n'importe quel(s) mot(s) et se terminent par "recherche scientifique" 
							(ex. les résultats retournés peuvent contenir "centre national <u>de la recherche</u> scientifique" et "centre national <u>de recherche</u> scientifique"). 
							<br>
							Attention ! Si vous tapez une requête trop vague, par ex. "centre national% recherche", vous risquez d'obtenir des résultats bruités, tel que : 
							"<u>Centre national de</u>s soins palliatifs et de la fin de vie à mettre en œuvre des traitements automatisés à des fins de <u>recherche</u>s"</li>	
						</ul>						
				
                </ul>
            </ul>
        </td>
        </TR>
        </TABLE>
                    