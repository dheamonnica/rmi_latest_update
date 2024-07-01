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
 namespace App\Http\Controllers\Installer; use App\Http\Controllers\Installer\Helpers\RequirementsChecker; use Illuminate\Routing\Controller; class RequirementsController extends Controller { protected $requirements; public function __construct(RequirementsChecker $checker) { $this->requirements = $checker; } public function requirements() { $phpSupportInfo = $this->requirements->checkPHPversion(config("\151\x6e\163\164\x61\x6c\154\x65\162\x2e\143\x6f\162\x65\56\x6d\151\156\120\x68\x70\x56\145\x72\163\151\x6f\156"), config("\151\156\x73\x74\141\x6c\154\x65\x72\56\143\157\162\x65\56\x6d\141\x78\x50\150\x70\126\145\x72\163\x69\x6f\156")); $requirements = $this->requirements->check(config("\x69\x6e\163\164\141\154\154\145\162\56\162\145\161\x75\151\162\x65\x6d\x65\156\164\x73")); return view("\151\156\x73\164\141\x6c\154\145\x72\56\162\x65\161\x75\151\162\145\x6d\x65\x6e\164\x73", compact("\x72\x65\161\x75\151\162\x65\x6d\145\x6e\x74\x73", "\160\x68\160\123\165\160\160\x6f\x72\164\x49\156\146\157")); } }
