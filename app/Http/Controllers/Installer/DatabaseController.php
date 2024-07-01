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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\DatabaseManager; use Exception; use Illuminate\Routing\Controller; use Illuminate\Support\Facades\DB; class DatabaseController extends Controller { private $databaseManager; public function __construct(DatabaseManager $databaseManager) { $this->databaseManager = $databaseManager; } public function database() { if ($this->checkDatabaseConnection()) { goto dvnXz; } return redirect()->back()->withErrors(["\144\141\164\x61\142\x61\163\145\x5f\143\157\x6e\156\x65\x63\164\x69\157\156" => trans("\151\156\x73\x74\141\154\154\145\x72\137\x6d\145\163\x73\141\147\x65\x73\x2e\145\156\166\151\x72\x6f\x6e\155\145\x6e\x74\56\x77\x69\x7a\x61\162\144\56\x66\157\162\x6d\x2e\x64\142\137\x63\157\156\156\145\143\x74\151\157\156\137\x66\x61\151\154\145\144")]); dvnXz: ini_set("\155\141\170\137\145\170\x65\143\x75\x74\x69\x6f\156\x5f\164\x69\x6d\145", 600); $response = $this->databaseManager->migrateAndSeed(); return redirect()->route("\111\156\x73\164\x61\x6c\x6c\145\162\56\146\151\x6e\141\x6c")->with(["\155\x65\163\163\x61\147\145" => $response]); } private function checkDatabaseConnection() { try { DB::connection()->getPdo(); return true; } catch (Exception $e) { return false; } } }
