<?php

namespace App\Http\Controllers\Admin;

use App\Models\System;
use App\Common\Authorizable;
use App\Jobs\ClearConfigCache;
use App\Events\System\SystemIsLive;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ResetDbAndImportDemoData;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Events\System\SystemInfoUpdated;
use App\Events\System\DownForMaintainace;
use Symfony\Component\Console\Output\BufferedOutput;
use App\Http\Requests\Validations\SaveEnvFileRequest;
use App\Http\Requests\Validations\UpdateSystemRequest;
use App\Http\Requests\Validations\ResetDatabaseRequest;
use App\Http\Requests\Validations\UpdateBasicSystemConfigRequest;

class SystemController extends Controller
{
    use Authorizable;

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
    public function view()
    {
        return view('admin.system.general');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBasicSystemConfigRequest $request)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        $system = System::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        $system->update($request->except('image', 'delete_image'));

        if ($request->hasFile('icon')) {
            $system->updateImage($request->file('icon'), 'icon');
            Cache::forget('favicon_img');
        }

        if ($request->hasFile('logo')) {
            $system->updateImage($request->file('logo'), 'logo');

            // Flush all logo sizes
            foreach (config('image.sizes') as $size => $value) {
                Cache::forget('system_logo_img_' . $size);
            }
        }

        if ($request->hasFile('trust_badge')) {
            $system->updateImage($request->file('trust_badge'), 'feature');
            Cache::forget('trust_badge_img');
        }

        event(new SystemInfoUpdated($system));

        return back()->with('success', trans('messages.updated', ['model' => $this->model_name]));
    }

    /**
     * Show the .env file editor.
     *
     * @return \Illuminate\Http\Response
     */
    public function modifyEnvFile(UpdateSystemRequest $request)
    {
        $envContents = file_get_contents(base_path('.env'));

        return view('admin.system.modify_env_file', compact('envContents'));
    }

    /**
     * Reset the database and import demo data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveEnvFile(SaveEnvFileRequest $request)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {
            file_put_contents(base_path('.env'), $request->env);
        } catch (\Exception $e) {
            Log::error('.env modification failed: ' . $e->getMessage());

            // add your error messages:
            $error = new \Illuminate\Support\MessageBag();
            $error->add('errors', trans('responses.failed'));

            return back()->withErrors($error);
        }

        $system = System::orderBy('id', 'asc')->first();

        event(new SystemInfoUpdated($system));

        ClearConfigCache::dispatch();

        return back()->with('success', trans('messages.env_saved'));
    }

    /**
     * Show confirmation page to import demo contents.
     *
     * @return \Illuminate\Http\Response
     */
    public function importDemoContents()
    {
        return view('admin.system.import_demo_contents');
    }

    /**
     * Reset the database and import demo data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resetDatabase(ResetDatabaseRequest $request)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {
            ResetDbAndImportDemoData::dispatch('reset');
        } catch (\Exception $e) {
            Log::error('Database Reset Failed: ' . $e->getMessage());

            // add your error messages:
            $error = new \Illuminate\Support\MessageBag();
            $error->add('errors', trans('responses.failed'));

            return back()->withErrors($error);
        }

        // Clear the cache config
        Cache::flush();

        return back()->with('success', trans('messages.demo_data_imported'));
    }

    /**
     * Show confirmation page to import demo contents.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearDemoConfirmation()
    {
        return view('admin.system.clear_demo_contents');
    }

    /**
     * Reset the database and import demo data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearDemoContents(ResetDatabaseRequest $request)
    {
        if (config('app.demo') == true) {
            return back()->with('warning', trans('messages.demo_restriction'));
        }

        try {
            ResetDbAndImportDemoData::dispatch('clean');
        } catch (\Exception $e) {
            Log::error('Database Cleanup Failed: ' . $e->getMessage());

            // add your error messages:
            $error = new \Illuminate\Support\MessageBag();
            $error->add('errors', trans('responses.failed'));

            return back()->withErrors($error);
        }

        // Clear the cache config
        Cache::flush();

        return back()->with('success', trans('messages.demo_data_cleared'));
    }

    /**
     * Toggle Maintenance Mode of the given id, Its uses the ajax middleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggleMaintenanceMode(UpdateSystemRequest $request)
    {
        if (config('app.demo') == true) {
            return response('error', 444);
        }

        $system = System::orderBy('id', 'asc')->first();

        $this->authorize('update', $system); // Check permission

        $system->maintenance_mode = !$system->maintenance_mode;

        if ($system->save()) {
            if ($system->maintenance_mode) {
                event(new DownForMaintainace($system));
            } else {
                event(new SystemIsLive($system));
            }

            // Clear the cache config
            Cache::forget('system_settings');

            return response('success', 200);
        }

        return response('error', 405);
    }

    /**
     * Take a database backup snapshot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function backup(UpdateSystemRequest $request)
    {
        $output = '';

        try {
            $outputLog = new BufferedOutput;

            Log::info('Backup cleanup called! ');

            // Remove all backups older than specified number of days in config.
            Artisan::queue('backup:clean', [], $outputLog);

            Log::info(Artisan::output());

            Log::info('Database Backup command called!');

            Artisan::queue('backup:run', ['--only-db' => true], $outputLog);

            Log::info(Artisan::output());
        } catch (Exception $e) {
            Log::error('Backup failed! ' . $outputLog);

            return back()->withErrors('Backup failed: ' . $output);
        }

        return back()->with('success', trans('messages.backup_done'));
    }

    /**
     * Uninstall the application license so that it can be reinstall on new location.
     * Script stops working immediately.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uninstallLicense(UpdateSystemRequest $request)
    {
        if ($request->isMethod('get')) {
            return view('admin.system.uninstall');
        }

        if ($request->do_action != 'UNINSTALL') {
            return back()->withErrors(trans('validation.do_action_invalid'));
        }

        if (Hash::check($request->password, $request->user()->password)) {
            try {
                // $license_notifications_array = incevioUninstallLicense(getMysqliConnection());

                // if ($license_notifications_array['notification_case'] != 'notification_license_ok') {
                //     throw new \Exception('License uninstallation failed: ' . $license_notifications_array['notification_text']);
                // }

                Schema::dropIfExists('mixdata');

                // Delete the installed file
                unlink(storage_path('installed'));
            } catch (\Exception $e) {
                $error_msg = $e->getMessage();

                Log::error('License uninstallation failed: ' . $error_msg);

                // Add your error messages
                $error = new \Illuminate\Support\MessageBag();
                $error->add('errors', $error_msg);

                return back()->withErrors($error);
            }

            // $MYSQLI_LINK = getMysqliConnection();
            // mysqli_query($MYSQLI_LINK, "SET FOREIGN_KEY_CHECKS = 0");
            // mysqli_query($MYSQLI_LINK, "DROP TABLE ".APL_DATABASE_TABLE);

            return back()->with('success', trans('messages.license_uninstalled'));
        }

        abort(403, 'Unauthorized action.');
    }
}
