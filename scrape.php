<?php
$toc = getTableOfContents('http://www.pearsonhighered.com/educator/product/Anthropology-14E/9780205957187.page');

echo print_r($toc, true);

function getTableOfContents( $url ) {
    $toc = array();

    $doc = new DOMDocument();

    if (!$doc->loadHTMLFile( $url )) {
        return 'UNABLE TO LOAD '.$url;
    }

    $toc_source = $doc->getElementById('table-of-contents');
    if (!count($toc_source)) {
        return 'NO table-of-contents ELEMENTS IN '.$url;
    }

    $toc_items = $toc_source->getElementsByTagName('p');
    if (!count($toc_items)) {
        return 'Empty TOC in '.$url;
    }
    foreach ($toc_items as $item) {
        $toc[] = $item->textContent;

        /*
         * failed to remove empty lines...
        $string = $item->textContent;
        if (strlen(trim($string))) {
        }
        */
    }

    return $toc;


}
?>