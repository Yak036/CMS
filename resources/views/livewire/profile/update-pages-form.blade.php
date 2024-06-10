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


    public string $facebook = '';
    public string $instagram = '';
    public string $tiktok = '';
    public string $x = '';
    public string $personalPage = '';
    public bool $encontrado = false;
    public $filename;
    /**
     * Mount the component.
     */
    public function mount(): void
    {

        $this->facebook = Auth::user()->facebook;
        $this->instagram = Auth::user()->instagram;
        $this->tiktok = Auth::user()->tiktok;
        $this->x = Auth::user()->x;
        $this->personalPage = Auth::user()->personalPage;




    /**
     * Send an email verification notification to the current user.
     */
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
      /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            
            'facebook' => ['string', 'max:250', new FacebookUrl],
            'instagram' => [ 'string', 'max:250', new InstagramUrl],
            'tiktok' => [ 'string', 'max:250', new TikTokUrl],
            'x' => [ 'string', 'max:250', new XUrl],
            'personalPage' => ['string', 'max:250', new https],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

} ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Paginas del usuario') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Actualiza tus redes sociales") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <!-- facebook -->
      <div>
        <x-input-label for="facebook" :value="__('Facebook (No obligatorio)')" />
        <x-text-input wire:model="facebook" id="facebook" class="block mt-1 w-full" type="text" name="facebook" 
          autofocus autocomplete="facebook" />
        <x-input-error :messages="$errors->get('facebook')" class="mt-2" />
      </div>

       <!-- instagram -->
    <div>
      <x-input-label for="instagram" :value="__('Instragram (No obligatorio)')" />
      <x-text-input wire:model="instagram" id="instagram" class="block mt-1 w-full" type="text" name="instagram"
        autofocus autocomplete="instagram" />
      <x-input-error :messages="$errors->get('instagram')" class="mt-2" />
    </div>

    <!-- tiktok -->
    <div>
      <x-input-label for="tiktok" :value="__('TikTok (No obligatorio)')" />
      <x-text-input wire:model="tiktok" id="tiktok" class="block mt-1 w-full" type="text" name="tiktok" 
        autofocus autocomplete="tiktok" />
      <x-input-error :messages="$errors->get('tiktok')" class="mt-2" />
    </div>

    <!-- x -->
    <div>
      <x-input-label for="x" :value="__('X (No obligatorio)')" />
      <x-text-input wire:model="x" id="x" class="block mt-1 w-full" type="text" name="x"  autofocus
        autocomplete="x" />
      <x-input-error :messages="$errors->get('x')" class="mt-2" />
    </div>

    <!-- personalPage -->
    <div>
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

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Guardado.') }}
            </x-action-message>
        </div>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/registerAlerts.js') }}"></script>
