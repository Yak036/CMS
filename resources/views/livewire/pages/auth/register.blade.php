<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Livewire\Forms\PaisesController;
use Illuminate\Support\Facades\Validator;
use App\Rules\FacebookUrl;
use App\Rules\InstagramUrl;
use App\Rules\TikTokUrl;
use App\Rules\XUrl;
use App\Rules\https;
use Spatie\Browsershot\Browsershot;
use Grabzit\GrabzItClient;
use Grabzit\GrabzItImageOptions;
new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $surname = '';
    public string $license = '';
    public string $email = '';
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
    public string $password = '';
    public string $password_confirmation = '';
    public bool $encontrado = false;
    public $token, $countries, $states, $cities, $filename, $localUrl;

    public function mount() {
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
        
    }

    public function mostrarEstados(){
      $this->nacionality = $this->country;
      $this->state = '';
      $this->states = [];
      $this->cities = [];
      $this->city = '';
        if (!empty($this->country)) {
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

    //TODO: Tomar captura
    public function Screenshot(){
    try {
      $validated = $this->validate([
            'personalPage' => ['required','string', 'max:250', new https],
        ]);


      $grabzIt = new \GrabzIt\GrabzItClient("MmE2MjUwYmVlM2I1NDQ3ODgyNWY3MGE4YjA3MTBmZWU", "YomRrV33BQQCBOeUCkJJ5cT_wneAHzUoK7ialDYCtro");
      $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      

      $this->filename = substr(str_shuffle(str_repeat($caracteres, 6)), 0, 6);;
      $options = new \GrabzIt\GrabzItImageOptions();
      $options->setFormat("png");

      $grabzIt->URLToImage("".$this->personalPage, $options);
      //Then call the Save or SaveTo method
      $grabzIt->SaveTo(public_path('media/'.$this->filename.'.png'));

      $this->encontrado = true;
      Flasher::addSuccess('Se encontro su pagina');
    } catch (\Exception $e) {
      Flasher::addError('Hubo un error al ubicar su pagina', [
        'timer' => 3000, // El tiempo en milisegundos que la alerta debe permanecer visible
        'overlay' => false, // Si deseas o no un fondo oscuro detrás de la alerta
        // Puedes agregar más opciones aquí según necesites
    ]);

}
      

      
      
    }


    public function register(): void
    {


  

        $validated = $this->validate([
            'name' => ['required', 'alpha', 'max:255'],
            'surname' => ['required', 'alpha', 'max:255'],
            'license' => ['required', 'string', 'max:30','unique:'.User::class],
            'country' => ['required'],
            'state' => [ 'required','string', 'max:100'],
            'city' => [ 'string', 'max:100'],
            'adress' => [ 'string', 'min:10', 'max:100'],
            'nacionality' => [ 'alpha', 'max:100'],
            'dateBirth' => ['required', 'date', 'before:-15 years', 'after: 70 years'],
            'facebook' => ['string', 'max:250', new FacebookUrl],
            'instagram' => [ 'string', 'max:250', new InstagramUrl],
            'tiktok' => [ 'string', 'max:250', new TikTokUrl],
            'x' => [ 'string', 'max:250', new XUrl],
            'personalPage' => ['string', 'max:250', new https],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(RouteServiceProvider::HOME, navigate: true);
    }


}


?>

<div class="">
  @flasher_render

  <form wire:submit="register" class="flex flex-wrap">

    <!-- Name -->
    <div class="w-1/2 pr-2">
      <x-input-label for="name" :value="__('Nombre')" />
      <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus
        autocomplete="name" />
      <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <!-- Surname -->
    <div class="w-1/2 pr-2">
      <x-input-label for="surname" :value="__('Apellido')" />
      <x-text-input wire:model="surname" id="surname" class="block mt-1 w-full" type="text" name="surname" required
        autofocus autocomplete="surname" />
      <x-input-error :messages="$errors->get('surname')" class="mt-2" />
    </div>

    <!-- License -->
    <div class="w-1/2 pr-2">
      <x-input-label for="license" :value="__('Cedula')" />
      <x-text-input wire:model="license" id="license" class="block mt-1 w-full" type="text" name="license" required
        autofocus autocomplete="license" />
      <x-input-error :messages="$errors->get('license')" class="mt-2" />
    </div>

    <!-- Email  -->
    <div class="w-1/2 pr-2">
      <x-input-label for="email" :value="__('Correo electronico')" />
      <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required
        autocomplete="username" />
      <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
    <!-- Seleccionar un pais -->
    @if (count($countries) > 1)
    <div class="w-full">
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
    <div class="w-full">
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
    <div class="w-full">
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
    <div class="w-full">
      <x-input-label for="adress" :value="__('Direccion')" />
      <x-text-input wire:model="adress" id="adress" class="block mt-1 w-full" type="text" name="adress" required
        autocomplete="adress" />
      <x-input-error :messages="$errors->get('adress')" class="mt-2" />
    </div>

    <!-- nacionalidad -->
    <div class="w-1/2 pr-2">
      <x-input-label for="nacionality" :value="__('Nacionalidad')" />
      <x-text-input wire:model="nacionality" id="nacionality" class="block mt-1 w-full bg-gray-200 cursor-not-allowed" type="text" name="nacionality"
        required autocomplete="nacionality" />
      <x-input-error :messages="$errors->get('nacionality')" class="mt-2" />
    </div>

    <!-- Fecha de nacimiento -->
    <div class="w-1/2 pr-2">
      <x-input-label for="dateBirth" :value="__('Fecha de nacimiento')" />
      <x-text-input wire:model="dateBirth" id="dateBirth" class="block mt-1 w-full" type="date" name="dateBirth"
        required autocomplete="dateBirth" />
      <x-input-error :messages="$errors->get('dateBirth')" class="mt-2" />
    </div>

    <!-- facebook -->
    <div class="w-full">
      <x-input-label for="facebook" :value="__('Facebook (No obligatorio)')" />
      <x-text-input wire:model="facebook" id="facebook" class="block mt-1 w-full" type="text" name="facebook" 
        autofocus autocomplete="facebook" />
      <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
    </div>

    <!-- instagram -->
    <div class="w-full">
      <x-input-label for="instagram" :value="__('Instragram (No obligatorio)')" />
      <x-text-input wire:model="instagram" id="instagram" class="block mt-1 w-full" type="text" name="instagram"
        autofocus autocomplete="instagram" />
      <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
    </div>

    <!-- tiktok -->
    <div class="w-full">
      <x-input-label for="tiktok" :value="__('TikTok (No obligatorio)')" />
      <x-text-input wire:model="tiktok" id="tiktok" class="block mt-1 w-full" type="text" name="tiktok" 
        autofocus autocomplete="tiktok" />
      <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
    </div>

    <!-- x -->
    <div class="w-full">
      <x-input-label for="x" :value="__('X (No obligatorio)')" />
      <x-text-input wire:model="x" id="x" class="block mt-1 w-full" type="text" name="x"  autofocus
        autocomplete="x" />
      <x-input-error :messages="$errors->get('x')" class="mt-2" />
    </div>

    <!-- personalPage -->
    <div class="w-full">
      <x-input-label for="personalPage" :value="__('Pagina personal (No obligatorio)')" />
      <x-text-input wire:model="personalPage" id="personalPage" class="block mt-1 w-full" type="text"
        name="personalPage" autofocus autocomplete="personalPage" />
      <x-input-error :messages="$errors->get('personalPage')" class="mt-2" />
        <button wire:click="Screenshot" type="button" class="personalPageButtom transition duration-300 ease-in-out bg-black hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
          Confirmar página
        </button>
        @if ($encontrado)
          <img src="{{ asset('media/'.$filename.'.png') }}" >
        @endif
        

    </div>


    <!-- Password -->
    <div class="w-1/2 pr-2">
      <x-input-label for="password" :value="__('Password')" />

      <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" name="password"
        required autocomplete="new-password" />

      <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Confirm Password -->
    <div class="w-1/2 pr-2">
      <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

      <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
        type="password" name="password_confirmation" required autocomplete="new-password" />

      <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
      
        
    </div>

    <div class="flex items-center justify-end mt-4">
      <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        href="{{ route('login') }}" wire:navigate>
        {{ __('¿Ya tienes cuenta?') }}
      </a>

      <x-primary-button>
        {{ __('Registrarse') }}
      </x-primary-button>
    </div>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/registerAlerts.js') }}"></script>
