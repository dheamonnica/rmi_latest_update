<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\Config;
// use App\Common\Authorizable;
use Illuminate\Http\Request;
use App\Events\Shop\ShopIsLive;
use App\Events\Shop\ShopUpdated;
use App\Events\Shop\ConfigUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\Shop\DownForMaintainace;
use App\Http\Requests\Validations\UpdateConfigRequest;
use App\Http\Requests\Validations\MerchantVerifyRequest;
use App\Http\Requests\Validations\UpdateBasicConfigRequest;
use App\Http\Requests\Validations\ToggleMaintenanceModeRequest;

class ConfigController extends Controller
{
    // use Authorizable;

    private $model_name;

    /**
     * construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->model_name = trans('app.model.config');
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewGeneralSetting()
    {
        // $files = \Illuminate\Support\Facades\Storage::disk('google')->allFiles();
        $shop = Shop::findOrFail(Auth::user()->merchantId());

        $bank_type = [
            'Bank Mandiri' => 'Bank Mandiri',
            'Bank Rakyat Indonesia (BRI)' => 'Bank Rakyat Indonesia (BRI)',
            'Bank Central Asia (BCA)' => 'Bank Central Asia (BCA)',
            'Bank Negara Indonesia (BNI)' => 'Bank Negara Indonesia (BNI)',
            'Bank Tabungan Negara (BTN)' => 'Bank Tabungan Negara (BTN)',
            'Bank CIMB Niaga' => 'Bank CIMB Niaga',
            'Bank Danamon' => 'Bank Danamon',
            'Bank Permata' => 'Bank Permata',
            'Bank Syariah Indonesia (BSI)' => 'Bank Syariah Indonesia (BSI)',
            'Bank Mega' => 'Bank Mega',
            'Bank Sinarmas' => 'Bank Sinarmas',
            'Bank Muamalat' => 'Bank Muamalat',
            'Bank Bukopin' => 'Bank Bukopin',
            'Bank Maybank Indonesia' => 'Bank Maybank Indonesia',
            'Bank OCBC NISP' => 'Bank OCBC NISP',
            'Bank Panin' => 'Bank Panin',
            'Bank UOB Indonesia' => 'Bank UOB Indonesia',
            'Bank HSBC Indonesia' => 'Bank HSBC Indonesia',
            'Bank JTrust' => 'Bank JTrust',
            'Bank QNB Indonesia' => 'Bank QNB Indonesia',
            'Bank Commonwealth' => 'Bank Commonwealth',
            'Bank Woori Saudara' => 'Bank Woori Saudara',
            'Bank DBS Indonesia' => 'Bank DBS Indonesia',
            'Bank Mayapada' => 'Bank Mayapada',
            'Bank Artha Graha Internasional' => 'Bank Artha Graha Internasional',
            'Bank BTPN' => 'Bank BTPN',
            'Bank Shinhan Indonesia' => 'Bank Shinhan Indonesia',
            'Bank Maspion' => 'Bank Maspion',
            'Bank Ganesha' => 'Bank Ganesha',
            'Bank Mestika' => 'Bank Mestika',
            'Bank Index Selindo' => 'Bank Index Selindo',
            'Bank KEB Hana' => 'Bank KEB Hana',
            'Bank Victoria International' => 'Bank Victoria International',
            'Bank Jago' => 'Bank Jago',
            'Bank MNC Internasional' => 'Bank MNC Internasional',
            'Bank BJB' => 'Bank BJB',
            'Bank DKI' => 'Bank DKI',
            'Bank Jateng' => 'Bank Jateng',
            'Bank Jatim' => 'Bank Jatim',
            'Bank Aceh Syariah' => 'Bank Aceh Syariah',
            'Bank Sumut' => 'Bank Sumut',
            'Bank Nagari' => 'Bank Nagari',
            'Bank Riau Kepri' => 'Bank Riau Kepri',
            'Bank Sumsel Babel' => 'Bank Sumsel Babel',
            'Bank Lampung' => 'Bank Lampung',
            'Bank Kalsel' => 'Bank Kalsel',
            'Bank Kalbar' => 'Bank Kalbar',
            'Bank Kaltimtara' => 'Bank Kaltimtara',
            'Bank Kalteng' => 'Bank Kalteng',
            'Bank Sulselbar' => 'Bank Sulselbar',
            'Bank SulutGo' => 'Bank SulutGo',
            'Bank NTB Syariah' => 'Bank NTB Syariah',
            'Bank NTT' => 'Bank NTT',
            'Bank Maluku Malut' => 'Bank Maluku Malut',
            'Bank Papua' => 'Bank Papua',
            'Bank Bengkulu' => 'Bank Bengkulu',
            'Bank Sulteng' => 'Bank Sulteng',
            'Bank Sultra' => 'Bank Sultra',
        ];
        

        $shop_config = Config::find(Auth::user()->merchantId(), [
            'ac_holder_name',
            'ac_number',
            'ac_iban',
            'ac_swift_bic_code',
            'ac_routing_number',
            'ac_type',
            'ac_bank_address',
        ]);

        return view('admin.config.general', compact('shop', 'shop_config', 'bank_type'));
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view()
    {
        $config = Config::findOrFail(Auth::user()->merchantId());

        $this->authorize('view', $config); // Check permission

        return view('admin.config.index', compact('config'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateBasicConfig(UpdateBasicConfigRequest $request, $id)
    {
        $config = Config::findOrFail($id);

        if (config('app.demo') == true && $config->shop_id <= config('system.demo.shops', 2)) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $this->authorize('update', $config); // Check permission

        $config->shop->update($request->all());

        event(new ShopUpdated($config->shop));

        if ($request->hasFile('logo') || ($request->input('delete_logo') == 1)) {
            $config->shop->deleteLogo();
        }

        if ($request->hasFile('logo')) {
            $config->shop->saveImage($request->file('logo'), 'logo');
        }

        if ($request->hasFile('cover_image') || ($request->input('delete_cover_image') == 1)) {
            $config->shop->deleteCoverImage();
        }

        if ($request->hasFile('cover_image')) {
            $config->shop->saveImage($request->file('cover_image'), 'cover');
        }

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Update configurations
     *
     * @param UpdateConfigRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateConfig(UpdateConfigRequest $request, $id)
    {
        if (config('app.demo') == true && $id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $config = Config::findOrFail($id);

        $this->authorize('update', $config); // Check permission

        if ($config->update($request->all())) {
            event(new ConfigUpdated($config->shop, Auth::user()));

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(MerchantVerifyRequest $request)
    {
        $config = Config::findOrFail(Auth::user()->merchantId());

        return view('admin.config.verify', compact('config'));
    }

    /**
     * Display the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveVerificationData(MerchantVerifyRequest $request)
    {
        $config = Config::findOrFail(Auth::user()->merchantId());

        if ($request->hasFile('documents')) {
            $config->saveAttachments($request->file('documents'));
        }

        $config->update(['pending_verification' => 1]);

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Toggle Maintenance Mode of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $node
     * @return \Illuminate\Http\Response
     */
    public function toggleNotification(Request $request, $node)
    {
        $config = Config::findOrFail($request->user()->merchantId());

        if (config('app.demo') == true && $config->shop_id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $this->authorize('update', $config); // Check permission

        $config->$node = !$config->$node;

        if ($config->save()) {
            event(new ConfigUpdated($config->shop, Auth::user()));

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Toggle Maintenance Mode of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleMaintenanceMode(ToggleMaintenanceModeRequest $request, $id)
    {
        if (config('app.demo') == true && $id <= config('system.demo.shops', 2)) {
            return response('error', 444);
        }

        $config = Config::findOrFail($id);

        $this->authorize('update', $config); // Check permission

        $config->maintenance_mode = !$config->maintenance_mode;

        if ($config->save()) {
            if ($config->maintenance_mode) {
                event(new DownForMaintainace($config->shop));
            } else {
                event(new ShopIsLive($config->shop));
            }

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Puputalet the edit form for bank details
     *
     * @param int $shopId
     * @return \Illuminate\Http\Response
     */
    public function editBankInfo($shopId)
    {
        $bankInfo = Config::find($shopId, [
            'shop_id',
            'ac_holder_name',
            'ac_number',
            'ac_type',
            'ac_routing_number',
            'ac_swift_bic_code',
            'ac_iban',
            'ac_bank_address'
        ]);

        return view('admin.config._update_bank_info', compact('bankInfo'));
    }

    /**
     * Update the bank information
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateBankInfo(Request $request)
    {
        Config::find(Auth::user()->shop_id)->update($request->all());

        return back()->with('success', trans('messages.created', ['model' => $this->model_name]));
    }
}
