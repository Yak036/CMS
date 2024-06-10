<?php

namespace App\Livewire\Forms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Component;



class PaisesController extends Component{

    public $token, $countries, $states, $cities, $search_state, $search_city;
    //* Se crea un constructo para buscar el Token q usaran los demas metodos
    public function mount() {
        
        $this->token = '';
        $this->states= [];
        $this->cities= [];
        $this->search_state = [];
        
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

    public function render(){
        return view('livewire.pages.auth.register');
    }


    public function mostrarEstados(){
        
        if (!empty($this->search_state)) {
       
            $statesResponse = Http::withHeaders([
                "Authorization"=> "Bearer ". $this->token,
                "Accept"=> "application/json"
            ])->get('https://www.universal-tutorial.com/api/states/'.$this->search_state);
            
            $this->states = $statesResponse->json();
        }
    }
    
    public function mostrarCiudades(){
        $citiesResponse = Http::withHeaders([
            "Authorization"=> "Bearer ". $this->token,
            "Accept"=> "application/json"
        ])->get('https://www.universal-tutorial.com/api/cities/'.$this->search_city);
        
        $this->cities = $citiesResponse->json();
    }
    
}