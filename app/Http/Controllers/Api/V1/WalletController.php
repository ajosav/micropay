<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\Wallet\FundWalletRequest;
use App\Contracts\Repository\WalletRepositoryInterface;
use App\Http\Requests\Wallet\WithdrawalRequest;

class WalletController extends Controller
{
    protected $wallet_repository;

    public function __construct(WalletRepositoryInterface $wallet_repository)
    {
        $this->middleware('jwt')->except(['fundWallet']);
        $this->wallet_repository = $wallet_repository;
    }

    public function fundMyWallet(FundWalletRequest $request)
    {
        $wallet = $request->user()->baseWallet();
        return $this->fund($request, $wallet);
    }

    public function fundWallet(FundWalletRequest $request)
    {
        $wallet = $this->wallet_repository->findByReference($request->wallet_id);
        return $this->fund($request, $wallet);
    }

    public function getWalletBalance(Request $request)
    {
        $wallet = $request->user()->baseWallet();
        return response()->success('Successful', ['balance' => $wallet->balance]);
    }

    public function withdrawFromWallet(WithdrawalRequest $request)
    {
        $wallet = $request->user()->baseWallet();
        $transaction = $wallet->withdraw($request->amount);
        return response()->success("Transaction in progress", $transaction);
    }

    private function fund($request, $wallet)
    {
        try {
            if($request->reference)
                $transaction = $wallet->deposit($request->amount, $request->only('reference'));
            else
                $transaction = $wallet->deposit($request->amount);
            
            return response()->suceess('Transaction initiated successfully', $transaction);
            
        } catch(QueryException $e) {
            report($e);
            return response()->errorResponse("Transaction could not be initiated due to an error, please try again later");
        }
    }
}
