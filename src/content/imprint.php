<?php

$sparql = '
    SELECT ?headline ?subtitle1 ?subtitle2 ?description2 ?subtitle3 ?description3 ?subtitle4 ?description4 ?source
        {
          GRAPH ?g {
            itcat_app:nav_privacy itcat_app:name ?headline .
    		itcat_app:imprint itcat_app:subtitle1 ?subtitle1 ;
                        itcat_app:subtitle2 ?subtitle2 ;
                        itcat_app:description2 ?description2 ;
                        itcat_app:subtitle3 ?subtitle3 ;
                        itcat_app:description3 ?description3 ;
                        itcat_app:subtitle4 ?subtitle4 ;
                        itcat_app:description4 ?description4 ;
    					itcat_app:source ?source .
            FILTER (langMatches(lang(?headline),"' . LANG . '"))
            FILTER (langMatches(lang(?subtitle1),"' . LANG . '"))
            FILTER (langMatches(lang(?subtitle2),"' . LANG . '"))
            FILTER (langMatches(lang(?description2),"' . LANG . '"))
            FILTER (langMatches(lang(?subtitle3),"' . LANG . '"))
            FILTER (langMatches(lang(?description3),"' . LANG . '"))
            FILTER (langMatches(lang(?subtitle4),"' . LANG . '"))
            FILTER (langMatches(lang(?description4),"' . LANG . '"))		
            FILTER (langMatches(lang(?source),"' . LANG . '"))
          }
        }';

$result = $db->query($sparql);
if (!$result) {
    print $db->errno() . ": " . $db->error() . "\n";
    exit;
}

while ($row = $result->fetch_array()) {
    echo '<section id="main" class="container">';
    echo '<header>';
    echo '<h2>'.$row['headline'].'</h2>';
    echo '</header>';
    echo '<div class="box">';
    echo '<br>';
    echo '<h3>'.$row['subtitle1'].':</h3>';
    echo '<p>
          Vera G. Meister<br />
          Magdeburger Straße 50<br />
          14770 Brandenburg an der Havel<br />
          <br />
          +49 3381 355 297<br />
          vera.meister@th-brandenburg.de
          </p>';
    echo '<h3>'.$row['subtitle2'].'</h3>';
    echo '<p>'.$row['description2'].'</p>';
    echo '<h3>'.$row['subtitle3'].'</h3>';
    echo '<p>'.$row['description3'].'</p>';
    echo '<h3>'.$row['subtitle4'].'</h3>';
    echo '<p>'.$row['description4'].'</p>';
    echo $row['source'].': http://www.e-recht24.de';
    echo ' </div></section>';
}


?>
<!--
<section id="main" class="container">
    <header>
        <h2>Imprint</h2>
    </header>
    <div class="box">
<br>
<h3>Angaben gemäß §5 TMG:</h3>
<p>
Vera G. Meister<br />
Magdeburger Straße 50<br />
14770 Brandenburg an der Havel<br />
<br />
+49 3381 355 297<br />
vera.meister@th-brandenburg.de
</p>

<h3>Haftung für Inhalte</h3>
<p>
Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
</p>

<h3>Haftung für Links</h3>
<p>
Unser Angebot enthält Links zu externen Webseiten Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.
</p>

<h3>Urheberrecht</h3>
<p>
Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers. Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet. Sollten Sie trotzdem auf eine Urheberrechtsverletzung aufmerksam werden, bitten wir um einen entsprechenden Hinweis. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Inhalte umgehend entfernen.
</p>

Quelle: http://www.e-recht24.de

    </div>
</section>-->