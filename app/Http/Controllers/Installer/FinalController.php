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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\DatabaseManager; use App\Http\Controllers\Installer\Helpers\EnvironmentManager; use App\Http\Controllers\Installer\Helpers\FinalInstallManager; use App\Http\Controllers\Installer\Helpers\InstalledFileManager; use Illuminate\Routing\Controller; class FinalController extends Controller { public function final(FinalInstallManager $finalInstall, EnvironmentManager $environment) { $finalMessages = $finalInstall->runFinal(); $finalEnvFile = $environment->getEnvContent(); return view("\x69\x6e\163\164\x61\154\154\145\162\56\146\x69\156\x69\x73\150\145\x64", compact("\146\x69\x6e\141\x6c\x4d\145\x73\x73\x61\147\145\163", "\x66\151\x6e\x61\x6c\x45\x6e\166\106\x69\x6c\145")); } public function seedDemo(DatabaseManager $databaseManager) { $response = $databaseManager->seedDemoData(); return redirect()->route("\111\x6e\x73\x74\141\x6c\x6c\x65\162\56\x66\151\x6e\151\x73\x68"); } public function finish(InstalledFileManager $fileManager) { $finalStatusMessage = $fileManager->update(); return redirect()->to(config("\151\x6e\x73\164\141\154\154\145\162\56\162\145\x64\151\x72\145\x63\164\x55\162\154"))->with("\x6d\145\163\163\x61\147\145", $finalStatusMessage); } }
