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
 namespace App\Http\Controllers\Installer\Helpers; use Illuminate\Support\Facades\DB; trait MigrationsHelper { public function getMigrations() { $migrations = glob(database_path() . DIRECTORY_SEPARATOR . "\x6d\x69\147\162\141\x74\x69\157\x6e\x73" . DIRECTORY_SEPARATOR . "\x2a\56\160\150\x70"); return str_replace("\56\160\150\x70", '', $migrations); } public function getExecutedMigrations() { return DB::table("\155\x69\147\162\x61\x74\151\157\x6e\x73")->get()->pluck("\155\151\x67\162\141\x74\151\157\x6e"); } }
