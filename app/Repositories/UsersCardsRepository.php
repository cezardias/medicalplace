<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Auth;

use App\UsersCards;

class UsersCardsRepository 
{
    private $model;
    
    public function __construct() {
        $this->model = new UsersCards();
    }

    public function create($cartao, $user) {

        $this->model->user_id = $user;
        $this->model->token = '';
        $this->model->brand = $cartao->brand;
        $this->model->first_digits = $cartao->first_digits;
        $this->model->last_digits = $cartao->last_digits;
        $this->model->exp_month = $cartao->exp_month;
        $this->model->exp_year = $cartao->exp_year;
        $this->model->holder = $cartao->holder->name;
        $this->model->principal = 0;
        $this->model->save();

    }

    public function getMeusCartoes($user_id) {
        $cartoes = DB::table('users_cards')
        ->select('users_cards.*')
        ->where([
            ['user_id',$user_id]
            ])
        ->orderBy('created_at')
        ->get();
        return $cartoes;
    }

    
}