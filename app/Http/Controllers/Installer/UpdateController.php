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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\DatabaseManager; use App\Http\Controllers\Installer\Helpers\InstalledFileManager; use Illuminate\Routing\Controller; class UpdateController extends Controller { use \App\Http\Controllers\Installer\Helpers\MigrationsHelper; public function welcome() { return view("\x69\156\x73\164\x61\154\x6c\x65\x72\56\x75\x70\x64\141\x74\x65\56\167\x65\154\x63\157\155\x65"); } public function overview() { $migrations = $this->getMigrations(); $dbMigrations = $this->getExecutedMigrations(); return view("\x69\156\x73\x74\x61\154\154\x65\162\x2e\165\x70\144\141\x74\x65\56\157\x76\145\162\x76\151\x65\x77", ["\156\x75\155\x62\x65\162\117\x66\x55\160\144\141\x74\x65\x73\120\145\156\144\151\156\x67" => count($migrations) - count($dbMigrations)]); } public function database() { $databaseManager = new DatabaseManager(); $response = $databaseManager->migrateAndSeed(); return redirect()->route("\x4c\141\162\141\166\145\x6c\125\x70\x64\x61\x74\145\x72\72\x3a\146\x69\156\141\x6c")->with(["\x6d\x65\163\x73\141\147\x65" => $response]); } public function finish(InstalledFileManager $fileManager) { $fileManager->update(); return view("\x69\156\163\164\x61\x6c\x6c\x65\162\56\x75\160\x64\x61\164\145\56\x66\x69\156\151\163\150\x65\144"); } }
