<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Manufacturer;
use App\Models\System;
use App\Helpers\ListHelper;
use Illuminate\Support\Str;
use App\Events\Shop\ShopCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use App\Jobs\CreateShopForMerchant;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\SubscribeShopToNewPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Jobs\CreateCustomerFromMerchant;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Requests\Validations\RegisterMerchantRequest;
use App\Notifications\Auth\SendVerificationEmail as EmailVerificationNotification;
use App\Notifications\SuperAdmin\VerdorRegistered as VerdorRegisteredNotification;
use Carbon\Carbon;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::DASHBOARD;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('verify');
    }

    /**
     * Show the application registration form.
     *
     * @param  string  $plan subscription plan
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm($plan = null)
    {
        $countries = ListHelper::countries(); 

        return view('auth.register', compact('countries', 'plan'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterMerchantRequest $request)
    {
        // Start transaction!
        DB::beginTransaction();

        // When otp-login plugin active
        if (is_incevio_package_loaded('otp-login')) {
            $phone = $request->input('phone');

            send_otp_code($phone, 'vendor.register');

            try {
                $merchant = $this->create($request->all());

                // Dispatching Shop create job
                CreateShopForMerchant::dispatch($merchant, $request->all());

                //Auth::guard()->login($merchant);

                if (is_subscription_enabled()) {
                    SubscribeShopToNewPlan::dispatch($merchant, $request->input('plan'));
                }

                if (!customer_can_register()) {
                    // Dispatching customer create job
                    CreateCustomerFromMerchant::dispatch($merchant);
                }
            } catch (\Exception $e) {
                // rollback the transaction and log the error
                DB::rollback();
                Log::error('Vendor Registration Failed: ' . $e->getMessage());

                // Set error messages:
                $error = new MessageBag();
                $error->add('errors', trans('responses.vendor_config_failed'));

                return redirect()->route('vendor.register')->withErrors($error)->withInput();
            }

            // Everything is fine. Now commit the transaction
            DB::commit();

            // Trigger after registration events
            $this->triggerAfterEvents($merchant);

            // Send notification to Admin
            if (config('system_settings.notify_when_vendor_registered')) {
                $system = System::orderBy('id', 'asc')->first();
                $system->superAdmin()->notify(new VerdorRegisteredNotification($merchant));
            }

            return redirect()->route('vendor.phoneverification.notice')->with(['phone_number' => $phone]);
        }

        try {
            $merchant = $this->create($request->all());

            if($request['shop_name']) {

                $merchant['role_id'] = 16;
                $merchant['business_name'] = $request->input('shop_name');
                $merchant['country_id'] = $request->input('country_id');
                 // Create a new manufacturer record
                Manufacturer::create([
                    'name' => $request->input('shop_name'),
                    'slug' =>  Str::slug($request->input('name')),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'country_id' => $request->input('country_id'),
                    'active' => '1',
                    'created_at' => Carbon::today(),
                    'manufacture_pic_name' => $request->input('name'),
                    'manufacture_pic_email' => $request->input('personal_email'),
                    'manufacture_pic_phone' => $request->input('personal_phone'),
                ]);
            } 

            if (!customer_can_register()) {
                // Dispatching customer create job
                CreateCustomerFromMerchant::dispatch($merchant);
            }
            

            // Dispatching Shop create job
            CreateShopForMerchant::dispatch($merchant, $request->all());

            Auth::guard()->login($merchant);

            if (is_subscription_enabled()) {
                SubscribeShopToNewPlan::dispatch($merchant, $request->input('plan'));
            }
        } catch (\Exception $e) {
            // rollback the transaction and log the error
            DB::rollback();
            Log::error('Vendor Registration Failed: ' . $e->getMessage());

            // Set error messages:
            $error = new MessageBag();
            $error->add('errors', trans('responses.vendor_config_failed'));

            return redirect()->route('vendor.register')->withErrors($error)->withInput();
        }

        // Everything is fine. Now commit the transaction
        DB::commit();

        // Trigger after registration events
        $this->triggerAfterEvents($merchant);

        // Send notification to Admin
        if (config('system_settings.notify_when_vendor_registered')) {
            $system = System::orderBy('id', 'asc')->first();
            $system->superAdmin()->notify(new VerdorRegisteredNotification($merchant));
        }

        return $this->registered($request, $merchant) ?: redirect($this->redirectPath());
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $request_data)
    {
        $data = [
            'name' => $request_data['name'],
            'email' => $request_data['email'],
            'password' => bcrypt($request_data['password']),
            'verification_token' => Str::random(40),
        ];

        if (is_incevio_package_loaded('otp-login')) {
            $data['phone'] = $request_data['phone'];
        }

        return User::create($data);
    }

    /**
     * Trigger some events after a valid registration.
     *
     * @param  User  $merchant
     * @return void
     */
    protected function triggerAfterEvents(User $merchant)
    {
        // Trigger the systems default event
        event(new Registered($merchant));

        // Trigger shop created event
        event(new ShopCreated($merchant->owns));

        // Send email verification notification
        $merchant->notify(new EmailVerificationNotification($merchant));
    }

    /**
     * Verify the User the given token.
     *
     * @param  string|null  $token
     * @return \Illuminate\Http\Response
     */
    public function verify($token = null)
    {
        if (!$token) {
            $user = Auth::user();

            $user->verification_token = Str::random(40);

            if ($user->save()) {
                $user->notify(new EmailVerificationNotification($user));

                return redirect()->back()->with('success', trans('auth.verification_link_sent'));
            }

            return redirect()->back()->with('success', trans('auth.verification_link_sent'));
        }

        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('admin.admin.dashboard')
                ->with('success', trans('auth.invalid_token'));
        }

        $user->verification_token = null;

        if ($user->save()) {
            return redirect()->route('admin.admin.dashboard')
                ->with('success', trans('auth.verification_successful'));
        }

        return redirect()->route('admin.admin.dashboard')->with('error', trans('auth.verification_failed'));
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered($request, $user)
    {
        //
    }
}
