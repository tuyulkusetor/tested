<?php
$cookieBOCOM = file_get_contents('cookie.txt');
$link = readline("MASUKKAN LINK ?? ");
echo "\nPROSESS AGAK LAMBAT KARENA HANYAK CEK HOTEL YANG MENDAPATKAN REWARD SAJA!!";

$page = file_get_contents('page.txt');
$pages = explode("\r\n", $page);
$uniquePages = array_unique($pages);

foreach ($uniquePages as $searchPage) {
    $linkPage = $link . '' . $searchPage;
    $resultSearch = searchHotel($linkPage, $cookieBOCOM);

    if (strpos($resultSearch, "Page Not Found")) {
        echo "LINK TIDAK VALID!! CEK ULANG!!";
    } else {
        preg_match_all('/4251">(.*)<\/h2>/U', $resultSearch, $track);
        preg_match('/43802">(.*)<\/div>/U', $resultSearch, $hotelNmss); // NAMA HOTEL
        preg_match('/16cb"><button aria-label=" (.*?)" aria-current="page"/U', $resultSearch, $pageress); // PAGE HALAMAN
        $namaHotel = $hotelNmss[1];
        echo "\nGET DATA HOTEL PAGE ~ " . $pageress[1] . " ~ CHECK HOTEL REWARD ONLY!!\n";
        preg_match_all('/<div class="f8425bf46a">(.*)<\/div>/U', $resultSearch, $trackss);
        preg_match_all('/class="a4225678b2"><a href="(.*)" class=/U', $resultSearch, $linkHtEL);
        preg_match_all('/>Earn Rp&nbsp;(.*)<\/span><\/span>/U', $resultSearch, $shrt);

        // print_r($linkHtEL);
        foreach ($linkHtEL[1] as $linkHOTEL) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $linkHOTEL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
            $headers = array();
            $headers[] = 'Authority: www.booking.com';
            $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
            $headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
            $headers[] = 'Cache-Control: max-age=0';
            $headers[] = 'Cookie: ' . $cookieBOCOM;
            $headers[] = 'Dnt: 1';
            $headers[] = 'Sec-Fetch-Dest: document';
            $headers[] = 'Sec-Fetch-Mode: navigate';
            $headers[] = 'Sec-Fetch-Site: none';
            $headers[] = 'Sec-Fetch-User: ?1';
            $headers[] = 'Upgrade-Insecure-Requests: 1';
            $headers[] = 'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $resultLink = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error: DiSiNi!! ' . curl_error($ch);
                echo die();
            }
            curl_close($ch);

            //SHORT LINK
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://t.ly/api/v1/link/shorten');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"long_url\":\"$linkHOTEL\",\"domain\":\"https://t.ly/\"}");
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
            $headers = array();
            $headers[] = 'Authority: t.ly';
            $headers[] = 'Accept: application/json';
            $headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
            $headers[] = 'Authorization: Bearer PupuS2Z3b6qGKfFo4EGY3FwCqoJJUsJZwmeDOAMrvTE5ExhNsOb7evgLsKdD';
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            preg_match('/"short_id":"(.*?)","expire/U', $result, $shrt);
            if (preg_match_all('/class="bui-badge__text">\n(.*)\n<\/span>/U', $resultLink, $linkHtEL)) {
                $rewardHotel = $linkHtEL[1][0];
                preg_match('/class="hp-header--title--text">(.*?)<\/span>/U', $resultLink, $namaHotel);

                if (strpos($rewardHotel, "Free!") !== false) {
                } elseif (strpos($rewardHotel, "Earn") !== false) {
                    if (strpos($resultLink, "No credit card") !== false) {

                        $hasilPAGE =  "[+] $namaHotel[1] => $rewardHotel => TIDAK BUTUH CREDIT CARD!! |LINK : https://t.ly/$shrt[1]\n";
                        echo strtoupper($hasilPAGE);
                    } else {
                        $hasilPAGE =  "[+] $namaHotel[1] => $rewardHotel => BUTUH CREDIT CARD!! |LINK : https://t.ly/$shrt[1]\n";
                        echo strtoupper($hasilPAGE);
                    }

                    $saveDATA = fopen("bocomLink.txt", "a");
                    fputs($saveDATA, "[+] HASIL PAGE : $pageress[1] => $hasilPAGE\r");
                    fclose($saveDATA);
                } else {
                    echo "[!] COOKIE MATI / HOTEL TIDAK ADA REWARD!!";
                }
            } else {
            }
        }
    }
}

echo "RESULT SAVE TO bocomLink.txt\n";

function searchHotel($linkPage, $cookieBOCOM)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkPage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    $headers = array(
        'Authority: www.booking.com',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
        'Cache-Control: max-age=0',
        'Cookie: ' . $cookieBOCOM,
        'Dnt: 1',
        'Sec-Fetch-Dest: document',
        'Sec-Fetch-Mode: navigate',
        'Sec-Fetch-Site: none',
        'Sec-Fetch-User: ?1',
        'Upgrade-Insecure-Requests: 1',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return $result;
}
function input($text)
{
    echo $text . " => : ";
    $a = trim(fgets(STDIN));
    return $a;
}
