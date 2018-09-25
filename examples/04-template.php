<?php

use Redbit\Vyfakturuj\Api\VyfakturujApi;

require_once __DIR__ . '/00-config.php';

echo "<h2>Vytvoření a úpravy šablony</h2>\n";

$vyfakturuj_api = new VyfakturujApi(VYFAKTURUJ_API_LOGIN, VYFAKTURUJ_API_KEY);

#
#
#####################################################################################
#####################################################################################
#####                                                                           #####
#####                                 Vytvoření                                 #####
#####                                                                           #####
#####################################################################################
#####################################################################################
#
#
// Vytvoříme nový kontakt
$opt_contact = array(
    'IC' => '123456789',
    'name' => '#API - Ukázkový kontakt pro pravidelnou fakturu',
    // "#API - " dáváme na začátek, chceme mít tento kontakt na začátku našeho adresáře
    'note' => 'Kontakt vytvořený přes API',
    'company' => 'Ukázkový kontakt',
    'street' => 'Pouliční 79/C',
    'city' => 'Praha',
    'zip' => '10300',
    'country' => 'Česká republika',
    'mail_to' => 'info@examle.com',
);
$contact = $vyfakturuj_api->createContact($opt_contact);    // vytvoříme nový kontakt

$_ID_CONTACT = $contact['id'];

/*
 * Některá čísla v příkladu níže jsou číselná označení systémových typů.
 * Například: 'type' => 2 znamená, že vytváříme Pravidelnou fakturu, nikoliv jen Šablonu.
 * Popis všech hodnot najdete v dokumentaci: https://vyfakturujcz.docs.apiary.io/#reference/faktury
 * Zkušenější uživatelé mohou použít výčet možných hodnot v přiložené třídě Redbit\Vyfakturuj\Api\VyfakturujEnum.
 */
$opt_template = array(
    'id_customer' => $_ID_CONTACT,// vložíme právě vytvořený kontakt
//    'id_customer' => 20224,// vložíme právě vytvořený kontakt
    'type' => 2,// chceme pravidelnou fakturu
    'name' => '#API - Test pravidelné faktury',
//    'id_customer' => $contact['id'],// vložíme právě vytvořený kontakt
    'items' => array(
        array(
            'text' => 'Stěrač na ponorku',
            'unit_price' => 990.25,
            'vat_rate' => 15,
        ),
        array(
            'text' => 'Kapalina do ostřikovačů 250 ml',
            'unit_price' => 59,
            'vat_rate' => 15,
        )
    )
);


$ret = $vyfakturuj_api->createTemplate($opt_template);    // vytvoříme novou fakturu

echo '<h5>Vytvořili jsme pravidelnou fakturu:</h5>';
echo '<pre><code class="json">' . json_encode($ret, JSON_PRETTY_PRINT) . '</code></pre>';

$_ID_ITEM = $ret['id'];    // uložíme si ID nového zaznamu
#
#
#####################################################################################
#####################################################################################
#####                                                                           #####
#####                                  Úprava                                   #####
#####                                                                           #####
#####################################################################################
#####################################################################################
#
#

$opt_template = array(
    'name' => '#API + Test pravidelné faktury',// změníme název
    'items' => array(
        array(
            'text' => 'Stěrač na ponorku',
            'unit_price' => 990.25,
            'vat_rate' => 21,// změníme DPH
        ),
        array(
            'text' => 'Kapalina do ostřikovačů 250 ml',
            'unit_price' => 59,
            'vat_rate' => 21,// změníme DPH
        )
    )
);

$ret2 = $vyfakturuj_api->updateTemplate($_ID_ITEM, $opt_template);    // upravíme zaznam

echo '<h5>Upravili jsme pravidelnou fakturu:</h5>';
echo '<pre><code class="json">' . json_encode($ret2, JSON_PRETTY_PRINT) . '</code></pre>';


#
#
#####################################################################################
#####################################################################################
#####                                                                           #####
#####                                   Čtení                                   #####
#####                                                                           #####
#####################################################################################
#####################################################################################
#
#


$ret3 = $vyfakturuj_api->getTemplate($_ID_ITEM);
// $ret3 = $vyfakturuj_api->getTemplates(); // vrati vsechny sablony a pravidelné faktury
// $ret3 = $vyfakturuj_api->getTemplates(array('type' => 2,'end_type' => 1)); // vrati vsechny pravidelné faktury, které nemají nastaveno datum ukončení

echo '<h5>Načetli jsme data o pravidelné faktuře faktuře ze systému:</h5>';
echo '<pre><code class="json">' . json_encode($ret3, JSON_PRETTY_PRINT) . '</code></pre>';


#
#
#####################################################################################
#####################################################################################
#####                                                                           #####
#####                                  Smazání                                  #####
#####                                                                           #####
#####################################################################################
#####################################################################################
#
#
exit;   // zablokování smazání

$ret4 = $vyfakturuj_api->deleteTemplate($_ID_ITEM);
$ret5 = $vyfakturuj_api->deleteContact($_ID_CONTACT);

echo '<h5>Načetli jsme data o průběhu smazání faktury ze systému:</h5>';
echo '<pre><code class="json">' . json_encode($ret4, JSON_PRETTY_PRINT) . '</code></pre>';
echo '<h5>Načetli jsme data o průběhu smazání kontaktu ze systému:</h5>';
echo '<pre><code class="json">' . json_encode($ret5, JSON_PRETTY_PRINT) . '</code></pre>';
