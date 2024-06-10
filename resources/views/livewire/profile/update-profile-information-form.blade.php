<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Rules\FacebookUrl;
use App\Rules\InstagramUrl;
use App\Rules\TikTokUrl;
use App\Rules\XUrl;
use App\Rules\https;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\PaisesController;
use Illuminate\Support\Facades\Validator;
use Spatie\Browsershot\Browsershot;
use Grabzit\GrabzItClient;
use Grabzit\GrabzItImageOptions;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $surname = '';
    public string $license = '';
    public string $country = '';
    public string $state = '';
    public string $city = '';
    public string $adress = '';
    public string $nacionality = '';
    public string $dateBirth = '';


    public string $facebook = '';
    public string $instagram = '';
    public string $tiktok = '';
    public string $x = '';
    public string $personalPage = '';

    public $token, $countries, $states, $cities, $filename, $localUrl;
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->surname = Auth::user()->surname;
        $this->license = Auth::user()->license;
        $this->country = Auth::user()->country;
        $this->state = Auth::user()->state;
        $this->city = Auth::user()->city;
        $this->adress = Auth::user()->adress;
        $this->nacionality = Auth::user()->nacionality;
        $this->dateBirth = Auth::user()->dateBirth;


        $this->token = '';
        $this->states= [];
        $this->cities= [];
        $this->countries = [];
        
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "api-token" => env("API_TOKEN"),
            "user-email" => env("USER_EMAIL")
        ])->get('https://www.universal-tutorial.com/api/getaccesstoken');

        $this->token = $response->json("auth_token");

        $countriesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/countries');

        $this->countries = $countriesResponse->json();

        if (!empty($this->country)) {
            $statesResponse = Http::withHeaders([
                "Authorization"=> "Bearer ". $this->token,
                "Accept"=> "application/json"
            ])->get('https://www.universal-tutorial.com/api/states/'.$this->country);
            
            $this->states = $statesResponse->json();
        }
        if (!empty($this->state)) {
        $citiesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/cities/'.$this->state);
        
        $this->cities = $citiesResponse->json();
        }
    }

    public function mostrarEstados(){
        if (!empty($this->country)) {
            $this->nacionality = $this->country;
            $this->state = '';
            $this->states = [];
            $this->cities = [];
            $this->city = '';
            $statesResponse = Http::withHeaders([
                "Authorization"=> "Bearer ". $this->token,
                "Accept"=> "application/json"
            ])->get('https://www.universal-tutorial.com/api/states/'.$this->country);
            
            $this->states = $statesResponse->json();
        }
    }
    public function mostrarCiudades(){
        $citiesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/cities/'.$this->state);
        
        $this->cities = $citiesResponse->json();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'surname' => ['required', 'alpha', 'max:255'],
            'license' => ['required', 'string', 'max:30',Rule::unique(User::class)->ignore($user->id)],
            'country' => ['required'],
            'state' => [ 'required','string', 'max:100'],
            'city' => [ 'string', 'max:100'],
            'adress' => [ 'string', 'min:10', 'max:100'],
            'nacionality' => [ 'alpha', 'max:100'],
            'dateBirth' => ['required', 'max:100'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: RouteServiceProvider::HOME);

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Informacion del usuario') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Actualiza tu informacion personal") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

            <!-- Surname -->
    <div>
        <x-input-label for="surname" :value="__('Apellido')" />
        <x-text-input wire:model="surname" id="surname" class="block mt-1 w-full" type="text" name="surname" required
        autofocus autocomplete="surname" />
        <x-input-error :messages="$errors->get('surname')" class="mt-2" />
    </div>

    <!-- License -->
    <div>
        <x-input-label for="license" :value="__('Cedula')" />
        <x-text-input wire:model="license" id="license" class="block mt-1 w-full" type="text" name="license" required
        autofocus autocomplete="license" />
        <x-input-error :messages="$errors->get('license')" class="mt-2" />
    </div>

    <div>
      <x-input-label for="email" :value="__('Correo electronico')" />
      <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
      <x-input-error class="mt-2" :messages="$errors->get('email')" />

      @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
          <div>
              <p class="text-sm mt-2 text-gray-800">
                  {{ __('Your email address is unverified.') }}

                  <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      {{ __('Click here to re-send the verification email.') }}
                  </button>
              </p>

              @if (session('status') === 'verification-link-sent')
                  <p class="mt-2 font-medium text-sm text-green-600">
                      {{ __('A new verification link has been sent to your email address.') }}
                  </p>
              @endif
          </div>
      @endif
  </div>

    @if (count($countries) > 1)
    <div class="mt-4">
      <x-input-label for="country" :value="__('Selecciona un pais')" />
      <select wire:change="mostrarEstados" wire:model="country" class="input gr" name="country" id="country">
        <option value=""> . . .</option>
        @foreach ($countries as $countri)
          <option value="{{ $countri['country_name'] }}">{{ $countri["country_name"] }}</option>
          @endforeach
      </select>
      <x-input-error :messages="$errors->get('country')" class="mt-2" />
    </div>
    @endif

    <!-- Estado -->
    @if (count($states) > 1)
    <div class="mt-4">
      <x-input-label for="state" :value="__('Selecciona un estado')" />
      <select wire:change="mostrarCiudades" wire:model="state" class="input gr" name="state" id="state">
        <option value=""> . . .</option>
        @foreach ($states as $state)
        <option value="{{ $state['state_name'] }}">{{ $state["state_name"] }}</option>
        @endforeach
      </select>
      <x-input-error :messages="$errors->get('state')" class="mt-2" />
    </div>
    @endif

    <!-- Ciudad -->
    @if (count($cities) > 1)
    <div class="mt-4">
        <x-input-label for="city" :value="__('Selecciona una ciudad')" />
        <select wire:model="city" class="input gr" name="city" id="city">
          <option value=""> . . .</option>
            @foreach ($cities as $city)
              <option value="{{ $city['city_name'] }}">{{ $city["city_name"] }}</option>
            @endforeach
      
        </select>
      <x-input-error :messages="$errors->get('city')" class="mt-2" />
    </div>
    @endif

    <!-- adress -->
    <div class="mt-4">
      <x-input-label for="adress" :value="__('Direccion')" />
      <x-text-input wire:model="adress" id="adress" class="block mt-1 w-full" type="text" name="adress" required
        autocomplete="adress" />
      <x-input-error :messages="$errors->get('adress')" class="mt-2" />
    </div>

    <!-- nacionalidad -->
    <div class="mt-4">
      <x-input-label for="nacionality" :value="__('Nacionalidad')" />
      <x-text-input wire:model="nacionality" id="nacionality" class="block mt-1 w-full bg-gray-200 cursor-not-allowed" type="text" name="nacionality"
        required autocomplete="nacionality" />
      <x-input-error :messages="$errors->get('nacionality')" class="mt-2" />
    </div>

    <!-- Fecha de nacimiento -->
    <div class="mt-4">
      <x-input-label for="dateBirth" :value="__('Fecha de nacimiento')" />
      <x-text-input wire:model="dateBirth" id="dateBirth" class="block mt-1 w-full" type="date" name="dateBirth"
        required autocomplete="dateBirth" />
      <x-input-error :messages="$errors->get('dateBirth')" class="mt-2" />
    </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Guardado.') }}
            </x-action-message>
        </div>
    </form>
</section>
