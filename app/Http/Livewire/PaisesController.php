<?php

namespace App\Http\Livewire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Component;



class PaisesController extends Component{

    public $token, $countries, $states, $cities, $search_state, $search_city;
    //* Se crea un constructo para buscar el Token q usaran los demas metodos
    public function mount() {
        $this->token = '';
        $this->states= [];
        $this->search_state = [];
        
        $response = Http::withHeaders([
            "Accept" => "application/json",
            "api-token" => env("API_TOKEN"),
            "user-email" => env("USER_EMAIL")
        ])->get('https://www.universal-tutorial.com/api/getaccesstoken');

        $this->token = $response->json("auth_token");
        
        dd($this->token);

    }

    public function mostrarPaises(){
        
        $countriesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/countries');

        $this->countries = $countriesResponse->json();
        
        //return view('auth/register', ['paisesController' => $this]);
        
        //return view('auth/register')->with('countries', $this->countries);
        
    }
    public function mostrarEstados(){
        if (!empty($this->search_state)) {
            dd($this->search_state);
            $statesResponse = Http::withHeaders([
                "Authorization"=> "Bearer ". $this->token,
                "Accept"=> "application/json"
            ])->get('https://www.universal-tutorial.com/api/states/'.$this->search_state);
            
            $this->states = $statesResponse->json();
            // return view('auth/register')->with('states', $this->states);
        }
    }
    
    public function mostrarCiudades(){
        $citiesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/cities/'.$this->search_city);
        
    }
    
    public function render(){
        return view('livewire.pages.auth.register');
    }
}