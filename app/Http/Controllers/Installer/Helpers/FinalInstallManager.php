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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Support\Facades\Artisan; use Symfony\Component\Console\Output\BufferedOutput; class FinalInstallManager { public function runFinal() { $outputLog = new BufferedOutput(); $this->generateKey($outputLog); $this->publishVendorAssets($outputLog); return $outputLog->fetch(); } private static function generateKey($outputLog) { try { if (!config("\x69\x6e\163\164\141\154\x6c\145\162\56\146\x69\x6e\x61\154\56\153\145\171")) { goto FcIio; } Artisan::call("\153\145\171\x3a\x67\x65\156\145\162\x61\x74\x65", ["\x2d\x2d\146\157\x72\143\145" => true], $outputLog); FcIio: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function publishVendorAssets($outputLog) { try { if (!config("\x69\156\x73\x74\x61\154\154\145\x72\x2e\x66\151\156\x61\154\x2e\x70\x75\x62\154\151\x73\x68")) { goto kKjCo; } Artisan::call("\x76\x65\156\144\x6f\162\72\x70\x75\142\154\x69\163\x68", ["\55\x2d\x61\x6c\154" => true], $outputLog); kKjCo: } catch (Exception $e) { return static::response($e->getMessage(), $outputLog); } return $outputLog; } private static function response($message, $outputLog) { return ["\163\164\141\x74\x75\x73" => "\x65\162\162\157\x72", "\x6d\145\x73\163\x61\147\x65" => $message, "\x64\x62\117\x75\x74\160\165\164\x4c\157\147" => $outputLog->fetch()]; } }
