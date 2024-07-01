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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\PermissionsChecker; use App\Http\Requests; use Illuminate\Routing\Controller; class PermissionsController extends Controller { protected $permissions; public function __construct(PermissionsChecker $checker) { $this->permissions = $checker; } public function permissions() { $permissions = $this->permissions->check(config("\151\156\x73\164\141\x6c\154\145\162\56\x70\x65\x72\155\x69\x73\163\151\x6f\156\163")); return view("\x69\156\x73\164\141\154\x6c\145\x72\x2e\x70\145\162\x6d\x69\x73\x73\x69\157\156\163", compact("\160\x65\x72\x6d\151\163\x73\x69\x6f\x6e\163")); } }
