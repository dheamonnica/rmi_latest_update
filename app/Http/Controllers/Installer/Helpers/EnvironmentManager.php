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
 namespace App\Http\Controllers\Installer\Helpers; use Exception; use Illuminate\Http\Request; class EnvironmentManager { private $envPath; private $envExamplePath; public function __construct() { $this->envPath = base_path("\x2e\145\x6e\166"); $this->envExamplePath = base_path("\56\x65\x6e\x76\x2e\145\x78\x61\x6d\x70\154\145"); } public function getEnvContent() { if (file_exists($this->envPath)) { goto PynEc; } if (file_exists($this->envExamplePath)) { goto kcAfI; } touch($this->envPath); goto FAprO; kcAfI: copy($this->envExamplePath, $this->envPath); FAprO: PynEc: return file_get_contents($this->envPath); } public function getEnvPath() { return $this->envPath; } public function getEnvExamplePath() { return $this->envExamplePath; } public function saveFileClassic(Request $input) { $message = trans("\151\156\163\164\x61\154\154\x65\162\137\x6d\145\x73\163\x61\x67\145\163\56\x65\x6e\x76\151\x72\x6f\x6e\x6d\x65\156\x74\x2e\x73\x75\x63\x63\145\x73\163"); try { file_put_contents($this->envPath, $input->get("\145\156\x76\103\x6f\156\x66\x69\x67")); } catch (Exception $e) { $message = trans("\151\156\163\x74\141\x6c\154\x65\x72\x5f\155\x65\163\x73\x61\147\x65\x73\x2e\145\156\166\151\x72\157\156\x6d\145\x6e\x74\56\x65\x72\x72\x6f\162\x73"); } return $message; } }
