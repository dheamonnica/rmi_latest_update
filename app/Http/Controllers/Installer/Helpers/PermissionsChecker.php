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
 namespace App\Http\Controllers\Installer\Helpers; class PermissionsChecker { protected $results = []; public function __construct() { $this->results["\160\x65\x72\155\x69\x73\163\x69\157\156\x73"] = []; $this->results["\145\162\x72\x6f\x72\x73"] = null; } public function check(array $folders) { foreach ($folders as $folder => $permission) { if (!($this->getPermission($folder) >= $permission)) { goto rGuKi; } $this->addFile($folder, $permission, true); goto xEr9s; rGuKi: $this->addFileAndSetErrors($folder, $permission, false); xEr9s: N_6_B: } yH_Jc: return $this->results; } private function getPermission($folder) { return substr(sprintf("\45\157", fileperms(base_path($folder))), -4); } private function addFile($folder, $permission, $isSet) { array_push($this->results["\160\145\x72\155\151\163\163\x69\157\x6e\163"], ["\146\x6f\154\144\145\162" => $folder, "\x70\145\162\x6d\x69\x73\163\151\x6f\x6e" => $permission, "\151\x73\x53\145\164" => $isSet]); } private function addFileAndSetErrors($folder, $permission, $isSet) { $this->addFile($folder, $permission, $isSet); $this->results["\x65\162\162\x6f\x72\163"] = true; } }
