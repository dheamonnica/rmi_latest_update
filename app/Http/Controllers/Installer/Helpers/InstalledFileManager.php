<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  2.0.14  |
    |              on 2024-02-26 06:11:06              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
/*
* Copyright (C) Incevio Systems, Inc - All Rights Reserved
* Unauthorized copying of this file, via any medium is strictly prohibited
* Proprietary and confidential
* Written by Munna Khan <help.zcart@gmail.com>, September 2018
*/
 namespace App\Http\Controllers\Installer\Helpers; class InstalledFileManager { public function create() { $installedLogFile = storage_path("\151\156\x73\x74\141\x6c\154\145\144"); $dateStamp = date("\x59\x2f\x6d\x2f\144\x20\x68\72\x69\72\163\141"); if (!file_exists($installedLogFile)) { goto uASLB; } $message = trans("\x69\x6e\163\x74\141\x6c\x6c\x65\162\x5f\155\145\163\163\141\147\145\163\x2e\165\x70\144\141\164\145\x72\56\154\157\x67\56\x73\x75\x63\143\145\x73\163\x5f\155\x65\x73\163\x61\x67\x65") . $dateStamp; file_put_contents($installedLogFile, $message . PHP_EOL, FILE_APPEND | LOCK_EX); goto SK4De; uASLB: $message = trans("\x69\x6e\x73\x74\x61\x6c\x6c\x65\162\x5f\155\x65\x73\163\x61\147\x65\163\56\151\x6e\163\x74\x61\154\x6c\x65\144\x2e\163\165\x63\x63\x65\x73\163\137\154\x6f\147\x5f\x6d\145\x73\163\141\x67\145") . $dateStamp . "\xa"; file_put_contents($installedLogFile, $message); SK4De: return $message; } public function update() { return $this->create(); } }
