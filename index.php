<?php
    function searchInJSON($pattern) {
        global $storage;
        $linkPrinted = false;
        echo "<h1 style='text-align: center;'>Searching for Pattern \"$pattern\" in the crawled pages!</h1><br />";
        foreach ($storage as $link => $data) {
            $title = $data['title'];
            $paragraphs = $data['paragraphs'];

            if (isset($title) && is_array($paragraphs)) {
                if (strpos($title, $pattern)) {
                    echo "<h3 style='display: inline-block; text-align: center;'>URL:</h3> $link<br />";
                    $linkPrinted = true;
                    echo "<h4 style='display: inline;'>Found in Title:</h4> $title<br />";
                }
                foreach ($paragraphs as $paragraph) {
                    if (strpos($paragraph, $pattern)) {
                        if (!$linkPrinted) {
                            echo "<h3 style='display: inline-block; text-align: center;'>URL:</h3> $link<br />";
                        }
                        echo "<h4 style='display: inline;'>Found in Paragraph:</h4> $paragraph<br />";
                    }
                }
            }
            echo "<br />";
        }
    }

    function htmlRequest($url) {
        global $cURLInitializer, $dom;
        curl_setopt($cURLInitializer, CURLOPT_URL, $url);

        $output = curl_exec($cURLInitializer);

        if(!empty($output)) {
            $success = @$dom->loadHTML($output);
            if (!$success) {
                die("Failed to load HTML from cURL output.");
            }
            return $success;
        }
    }

    function getHTMLData($element) {
        global $dom;
        $data = $dom->getElementsByTagName($element);
        return $data;
    }

    function rootTextMatching($string) {
        global $patterns;
        foreach($patterns as $pattern) {
            if (is_array($pattern)) {
                foreach ($pattern as $pat) {
                    if (fnmatch($pat, $string)) {
                        return true;
                    }
                }
            } else {
                if (fnmatch($pattern, $string)) {
                    return true;
                }
            }
        }
        return false;
    }

    function extractTitle($link) {
        global $storage;
        $title = getHTMLData("title");
        if (isset($title->item(0)->nodeValue)) {
            $storage[$link]['title'] = $title->item(0)->nodeValue;
            echo "<h3 style='display: inline;'>Title of the Crawled URL:</h3> " . $title->item(0)->nodeValue . "<br />";
        } else {
            echo "<h3 style='display: inline;'>Title of the Crawled URL:</h3> NULL!<br />";
        }
    }

    function extractMetaTags() {
        $metaTags = getHTMLData("meta");
        echo "<h3>Meta Tags from the Crawled URL: </h3>";
        foreach ($metaTags as $metaTag) {
            if (!empty($metaTag->getAttribute("name")) && !empty($metaTag->getAttribute("content"))) {
                echo "<h4 style='display: inline;'>Meta Name Attribute:</h4> " . $metaTag->getAttribute("name") . "<br />";
                echo "<h4 style='display: inline;'>Meta Content Attribute:</h4> " . $metaTag->getAttribute("content") . "<br /><br />";
            }
        }
    }

    function extractParagraphs($link) {
        global $storage;
        $paragraphElements = getHTMLData("p");
        echo "<h3>Paragraphs from the Crawled URL: </h3><br />";
        foreach ($paragraphElements as $paragraphElement) {
            if (!empty($paragraphElement->nodeValue)) {
                $storage[$link]['paragraphs'][] = $paragraphElement->nodeValue;
                echo $paragraphElement->nodeValue . "<br />";
            }
        }
    }

    $seed_url = "https://www.nike.com";
    $depth = $currentDepth = 30;
    $patterns[] = ["*/member/inbox", "*/member/settings", "*/p/", "*/checkout/", "/*.swf$", "/*.pdf$", "/pdf/", "/ar/help/", "/br/help/", "/hk/help/", "/uy/help/", "/xf/help/", "/xl/help/", "/xm/help/", "/fragments/recommendations-carousel", "/kr/en$", "/kr/en/"];
    $hyperlinks = array();
    $visited = array();
    $storage = array();
    $dom = new DOMDocument;
    $patternToFind = "asks";

    $cURLInitializer = curl_init();

    curl_setopt($cURLInitializer, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($cURLInitializer, CURLOPT_HEADER, 0);

    while ($currentDepth >= 0) {
        if ($currentDepth == $depth) {
            if (htmlRequest($seed_url)) {
                $anchors = getHTMLData("a");
                foreach ($anchors as $anchor) {
                    $hyperlinks[] = $anchor->getAttribute("href");
                }
            }
        } else {
            foreach ($hyperlinks as $hyperlink) {
                if ($currentDepth >= 0) {
                    if (!(rootTextMatching($hyperlink)) && !(in_array($hyperlink, $visited))) {
                        if (htmlRequest($hyperlink)) {
                            $visited[] = $hyperlink;
                            $storage[$hyperlink] = array(
                                'title' => null,
                                'paragraphs' => array(),
                            );
                            echo "<h1 style='text-align: center;'>Crawled URL: " . $hyperlink . "</h1><br />";
                            extractTitle($hyperlink);
                            extractMetaTags();
                            extractParagraphs($hyperlink);
                            echo "<br />-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------<br /><br />";
                            $currentDepth--;
                        }
                    }
                }
            }
        }
        $currentDepth--;
    }

    

    $jsonData = json_encode($storage, JSON_PRETTY_PRINT);

    $filename = 'scraped_data.json';
    file_put_contents($filename, $jsonData);

    echo "Scraped data has been written to $filename. <br />";

    searchInJSON($patternToFind);
    curl_close($cURLInitializer);
?>